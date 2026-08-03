<?php

namespace App\Http\Controllers;

use App\Models\OrganisationCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $customers = User::query()
                ->role('customer')
                ->leftJoin('customer_profiles', 'customer_profiles.user_id', '=', 'users.id')
                ->leftJoin('organisation_category_user', 'organisation_category_user.user_id', '=', 'users.id')
                ->leftJoin('organisation_categories', function ($join) {
                    $join->on('organisation_categories.id', '=', 'organisation_category_user.organisation_category_id')
                        ->where('organisation_categories.type', '=', 'customer');
                })
                ->select([
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.phone',
                    'users.is_active',
                    'customer_profiles.county',
                    'customer_profiles.school_name',
                    'users.created_at',
                    'users.last_login_at',
                    DB::raw("GROUP_CONCAT(DISTINCT organisation_categories.name ORDER BY organisation_categories.name SEPARATOR ', ') as organisation_names"),
                ])
                ->groupBy([
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.phone',
                    'users.is_active',
                    'customer_profiles.county',
                    'customer_profiles.school_name',
                    'users.created_at',
                    'users.last_login_at',
                ]);

            return DataTables::of($customers)
                ->addIndexColumn()
                ->editColumn('organisation_names', function ($row) {
                    if (! $row->organisation_names) {
                        return '-';
                    }

                    return collect(explode(', ', $row->organisation_names))
                        ->map(fn ($name) => '<span class="customer-category-badge">' . e($name) . '</span>')
                        ->implode(' ');
                })
                ->editColumn('school_name', fn ($row) => $row->school_name ?: '-')
                ->editColumn('county', fn ($row) => $row->county ?: '-')
                ->addColumn('status', function ($row) {
                    $badge = $row->is_active ? 'customer-status-badge' : 'customer-status-badge inactive';
                    $label = $row->is_active ? 'Active' : 'Inactive';
                    return '<span class="' . $badge . '">' . $label . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="customer-actions">';
                    $btn .= '<button type="button" class="customer-action-btn edit" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Edit"><i class="mdi mdi-pencil"></i></button>';
                    $btn .= '<button type="button" class="customer-action-btn delete" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Delete"><i class="mdi mdi-trash-can-outline"></i></button>';
                    $btn .= '</div>';

                    return $btn;
                })
                ->editColumn('created_at', fn ($row) => $row->created_at->format('d M Y'))
                ->editColumn('last_login_at', fn ($row) => $row->last_login_at?->format('d M Y H:i') ?? '-')
                ->rawColumns(['status', 'action', 'organisation_names'])
                ->make(true);
        }

        $organisation = OrganisationCategory::orderBy('type')->orderBy('name')->get();

        return view('admin.customers.index', compact('organisation'));
    }

    public function edit($id)
    {
        $user = User::with('customerProfile', 'organisationCategories')->findOrFail($id);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'is_active' => $user->is_active,
            'school_name' => $user->customerProfile?->school_name ?? '',
            'county' => $user->customerProfile?->county ?? '',
            'categories' => $user->organisationCategories->where('type', 'customer')->pluck('id'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
            'school_name' => 'nullable|string|max:255',
            'county' => 'nullable|string|max:255',
            'customer_organisation' => 'required|array|min:1',
            'customer_organisation.*' => [
                'integer',
                Rule::exists('organisation_categories', 'id')->where('type', 'customer'),
            ],
        ]);

        $user = User::with('customerProfile')->findOrFail($id);

        DB::transaction(function () use ($user, $validated) {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'is_active' => $validated['is_active'],
            ]);

            $user->customerProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'school_name' => $validated['school_name'] ?? null,
                    'county' => $validated['county'] ?? null,
                ]
            );

            $this->syncOrganisationCategories(
                $user,
                'customer',
                $validated['customer_organisation']
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully.',
        ]);
    }

    public function customerDestroy($id)
    {
        $user = User::with('organisationCategories')->findOrFail($id); 

        DB::transaction(function () use ($user) {
            $user->customerProfile()->delete();
            $user->organisationCategories()->detach();
            $user->delete();
        });

        return response()->json([
            'success' => true
        ]);
    }

    protected function syncOrganisationCategories($user, string $type, array $selectedIds): void
    {
        $typeCategoryIds = OrganisationCategory::where('type', $type)->pluck('id');
        $existingTypeIds = $user->organisationCategories()
            ->whereIn('organisation_categories.id', $typeCategoryIds)
            ->pluck('organisation_categories.id');

        $idsToDetach = $existingTypeIds->diff($selectedIds)->all();

        if (! empty($idsToDetach)) {
            $user->organisationCategories()->detach($idsToDetach);
        }

        $user->organisationCategories()->syncWithoutDetaching($selectedIds);
    }
}
