<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Subscription;
use Yajra\DataTables\Facades\DataTables;

class SubscribedUsersController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $subscriptions = Subscription::query()
                ->where('subscriptions.type', 'default')
                ->leftJoin('users', 'users.id', '=', 'subscriptions.user_id')
                ->leftJoin('plans', 'plans.stripe_price_id', '=', 'subscriptions.stripe_price')
                ->select([
                    'subscriptions.id',
                    'subscriptions.user_id',
                    'subscriptions.stripe_status',
                    'subscriptions.stripe_price',
                    'subscriptions.trial_ends_at',
                    'subscriptions.ends_at',
                    'subscriptions.created_at',
                    'users.name',
                    'users.email',
                    'users.phone',
                    'users.is_active',
                    'plans.name as plan_name',
                    'plans.price as plan_price',
                    'plans.duration',
                    'plans.duration_months',
                ]);

            return DataTables::of($subscriptions)
                ->addIndexColumn()
                ->editColumn('name', function ($row) {
                    $statusIcon = $row->is_active
                        ? '<span class="subs-user-dot"></span>'
                        : '<span class="subs-user-dot inactive"></span>';
                    return $statusIcon . e($row->name);
                })
                ->editColumn('email', fn ($row) => $row->email ?: '-')
                ->addColumn('role', function ($row) {
                    $user = \App\Models\User::find($row->user_id);
                    $role = $user?->getRoleNames()->first() ?? 'customer';
                    return '<span class="subs-role-badge">' . e(ucfirst($role)) . '</span>';
                })
                ->addColumn('plan', fn ($row) => $row->plan_name ?: '-')
                ->addColumn('price', function ($row) {
                    if ($row->plan_price === null) {
                        return '-';
                    }
                    return '£' . number_format($row->plan_price / 100, 2);
                })
                ->addColumn('duration', function ($row) {
                    if ($row->duration === 'lifetime') {
                        return 'Lifetime';
                    }
                    if ($row->duration_months >= 12) {
                        $years = $row->duration_months / 12;
                        return $years == 1 ? '1 Year' : "{$years} Years";
                    }
                    return $row->duration_months == 1 ? '1 Month' : "{$row->duration_months} Months";
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d M Y');
                })
                ->addColumn('renews', function ($row) {
                    if ($row->ends_at) {
                        return Carbon::parse($row->ends_at)->format('d M Y');
                    }
                    if ($row->trial_ends_at) {
                        $renewAt = Carbon::parse($row->trial_ends_at);
                        return $renewAt->format('d M Y') . ' <span class="text-muted">(trial)</span>';
                    }
                    if ($row->duration_months > 0) {
                        return Carbon::parse($row->created_at)->addMonths($row->duration_months)->format('d M Y');
                    }
                    return '-';
                })
                ->addColumn('status', function ($row) {
                    $status = $row->stripe_status;
                    $map = [
                        'active' => ['subs-status-badge', 'Active'],
                        'trialing' => ['subs-status-badge trialing', 'Trialing'],
                        'past_due' => ['subs-status-badge past-due', 'Past Due'],
                        'canceled' => ['subs-status-badge canceled', 'Canceled'],
                        'incomplete' => ['subs-status-badge incomplete', 'Incomplete'],
                        'incomplete_expired' => ['subs-status-badge incomplete', 'Incomplete Expired'],
                        'unpaid' => ['subs-status-badge canceled', 'Unpaid'],
                    ];
                    [$class, $label] = $map[$status] ?? ['subs-status-badge', ucfirst($status)];
                    return '<span class="' . $class . '">' . e($label) . '</span>';
                })
                ->filterColumn('email', function ($query, $keyword) {
                    $query->where('users.email', 'like', "%{$keyword}%");
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where('users.name', 'like', "%{$keyword}%");
                })
                ->filterColumn('plan', function ($query, $keyword) {
                    $query->where('plans.name', 'like', "%{$keyword}%");
                })
                ->orderColumn('name', 'users.name $1')
                ->orderColumn('email', 'users.email $1')
                ->orderColumn('created_at', 'subscriptions.created_at $1')
                ->orderColumn('plan', 'plans.name $1')
                ->orderColumn('price', 'plans.price $1')
                ->orderColumn('duration', 'plans.duration_months $1')
                ->orderColumn('status', 'subscriptions.stripe_status $1')
                ->rawColumns(['name', 'role', 'renews', 'status'])
                ->make(true);
        }

        $stats = [
            'total' => Subscription::where('type', 'default')->count(),
            'active' => Subscription::where('type', 'default')->where('stripe_status', 'active')->count(),
            'past_due' => Subscription::where('type', 'default')->where('stripe_status', 'past_due')->count(),
            'canceled' => Subscription::where('type', 'default')->where('stripe_status', 'canceled')->count(),
            'revenue' => Subscription::query()
                ->where('type', 'default')
                ->where('stripe_status', 'active')
                ->leftJoin('plans', 'plans.stripe_price_id', '=', 'subscriptions.stripe_price')
                ->whereNotNull('plans.price')
                ->sum('plans.price'),
        ];

        return view('admin.subscribed-users.index', compact('stats'));
    }
}
