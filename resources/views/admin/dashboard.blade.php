@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="content-wrapper p-3">
    <div class="row">
        <div class="col-sm-12 mb-3">
            <div class="row">
                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Total Jobs</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="stat text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign align-middle"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                    </div>
                                </div>
                            </div>
                            <h2 class="mt-1 mb-3">{{ $jobs->count() ?? '0' }}</h2>
                            <div class="mb-0">
                                <span class="badge badge-success">0%</span>
                                <span class="text-muted">Since last week</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Total Orders</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="stat text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag align-middle"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                    </div>
                                </div>
                            </div>
                            <h2 class="mt-1 mb-3">0</h2>
                            <div class="mb-0">
                                <span class="badge badge-danger">0%</span>
                                <span class="text-muted">Since last week</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Total Supplier</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="stat text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity align-middle"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                                    </div>
                                </div>
                            </div>
                            <h2 class="mt-1 mb-3">{{ $supplierCount ?? '0' }}</h2>
                            <div class="mb-0">
                                <span class="badge badge-success">0%</span>
                                <span class="text-muted">Since last week</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Total Customer</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="stat text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart align-middle"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                    </div>
                                </div>
                            </div>
                            <h2 class="mt-1 mb-3">{{ $customerCount ?? '0' }}</h2>
                            <div class="mb-0">
                                <span class="badge badge-success">0%</span>
                                <span class="text-muted">Since last week</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div class="row">
            <div class="col-lg-8 d-flex flex-column">
                <div class="row flex-grow">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card card-rounded">
                        <div class="card-body">
                            <div class="d-sm-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="card-title card-title-dash">Quote Overview</h4>
                                <p class="card-subtitle card-subtitle-dash">Customer Quote post by Month</p>
                            </div>
                            {{-- <div>
                                <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle toggle-dark mb-0 me-0" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> This month </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                    <h6 class="dropdown-header">Settings</h6>
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <a class="dropdown-item" href="#">Something else here</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">Separated link</a>
                                </div>
                                </div>
                            </div> --}}
                            </div>
                            <div class="d-sm-flex align-items-center mt-1 justify-content-between">
                            <div class="d-sm-flex align-items-center mt-4 justify-content-between">
                                <h2 class="me-2 fw-bold">0</h2>
                                {{-- <h4 class="me-2">USD</h4> --}}
                                <h4 class="text-success">(+0%)</h4>
                            </div>
                            <div class="me-3">
                                <div id="marketingOverview-legend"></div>
                            </div>
                            </div>
                            <div class="chartjs-bar-wrapper mt-3">
                            <canvas id="marketingOverview"></canvas>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row flex-grow">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card card-rounded">
                            <div class="card-body">
                                <div class="d-sm-flex justify-content-between align-items-start">
                                    <div>
                                        <h4 class="card-title card-title-dash">Latest Quotes</h4>
                                        {{-- <p class="card-subtitle card-subtitle-dash">You have 0+ new Quotes</p> --}}
                                    </div>
                                    <div>
                                        {{-- <button class="btn btn-primary text-white mb-0 me-0" type="button"><i class="mdi mdi-account-plus"></i>Add new member</button> --}}
                                    </div>
                                </div>
                                <div class="table-responsive  mt-1">
                                <table class="table w-100 select-table">
                                    <thead>
                                    <tr>                                        
                                        <th>Customer</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($jobs->take(10) as $job )
                                            <tr>                                        
                                                <td>
                                                    <div>
                                                        <h6>{{ $job->user?->name ?? '' }}</h6>
                                                        <p>{{ $job->user?->email ?? '' }}</p>
                                                    </div>
                                                </td>
                                                <td>
                                                <h6>{{ $job->title ?? '' }}</h6>
                                                {{-- <p class="text-truncate">{{ $job->description ?? '' }}</p> --}}
                                                </td>
                                                <td>{{ $job->category ?? '' }}</td>
                                                <td>
                                                <div class="badge badge-opacity-success">{{ $job->status }}</div>
                                                </td>
                                            </tr>  
                                        @empty    
                                            <tr>
                                                <td colspan="4" class="text-center">No quotes found.</td>
                                            </tr>                                      
                                        @endforelse                                    
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 d-flex flex-column">
                <div class="row flex-grow">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card card-rounded">
                            <div class="card-body">
                                <div class="row">
                                <div class="col-lg-12">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="card-title card-title-dash">Type By Amount</h4>
                                    </div>
                                    <div>
                                    <canvas class="my-auto" id="doughnutChart"></canvas>
                                    </div>
                                    <div id="doughnutChart-legend" class="mt-5 text-center"></div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row flex-grow">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card card-rounded">
                        <div class="card-body">
                            <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h4 class="card-title card-title-dash"> Report</h4>
                                </div>
                                <div>
                                    <div class="dropdown">
                                    <button class="btn btn-light dropdown-toggle toggle-dark mb-0 me-0" type="button" id="dropdownMenuButton3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Month Wise </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton3">
                                        <h6 class="dropdown-header">week Wise</h6>
                                        <a class="dropdown-item" href="#">Year Wise</a>
                                    </div>
                                    </div>
                                </div>
                                </div>
                                <div class="mt-3">
                                <canvas id="leaveReport"></canvas>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="row flex-grow">
                </div>
            </div>
        </div>
    </div>
@endsection

