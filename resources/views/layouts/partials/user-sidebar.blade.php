<!-- Mobile Menu Button -->
<button id="mobileMenuBtn" class="md:hidden fixed top-20 left-4 z-50 bg-gray-700 text-white p-2 rounded-lg shadow-lg" onclick="toggleMobileSidebar()">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</button>

<!-- Mobile Sidebar Overlay -->
<div id="mobileOverlay" class="md:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden" onclick="toggleMobileSidebar()"></div>

<!-- Mobile Sidebar -->
<aside id="mobileSidebar" class="md:hidden fixed left-0 top-0 w-64 h-full bg-gray-700 text-white overflow-y-auto z-40 transform -translate-x-full transition-transform duration-300">
    @include('layouts.partials.user-sidebar-content')
</aside>

<!-- Desktop Sidebar -->
<aside class="hidden md:block fixed left-0 top-16 w-64 h-full bg-gray-700 text-white overflow-y-auto z-30">
    @include('layouts.partials.user-sidebar-content')
</aside>

<script>
function toggleMobileSidebar() {
    const sidebar = document.getElementById('mobileSidebar');
    const overlay = document.getElementById('mobileOverlay');
    
    if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }
}
</script>
