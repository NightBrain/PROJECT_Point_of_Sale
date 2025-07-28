<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    <!-- Tailwind CSS & Libs -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" href="{{ asset('https://cdn-icons-png.freepik.com/512/4990/4990622.png') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const mainContent = document.getElementById("main-content");

            sidebar.classList.toggle("w-64");
            sidebar.classList.toggle("w-20");

            mainContent.classList.toggle("md:ml-64");
            mainContent.classList.toggle("md:ml-20");

            // ซ่อน/แสดงข้อความเมนู
            document.querySelectorAll(".menu-text").forEach(el => {
                el.classList.toggle("hidden");
            });
        }
    </script>
</head>

<body class="bg-gradient-to-br from-blue-50 to-purple-100 min-h-screen font-sans">

    <!-- ✅ Sidebar -->
    <div id="sidebar"
        class="fixed top-0 left-0 w-64 h-full bg-gradient-to-b from-purple-300 via-pink-200 to-yellow-200
        shadow-xl transition-all duration-300 z-50 rounded-r-xl flex flex-col">

        <!-- Header + ปุ่ม Toggle -->
        <div class="p-4 flex justify-between items-center border-b border-white/40">
            <h2 class="text-lg font-extrabold text-white drop-shadow-md menu-text">🌈 Admin POS</h2>
            <button onclick="toggleSidebar()" class="text-white text-xl hover:text-gray-200 transition">
                ☰
            </button>
        </div>

        <!-- เมนู -->
        <nav class="flex-grow mt-4 px-3 space-y-2 overflow-auto">
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg bg-white/30 hover:bg-white/50 
                text-gray-500 font-semibold shadow transition">
                <span class="text-xl">📊</span>
                <span class="menu-text">Dashboard</span>
            </a>
            <a href="{{ route('products.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg bg-white/30 hover:bg-white/50 
                text-gray-500 font-semibold shadow transition">
                <span class="text-xl">📦</span>
                <span class="menu-text">Manage products</span>
            </a>
            <a href="{{ route('pos') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg bg-white/30 hover:bg-white/50 
                text-gray-500 font-semibold shadow transition">
                <span class="text-xl">🛒</span>
                <span class="menu-text">POS</span>
            </a>
            <a href="{{ route('sales.history') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg bg-white/30 hover:bg-white/50 
                text-gray-500 font-semibold shadow transition">
                <span class="text-xl">⌛</span>
                <span class="menu-text">Order History</span>
            </a>
            <a href="{{ route('employees.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg bg-white/30 hover:bg-white/50 
                text-gray-500 font-semibold shadow transition">
                <span class="text-xl">👥</span>
                <span class="menu-text">Manage Staff</span>
            </a>
            <a href="{{ route('activity.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg bg-white/30 hover:bg-white/50 
                text-gray-500 font-semibold shadow transition">
                <span class="text-xl">🎭</span>
                <span class="menu-text">Activity Log</span>
            </a>
        </nav>

        <!-- ปุ่ม Logout -->
        <div class="px-3 pb-6 mt-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-lg 
                    bg-red-300/60 hover:bg-red-400 text-gray-500 font-semibold shadow transition">
                    <span class="text-xl">🚪</span>
                    <span class="menu-text">ออกจากระบบ</span>
                </button>
            </form>
        </div>

        <!-- Copyright -->
        <div class="text-center text-xs text-gray-700 pb-3 menu-text">
            © {{ date('Y') }} POS System. All rights reserved. Designed & Developed By
            <a href="https://github.com/NightBrain" class="text-red-400">NightBrain</a>
        </div>
    </div>

    <!-- ✅ Top Bar (เต็มจอเสมอ) -->
    <nav
        class="fixed top-0 left-0 w-full h-16 
        bg-gradient-to-r from-pink-200 via-purple-200 to-blue-200 
        shadow-md flex justify-end items-center px-6 z-40">
        <div class="hidden md:block text-gray-600 font-medium">
            👋 สวัสดี, {{ Auth::user()->name ?? 'Admin' }}
        </div>
    </nav>

    <!-- ✅ Main Content -->
    <div id="main-content" class="pt-20 md:ml-64 p-6 transition-all duration-300">
        {{ $slot ?? '' }}
        @yield('content')
    </div>

</body>

</html>
