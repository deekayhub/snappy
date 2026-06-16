<?php

namespace App\Http\Controllers;

use App\Models\OrganisationCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $suppliers = User::query()
                ->role('supplier')
                ->leftJoin('supplier_profiles', 'supplier_profiles.user_id', '=', 'users.id')
                ->leftJoin('organisation_category_user', 'organisation_category_user.user_id', '=', 'users.id')
                ->leftJoin('organisation_categories', function ($join) {
                    $join->on('organisation_categories.id', '=', 'organisation_category_user.organisation_category_id')
                        ->where('organisation_categories.type', '=', 'supplier');
                })
                ->select([
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.phone',
                    'users.is_active',
                    'supplier_profiles.company_name',
                    'supplier_profiles.address',
                    'supplier_profiles.website',
                    'users.created_at',
                    DB::raw("GROUP_CONCAT(DISTINCT organisation_categories.name ORDER BY organisation_categories.name SEPARATOR ', ') as organisation_names"),
                ])
                ->groupBy([
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.phone',
                    'users.is_active',
                    'supplier_profiles.company_name',
                    'supplier_profiles.address',
                    'supplier_profiles.website',
                    'users.created_at',
                ]);

            return DataTables::of($suppliers)
                ->addIndexColumn()
                ->editColumn('company_name', fn ($row) => $row->company_name ?: '-')
                ->editColumn('organisation_names', fn ($row) => $row->organisation_names ?: '-')
                ->editColumn('address', fn ($row) => $row->address ?: '-')
                ->editColumn('website', fn ($row) => $row->website ?: '-')
                ->addColumn('action', function ($row) { 
                    $btn = '<div class="supplier-actions">';
                    $btn .= '<button type="button" class="supplier-action-btn edit" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Edit"><i class="mdi mdi-pencil"></i></button>';
                    $btn .= '<button type="button" class="supplier-action-btn delete" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Delete"><i class="mdi mdi-trash-can-outline"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->addColumn('status', function ($row) {
                    $badge = $row->is_active ? 'supplier-status-badge' : 'supplier-status-badge inactive';
                    $label = $row->is_active ? 'Active' : 'Inactive';
                    return '<span class="' . $badge . '">' . $label . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y');
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $organisation = OrganisationCategory::orderBy('type')->orderBy('name')->get();

        return view('admin.suppliers.index', compact('organisation'));
    }

    public function edit($id)
    {
        $user = User::with('supplierProfile', 'organisationCategories')->findOrFail($id);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'is_active' => $user->is_active,
            'company_name' => $user->supplierProfile?->company_name ?? '',
            'address' => $user->supplierProfile?->address ?? '',
            'website' => $user->supplierProfile?->website ?? '',
            'categories' => $user->organisationCategories->where('type', 'supplier')->pluck('id'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'supplier_organisation' => 'required|array|min:1',
            'supplier_organisation.*' => [
                'integer',
                Rule::exists('organisation_categories', 'id')->where('type', 'supplier'),
            ],
        ]);

        $user = User::with('supplierProfile')->findOrFail($id);

        DB::transaction(function () use ($user, $validated) {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'is_active' => $validated['is_active'],
            ]);

            $user->supplierProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $validated['company_name'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'website' => $validated['website'] ?? null,
                ]
            );

            $this->syncOrganisationCategories(
                $user,
                'supplier',
                $validated['supplier_organisation']
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Supplier updated successfully.',
        ]);
    }

    public function supplierDestroy($id)
    {
        $user = User::with('organisationCategories')->findOrFail($id); 

        DB::transaction(function () use ($user) {
            $user->supplierProfile()->delete();
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
