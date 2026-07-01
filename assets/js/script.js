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
 * Tampilkan/sembunyikan card form tambah data (mis. Tambah Pemasukan, Tambah Kategori).
 * Dipakai lewat: onclick="toggleFormCard('idCardForm')"
 */
function toggleFormCard(id) {
    var card = document.getElementById(id);
    if (!card) {
        return;
    }

    if (card.style.display === 'none') {
        card.style.display = 'block';
        card.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        card.style.display = 'none';
    }
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
