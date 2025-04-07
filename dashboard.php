<?php
require_once 'php/auth.php';
require_once 'php/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get letter statistics
$query = "SELECT 
    (SELECT COUNT(*) FROM surat_masuk) AS total_masuk,
    (SELECT COUNT(*) FROM surat_keluar) AS total_keluar,
    (SELECT COUNT(*) FROM surat_masuk WHERE DATE(tanggal) = CURDATE()) AS masuk_hari_ini,
    (SELECT COUNT(*) FROM surat_keluar WHERE DATE(tanggal) = CURDATE()) AS keluar_hari_ini";
$stats = $conn->query($query)->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Surat Masama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-blue-800 text-white w-64 p-4">
            <div class="mb-8">
                <h1 class="text-xl font-bold">KECAMATAN MASAMA</h1>
                <p class="text-sm">Kabupaten Banggai</p>
            </div>
            <nav>
                <a href="dashboard.php" class="block py-2 px-4 bg-blue-700 rounded-lg mb-2">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                <a href="surat_masuk.php" class="block py-2 px-4 hover:bg-blue-700 rounded-lg mb-2">
                    <i class="fas fa-envelope mr-2"></i>Surat Masuk
                </a>
                <a href="surat_keluar.php" class="block py-2 px-4 hover:bg-blue-700 rounded-lg mb-2">
                    <i class="fas fa-paper-plane mr-2"></i>Surat Keluar
                </a>
                <a href="?logout" class="block py-2 px-4 hover:bg-blue-700 rounded-lg mt-8">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <h2 class="text-2xl font-bold mb-6">Dashboard</h2>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                            <i class="fas fa-envelope fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-gray-500">Total Surat Masuk</p>
                            <h3 class="text-2xl font-bold"><?= $stats['total_masuk'] ?></h3>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                            <i class="fas fa-paper-plane fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-gray-500">Total Surat Keluar</p>
                            <h3 class="text-2xl font-bold"><?= $stats['total_keluar'] ?></h3>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                            <i class="fas fa-envelope-open-text fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-gray-500">Surat Masuk Hari Ini</p>
                            <h3 class="text-2xl font-bold"><?= $stats['masuk_hari_ini'] ?></h3>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                            <i class="fas fa-share-square fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-gray-500">Surat Keluar Hari Ini</p>
                            <h3 class="text-2xl font-bold"><?= $stats['keluar_hari_ini'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Chart -->
            <div class="bg-white p-6 rounded-lg shadow mb-8">
                <h3 class="text-lg font-semibold mb-4">Statistik Bulanan</h3>
                <canvas id="letterChart" height="150"></canvas>
            </div>
            
            <!-- Recent Letters -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Surat Masuk Terbaru</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">No. Surat</th>
                                    <th class="text-left py-2">Tanggal</th>
                                    <th class="text-left py-2">Perihal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT nomor_surat, tanggal, perihal FROM surat_masuk 
                                          ORDER BY created_at DESC LIMIT 5";
                                $result = $conn->query($query);
                                while ($row = $result->fetch_assoc()):
                                ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2"><?= $row['nomor_surat'] ?></td>
                                    <td class="py-2"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                    <td class="py-2"><?= $row['perihal'] ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Surat Keluar Terbaru</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">No. Surat</th>
                                    <th class="text-left py-2">Tanggal</th>
                                    <th class="text-left py-2">Perihal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT nomor_surat, tanggal, perihal FROM surat_keluar 
                                          ORDER BY created_at DESC LIMIT 5";
                                $result = $conn->query($query);
                                while ($row = $result->fetch_assoc()):
                                ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2"><?= $row['nomor_surat'] ?></td>
                                    <td class="py-2"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                    <td class="py-2"><?= $row['perihal'] ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Chart.js implementation
        const ctx = document.getElementById('letterChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Surat Masuk',
                    data: [12, 19, 3, 5, 2, 3, 7, 8, 9, 10, 11, 12],
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }, {
                    label: 'Surat Keluar',
                    data: [8, 15, 5, 3, 4, 6, 4, 7, 8, 5, 9, 10],
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>