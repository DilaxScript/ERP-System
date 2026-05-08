<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Favicons -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/favicon/apple-touch-icon.png') }}" sizes="180x180">
    <link rel="icon" href="{{ asset('assets/img/favicon/favicon-32x32.png') }}" sizes="32x32" type="image/png">
    <link rel="icon" href="{{ asset('assets/img/favicon/favicon-16x16.png') }}" sizes="16x16" type="image/png">
    <link rel="mask-icon" href="{{ asset('assets/img/favicon/safari-pinned-tab.svg') }}" color="#563d7c">
    <link rel="icon" href="{{ asset('assets/img/favicon/favicon.ico') }}">
    <meta name="msapplication-config" content="{{ asset('assets/img/favicons/browserconfig.xml') }}">
    <meta name="theme-color" content="#563d7c">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Volt Bootstrap 5 Admin Dashboard Template">
    <meta name="author" content="Themesberg">
    <meta name="keywords" content="bootstrap, admin, dashboard, volt, laravel">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fontawesome -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

    <!-- Sweet Alert -->
    <link href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

    <!-- Volt CSS -->
    <link href="{{ asset('css/volt.css') }}" rel="stylesheet">
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">

    <style>
        :root {
            --app-bg: #f3f7fb;
            --panel-border: rgba(148, 163, 184, 0.18);
            --shadow-soft: 0 20px 45px rgba(15, 23, 42, 0.08);
            --radius-xl: 28px;
        }

        html, body {
            background:
                radial-gradient(circle at top left, rgba(31, 117, 254, 0.08), transparent 28%),
                radial-gradient(circle at right, rgba(244, 185, 66, 0.10), transparent 24%),
                var(--app-bg);
            color: #152033;
        }

        body {
            min-height: 100vh;
        }

        .app-shell {
            min-height: 100vh;
            padding: 0.75rem;
        }

        .app-frame {
            width: 86.5%;
            max-width: none;
            margin: 0;
            min-height: calc(100vh - 1.5rem);
        }

        .app-layout {
            gap: 1rem;
            align-items: flex-start;
            width: 100%;
        }

        .app-sidebar-col {
            width: 292px;
            flex: 0 0 292px;
        }

        .app-main-col {
            flex: 1 1 auto;
            min-width: 0;
        }

        .app-main-inner {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0.15rem 0 0;
        }

        main.content {
            padding: 0 0 2rem;
            min-height: calc(100vh - 2rem);
            width: 100%;
            max-width: 100%;
        }

        .card,
        .dropdown-menu,
        .table-wrapper,
        .navbar-top {
            border-color: var(--panel-border) !important;
        }

        .card,
        .table-wrapper {
            box-shadow: var(--shadow-soft) !important;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .table-wrapper {
            border-radius: 26px !important;
            background: rgba(255, 255, 255, 0.94);
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .mobile-menu-btn {
            background: linear-gradient(135deg, #17355f 0%, #1f75fe 100%) !important;
            color: white !important;
            border: none;
            border-radius: 999px;
            padding: 10px 18px;
            font-weight: 700;
            box-shadow: 0 14px 30px rgba(23, 53, 95, 0.22);
        }

        .table thead th {
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.08em;
            color: #6b7a92;
            border-bottom: 1px solid rgba(148, 163, 184, 0.14);
            background: linear-gradient(180deg, #fbfdff 0%, #f3f8ff 100%);
            padding: 1rem 1rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .table tbody td {
            vertical-align: middle;
            padding: 1rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
            color: #1f2937;
            background: transparent;
        }

        .table tbody tr {
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .table-hover tbody tr:hover {
            background: rgba(31, 117, 254, 0.04);
        }

        .table tbody tr:last-child td {
            border-bottom: 0;
        }

        .table tbody td:first-child,
        .table thead th:first-child {
            padding-left: 1.2rem;
        }

        .table tbody td:last-child,
        .table thead th:last-child {
            padding-right: 1.2rem;
        }

        .table .btn-link {
            color: #17355f !important;
        }

        .table .dropdown-menu {
            border-radius: 16px;
            padding: 0.45rem 0;
            border: 1px solid rgba(148, 163, 184, 0.14);
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.1);
        }

        .table .dropdown-item {
            font-weight: 600;
            padding: 0.65rem 0.95rem;
        }

        .table .dropdown-item:hover {
            background: rgba(31, 117, 254, 0.08);
        }

        @media (max-width: 991.98px) {
            #sidebarMenu {
                max-height: calc(100vh - 90px);
                overflow-y: auto;
                margin: 0 0 1rem;
                border-radius: 24px;
                box-shadow: var(--shadow-soft);
            }

            .app-shell {
                padding: 0.5rem;
            }

            .app-layout {
                display: block;
            }

            .app-sidebar-col,
            .app-main-col {
                width: 100%;
                flex: none;
            }

            main.content {
                padding: 0.25rem 0 2rem;
            }
        }

        @media (max-width: 767.98px) {
            .table thead th,
            .table tbody td {
                padding: 0.85rem 0.8rem;
            }

            .table tbody td:first-child,
            .table thead th:first-child {
                padding-left: 0.9rem;
            }

            .table tbody td:last-child,
            .table thead th:last-child {
                padding-right: 0.9rem;
            }
        }
    </style>


    @livewireStyles
    <title>{{ config('app.name') }} | @yield("title")</title>
</head>

<body>
    @php
        $route = request()->route()->getName();
        $simpleRoutes = ['login', 'register', 'forgot-password', 'reset-password'];
    @endphp

    @if (in_array($route, $simpleRoutes))
        {{-- Simple Pages --}}
        @yield("content")
        @include('layouts.footer2')
    @else
        {{-- Full App Layout --}}
        <div class="app-shell">
            <div class="app-frame">
                {{-- 📱 Mobile Toggle Button --}}
                <div class="d-lg-none">
                    <button class="btn mobile-menu-btn m-2" type="button"
        data-bs-toggle="collapse" data-bs-target="#sidebarMenu"
        aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
    ☰ Open Menu
</button>
                </div>

                <div class="app-layout">
                    {{-- Sidebar --}}
                    <aside class="app-sidebar-col">
                        @include('layouts.sidenav')
                    </aside>

                    {{-- Main Content --}}
                    <section class="app-main-col">
                        <div class="app-main-inner">
                            @include('layouts.nav')
                            <main class="content">
                                <livewire:top-bar />
                                @yield("content")
                            </main>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    @endif

    @livewireScripts

    <!-- JS Scripts -->
    <script src="{{ asset('js/theme.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/on-screen.umd.min.js') }}"></script>
    <script src="{{ asset('assets/js/smooth-scroll.polyfills.min.js') }}"></script>
    <script src="{{ asset('assets/js/volt.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>

    @stack('scripts')

    @if (Session::has('message'))
        <script>
            Swal.fire({
                timer: 2500,
                icon: "{{ Session::get('icon') }}",
                title: "{{ Session::get('title') }}",
                text: "{{ Session::get('message') }}",
            });
        </script>
    @endif
</body>
</html>
