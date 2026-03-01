<!-- Mobile Menu Button -->
<button id="mobileMenuBtn" class="md:hidden fixed top-20 left-4 z-50 bg-gray-800 text-white p-2 rounded-lg" onclick="toggleMobileSidebar()">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</button>

<!-- Mobile Sidebar Overlay -->
<div id="mobileSidebarOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden" onclick="toggleMobileSidebar()"></div>

<!-- Mobile Sidebar -->
<div id="mobileSidebar" class="hidden fixed left-0 top-16 w-64 h-full bg-gray-800 text-white overflow-y-auto z-50 md:hidden">
    @include('layouts.partials.admin-sidebar-content')
</div>

<!-- Desktop Sidebar -->
<aside class="hidden md:block fixed left-0 top-16 w-64 h-full bg-gray-800 text-white overflow-y-auto z-30">
    @include('layouts.partials.admin-sidebar-content')
</aside>

<script>
function toggleMobileSidebar() {
    const mobileSidebar = document.getElementById('mobileSidebar');
    const overlay = document.getElementById('mobileSidebarOverlay');
    
    if (mobileSidebar.classList.contains('hidden')) {
        mobileSidebar.classList.remove('hidden');
        overlay.classList.remove('hidden');
    } else {
        mobileSidebar.classList.add('hidden');
        overlay.classList.add('hidden');
    }
}
</script>
