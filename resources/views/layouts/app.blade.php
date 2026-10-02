<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        (function() {
            let theme = 'light';
            const forceLightTheme = @json(!auth()->check() && request()->routeIs('login', 'register'));
            if (!forceLightTheme) {
                try {
                    theme = localStorage.getItem('g-sched-theme') === 'dark' ? 'dark' : 'light';
                } catch (error) {
                    // Keep the default light theme when storage is unavailable.
                }
            }
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <title>{{ config('app.name', 'G-SCHED') }} @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    @unless(request()->routeIs('register'))
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    @endunless
    @yield('styles')
    <style>
        :root {
            /* G-SCHED Official Color Palette */
            --navy: #053F5C;
            --medium-blue: #429EBD;
            --action-blue: #176B88;
            --light-blue: #9FE7F5;
            --yellow: #F7AD19;
            --orange: #F27F0C;
            --status-available-text: #176B88;
            --status-booked-text: #A64B00;
            --link-color: #176B88;
            
            /* Semantic colors */
            --text-primary: #053F5C;
            --text-secondary: #475569;
            --text-muted: #64748B;
            
            /* Backgrounds */
            --bg-light: #F7FAFC;
            --card-bg: #FFFFFF;
            --body-bg: #FFFFFF;
            --body-text: #212529;

            /* Borders */
            --border-color: #E2E8F0;
            --border-color-light: #F1F5F9;
            --badge-text-light: #FFFFFF;

            /* Navbar & Sidebar */
            --navbar-height: 76px;
            --sidebar-bg: #2148db;
            --sidebar-border: rgba(255, 210, 0, 0.3);
            --sidebar-text: #FFFFFF;
            --sidebar-collapsed-width: 76px;
            --sidebar-expanded-width: 250px;
            
            /* Shadow */
            --shadow-sm: 0 1px 3px rgba(5, 63, 92, 0.08), 0 1px 2px rgba(5, 63, 92, 0.05);
        }

        @media (max-width: 767.98px) {
            :root {
                --navbar-height: 56px;
            }
        }

        .app-navbar {
            --navbar-yellow: #FFD200;
            --navbar-blue: #2148db;
            --navbar-dropdown-shadow: 0 10px 30px rgba(5, 63, 92, 0.1);
            height: var(--navbar-height);
            background-color: var(--navbar-yellow);
            border-bottom: 1px solid var(--border-color);
        }

        .app-navbar .navbar-brand {
            color: var(--navbar-blue);
            font-size: 1.25rem;
        }

        .app-navbar .navbar-brand-logo {
            width: 36px;
            height: 36px;
            object-fit: cover;
            border-radius: 50%;
        }

        .app-navbar .navbar-toggler {
            border: 0;
        }

        .app-navbar .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%232148db' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .app-navbar .app-navbar-control {
            color: var(--navbar-blue);
            border-radius: 0.5rem;
            transition: background-color 0.15s ease-in-out;
        }

        .app-navbar .navbar-brand:hover,
        .app-navbar .app-navbar-control:hover,
        .app-navbar .navbar-toggler:hover {
            background-color: rgba(33, 72, 219, 0.12);
        }

        .app-navbar .navbar-brand:focus-visible,
        .app-navbar .app-navbar-control:focus-visible,
        .app-navbar .navbar-toggler:focus-visible {
            outline: 3px solid var(--navbar-blue);
            outline-offset: 2px;
            box-shadow: none;
        }

        .app-navbar .app-navbar-control[aria-expanded="true"],
        .app-navbar .navbar-toggler[aria-expanded="true"] {
            background-color: rgba(33, 72, 219, 0.16);
        }

        .app-navbar .app-navbar-sidebar-icon,
        .app-navbar .app-navbar-menu-icon,
        .app-navbar .app-navbar-notification-icon {
            color: var(--navbar-blue);
        }

        .app-navbar .app-navbar-sidebar-icon {
            font-size: 1.5rem;
        }

        .app-navbar .app-navbar-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0.25rem 0.5rem;
            background-color: var(--navbar-blue);
            color: #FFFFFF;
            font-size: 0.65rem;
            font-weight: 700;
        }

        .app-navbar .dropdown-menu {
            border: 0;
            border-radius: 1rem;
            box-shadow: var(--navbar-dropdown-shadow);
        }

        .app-navbar .app-navbar-notifications-menu {
            width: 480px;
            min-width: 0;
            max-width: calc(100vw - 30px);
            max-height: 450px;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .app-navbar .app-navbar-account-menu {
            min-width: 200px;
            max-width: 90vw;
        }

        .app-navbar .app-navbar-dropdown-header,
        .app-navbar .app-navbar-view-all,
        .app-navbar .app-navbar-logout {
            color: var(--navbar-blue);
        }

        .app-navbar .app-navbar-dropdown-header {
            font-weight: 600;
        }

        .app-navbar .app-navbar-view-all {
            font-weight: 500;
        }

        .app-navbar .app-navbar-notification-link {
            border-bottom: 1px solid var(--border-color-light);
            white-space: normal;
        }

        .app-navbar .app-navbar-notification-icon-wrap {
            width: 40px;
            height: 40px;
            border-radius: 0.75rem;
            background: rgba(159, 231, 245, 0.25);
        }

        .app-navbar .app-navbar-notification-title {
            color: var(--text-primary);
        }

        .app-navbar .app-navbar-notification-time {
            color: var(--text-muted);
        }

        .app-navbar .dropdown-divider {
            border-color: var(--border-color-light);
        }

        .app-navbar .dropdown-item:hover,
        .app-navbar .dropdown-item:focus-visible {
            color: var(--navbar-blue);
            background-color: rgba(33, 72, 219, 0.1);
        }

        .app-navbar .dropdown-item:focus-visible {
            outline: 2px solid var(--navbar-blue);
            outline-offset: -2px;
        }

        .app-navbar .dropdown-item:active {
            color: var(--navbar-blue);
            background-color: rgba(33, 72, 219, 0.16);
        }

        .app-navbar .avatar-sm {
            background-color: var(--navbar-blue);
        }

        .app-navbar .app-navbar-actions {
            flex: 0 0 auto;
            flex-direction: row;
            gap: 0.5rem;
            margin-left: auto;
            padding-left: 0;
        }

        .app-navbar .app-navbar-actions .nav-item {
            flex: 0 0 auto;
        }

        .app-navbar .app-navbar-user-name {
            color: var(--navbar-blue);
            white-space: nowrap;
        }

        .app-navbar .app-navbar-avatar-initial {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            background-color: var(--navbar-blue);
            color: #FFFFFF;
        }

        .app-navbar .navbar-brand-logo {
            flex: 0 0 auto;
        }

        .app-navbar .app-navbar-brand-text {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .app-navbar .app-navbar-sidebar-toggle {
            display: none;
        }

        .app-navbar #sidebarToggle,
        .app-navbar #themeToggle {
            box-sizing: border-box;
            width: 44px;
            min-width: 44px;
            height: 44px;
            min-height: 44px;
            padding: 0 !important;
        }

        body {
            font-family: 'Inter', 'Roboto', sans-serif;
            font-size: clamp(0.875rem, 0.85vw + 0.8rem, 1rem);
        }

        .row.g-4 { --bs-gutter-y: 1.5rem; }

        img {
            max-width: 100%;
            height: auto;
        }

        @media (max-width: 576px) {
            .container-fluid {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            h1.h2 { font-size: 1.5rem; }
            h1.h1 { font-size: 1.75rem; }
            h2 { font-size: 1.25rem; }
            h3 { font-size: 1.1rem; }
            h4 { font-size: 1rem; }
            h5 { font-size: 0.95rem; }
            h6 { font-size: 0.85rem; }
        }

        @media (max-width: 767.98px) {
            .app-navbar .container-fluid {
                flex-wrap: nowrap;
                gap: 0.25rem;
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            .app-navbar .navbar-brand {
                flex: 0 1 auto;
                min-width: 0;
                margin-right: 0.25rem;
                font-size: 1rem;
            }

            .app-navbar .navbar-brand-logo {
                width: 28px;
                height: 28px;
                margin-right: 0.25rem !important;
            }

            .app-navbar .app-navbar-control {
                flex: 0 0 auto;
            }

            .app-navbar .app-navbar-sidebar-icon {
                font-size: 1.25rem;
            }

            .app-navbar .app-navbar-actions {
                gap: 0;
            }

            .app-navbar .app-navbar-actions .nav-link {
                padding: 0.25rem !important;
            }

            .app-navbar .app-navbar-notification-icon {
                font-size: 1.25rem !important;
            }

            .app-navbar .app-navbar-user-name {
                max-width: clamp(3.5rem, 15vw, 5rem);
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                font-size: 0.85rem;
            }

            .app-navbar .app-navbar-avatar-initial,
            .app-navbar .app-navbar-actions .avatar-sm {
                width: 30px;
                height: 30px;
            }
        }

        @media (max-width: 430px) {
            .app-navbar .navbar-brand-school-logo,
            .app-navbar .app-navbar-user-name {
                display: none;
            }
        }

        @media (max-width: 991.98px) {
            .app-navbar .app-navbar-actions .dropdown-menu {
                position: fixed !important;
                top: var(--navbar-height) !important;
                right: 0.5rem !important;
                left: auto !important;
                transform: none !important;
            }
        }

        @media (min-width: 576px) and (max-width: 768px) {
            h1.h2 { font-size: 1.75rem; }
            h1.h1 { font-size: 2rem; }
            h2 { font-size: 1.4rem; }
            h3 { font-size: 1.2rem; }
            h4 { font-size: 1.05rem; }
            h5 { font-size: 0.95rem; }
        }

        .sidebar-wrapper {
            position: fixed;
            top: var(--navbar-height);
            left: 0;
            width: var(--sidebar-collapsed-width);
            min-width: var(--sidebar-collapsed-width);
            max-width: var(--sidebar-collapsed-width);
            height: calc(100vh - var(--navbar-height));
            z-index: 1050;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: width 0.3s ease, min-width 0.3s ease, max-width 0.3s ease, box-shadow 0.3s ease;
        }

        #sidebar {
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
        }

        @media (min-width: 768px) and (hover: hover) {
            .sidebar-wrapper:hover {
                width: var(--sidebar-expanded-width);
                min-width: var(--sidebar-expanded-width);
                max-width: var(--sidebar-expanded-width);
                box-shadow: 0 0 24px rgba(5, 63, 92, 0.12);
            }
        }

        @media (min-width: 768px) {
            .sidebar-wrapper {
                top: var(--navbar-height);
                height: calc(100vh - var(--navbar-height));
                transform: none !important;
            }
        }

        /* Collapsed sidebar text/icon visibility */
        @media (min-width: 768px) {
            .sidebar-wrapper:not(:hover) .sidebar-text,
            .sidebar-wrapper:not(:hover) .sidebar-label {
                opacity: 0;
                width: 0;
                max-width: 0;
                white-space: nowrap;
                overflow: hidden;
                transition: opacity 0.1s ease 0.1s, max-width 0.1s ease 0.1s;
            }

            .sidebar-wrapper:hover .sidebar-text,
            .sidebar-wrapper:hover .sidebar-label {
                opacity: 1;
                width: auto;
                max-width: 13rem;
                transition: opacity 0.1s ease 0.2s, max-width 0.1s ease 0.2s;
            }

            .sidebar-wrapper .nav-link {
                display: flex;
                align-items: center;
                white-space: nowrap;
            }

            .sidebar-wrapper .sidebar-icon {
                flex-shrink: 0;
                width: 24px;
                text-align: center;
            }
        }

        @media (max-width: 767.98px) {
            .sidebar-wrapper .sidebar .nav-link {
                display: flex;
                align-items: center;
                white-space: nowrap;
            }

            .sidebar-wrapper .sidebar-icon {
                flex-shrink: 0;
                width: 24px;
                text-align: center;
            }

            .sidebar-wrapper:not(.show) .sidebar-text,
            .sidebar-wrapper:not(.show) .sidebar-label {
                flex: 0 0 auto;
                opacity: 0;
                width: 0;
                max-width: 0;
                overflow: hidden;
                transition: opacity 0.1s ease, max-width 0.1s ease;
            }

            .sidebar-wrapper.show .sidebar-text,
            .sidebar-wrapper.show .sidebar-label {
                opacity: 1;
                width: auto;
                max-width: 13rem;
                transition: opacity 0.1s ease 0.2s, max-width 0.1s ease 0.2s;
            }

            .sidebar-wrapper:not(.show) .sidebar .nav-link {
                justify-content: center;
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .sidebar-wrapper.show .sidebar .nav-link {
                justify-content: flex-start;
            }

            .sidebar-wrapper:not(.show) .sidebar-iso-badge {
                justify-content: center;
                padding-right: 0;
                padding-left: 0;
            }

            .sidebar-wrapper:not(.show) .sidebar-iso-copy {
                display: none;
            }
        }

        /* Sidebar content vertical centering */
        .sidebar-wrapper .sidebar {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .sidebar-wrapper .sidebar nav {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            min-height: 0;
            overflow-y: auto;
        }

        .sidebar-wrapper .sidebar ul.nav {
            display: flex;
            flex-direction: column;
            flex: 1;
            justify-content: flex-start;
            gap: 0.25rem;
            padding-top: 2rem;
        }

        .sidebar-wrapper .sidebar .nav-item:first-child {
            flex-shrink: 0;
            position: absolute;
            top: 1rem;
            left: 0;
            right: 0;
            text-align: left;
            padding-left: 1rem;
        }

        .sidebar-wrapper .sidebar .nav-link {
            justify-content: center;
        }

        .sidebar-iso-footer {
            flex: 0 0 auto;
            padding: 0.75rem 0.5rem 1rem;
        }

        .sidebar-iso-badge {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            min-height: 3.25rem;
            padding: 0.5rem 0.6rem;
            border: 1px solid rgba(255, 255, 255, 0.38);
            border-radius: 0.55rem;
            background: rgba(255, 255, 255, 0.08);
            color: var(--sidebar-text);
        }

        .sidebar-iso-mark {
            display: flex;
            flex: 0 0 2.35rem;
            align-items: center;
            justify-content: center;
            width: 2.35rem;
            height: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.72);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            line-height: 1;
        }

        .sidebar-iso-copy {
            display: flex;
            min-width: 0;
            flex-direction: column;
            line-height: 1.25;
            white-space: nowrap;
        }

        .sidebar-iso-title {
            font-size: 0.76rem;
            font-weight: 600;
        }

        .sidebar-iso-caption {
            margin-top: 0.1rem;
            font-size: 0.65rem;
            opacity: 0.82;
        }

        @media (min-width: 768px) {
            .sidebar-wrapper:hover .sidebar .nav-link {
                justify-content: flex-start;
            }

            .sidebar-wrapper:not(:hover) .sidebar .nav-link {
                justify-content: center;
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .sidebar-wrapper:not(:hover) .sidebar-iso-footer {
                padding-right: 0.5rem;
                padding-left: 0.5rem;
            }

            .sidebar-wrapper:not(:hover) .sidebar-iso-badge {
                justify-content: center;
                padding-right: 0;
                padding-left: 0;
            }

            .sidebar-wrapper:not(:hover) .sidebar-iso-copy {
                display: none;
            }
        }

        /* Main content shifts when sidebar expands on hover */
        .main-content,
        main.main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease;
        }

        /* Sidebar overlay on hover - content stays fixed */
        /* .sidebar-wrapper:hover ~ .main-content {
            margin-left: var(--sidebar-expanded-width) !important;
        } */

        .sidebar .nav-link {
            padding: 0.75rem 1rem;
            margin: 0.125rem 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        #sidebar .sidebar-label {
            color: var(--sidebar-text);
            letter-spacing: 0.05em;
        }

        #sidebar .nav-link {
            color: var(--sidebar-text);
        }

        #sidebar .nav-link:hover:not(.active) {
            background: rgba(255, 210, 0, 0.1);
            color: var(--sidebar-text);
        }

        #sidebar .nav-link.active {
            background: rgba(255, 210, 0, 0.22);
            border: 1px solid rgba(255, 210, 0, 0.55);
            box-shadow: inset 0 0 0.75rem rgba(255, 210, 0, 0.1), 0 0 0.75rem rgba(255, 210, 0, 0.12);
            color: var(--sidebar-text);
            font-weight: 500;
        }

        #sidebar .nav-link:focus-visible {
            outline: 3px solid var(--sidebar-text);
            outline-offset: 2px;
        }

        @media (max-width: 767.98px) {
            .sidebar .nav-link {
                padding: 0.85rem 1.25rem;
                font-size: 1rem;
            }
        }

        @media (max-width: 576px) {
            .sidebar .nav-link {
                padding: 1rem 1.25rem;
                font-size: 1.1rem;
            }
        }

        /* Bootstrap button overrides to use G-SCHED palette */
        .btn-primary {
            background-color: var(--action-blue);
            border-color: var(--action-blue);
            color: #FFFFFF;
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--navy);
            border-color: var(--navy);
            color: #FFFFFF;
        }
        .btn-secondary {
            background-color: var(--border-color);
            border-color: var(--border-color);
            color: var(--text-primary);
        }
        .btn-secondary:hover, .btn-secondary:focus {
            background-color: var(--navy);
            border-color: var(--navy);
            color: #FFFFFF;
        }
        .btn-success {
            background-color: var(--action-blue);
            border-color: var(--action-blue);
            color: #FFFFFF;
        }
        .btn-success:hover, .btn-success:focus {
            background-color: var(--navy);
            border-color: var(--navy);
            color: #FFFFFF;
        }
        .btn-warning {
            background-color: var(--yellow);
            border-color: var(--yellow);
            color: var(--navy);
        }
        .btn-warning:hover, .btn-warning:focus {
            background-color: var(--orange);
            border-color: var(--orange);
            color: var(--navy);
        }
        .btn-danger {
            background-color: var(--orange);
            border-color: var(--orange);
            color: #032536;
        }
        .btn-danger:hover, .btn-danger:focus {
            background-color: var(--orange);
            border-color: var(--orange);
            color: #032536;
        }
        .btn-info {
            background-color: var(--action-blue);
            border-color: var(--action-blue);
            color: #FFFFFF;
        }
        .btn-info:hover, .btn-info:focus {
            background-color: var(--navy);
            border-color: var(--navy);
            color: #FFFFFF;
        }

        /* Badge overrides */
        .badge.bg-primary { background-color: var(--action-blue) !important; color: #FFFFFF !important; }
        .badge.bg-secondary { background-color: var(--navy) !important; color: #FFFFFF !important; }
        .badge.bg-success { background-color: var(--action-blue) !important; color: #FFFFFF !important; }
        .badge.bg-warning { background-color: var(--yellow) !important; color: #053F5C !important; }
        .badge.bg-danger { background-color: var(--orange) !important; color: #032536 !important; }
        .badge.bg-info { background-color: var(--action-blue) !important; color: #FFFFFF !important; }

        /* Text color overrides */
        .text-primary { color: var(--text-primary) !important; }
        .text-warning { color: #8A5D00 !important; }
        .text-success { color: var(--action-blue) !important; }
        .text-danger { color: #B54708 !important; }
        .text-info { color: var(--action-blue) !important; }
        .text-secondary { color: var(--text-muted) !important; }
        .text-muted { color: var(--text-muted) !important; }

        /* Table header background */
        thead.bg-light {
            background-color: var(--bg-light);
        }

        /* Alert overrides */
        .alert-success { background-color: rgba(159, 231, 245, 0.2); color: var(--text-primary); }
        .alert-danger { background-color: rgba(242, 127, 12, 0.2); color: var(--text-primary); }
        .alert-warning { background-color: rgba(247, 173, 25, 0.2); color: var(--text-primary); }
        .alert-info { background-color: rgba(159, 231, 245, 0.2); color: var(--text-primary); }

        /* Unified Primary Action Button - shared across all dashboards */
        .btn-primary-action {
            background-color: var(--action-blue);
            border: none;
            border-radius: 0.5rem;
            color: #FFFFFF;
            font-weight: 500;
            padding: 0.625rem 1.25rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.2s ease;
            min-height: 44px;
        }
        .btn-primary-action:hover {
            background-color: var(--navy);
        }
        .btn-primary-action:focus {
            background-color: var(--navy);
            box-shadow: 0 0 0 3px rgba(66, 158, 189, 0.3);
        }
        .btn-primary-action i {
            color: #FFFFFF;
        }

        .sidebar-overlay {
            display: none;
        }

        .avatar-sm {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #2148db;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            color: #FFFFFF;
            object-fit: cover;
        }

        .profile-photo-preview {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #e2e8f0;
        }

        @media (max-width: 767.98px) {
            .sidebar-overlay {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0,0,0,0.5);
                z-index: 1040;
            }

             .sidebar-overlay.hidden {
                display: none;
            }
        }

        @media (max-width: 767.98px) {
            body {
                margin-top: var(--navbar-height);
                padding-bottom: 70px;
            }
        }

        /* Login page: full-screen, no scroll */
        body.login-page {
            margin-top: 0 !important;
            padding-bottom: 0 !important;
            overflow: hidden;
            height: 100vh;
            min-height: 100vh;
        }

        body.login-page .main-content {
            padding-top: 0;
            padding-bottom: 0;
            padding-left: 0;
            padding-right: 0;
            margin-left: 0 !important;
            height: 100vh;
            min-height: 100vh;
            overflow: hidden;
        }

        body.login-page .login-split {
            height: 100vh;
            min-height: 100vh;
            max-height: 100vh;
            overflow: hidden;
        }

        @media (min-width: 768px) {
            body {
                margin-top: 76px;
                padding-bottom: 0;
            }
        }

        .main-content {
            padding-top: 1.5rem;
        }

        @media (max-width: 767.98px) {
            .main-content {
                padding-top: 1rem;
            }
        }

         /* Mobile Bottom Navigation */
        @media (max-width: 767.98px) {
            .mobile-bottom-nav {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: var(--card-bg);
                border-top: 1px solid var(--border-color-light);
                box-shadow: 0 -2px 8px rgba(0,0,0,0.05);
                z-index: 1030;
                display: flex;
                justify-content: space-around;
                align-items: center;
                padding-top: 8px;
                padding-bottom: 8px;
                padding-bottom: calc(8px + env(safe-area-inset-bottom, 0px));
            }

            .mobile-bottom-nav .nav-item {
                flex: 1;
                text-align: center;
            }

            .mobile-bottom-nav .nav-link {
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 6px 4px;
                color: var(--text-muted);
                font-size: 10px;
                font-weight: 500;
                text-decoration: none;
                border-radius: 0;
                min-width: 44px;
                min-height: 44px;
            }

            .mobile-bottom-nav .nav-link.active {
                color: var(--medium-blue);
            }

            .mobile-bottom-nav .nav-link i {
                font-size: 20px;
                margin-bottom: 2px;
            }

            .mobile-bottom-nav .nav-link.active i {
                color: var(--medium-blue);
            }

            .mobile-content {
                padding-bottom: 80px;
            }

            .px-md-4 {
                padding-left: 16px !important;
                padding-right: 16px !important;
            }
        }

        html[data-theme="dark"] {
            color-scheme: dark;
            --navy: #0A3147;
            --medium-blue: #8DDDED;
            --action-blue: #176B88;
            --light-blue: #B9F0FA;
            --status-available-text: #8DDDED;
            --status-booked-text: #FFAA4C;
            --link-color: #8DDDED;
            --text-primary: #E5EEF7;
            --text-secondary: #C2CEDB;
            --text-muted: #A3B1C2;
            --bg-light: #111D2D;
            --card-bg: #19283B;
            --body-bg: #0D1725;
            --body-text: #E5EEF7;
            --border-color: #35465C;
            --border-color-light: #2A3A4F;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.28), 0 1px 2px rgba(0, 0, 0, 0.2);
            --bs-body-bg: var(--body-bg);
            --bs-body-color: var(--body-text);
            --bs-secondary-color: var(--text-muted);
            --bs-tertiary-color: var(--text-muted);
            --bs-emphasis-color: var(--text-primary);
            --bs-border-color: var(--border-color);
            --bs-card-bg: var(--card-bg);
            --bs-card-color: var(--body-text);
            --bs-card-border-color: var(--border-color);
            --bs-dropdown-bg: var(--card-bg);
            --bs-dropdown-color: var(--body-text);
            --bs-dropdown-link-color: var(--body-text);
            --bs-modal-bg: var(--card-bg);
            --bs-modal-color: var(--body-text);
            --bs-table-bg: var(--card-bg);
            --bs-table-color: var(--body-text);
            --bs-table-border-color: var(--border-color);
            --bs-secondary-bg: #19283B;
            --bs-tertiary-bg: #111D2D;
            --bs-light-rgb: 25, 40, 59;
            --bs-dark-rgb: 7, 17, 29;
            --bs-link-color: #8DDDED;
            --bs-link-hover-color: #B9F0FA;
        }

        html[data-theme="dark"] body {
            background-color: var(--body-bg);
            color: var(--body-text);
        }

        html[data-theme="dark"] .card,
        html[data-theme="dark"] .modal-content,
        html[data-theme="dark"] .dropdown-menu,
        html[data-theme="dark"] .list-group-item,
        html[data-theme="dark"] .offcanvas,
        html[data-theme="dark"] .popover,
        html[data-theme="dark"] .accordion-item {
            color: var(--body-text);
        }

        html[data-theme="dark"] .card,
        html[data-theme="dark"] .modal-content,
        html[data-theme="dark"] .dropdown-menu,
        html[data-theme="dark"] .list-group-item,
        html[data-theme="dark"] .offcanvas,
        html[data-theme="dark"] .popover,
        html[data-theme="dark"] .accordion-item {
            background-color: var(--card-bg);
        }

        html[data-theme="dark"] .bg-white,
        html[data-theme="dark"] .bg-light {
            background-color: var(--card-bg) !important;
        }

        html[data-theme="dark"] .text-dark,
        html[data-theme="dark"] .text-body,
        html[data-theme="dark"] .dropdown-item {
            color: var(--body-text) !important;
        }

        html[data-theme="dark"] .text-success,
        html[data-theme="dark"] .text-info {
            color: var(--light-blue) !important;
        }

        html[data-theme="dark"] .text-warning {
            color: #FFD166 !important;
        }

        html[data-theme="dark"] .text-danger {
            color: #FF8A8A !important;
        }

        html[data-theme="dark"] .table-light {
            --bs-table-bg: var(--bg-light);
            --bs-table-color: var(--body-text);
            --bs-table-border-color: var(--border-color);
        }

        html[data-theme="dark"] .dropdown-item:hover,
        html[data-theme="dark"] .dropdown-item:focus-visible {
            background-color: #263A53;
        }

        html[data-theme="dark"] .dropdown-divider {
            border-color: var(--border-color);
        }

        html[data-theme="dark"] .alert-success,
        html[data-theme="dark"] .alert-info {
            background-color: rgba(104, 187, 213, 0.16);
            border-color: rgba(104, 187, 213, 0.32);
            color: var(--text-primary);
        }

        html[data-theme="dark"] .alert-danger {
            background-color: rgba(242, 127, 12, 0.18);
            border-color: rgba(242, 127, 12, 0.35);
            color: var(--text-primary);
        }

        html[data-theme="dark"] .alert-warning {
            background-color: rgba(247, 173, 25, 0.18);
            border-color: rgba(247, 173, 25, 0.35);
            color: var(--text-primary);
        }

        html[data-theme="dark"] .table {
            --bs-table-bg: var(--card-bg);
            --bs-table-color: var(--body-text);
            --bs-table-border-color: var(--border-color);
            --bs-table-striped-bg: #1D2E43;
            --bs-table-striped-color: var(--body-text);
            --bs-table-hover-bg: #263A53;
            --bs-table-hover-color: var(--body-text);
        }

        html[data-theme="dark"] .form-control,
        html[data-theme="dark"] .form-select {
            background-color: #111D2D;
            border-color: var(--border-color);
            color: var(--body-text);
        }

        html[data-theme="dark"] .form-control::placeholder {
            color: var(--text-muted);
        }

        html[data-theme="dark"] .form-control:disabled,
        html[data-theme="dark"] .form-select:disabled {
            background-color: #202F43;
        }

        html[data-theme="dark"] .modal-header,
        html[data-theme="dark"] .modal-footer,
        html[data-theme="dark"] .list-group-item,
        html[data-theme="dark"] .accordion-item {
            border-color: var(--border-color);
        }

        html[data-theme="dark"] .app-navbar .app-navbar-dropdown-header,
        html[data-theme="dark"] .app-navbar .app-navbar-view-all,
        html[data-theme="dark"] .app-navbar .app-navbar-logout {
            color: var(--text-primary);
        }

        html[data-theme="dark"] .app-navbar .app-navbar-notification-title {
            color: var(--text-primary);
        }

        html[data-theme="dark"] .app-navbar .app-navbar-notification-time {
            color: var(--text-muted);
        }

        html[data-theme="dark"] .btn-info {
            color: #FFFFFF;
        }

        .app-navbar-theme-toggle i {
            font-size: 1.35rem;
            line-height: 1;
        }

        @media (max-width: 767.98px) {
            .app-navbar-theme-toggle i {
                font-size: 1.15rem;
            }
        }

        @media (max-width: 1024px) {
            html,
            body {
                max-width: 100%;
                overflow-x: clip;
            }

            body.login-page {
                height: auto;
                min-height: 100dvh;
                overflow-x: clip;
                overflow-y: auto;
            }

            body.login-page .main-content {
                height: auto;
                min-height: 100dvh;
                overflow: visible;
            }

            body.login-page .login-split {
                height: auto;
                min-height: 100dvh;
                max-height: none;
                overflow: visible;
            }

            .app-navbar #sidebarToggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            body.has-sidebar .main-content,
            body.has-sidebar main.main-content {
                width: 100%;
                max-width: 100%;
                flex: 1 1 0%;
            }

            .sidebar-wrapper {
                top: var(--navbar-height);
                height: calc(100dvh - var(--navbar-height));
                max-height: calc(100dvh - var(--navbar-height));
                width: var(--sidebar-expanded-width);
                min-width: var(--sidebar-expanded-width);
                max-width: var(--sidebar-expanded-width);
                transform: translateX(-100%) !important;
                transition: transform 0.25s ease, box-shadow 0.25s ease;
                box-shadow: none;
            }

            .sidebar-wrapper.show {
                transform: translateX(0) !important;
                box-shadow: 0 0 24px rgba(5, 63, 92, 0.2);
            }

            .sidebar-wrapper .sidebar-text,
            .sidebar-wrapper .sidebar-label,
            .sidebar-wrapper:not(:hover) .sidebar-text,
            .sidebar-wrapper:not(:hover) .sidebar-label {
                opacity: 1;
                width: auto;
                max-width: 13rem;
                overflow: visible;
            }

            .sidebar-wrapper .sidebar .nav-link,
            .sidebar-wrapper:not(:hover) .sidebar .nav-link {
                justify-content: flex-start;
            }

            .sidebar-wrapper .sidebar-iso-badge,
            .sidebar-wrapper:not(:hover) .sidebar-iso-badge {
                justify-content: flex-start;
                padding-right: 0.6rem;
                padding-left: 0.6rem;
            }

            .sidebar-wrapper .sidebar-iso-copy,
            .sidebar-wrapper:not(:hover) .sidebar-iso-copy {
                display: flex;
            }

            .sidebar-overlay {
                display: block;
                position: fixed;
                top: var(--navbar-height);
                right: 0;
                bottom: 0;
                left: 0;
                width: 100vw;
                height: calc(100dvh - var(--navbar-height));
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040;
            }

            .sidebar-overlay.hidden {
                display: none;
            }
        }

        @media (min-width: 1025px) {
            body.has-sidebar .main-content,
            body.has-sidebar main.main-content {
                margin-left: var(--sidebar-collapsed-width);
            }
        }

        @media (max-width: 1024px) {
            .main-content .table-responsive {
                max-width: 100%;
                overflow-x: auto !important;
                overflow-y: hidden;
                overscroll-behavior-inline: contain;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
                scrollbar-color: var(--medium-blue) var(--bg-light);
            }

            .main-content .table-responsive:has(> .table > thead > tr > th:nth-child(6)) > .table {
                min-width: 42rem;
            }

            .main-content .table-responsive::-webkit-scrollbar {
                height: 8px;
            }

            .main-content .table-responsive::-webkit-scrollbar-thumb {
                background: var(--medium-blue);
                border-radius: 8px;
            }

            .main-content .table-responsive::-webkit-scrollbar-track {
                background: var(--bg-light);
            }

            .main-content form .d-flex.gap-2,
            .main-content form .btn-group {
                flex-wrap: wrap;
            }

            .main-content .pagination {
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.2rem;
            }

            .main-content .modal-dialog {
                max-width: calc(100vw - 1rem);
            }

            .main-content canvas {
                max-width: 100%;
            }

            .main-content .calendar-component {
                flex-direction: column;
                width: 100%;
                height: auto;
                min-height: 0;
                max-height: none;
            }

            .main-content .calendar-left-panel,
            .main-content .calendar-right-panel {
                width: 100%;
                min-width: 0;
                height: auto;
                min-height: 0;
            }

            .main-content .calendar-right-panel {
                overflow: visible;
            }

            .main-content .fc {
                max-width: 100%;
                min-width: 0;
            }
        }

        @media (min-width: 768px) and (max-width: 900px) {
            .main-content form.row.g-3 > :is(.col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6) {
                width: 50%;
            }
        }

        @media (max-width: 767.98px) {
            body.has-sidebar {
                max-width: 100%;
                overflow-x: clip;
            }

            body:not(.has-sidebar) .main-content,
            body:not(.has-sidebar) main.main-content {
                margin-left: 0 !important;
            }

            .main-content .card-header,
            .main-content .card-body {
                padding: 1rem;
            }

            .main-content :is(.card, .card-body, .card-header, .row, [class*="col-"]) {
                min-width: 0;
            }

            .main-content :is(p, a, span, td, th, label, .form-text) {
                overflow-wrap: break-word;
                word-break: normal;
            }

            .main-content :is(h1, h2, h3, h4, h5, h6, li, dt, dd, small, strong, em, button, .badge, .dropdown-item, .text-muted, .text-secondary, .text-primary) {
                min-width: 0;
                overflow-wrap: break-word;
                word-break: normal;
            }

            .main-content .form-control,
            .main-content .form-select {
                min-width: 0;
                max-width: 100%;
            }

            .main-content form .d-flex.gap-2,
            .main-content form .btn-group {
                flex-wrap: wrap;
            }

            .main-content form.row.g-3 > [class*="col-"] {
                width: 100%;
            }

            .main-content .pagination {
                flex-wrap: wrap;
                gap: 0.2rem;
            }

            .modal-dialog {
                margin: 0.5rem;
            }

            .main-content .calendar-day {
                min-width: 0;
            }
        }

        @media (max-width: 991.98px) {
            .main-content :is(.mobile-appt-card, .mobile-request-card, .mobile-user-card) {
                min-width: 0;
                overflow-wrap: break-word;
            }

            .main-content :is(.mobile-appt-card, .mobile-request-card, .mobile-user-card) :is(.appt-header, .request-header, .user-header) {
                min-width: 0;
                gap: 0.5rem;
            }

            .main-content :is(.mobile-appt-card, .mobile-request-card, .mobile-user-card) :is(.appt-header, .request-header, .user-header) > :first-child {
                flex: 1 1 auto;
                min-width: 0;
                overflow-wrap: break-word;
            }

            .main-content :is(.mobile-appt-card, .mobile-request-card, .mobile-user-card) :is(.status-badge, .role-badge) {
                flex: 0 0 auto;
                max-width: 48%;
                white-space: normal;
                overflow-wrap: normal;
                text-align: center;
            }

            .main-content .mobile-availability-list .mobile-appt-card .status-badge {
                width: max-content;
                max-width: 100%;
                min-width: 0;
                white-space: nowrap;
                overflow-wrap: normal;
            }

            .main-content :is(.mobile-appt-card, .mobile-request-card, .mobile-user-card) :is(.appt-detail-row, .request-detail-row, .user-detail-row) {
                display: grid;
                grid-template-columns: minmax(5rem, 34%) minmax(0, 1fr);
                align-items: start;
                column-gap: 0.75rem;
            }

            .main-content :is(.mobile-appt-card, .mobile-request-card, .mobile-user-card) :is(.appt-detail-value, .request-detail-value, .user-detail-value) {
                min-width: 0;
                overflow-wrap: break-word;
                text-align: right;
            }

            .main-content :is(.mobile-appt-actions, .mobile-request-actions, .mobile-user-actions) {
                align-items: stretch;
            }

            .main-content :is(.mobile-appt-actions, .mobile-request-actions, .mobile-user-actions) > :is(.btn, a, form, span) {
                flex: 1 1 4rem;
                min-width: 0;
            }

            .main-content :is(.mobile-appt-actions, .mobile-request-actions, .mobile-user-actions) > form {
                display: flex;
            }

            .main-content :is(.mobile-appt-actions, .mobile-request-actions, .mobile-user-actions) .btn {
                width: 100%;
                min-width: 0;
                min-height: 44px;
                height: auto;
                white-space: normal;
            }

            .main-content .card-header.d-flex {
                flex-wrap: wrap;
            }

            .main-content .card-header.d-flex > :first-child {
                min-width: 0;
            }
        }

        @media (max-width: 575.98px) {
            .main-content .calendar-right-panel {
                padding: 0.5rem 0.25rem;
            }

            .main-content .pagination .page-link {
                min-width: 2.25rem;
                padding-right: 0.4rem;
                padding-left: 0.4rem;
            }

            .main-content .modal-footer {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .main-content .modal-footer > .btn {
                flex: 1 1 8rem;
            }
        }

        body.registration-page {
            margin-top: 0 !important;
            padding-bottom: 0 !important;
        }

        .main-content .responsive-table-wrap.has-horizontal-overflow::after {
            display: block;
            padding: 0.35rem 0.75rem;
            color: var(--text-muted);
            font-size: 0.75rem;
            line-height: 1.4;
            content: "Swipe or scroll horizontally to view all columns";
        }

        .main-content .responsive-table-wrap:focus-visible {
            outline: 3px solid var(--medium-blue);
            outline-offset: 2px;
        }

        .main-content .responsive-table-wrap--dense > .table {
            min-width: 42rem;
        }

        @media (max-width: 359.98px) {
            .app-navbar .app-navbar-sidebar-icon {
                font-size: 1.1rem;
            }

            .app-navbar .app-navbar-actions {
                gap: 0;
            }
        }
    </style>
</head>
    <body class="{{ auth()->check() ? 'has-sidebar ' : '' }}{{ $bodyClass ?? '' }}">
    @if(!@isset($hide_navbar) || !$hide_navbar)
        @include('layouts.navbar')
    @endif

    <div class="d-flex">
        <div id="sidebarOverlay" class="sidebar-overlay hidden" onclick="toggleSidebar()"></div>

        @if(auth()->check())
            <div id="sidebarWrapper" class="sidebar-wrapper">
                @include('layouts.sidebar')
            </div>
        @endif

        <main class="main-content px-md-4 flex-grow-1 {{ auth()->check() && auth()->user()->isStudent() ? 'mobile-content' : '' }}" style="min-width: 0;">
            @yield('content')
        </main>
    </div>

    @if(auth()->check() && auth()->user()->isStudent())
    <nav class="mobile-bottom-nav d-md-none">
        <div class="nav-item">
            <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Home</span>
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link {{ request()->routeIs('student.appointments*') ? 'active' : '' }}" href="{{ route('student.appointments.index') }}">
                <i class="bi bi-calendar-check"></i>
                <span>Appointments</span>
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link {{ request()->routeIs('student.notifications*') || request()->routeIs('notifications.show') ? 'active' : '' }}" href="{{ route('student.notifications') }}">
                <i class="bi bi-bell"></i>
                <span>Notifications</span>
            </a>
        </div>
    </nav>
    @endif

    <script>
        function toggleSidebar(forceOpen) {
            const sidebar = document.getElementById('sidebarWrapper');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar && overlay) {
                const shouldOpen = typeof forceOpen === 'boolean'
                    ? forceOpen
                    : !sidebar.classList.contains('show');
                sidebar.classList.toggle('show', shouldOpen);
                overlay.classList.toggle('hidden', !shouldOpen);

                const toggle = document.getElementById('sidebarToggle');
                if (toggle) {
                    toggle.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
                    toggle.setAttribute('aria-label', shouldOpen ? 'Close navigation' : 'Open navigation');
                }

                const sidebarIsOffcanvas = window.matchMedia('(max-width: 1024px)').matches;
                sidebar.toggleAttribute('inert', sidebarIsOffcanvas && !shouldOpen);
                sidebar.setAttribute('aria-hidden', sidebarIsOffcanvas && !shouldOpen ? 'true' : 'false');
            }
        }

        toggleSidebar(false);

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                toggleSidebar(false);
            }
        });

        document.querySelectorAll('#sidebarWrapper .nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.matchMedia('(max-width: 1024px)').matches) {
                    toggleSidebar(false);
                }
            });
        });
    </script>
    <script>
        (function() {
            const themeToggle = document.getElementById('themeToggle');
            if (!themeToggle) {
                return;
            }

            function applyTheme(theme, persist) {
                const isDark = theme === 'dark';
                document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
                themeToggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                themeToggle.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
                themeToggle.title = isDark ? 'Switch to light mode' : 'Switch to dark mode';
                themeToggle.querySelector('i').className = isDark
                    ? 'bi bi-sun-fill'
                    : 'bi bi-moon-stars-fill';

                if (persist) {
                    try {
                        localStorage.setItem('g-sched-theme', isDark ? 'dark' : 'light');
                    } catch (error) {
                        // Theme remains active for this page when storage is unavailable.
                    }
                }

                window.dispatchEvent(new CustomEvent('g-sched-theme-changed', {
                    detail: { theme: isDark ? 'dark' : 'light' }
                }));
            }

            applyTheme(document.documentElement.getAttribute('data-theme'), false);
            themeToggle.addEventListener('click', function() {
                applyTheme(document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark', true);
            });
        })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @unless(request()->routeIs('register'))
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    @endunless
    @yield('scripts')
    <script>
        (function() {
            document.querySelectorAll('main.main-content table').forEach(function(table) {
                if (table.closest('.fc, .fc-view, .fc-scrollgrid')) {
                    return;
                }

                let wrapper = table.closest('.table-responsive');
                if (!wrapper) {
                    wrapper = document.createElement('div');
                    table.parentNode.insertBefore(wrapper, table);
                    wrapper.appendChild(table);
                }

                wrapper.classList.add('table-responsive', 'responsive-table-wrap');
                const columns = table.querySelector('thead tr')?.children.length
                    || table.querySelector('tr')?.children.length
                    || 0;
                if (columns > 4) {
                    wrapper.classList.add('responsive-table-wrap--dense');
                }

                wrapper.setAttribute('role', 'region');
                wrapper.setAttribute('aria-label', 'Scrollable data table');

                function updateScrollAffordance() {
                    const overflows = wrapper.scrollWidth > wrapper.clientWidth + 1;
                    wrapper.classList.toggle('has-horizontal-overflow', overflows);
                    if (overflows) {
                        wrapper.setAttribute('tabindex', '0');
                    } else {
                        wrapper.removeAttribute('tabindex');
                    }
                }

                updateScrollAffordance();
                window.addEventListener('resize', updateScrollAffordance, { passive: true });
            });
        })();
    </script>
</body>
</html>
