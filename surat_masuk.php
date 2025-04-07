<?php
require_once 'php/auth.php';
require_once 'php/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor_surat = $_POST['nomor_surat'];
    $tanggal = DateTime::createFromFormat('d/m/Y', $_POST['tanggal'])->format('Y-m-d');
    $perihal = $_POST['perihal'];
    $pengirim = $_POST['pengirim'];
    $ditujukan = $_POST['ditujukan'];
    $isi = $_POST['isi'];

    $stmt = $conn->prepare("INSERT INTO surat_masuk (nomor_surat, tanggal, perihal, pengirim, ditujukan, isi) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nomor_surat, $tanggal, $perihal, $pengirim, $ditujukan, $isi);
    $stmt->execute();
    
    header("Location: surat_masuk.php?success=1");
    exit();
}

// Handle search
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM surat_masuk WHERE nomor_surat LIKE ? OR perihal LIKE ? ORDER BY tanggal DESC";
$stmt = $conn->prepare($query);
$search_param = "%$search%";
$stmt->bind_param("ss", $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Masuk - Sistem Surat Masama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include 'partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Surat Masuk</h2>
                <button onclick="document.getElementById('addModal').classList.remove('hidden')" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i>Tambah Surat
                </button>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    Surat masuk berhasil ditambahkan!
                </div>
            <?php endif; ?>

            <!-- Search Bar -->
            <div class="mb-6">
                <form action="surat_masuk.php" method="GET" class="flex">
                    <input type="text" name="search" placeholder="Cari surat masuk..." 
                           value="<?= htmlspecialchars($search) ?>"
                           class="flex-1 px-4 py-2 border rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-lg hover:bg-blue-700">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Letters Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perihal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ditujukan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap"><?= $row['nomor_surat'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                <td class="px-6 py-4"><?= $row['perihal'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= $row['pengirim'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= $row['ditujukan'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="detail_surat_masuk.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:text-blue-800 mr-3">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit_surat_masuk.php?id=<?= $row['id'] ?>" class="text-yellow-600 hover:text-yellow-800 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete_surat_masuk.php?id=<?= $row['id'] ?>" class="text-red-600 hover:text-red-800" onclick="return confirm('Hapus surat ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Letter Modal -->
    <div id="addModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
            <div class="flex justify-between items-center border-b p-4">
                <h3 class="text-lg font-semibold">Tambah Surat Masuk</h3>
                <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="surat_masuk.php" method="POST" class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Nomor Surat</label>
                        <input type="text" name="nomor_surat" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Tanggal (dd/mm/yyyy)</label>
                        <input type="text" name="tanggal" placeholder="01/01/2023" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Perihal</label>
                    <input type="text" name="perihal" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Pengirim</label>
                        <input type="text" name="pengirim" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Ditujukan</label>
                        <input type="text" name="ditujukan" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Isi Surat</label>
                    <textarea name="isi" rows="4"
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>