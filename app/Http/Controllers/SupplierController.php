<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $suppliers = User::query()
                ->select([
                    'id',
                    'name',
                    'email',
                    'phone',
                    'company_name',
                    'organisation',
                    'address',
                    'county',
                    'created_at'
                ]);

            return DataTables::of($suppliers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="supplier-actions">';
                    $btn .= '<button type="button" class="supplier-action-btn edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="mdi mdi-pencil"></i></button>';
                    $btn .= '<button type="button" class="supplier-action-btn delete" data-toggle="tooltip" data-placement="top" title="Delete"><i class="mdi mdi-trash-can-outline"></i></button>';
                    $btn .= '</div>';
                    return $btn;
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
