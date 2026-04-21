<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>
        {{ config('app.name', 'Snappy') }}
        @hasSection('title')
            | @yield('title')
        @endif
    </title>
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png')}}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        @media (min-width: 992px) {
            .page-body-wrapper .sidebar {
                position: fixed;
                top: 70px;
                left: 0;
                bottom: 0;
                width: 244px;
                overflow-y: auto;
                z-index: 1000;
            }

            .page-body-wrapper .main-panel {
                margin-left: 244px;
                width: calc(100% - 244px);
                min-height: calc(100vh - 70px);
            }

            .sidebar-icon-only .page-body-wrapper .sidebar {
                width: 70px;
            }

            .sidebar-icon-only .page-body-wrapper .main-panel {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
        }
    </style>
    @stack('styles')
  </head>
  <body class="with-welcome-text">
    <div class="container-scroller">
    @include('layouts.toast')
      @include('customer-panel.layouts.header')
      <div class="container-fluid page-body-wrapper">
        @include('customer-panel.layouts.sidebar')
        <div class="main-panel">
            @yield('content')
        </div>
      </div>
    </div>
    <script src="{{ asset('admin/assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('admin/assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('admin/assets/js/template.js') }}"></script>
    <script src="{{ asset('admin/assets/js/settings.js') }}"></script>
    <script src="{{ asset('admin/assets/js/hoverable-collapse.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    @stack('scripts')
  </body>
</html>
