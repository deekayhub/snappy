<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                    $btn .= '<button type="button" class="supplier-action-btn edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="mdi mdi-pencil"></i></button>';
                    $btn .= '<button type="button" class="supplier-action-btn delete" data-toggle="tooltip" data-placement="top" title="Delete"><i class="mdi mdi-trash-can-outline"></i></button>';
                    $btn .= '</div>';
                    return '';
                })
                ->addColumn('status', function ($row) {
                    return '<span class="supplier-status-badge">Active</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y');
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.suppliers.index');
    }
}
