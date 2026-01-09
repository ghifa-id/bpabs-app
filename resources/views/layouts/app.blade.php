<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - BPABS</title>
    <link href="{{ asset('assets/img/logo.png') }}" rel="icon">
    <link href="{{ asset('assets/img/logo.png') }}" rel="apple-touch-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe', 
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Smooth animations */
        * {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Logo styling */
        .logo-main {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .logo-small {
            width: 32px;
            height: 32px;
            object-fit: contain;
            border-radius: 0.5rem;
        }
        
        .logo-mini {
            width: 24px;
            height: 24px;
            object-fit: contain;
            border-radius: 0.375rem;
        }
        
        /* Sidebar animations */
        .sidebar-enter {
            transform: translateX(-100%);
            opacity: 0;
        }
        
        .sidebar-enter-active {
            transform: translateX(0);
            opacity: 1;
        }
        
        .sidebar-exit {
            transform: translateX(0);
            opacity: 1;
        }
        
        .sidebar-exit-active {
            transform: translateX(-100%);
            opacity: 0;
        }
        
        /* Custom scrollbar */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
        }
        
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 6px;
            border: transparent;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(156, 163, 175, 0.8);
        }

        /* Mobile-first responsive navigation */
        @media (max-width: 1023px) {
            .sidebar-mobile {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 50;
                width: 280px;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            
            .sidebar-mobile.open {
                transform: translateX(0);
            }
        }

        /* Desktop sidebar collapse */
        @media (min-width: 1024px) {
            .sidebar-collapsed {
                width: 4.5rem !important;
            }
            
            .sidebar-collapsed .sidebar-text,
            .sidebar-collapsed .sidebar-brand-text {
                opacity: 0;
                width: 0;
                overflow: hidden;
            }
            
            .sidebar-collapsed .nav-link {
                justify-content: center;
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            
            .sidebar-collapsed .nav-link:hover {
                background-color: rgba(59, 130, 246, 0.2);
                border-radius: 0.5rem;
            }

            /* Hide dropdown when collapsed */
            .sidebar-collapsed .dropdown-content {
                display: none !important;
            }

            .sidebar-collapsed .dropdown-arrow {
                display: none !important;
            }
        }

        /* Tooltip for collapsed sidebar */
        .tooltip-collapsed {
            position: relative;
        }

        .sidebar-collapsed .tooltip-collapsed:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            left: calc(100% + 0.75rem);
            top: 50%;
            transform: translateY(-50%);
            background-color: #1f2937;
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            white-space: nowrap;
            z-index: 60;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .sidebar-collapsed .tooltip-collapsed:hover::before {
            content: '';
            position: absolute;
            left: calc(100% + 0.25rem);
            top: 50%;
            transform: translateY(-50%);
            border: 0.375rem solid transparent;
            border-right-color: #1f2937;
            z-index: 60;
        }

        /* Glass effect for mobile overlay */
        .glass-overlay {
            backdrop-filter: blur(4px);
            background-color: rgba(0, 0, 0, 0.3);
        }

        /* Smooth hover effects */
        .hover-lift:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        /* Active navigation indicator */
        .nav-active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 2rem;
            background-color: #fbbf24;
            border-radius: 0 0.25rem 0.25rem 0;
        }

        /* Pulse animation for notifications */
        @keyframes pulse-soft {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .pulse-soft {
            animation: pulse-soft 2s infinite;
        }

        /* Dropdown animation */
        .dropdown-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }

        .dropdown-content.open {
            max-height: 300px;
        }

        .dropdown-arrow {
            transition: transform 0.3s ease-in-out;
        }

        .dropdown-arrow.rotated {
            transform: rotate(180deg);
        }

        /* Submenu styling */
        .submenu-item {
            border-left: 2px solid rgba(59, 130, 246, 0.3);
            margin-left: 1rem;
            padding-left: 0.75rem;
        }

        .submenu-item:hover {
            border-left-color: rgba(59, 130, 246, 0.6);
            background-color: rgba(59, 130, 246, 0.1);
        }

        /* Header logo animation */
        .header-logo {
            transition: transform 0.2s ease-in-out;
        }

        .header-logo:hover {
            transform: scale(1.05);
        }

        /* Breadcrumb with logo */
        .breadcrumb-logo {
            width: 20px;
            height: 20px;
            object-fit: contain;
            border-radius: 0.25rem;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <!-- Mobile Overlay -->
    <div id="overlay" 
         class="fixed inset-0 glass-overlay z-40 lg:hidden opacity-0 invisible transition-all duration-300"
         onclick="closeMobileSidebar()">
    </div>

    <!-- Sidebar -->
    <aside id="sidebar" 
           class="sidebar-mobile bg-gradient-to-b from-primary-800 to-primary-900 text-white lg:fixed lg:inset-y-0 lg:left-0 lg:z-30 lg:w-64 lg:transform-none">
        <!-- Sidebar Header dengan Logo -->
        <div class="flex items-center justify-between p-6 border-b border-primary-700/50">
            <div class="flex items-center space-x-3">
                <!-- Logo utama di sidebar -->
                <div class="relative">
                    <img src="{{ asset('assets/img/logo.png') }}" 
                         alt="BPABS Logo" 
                         class="logo-main bg-white p-1 shadow-lg"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <!-- Fallback icon jika logo tidak ditemukan -->
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg" style="display: none;">
                        <i class="fas fa-tint text-white text-lg"></i>
                    </div>
                </div>
                <div class="sidebar-brand-text">
                    <h1 class="text-xl font-bold">BPABS</h1>
                    <p class="text-xs text-primary-200 opacity-90">Manajemen Air</p>
                </div>
            </div>
            <!-- Close button for mobile -->
            <button class="lg:hidden p-2 hover:bg-primary-700 rounded-lg transition-colors" 
                    onclick="closeMobileSidebar()">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 custom-scrollbar overflow-y-auto">
            <div class="space-y-2">
                @if(auth()->user()->role == 'admin')
                    <!-- Admin Navigation -->
                    <a href="{{ route('admin.dashboard.index') }}" 
                       class="nav-link tooltip-collapsed group flex items-center px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('admin.dashboard.*') ? 'bg-primary-700 nav-active' : '' }}"
                       data-tooltip="Dashboard">
                        <i class="fas fa-home w-5 h-5 text-primary-200 group-hover:text-white"></i>
                        <span class="ml-3 sidebar-text">Dashboard</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}" 
                       class="nav-link tooltip-collapsed group flex items-center px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('admin.users.*') ? 'bg-primary-700 nav-active' : '' }}"
                       data-tooltip="Kelola Pengguna">
                        <i class="fas fa-users-cog w-5 h-5 text-primary-200 group-hover:text-white"></i>
                        <span class="ml-3 sidebar-text">Kelola Pelanggan</span>
                    </a>
                    
                    <a href="{{ route('admin.meteran.index') }}" 
                       class="nav-link tooltip-collapsed group flex items-center px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('admin.meteran.*') ? 'bg-primary-700 nav-active' : '' }}"
                       data-tooltip="Meteran">
                        <i class="fas fa-tachometer-alt w-5 h-5 text-primary-200 group-hover:text-white"></i>
                        <span class="ml-3 sidebar-text">Meteran</span>
                    </a>
                    
                    <a href="{{ route('admin.tarif.index') }}" 
                       class="nav-link tooltip-collapsed group flex items-center px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('admin.tarif.*') ? 'bg-primary-700 nav-active' : '' }}"
                       data-tooltip="Tarif">
                        <i class="fas fa-tags w-5 h-5 text-primary-200 group-hover:text-white"></i>
                        <span class="ml-3 sidebar-text">Tarif</span>
                    </a>
                    
                    <a href="{{ route('admin.tagihan.index') }}" 
                       class="nav-link tooltip-collapsed group flex items-center px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('admin.tagihan.*') ? 'bg-primary-700 nav-active' : '' }}"
                       data-tooltip="Tagihan">
                        <i class="fas fa-file-invoice w-5 h-5 text-primary-200 group-hover:text-white"></i>
                        <span class="ml-3 sidebar-text">Tagihan</span>
                    </a>
                    
                    <a href="{{ route('admin.pembayaran.index') }}" 
                       class="nav-link tooltip-collapsed group flex items-center px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('admin.pembayaran.*') ? 'bg-primary-700 nav-active' : '' }}"
                       data-tooltip="Pembayaran">
                        <i class="fas fa-credit-card w-5 h-5 text-primary-200 group-hover:text-white"></i>
                        <span class="ml-3 sidebar-text">Pembayaran</span>
                    </a>

                    <!-- Laporan Dropdown for Admin -->
                    <div class="dropdown-menu">
                        <button onclick="toggleDropdown('admin-laporan')" 
                                class="nav-link tooltip-collapsed group flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('admin.laporan.*') ? 'bg-primary-700 nav-active' : '' }}"
                                data-tooltip="Laporan">
                            <i class="fas fa-chart-bar w-5 h-5 text-primary-200 group-hover:text-white"></i>
                            <span class="ml-3 sidebar-text">Laporan</span>
                            <i class="fas fa-chevron-down dropdown-arrow ml-auto sidebar-text text-primary-200 group-hover:text-white" id="admin-laporan-arrow"></i>
                        </button>
                        <div id="admin-laporan" class="dropdown-content sidebar-text">
                            <div class="mt-2 space-y-1">
                                <a href="{{ route('admin.laporan.pengguna') }}" 
                                class="submenu-item group flex items-center px-4 py-2 text-xs font-medium rounded-lg hover:bg-primary-700/30 transition-colors {{ request()->routeIs('admin.laporan.pengguna*') ? 'bg-primary-700/50' : '' }}">
                                    <i class="fas fa-users w-4 h-4 text-primary-300 group-hover:text-white mr-3"></i>
                                    <span>Laporan Pelanggan</span>
                                </a>
                                <a href="{{ route('admin.laporan.meteran') }}" 
                                class="submenu-item group flex items-center px-4 py-2 text-xs font-medium rounded-lg hover:bg-primary-700/30 transition-colors {{ request()->routeIs('admin.laporan.meteran*') ? 'bg-primary-700/50' : '' }}">
                                    <i class="fas fa-tachometer-alt w-4 h-4 text-primary-300 group-hover:text-white mr-3"></i>
                                    <span>Laporan Meteran</span>
                                </a>
                                <a href="{{ route('admin.laporan.tarif') }}" 
                                class="submenu-item group flex items-center px-4 py-2 text-xs font-medium rounded-lg hover:bg-primary-700/30 transition-colors {{ request()->routeIs('admin.laporan.tarif*') ? 'bg-primary-700/50' : '' }}">
                                    <i class="fas fa-tags w-4 h-4 text-primary-300 group-hover:text-white mr-3"></i>
                                    <span>Laporan Tarif</span>
                                </a>
                                <a href="{{ route('admin.laporan.tagihan') }}" 
                                class="submenu-item group flex items-center px-4 py-2 text-xs font-medium rounded-lg hover:bg-primary-700/30 transition-colors {{ request()->routeIs('admin.laporan.tagihan*') ? 'bg-primary-700/50' : '' }}">
                                    <i class="fas fa-file-invoice w-4 h-4 text-primary-300 group-hover:text-white mr-3"></i>
                                    <span>Laporan Tagihan</span>
                                </a>
                                <a href="{{ route('admin.laporan.pembayaran') }}" 
                                class="submenu-item group flex items-center px-4 py-2 text-xs font-medium rounded-lg hover:bg-primary-700/30 transition-colors {{ request()->routeIs('admin.laporan.pembayaran*') ? 'bg-primary-700/50' : '' }}">
                                    <i class="fas fa-credit-card w-4 h-4 text-primary-300 group-hover:text-white mr-3"></i>
                                    <span>Laporan Pembayaran</span>
                                </a>
                            </div>
                        </div>
                    </div>

                @elseif(auth()->user()->role == 'pelanggan')
                    <!-- Pelanggan Navigation -->
                    <a href="{{ route('pelanggan.dashboard.index') }}" 
                       class="nav-link tooltip-collapsed group flex items-center px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('pelanggan.dashboard.*') ? 'bg-primary-700 nav-active' : '' }}"
                       data-tooltip="Dashboard">
                        <i class="fas fa-home w-5 h-5 text-primary-200 group-hover:text-white"></i>
                        <span class="ml-3 sidebar-text">Dashboard</span>
                    </a>

                    <a href="{{ route('pelanggan.profil.index') }}" 
                       class="nav-link tooltip-collapsed group flex items-center px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('pelanggan.profil.*') ? 'bg-primary-700 nav-active' : '' }}"
                       data-tooltip="Data Profil">
                        <i class="fas fa-user w-5 h-5 text-primary-200 group-hover:text-white"></i>
                        <span class="ml-3 sidebar-text">Data Profil</span>
                    </a>
                    
                    <a href="{{ route('pelanggan.tagihan.index') }}" 
                       class="nav-link tooltip-collapsed group flex items-center px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('pelanggan.tagihan.*') ? 'bg-primary-700 nav-active' : '' }}"
                       data-tooltip="Tagihan">
                        <i class="fas fa-file-invoice w-5 h-5 text-primary-200 group-hover:text-white"></i>
                        <span class="ml-3 sidebar-text">Tagihan</span>
                        @if(isset($unpaidCount) && $unpaidCount > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full pulse-soft">{{ $unpaidCount }}</span>
                        @endif
                    </a>
                    
                    <a href="{{ route('pelanggan.riwayat.index') }}" 
                       class="nav-link tooltip-collapsed group flex items-center px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('pelanggan.riwayat.*') ? 'bg-primary-700 nav-active' : '' }}"
                       data-tooltip="Riwayat Pembayaran">
                        <i class="fas fa-history w-5 h-5 text-primary-200 group-hover:text-white"></i>
                        <span class="ml-3 sidebar-text">Riwayat Pembayaran</span>
                    </a>

                @elseif(auth()->user()->role == 'petugas')
                    <!-- Pelanggan Navigation -->
                    <a href="{{ route('petugas.dashboard.index') }}" 
                       class="nav-link tooltip-collapsed group flex items-center px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('pelanggan.dashboard.*') ? 'bg-primary-700 nav-active' : '' }}"
                       data-tooltip="Dashboard">
                        <i class="fas fa-home w-5 h-5 text-primary-200 group-hover:text-white"></i>
                        <span class="ml-3 sidebar-text">Dashboard</span>
                    </a>

                    <a href="{{ route('petugas.meteran.index') }}" 
                       class="nav-link tooltip-collapsed group flex items-center px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('superuser.meteran.*') ? 'bg-primary-700 nav-active' : '' }}"
                       data-tooltip="Meteran">
                        <i class="fas fa-tachometer-alt w-5 h-5 text-primary-200 group-hover:text-white"></i>
                        <span class="ml-3 sidebar-text">Pembacaan Meteran</span>
                    </a>

                @else
                    <!-- Superuser Navigation -->
                    <a href="{{ route('superuser.dashboard.index') }}" 
                       class="nav-link tooltip-collapsed group flex items-center px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('superuser.dashboard.*') ? 'bg-primary-700 nav-active' : '' }}"
                       data-tooltip="Dashboard">
                        <i class="fas fa-home w-5 h-5 text-primary-200 group-hover:text-white"></i>
                        <span class="ml-3 sidebar-text">Dashboard</span>
                    </a>

                    <a href="{{ route('superuser.users.index') }}" 
                       class="nav-link tooltip-collapsed group flex items-center px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('superuser.users.*') ? 'bg-primary-700 nav-active' : '' }}"
                       data-tooltip="Kelola User">
                        <i class="fas fa-users-cog w-5 h-5 text-primary-200 group-hover:text-white"></i>
                        <span class="ml-3 sidebar-text">Kelola User</span>
                    </a>
                    
                    <a href="{{ route('superuser.meteran.index') }}" 
                       class="nav-link tooltip-collapsed group flex items-center px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('superuser.meteran.*') ? 'bg-primary-700 nav-active' : '' }}"
                       data-tooltip="Meteran">
                        <i class="fas fa-tachometer-alt w-5 h-5 text-primary-200 group-hover:text-white"></i>
                        <span class="ml-3 sidebar-text">Meteran</span>
                    </a>

                    <a href="{{ route('superuser.tarif.index') }}" 
                       class="nav-link tooltip-collapsed group flex items-center px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('superuser.tarif.*') ? 'bg-primary-700 nav-active' : '' }}"
                       data-tooltip="Tarif">
                        <i class="fas fa-tags w-5 h-5 text-primary-200 group-hover:text-white"></i>
                        <span class="ml-3 sidebar-text">Tarif</span>
                    </a>

                    <a href="{{ route('superuser.tagihan.index') }}" 
                       class="nav-link tooltip-collapsed group flex items-center px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('superuser.tagihan.*') ? 'bg-primary-700 nav-active' : '' }}"
                       data-tooltip="Tagihan">
                        <i class="fas fa-file-invoice w-5 h-5 text-primary-200 group-hover:text-white"></i>
                        <span class="ml-3 sidebar-text">Tagihan</span>
                    </a>
                    
                    <a href="{{ route('superuser.pembayaran.index') }}" 
                       class="nav-link tooltip-collapsed group flex items-center px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('superuser.pembayaran.*') ? 'bg-primary-700 nav-active' : '' }}"
                       data-tooltip="Pembayaran">
                        <i class="fas fa-chart-line w-5 h-5 text-primary-200 group-hover:text-white"></i>
                        <span class="ml-3 sidebar-text">Pembayaran</span>
                    </a>

                    <!-- Laporan Dropdown for Superuser -->
                    <div class="dropdown-menu">
                        <button onclick="toggleDropdown('superuser-laporan')" 
                                class="nav-link tooltip-collapsed group flex items-center w-full px-4 py-3 text-sm font-medium rounded-xl hover:bg-primary-700/50 relative {{ request()->routeIs('superuser.laporan.*') ? 'bg-primary-700 nav-active' : '' }}"
                                data-tooltip="Laporan">
                            <i class="fas fa-chart-bar w-5 h-5 text-primary-200 group-hover:text-white"></i>
                            <span class="ml-3 sidebar-text">Laporan</span>
                            <i class="fas fa-chevron-down dropdown-arrow ml-auto sidebar-text text-primary-200 group-hover:text-white" id="superuser-laporan-arrow"></i>
                        </button>
                        <div id="superuser-laporan" class="dropdown-content sidebar-text">
                            <div class="mt-2 space-y-1">
                                <a href="#" 
                                   class="submenu-item group flex items-center px-4 py-2 text-xs font-medium rounded-lg hover:bg-primary-700/30 transition-colors {{ request()->routeIs('superuser.laporan.pengguna') ? 'bg-primary-700/50' : '' }}">
                                    <i class="fas fa-users w-4 h-4 text-primary-300 group-hover:text-white mr-3"></i>
                                    <span>Laporan Pengguna</span>
                                </a>
                                <a href="#" 
                                   class="submenu-item group flex items-center px-4 py-2 text-xs font-medium rounded-lg hover:bg-primary-700/30 transition-colors {{ request()->routeIs('superuser.laporan.meteran') ? 'bg-primary-700/50' : '' }}">
                                    <i class="fas fa-tachometer-alt w-4 h-4 text-primary-300 group-hover:text-white mr-3"></i>
                                    <span>Laporan Meteran</span>
                                </a>
                                <a href="#" 
                                   class="submenu-item group flex items-center px-4 py-2 text-xs font-medium rounded-lg hover:bg-primary-700/30 transition-colors {{ request()->routeIs('superuser.laporan.tarif') ? 'bg-primary-700/50' : '' }}">
                                    <i class="fas fa-tags w-4 h-4 text-primary-300 group-hover:text-white mr-3"></i>
                                    <span>Laporan Tarif</span>
                                </a>
                                <a href="#" 
                                   class="submenu-item group flex items-center px-4 py-2 text-xs font-medium rounded-lg hover:bg-primary-700/30 transition-colors {{ request()->routeIs('superuser.laporan.tagihan') ? 'bg-primary-700/50' : '' }}">
                                    <i class="fas fa-file-invoice w-4 h-4 text-primary-300 group-hover:text-white mr-3"></i>
                                    <span>Laporan Tagihan</span>
                                </a>
                                <a href="#" 
                                   class="submenu-item group flex items-center px-4 py-2 text-xs font-medium rounded-lg hover:bg-primary-700/30 transition-colors {{ request()->routeIs('superuser.laporan.pembayaran') ? 'bg-primary-700/50' : '' }}">
                                    <i class="fas fa-credit-card w-4 h-4 text-primary-300 group-hover:text-white mr-3"></i>
                                    <span>Laporan Pembayaran</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </nav>

        <!-- Sidebar Footer with Logo -->
        <div class="p-4 border-t border-primary-700/50">
            <div class="sidebar-text">
                <div class="flex items-center space-x-3 p-3 bg-primary-700/30 rounded-xl">
                    <div class="w-8 h-8 bg-gradient-to-r from-green-400 to-blue-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-primary-200 capitalize">{{ auth()->user()->role }}</p>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-64" id="main-content">
        <!-- Top Navigation Bar dengan Logo -->
        <header class="bg-white/90 backdrop-blur-sm border-b border-gray-200 sticky top-0 z-20 shadow-sm">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Left Section -->
                    <div class="flex items-center space-x-4">
                        <!-- Mobile Menu Button -->
                        <button class="lg:hidden p-2 -ml-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                                onclick="openMobileSidebar()">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        
                        <!-- Desktop Collapse Button -->
                        <button class="hidden lg:block p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                                onclick="toggleDesktopSidebar()" id="collapse-btn">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        
                        <!-- Logo dan Breadcrumb di Header -->
                        <div class="flex items-center space-x-3">
                            <!-- Logo kecil di header -->
                            <img src="{{ asset('assets/img/logo.png') }}" 
                                 alt="BPABS" 
                                 class="breadcrumb-logo header-logo"
                                 onerror="this.style.display='none';">
                            <div class="border-l border-gray-300 pl-3">
                                <h1 class="text-xl font-semibold text-gray-900">@yield('header', 'Dashboard')</h1>
                                <p class="text-sm text-gray-500 hidden sm:block">@yield('subtitle', 'Selamat datang di sistem BPABS')</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Section -->
                    <div class="flex items-center space-x-2">
                        <!-- Logo kecil tambahan di pojok kanan (opsional) -->
                        <div class="hidden md:flex items-center space-x-3 mr-4">
                            <img src="{{ asset('assets/img/logo.png') }}" 
                                 alt="BPABS" 
                                 class="logo-mini opacity-60 hover:opacity-100 transition-opacity"
                                 onerror="this.style.display='none';">
                            <span class="text-sm text-gray-500 font-medium">BPABS System</span>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="relative">
                            <button class="flex items-center space-x-2 p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                                    onclick="toggleUserMenu()">
                                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <span class="hidden sm:block text-sm font-medium">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs hidden sm:block"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div id="user-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50 opacity-0 invisible transform scale-95 transition-all duration-200">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                                </div>
                                
                                <!-- Route profil berdasarkan role user -->
                                @if(auth()->user()->role == 'admin')
                                    <a href="{{ route('admin.users.edit', auth()->id()) }}" 
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-user-cog w-4 h-4 mr-3"></i>
                                        Profil
                                    </a>
                                @elseif(auth()->user()->role == 'pelanggan')
                                    <a href="{{ route('pelanggan.profil.index') }}" 
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-user-cog w-4 h-4 mr-3"></i>
                                        Profil
                                    </a>
                                @else
                                    <a href="{{ route('superuser.users.edit', auth()->id()) }}" 
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-user-cog w-4 h-4 mr-3"></i>
                                        Profil
                                    </a>
                                @endif
                                
                                <hr class="my-2">
                                <form action="{{ route('logout') }}" method="POST" class="block">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-sign-out-alt w-4 h-4 mr-3"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="p-4 sm:p-6 lg:p-8">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center shadow-sm" role="alert">
                    <i class="fas fa-check-circle mr-3 text-green-600"></i>
                    <span>{{ session('success') }}</span>
                    <button class="ml-auto text-green-600 hover:text-green-800" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center shadow-sm" role="alert">
                    <i class="fas fa-exclamation-circle mr-3 text-red-600"></i>
                    <span>{{ session('error') }}</span>
                    <button class="ml-auto text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-sm" role="alert">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-triangle mr-3 text-red-600"></i>
                        <span class="font-medium">Terjadi kesalahan:</span>
                    </div>
                    <ul class="list-disc list-inside text-sm space-y-1 ml-6">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Main Content Area -->
            @yield('content')
        </div>

        <!-- Footer dengan Logo -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center space-x-3 mb-4 md:mb-0">
                        <img src="{{ asset('assets/img/logo.png') }}" 
                             alt="BPABS Logo" 
                             class="logo-small"
                             onerror="this.style.display='none';">
                        <div>
                            <p class="text-sm font-medium text-gray-900">BPABS - Sistem Manejemen Air</p>
                            <p class="text-xs text-gray-500">© {{ date('Y') }} Sistem Pengelolaan Air Bersih</p>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500">
                        <span>Versi 1.0 | </span>
                        <span>{{ auth()->user()->role === 'petugas' ? 'Petugas' : (auth()->user()->role === 'admin' ? 'Administrator' : (auth()->user()->role === 'pelanggan' ? 'Pelanggan' : 'Superuser')) }}</span>
                    </div>
                </div>
            </div>
        </footer>
    </main>

    <!-- JavaScript (sama seperti sebelumnya) -->
    <script>
        let sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        let userMenuOpen = false;
        let dropdownStates = {};

        // Initialize sidebar state
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth >= 1024 && sidebarCollapsed) {
                toggleDesktopSidebar(false);
            }
            
            // Auto-open dropdown if current route matches
            initializeActiveDropdowns();
        });

        // Initialize active dropdowns based on current route
        function initializeActiveDropdowns() {
            const currentRoute = window.location.pathname;
            
            // Check if we're on a laporan route and open the appropriate dropdown
            if (currentRoute.includes('/admin/laporan/')) {
                toggleDropdown('admin-laporan', true);
            } else if (currentRoute.includes('/superuser/laporan/')) {
                toggleDropdown('superuser-laporan', true);
            } else if (currentRoute.includes('/petugas/laporan')){
                toggleDropdown('petugas-laporan', true);
            }
        }

        // Dropdown toggle function
        function toggleDropdown(dropdownId, forceOpen = false) {
            const dropdown = document.getElementById(dropdownId);
            const arrow = document.getElementById(dropdownId + '-arrow');
            
            if (!dropdown || !arrow) return;
            
            const isOpen = dropdownStates[dropdownId] || false;
            
            if (forceOpen || !isOpen) {
                // Open dropdown
                dropdown.classList.add('open');
                arrow.classList.add('rotated');
                dropdownStates[dropdownId] = true;
            } else {
                // Close dropdown
                dropdown.classList.remove('open');
                arrow.classList.remove('rotated');
                dropdownStates[dropdownId] = false;
            }
        }

        // Mobile sidebar functions
        function openMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            
            sidebar.classList.add('open');
            overlay.classList.remove('opacity-0', 'invisible');
            overlay.classList.add('opacity-100', 'visible');
            document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
        }

        function closeMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            
            sidebar.classList.remove('open');
            overlay.classList.remove('opacity-100', 'visible');
            overlay.classList.add('opacity-0', 'invisible');
            document.body.classList.remove('overflow-hidden');
        }

        // Desktop sidebar toggle
        function toggleDesktopSidebar(save = true) {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            if (sidebarCollapsed) {
                sidebar.classList.remove('sidebar-collapsed');
                mainContent.classList.remove('lg:ml-18');
                mainContent.classList.add('lg:ml-64');
                sidebarCollapsed = false;
            } else {
                sidebar.classList.add('sidebar-collapsed');
                mainContent.classList.remove('lg:ml-64');
                mainContent.classList.add('lg:ml-18');
                sidebarCollapsed = true;
                
                // Close all dropdowns when sidebar collapses
                Object.keys(dropdownStates).forEach(dropdownId => {
                    const dropdown = document.getElementById(dropdownId);
                    const arrow = document.getElementById(dropdownId + '-arrow');
                    if (dropdown && arrow) {
                        dropdown.classList.remove('open');
                        arrow.classList.remove('rotated');
                        dropdownStates[dropdownId] = false;
                    }
                });
            }
            
            if (save) {
                localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
            }
        }

        // User menu toggle
        function toggleUserMenu() {
            const userMenu = document.getElementById('user-menu');
            
            if (userMenuOpen) {
                userMenu.classList.remove('opacity-100', 'visible', 'scale-100');
                userMenu.classList.add('opacity-0', 'invisible', 'scale-95');
                userMenuOpen = false;
            } else {
                userMenu.classList.remove('opacity-0', 'invisible', 'scale-95');
                userMenu.classList.add('opacity-100', 'visible', 'scale-100');
                userMenuOpen = true;
            }
        }

        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('user-menu');
            const userButton = event.target.closest('[onclick="toggleUserMenu()"]');
            
            if (!userButton && !userMenu.contains(event.target) && userMenuOpen) {
                toggleUserMenu();
            }
        });

        // Auto-close mobile sidebar on navigation
        document.querySelectorAll('#sidebar nav a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 1024) {
                    setTimeout(closeMobileSidebar, 150);
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                closeMobileSidebar();
            } else {
                const sidebar = document.getElementById('sidebar');
                const mainContent = document.getElementById('main-content');
                
                sidebar.classList.remove('sidebar-collapsed');
                mainContent.classList.remove('lg:ml-18');
                mainContent.classList.add('lg:ml-64');
            }
        });

        // Smooth scroll for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>