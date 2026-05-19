<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BoardingHub - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transform -translate-x-full md:translate-x-0 md:static md:inset-0 transition-transform duration-300 ease-in-out border-r border-slate-800">
            <div class="p-6 flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-900/50">
                        <i class="fas fa-home-user text-lg"></i>
                    </div>
                    <h1 class="text-xl font-black tracking-tight">Boarding<span class="text-indigo-400">Hub</span></h1>
                </div>
                <button id="close-sidebar" class="md:hidden text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <nav class="mt-6 px-4 space-y-1 pb-20 h-[calc(100vh-100px)] overflow-y-auto custom-scrollbar">
                @if(auth()->user()->role === 'admin')
                <!-- Overview -->
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-3 mb-5 mt-4">Tổng quan</div>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 p-3 rounded-xl {{ Request::routeIs('admin.dashboard') ? 'bg-indigo-600 shadow-lg shadow-indigo-900/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-chart-line w-5 text-center"></i>
                    <span class="font-bold text-sm">Bảng điều khiển</span>
                </a>

                <!-- Residence Management -->
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-3 mb-5 mt-12 pt-6 border-t border-slate-800/50">Quản lý cư trú</div>
                <a href="{{ route('buildings.index') }}" class="flex items-center space-x-3 p-3 rounded-xl {{ Request::routeIs('buildings.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-900/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-building w-5 text-center"></i>
                    <span class="font-bold text-sm">Tòa nhà & Khu trọ</span>
                </a>
                <a href="{{ route('rooms.index') }}" class="flex items-center space-x-3 p-3 rounded-xl {{ Request::routeIs('rooms.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-900/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-door-open w-5 text-center"></i>
                    <span class="font-bold text-sm">Phòng trọ</span>
                </a>
                <a href="{{ route('tenants.index') }}" class="flex items-center space-x-3 p-3 rounded-xl {{ Request::routeIs('tenants.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-900/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span class="font-bold text-sm">Người thuê</span>
                </a>
                <a href="{{ route('contracts.index') }}" class="flex items-center space-x-3 p-3 rounded-xl {{ Request::routeIs('contracts.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-900/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-file-contract w-5 text-center"></i>
                    <span class="font-bold text-sm">Hợp đồng</span>
                </a>

                <!-- Services & Utilities -->
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-3 mb-5 mt-12 pt-6 border-t border-slate-800/50">Dịch vụ & Tiện ích</div>
                <a href="{{ route('meter-readings.index') }}" class="flex items-center space-x-3 p-3 rounded-xl {{ Request::routeIs('meter-readings.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-900/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-tachometer-alt w-5 text-center"></i>
                    <span class="font-bold text-sm">Chỉ số Điện Nước</span>
                </a>
                <a href="{{ route('services.index') }}" class="flex items-center space-x-3 p-3 rounded-xl {{ Request::routeIs('services.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-900/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-concierge-bell w-5 text-center"></i>
                    <span class="font-bold text-sm">Dịch vụ cố định</span>
                </a>
                <a href="{{ route('parking.index') }}" class="flex items-center space-x-3 p-3 rounded-xl {{ Request::routeIs('parking.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-900/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-motorcycle w-5 text-center"></i>
                    <span class="font-bold text-sm">Thẻ gửi xe</span>
                </a>

                <!-- Finance & Maintenance -->
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-3 mb-5 mt-12 pt-6 border-t border-slate-800/50">Tài chính & Bảo trì</div>
                <a href="{{ route('invoices.index') }}" class="flex items-center space-x-3 p-3 rounded-xl {{ Request::routeIs('invoices.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-900/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-file-invoice-dollar w-5 text-center"></i>
                    <span class="font-bold text-sm">Hóa đơn thu tiền</span>
                </a>
                <a href="{{ route('issues.index') }}" class="flex items-center space-x-3 p-3 rounded-xl {{ Request::routeIs('issues.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-900/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-tools w-5 text-center"></i>
                    <span class="font-bold text-sm">Sự cố & Sửa chữa</span>
                </a>

                <!-- System -->
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-3 mb-5 mt-12 pt-6 border-t border-slate-800/50">Hệ thống</div>
                <a href="{{ route('notifications.index') }}" class="flex items-center space-x-3 p-3 rounded-xl {{ Request::routeIs('notifications.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-900/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-bell w-5 text-center"></i>
                    <span class="font-bold text-sm">Thông báo</span>
                </a>
                <a href="{{ route('settings.index') }}" class="flex items-center space-x-3 p-3 rounded-xl {{ Request::is('admin/settings*') ? 'bg-indigo-600 shadow-lg shadow-indigo-900/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-cog w-5 text-center"></i>
                    <span class="font-bold text-sm">Cài đặt</span>
                </a>
                @else
                <!-- Tenant Sidebar -->
                <!-- Overview -->
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-3 mb-5 mt-4">Tổng quan</div>
                <a href="{{ route('tenant.dashboard') }}" class="flex items-center space-x-3 p-3 rounded-xl {{ Request::routeIs('tenant.dashboard') ? 'bg-indigo-600 shadow-lg shadow-indigo-900/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-home w-5 text-center"></i>
                    <span class="font-bold text-sm">Bảng điều khiển</span>
                </a>

                <!-- Services -->
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-3 mb-5 mt-12 pt-6 border-t border-slate-800/50">Dịch vụ & Tài chính</div>
                <a href="{{ route('tenant.invoices.index') }}" class="flex items-center space-x-3 p-3 rounded-xl {{ Request::routeIs('tenant.invoices.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-900/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-file-invoice-dollar w-5 text-center"></i>
                    <span class="font-bold text-sm">Hóa đơn của tôi</span>
                </a>
                <a href="{{ route('tenant.issues.index') }}" class="flex items-center space-x-3 p-3 rounded-xl {{ Request::routeIs('tenant.issues.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-900/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-tools w-5 text-center"></i>
                    <span class="font-bold text-sm">Báo cáo sự cố</span>
                </a>

                <!-- Personal -->
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-3 mb-5 mt-12 pt-6 border-t border-slate-800/50">Cá nhân & Hỗ trợ</div>
                <a href="{{ route('tenant.notifications.index') }}" class="flex items-center space-x-3 p-3 rounded-xl {{ Request::routeIs('tenant.notifications.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-900/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-bell w-5 text-center"></i>
                    <span class="font-bold text-sm">Thông báo</span>
                </a>
                <a href="{{ route('tenant.profile') }}" class="flex items-center space-x-3 p-3 rounded-xl {{ Request::routeIs('tenant.profile') ? 'bg-indigo-600 shadow-lg shadow-indigo-900/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }} transition-all">
                    <i class="fas fa-user w-5 text-center"></i>
                    <span class="font-bold text-sm">Hồ sơ cá nhân</span>
                </a>
                @endif
                
                <div class="pt-8 pb-2">
                    <form action="{{ route('logout') }}" method="POST" id="logout-form" class="hidden">@csrf</form>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-red-500/10 text-red-400 transition-all group">
                        <i class="fas fa-power-off w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-bold text-sm">Đăng xuất</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Sidebar Overlay (Mobile) -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden md:hidden transition-opacity duration-300 opacity-0"></div>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-screen overflow-y-auto w-full">
            <!-- Topbar -->
            <header class="bg-white/80 backdrop-blur-md border-b h-16 flex items-center justify-between px-4 md:px-8 sticky top-0 z-30">
                <div class="flex items-center">
                    <button id="mobile-menu-btn" class="md:hidden mr-4 w-10 h-10 flex items-center justify-center bg-gray-50 rounded-xl text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition-all">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2 class="text-lg md:text-xl font-bold text-gray-800 truncate max-w-[150px] md:max-w-none">@yield('title')</h2>
                </div>
                <div class="flex items-center space-x-2 md:space-x-4">
                    <div class="flex items-center space-x-2 md:space-x-3 border-l pl-2 md:pl-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=4f46e5&color=fff" class="w-8 h-8 rounded-xl shadow-sm">
                        <div class="hidden sm:flex flex-col">
                            <span class="text-sm font-bold text-gray-700 leading-none">{{ auth()->user()->name }}</span>
                            <span class="text-[9px] text-gray-400 uppercase font-black mt-1 tracking-wider">
                                {{ auth()->user()->role === 'admin' ? 'Quản trị viên' : 'Khách thuê' }}
                            </span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="p-4 md:p-8">
                @if(session('success'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 text-emerald-700 shadow-sm rounded-r-lg" role="alert">
                    <p class="font-bold flex items-center"><i class="fas fa-check-circle mr-2"></i> Thành công</p>
                    <p class="text-sm pl-6">{{ session('success') }}</p>
                </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const closeSidebarBtn = document.getElementById('close-sidebar');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            if (!sidebar.classList.contains('-translate-x-full')) {
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    overlay.classList.remove('opacity-0');
                }, 10);
            } else {
                overlay.classList.add('opacity-0');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300);
            }
        }

        mobileMenuBtn?.addEventListener('click', toggleSidebar);
        closeSidebarBtn?.addEventListener('click', toggleSidebar);
        overlay?.addEventListener('click', toggleSidebar);
    </script>
    @include('partials.ai-chat')
</body>
</html>
