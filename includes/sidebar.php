<?php
/**
 * Sidebar navigasi + pembuka area konten utama.
 * Aktif menu ditentukan dari nama file yang sedang dibuka.
 */

$currentPage = basename($_SERVER['SCRIPT_NAME']);

function menuActive($page, $currentPage)
{
    return $page === $currentPage ? 'active' : '';
}
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <span class="logo-icon"><i class="fa-solid fa-wallet"></i></span>
        <span class="logo-text">Smart Expense</span>
    </div>

    <nav class="sidebar-menu">
        <a href="<?php echo BASE_URL; ?>pages/dashboard.php" class="menu-item <?php echo menuActive('dashboard.php', $currentPage); ?>">
            <span class="menu-icon"><i class="fa-solid fa-gauge-high"></i></span>
            <span>Dashboard</span>
        </a>
        <a href="<?php echo BASE_URL; ?>pages/pemasukan.php" class="menu-item <?php echo menuActive('pemasukan.php', $currentPage); ?>">
            <span class="menu-icon"><i class="fa-solid fa-sack-dollar"></i></span>
            <span>Pemasukan</span>
        </a>
        <a href="<?php echo BASE_URL; ?>pages/pengeluaran.php" class="menu-item <?php echo menuActive('pengeluaran.php', $currentPage); ?>">
            <span class="menu-icon"><i class="fa-solid fa-money-bill-transfer"></i></span>
            <span>Pengeluaran</span>
        </a>
        <a href="<?php echo BASE_URL; ?>pages/kategori.php" class="menu-item <?php echo menuActive('kategori.php', $currentPage); ?>">
            <span class="menu-icon"><i class="fa-solid fa-tags"></i></span>
            <span>Kategori</span>
        </a>
        <a href="<?php echo BASE_URL; ?>pages/laporan.php" class="menu-item <?php echo menuActive('laporan.php', $currentPage); ?>">
            <span class="menu-icon"><i class="fa-solid fa-chart-line"></i></span>
            <span>Laporan</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="<?php echo BASE_URL; ?>auth/logout.php" class="menu-item menu-logout">
            <span class="menu-icon"><i class="fa-solid fa-right-from-bracket"></i></span>
            <span>Logout</span>
        </a>
    </div>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="main-content">
    <header class="topbar">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Buka menu">
            <i class="fa-solid fa-bars"></i>
        </button>
        <h1 class="page-title"><?php echo htmlspecialchars($pageTitle ?? ''); ?></h1>
    </header>

    <main class="content">
