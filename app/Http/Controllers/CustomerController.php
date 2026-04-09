<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                    'customer_profiles.county',
                    'customer_profiles.school_name',
                    'users.created_at',
                    DB::raw("GROUP_CONCAT(DISTINCT organisation_categories.name ORDER BY organisation_categories.name SEPARATOR ', ') as organisation_names"),
                ])
                ->groupBy([
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.phone',
                    'customer_profiles.county',
                    'customer_profiles.school_name',
                    'users.created_at',
                ]);

            return DataTables::of($customers)
                ->addIndexColumn()
                ->editColumn('organisation_names', fn ($row) => $row->organisation_names ?: '-')
                ->editColumn('school_name', fn ($row) => $row->school_name ?: '-')
                ->editColumn('county', fn ($row) => $row->county ?: '-')
                ->addColumn('status', fn () => '<span class="customer-status-badge">Active</span>')
                ->addColumn('action', function ($row) {
                    $btn = '<div class="customer-actions">';
                    $btn .= '<button type="button" class="customer-action-btn edit" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Edit"><i class="mdi mdi-pencil"></i></button>';
                    $btn .= '<button type="button" class="customer-action-btn delete" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Delete"><i class="mdi mdi-trash-can-outline"></i></button>';
                    $btn .= '</div>';

                    return $btn;
                })
                ->editColumn('created_at', fn ($row) => $row->created_at->format('d M Y'))
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.customers.index');
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
}
