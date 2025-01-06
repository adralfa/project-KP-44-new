<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_reporting(0);
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

// Include file koneksi database
include '../../koneksi.php';

// Proses Tambah/Edit/Hapus Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $nik = $conn->real_escape_string($_POST['nik']);
    $nama_dosen = $conn->real_escape_string($_POST['nama_dosen']);

    if ($action === 'add') {
        $check_sql = "SELECT COUNT(*) AS count FROM dosen WHERE nik = '$nik'";
        $check_result = $conn->query($check_sql);
        $check_count = $check_result->fetch_assoc()['count'];

        if ($check_count > 0) {
            // Jika NIK sudah ada, tampilkan pesan error
            $error_message = "NIK sudah terdaftar. Silakan gunakan NIK yang lain.";
        } else {
            // Tambah data
            $sql = "INSERT INTO dosen (nik, nama_dosen) VALUES ('$nik', '$nama_dosen')";
            $conn->query($sql);
            header("Location: dosen.php");
            exit;
        }
    } elseif ($action === 'edit') {
        $sql = "UPDATE dosen SET nama_dosen = '$nama_dosen' WHERE nik = '$nik'";
        $conn->query($sql);
        $_SESSION['success_message'] = "Data dosen berhasil diperbarui.";
    } elseif ($action === 'delete') {
        $sql = "DELETE FROM dosen WHERE nik = '$nik'";
        $conn->query($sql);
        $_SESSION['success_message'] = "Data dosen berhasil dihapus.";
    }
    header("Location: dosen2.php");
    exit;
}

// Default nilai form kosong
$edit_nik = '';
$edit_nama_dosen = '';

// Cek apakah ada parameter edit
if (isset($_GET['edit_nik'])) {
    $edit_nik = $conn->real_escape_string($_GET['edit_nik']);
    $sql_edit = "SELECT * FROM dosen WHERE nik = '$edit_nik'";
    $result_edit = $conn->query($sql_edit);

    if ($result_edit->num_rows > 0) {
        $row_edit = $result_edit->fetch_assoc();
        $edit_nik = $row_edit['nik'];
        $edit_nama_dosen = $row_edit['nama_dosen'];
    }
}

// Konfigurasi pagination dan pencarian
$items_per_page = 10;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$search = isset($_GET['search']) ? trim($conn->real_escape_string($_GET['search'])) : '';
$where_clause = $search ? "WHERE d.nama_dosen LIKE '%$search%' OR d.nik LIKE '%$search%'" : '';

$sql_count = "SELECT COUNT(*) AS total FROM dosen d $where_clause";
$total_rows = $conn->query($sql_count)->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $items_per_page);

$offset = ($page - 1) * $items_per_page;
$sql = "
    SELECT 
        d.nik, 
        d.nama_dosen, 
        COALESCE(GROUP_CONCAT(DISTINCT kc.no_kelompok ORDER BY kc.no_kelompok ASC SEPARATOR ', '), '-') AS kelompok
    FROM dosen d
    LEFT JOIN kpconnection kc ON d.nik = kc.nik
    $where_clause
    GROUP BY d.nik, d.nama_dosen
    LIMIT $offset, $items_per_page
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dosen</title>
    <link rel="stylesheet" href="../../../css/adminlte.min.css" crossorigin="anonymous"/>
    <script src="../../../js/adminlte.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<div class="container-fluid px-5 py-3">
    <h1>Data Dosen</h1>
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="dosen2.php">
        <input type="hidden" name="action" value="<?php echo empty($edit_nik) ? 'add' : 'edit'; ?>">
        <div class="mb-3">
            <label for="nik" class="form-label">NIK</label>
            <input type="text" id="nik" name="nik" class="form-control" value="<?php echo htmlspecialchars($edit_nik); ?>" <?php echo empty($edit_nik) ? '' : 'readonly'; ?> required>
        </div>
        <div class="mb-3">
            <label for="nama_dosen" class="form-label">Nama Dosen</label>
            <input type="text" id="nama_dosen" name="nama_dosen" class="form-control" value="<?php echo htmlspecialchars($edit_nama_dosen); ?>" required>
        </div>
        <button type="submit" class="btn btn-success"><?php echo empty($edit_nik) ? 'Tambah' : 'Perbarui'; ?></button>
        <a href="dosen2.php" class="btn btn-secondary">Batal</a>
    </form>
    <hr>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>NIK</th>
                <th>Nama Dosen</th>
                <th>Kelompok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nik']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_dosen']); ?></td>
                        <td><?php echo htmlspecialchars($row['kelompok']); ?></td>
                        <td>
                            <a href="dosen2.php?edit_nik=<?php echo $row['nik']; ?>" class="btn btn-primary btn-sm">Ubah</a>
                            <form method="POST" action="dosen.php" class="d-inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="nik" value="<?php echo htmlspecialchars($row['nik']); ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Data tidak ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
