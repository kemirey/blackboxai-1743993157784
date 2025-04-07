<?php
require_once '../php/auth.php';
require_once '../php/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
?>
<div class="bg-blue-800 text-white w-64 p-4">
    <div class="mb-8">
        <h1 class="text-xl font-bold">KECAMATAN MASAMA</h1>
        <p class="text-sm">Kabupaten Banggai</p>
    </div>
    <nav>
        <a href="../dashboard.php" class="block py-2 px-4 <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'bg-blue-700' : 'hover:bg-blue-700' ?> rounded-lg mb-2">
            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
        </a>
        <a href="../surat_masuk.php" class="block py-2 px-4 <?= basename($_SERVER['PHP_SELF']) === 'surat_masuk.php' ? 'bg-blue-700' : 'hover:bg-blue-700' ?> rounded-lg mb-2">
            <i class="fas fa-envelope mr-2"></i>Surat Masuk
        </a>
        <a href="../surat_keluar.php" class="block py-2 px-4 <?= basename($_SERVER['PHP_SELF']) === 'surat_keluar.php' ? 'bg-blue-700' : 'hover:bg-blue-700' ?> rounded-lg mb-2">
            <i class="fas fa-paper-plane mr-2"></i>Surat Keluar
        </a>
        <a href="../?logout" class="block py-2 px-4 hover:bg-blue-700 rounded-lg mt-8">
            <i class="fas fa-sign-out-alt mr-2"></i>Logout
        </a>
    </nav>
</div>