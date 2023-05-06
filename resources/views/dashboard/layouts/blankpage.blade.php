@auth
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>@yield('title', 'Wuusu Admin')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('dashboard/img/mainlogo.jpg') }}">

    <!-- Custom fonts for this template-->
    <link href="{{ asset('dashboard/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        
    <!-- Custom styles for this template-->
    <link href="{{ asset('dashboard/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- Custom styles for this page -->
    @yield('stylesheets')
</head>

<body id="page-top">

    <div class="shadow-sm w-100" style="position: fixed; z-index: 1; background-color: white; border-bottom: 1px solid #ddd;">
        <div class="px-4 w-100" style="padding-top: 16px; padding-bottom: 16px;">
            <table style="width: 100%;">
                <tr>
                    <td>
                        <!-- Page Heading -->
                        <h1 class="h3 mb-0 text-dark" style="text-shadow: 1px 2px #ccc; font-weight: 700;">@yield('title', 'Page of Wuusu Admin')</h1>
                    </td>
                    <td>
                        <div class="d-flex flex-row-reverse">
                            
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div style="height: 90px;"></div>

    <!-- Begin Page Content -->
    <div class="container-fluid px-4">

        <!-- Alerts -->
        @include('dashboard.includes.alerts')
        <!-- End of Alerts -->

        <!-- Content -->
        @yield('content', 'This page is empty.')
        <!-- End of Content -->

    </div>
    <!-- /.container-fluid -->

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('dashboard/vendor/jquery/jquery.js') }}"></script>
    <script src="{{ asset('dashboard/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('dashboard/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('dashboard/js/sb-admin-2.min.js') }}"></script>

    <!-- Google Recaptcha v3 -->
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>

    <!-- Page level plugins -->
    @yield('scripts')

</body>

</html>

@else
    <?php
        echo redirect()->route('web.login', ['redirect_to' => request()->getPathInfo()]);
    ?>
@endauth