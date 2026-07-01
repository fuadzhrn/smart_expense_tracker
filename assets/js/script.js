/* =========================================================
   Smart Expense Tracker - Main Script
========================================================= */

/**
 * Konfirmasi sebelum menghapus data.
 * Dipakai lewat: onclick="return confirmDelete()"
 */
function confirmDelete(message) {
    return confirm(message || 'Yakin ingin menghapus data ini?');
}

/**
 * Toggle sidebar untuk tampilan layar kecil.
 */
document.addEventListener('DOMContentLoaded', function () {
    var sidebar = document.getElementById('sidebar');
    var toggleBtn = document.getElementById('sidebarToggle');
    var overlay = document.getElementById('sidebarOverlay');

    if (!sidebar || !toggleBtn || !overlay) {
        return;
    }

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('show');
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    }

    toggleBtn.addEventListener('click', function () {
        if (sidebar.classList.contains('open')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    overlay.addEventListener('click', closeSidebar);
});
