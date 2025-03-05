<?php
session_start();
require 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: catatan.php");
    exit();
}

$id = $_GET['id'];

// Ambil data catatan berdasarkan ID
$stmt = $conn->prepare("SELECT * FROM catatan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$catatan = $result->fetch_assoc();

if (!$catatan) {
    echo "<script>alert('Catatan tidak ditemukan!'); window.location.href='catatan.php';</script>";
    exit();
}

if (isset($_POST['simpan'])) {
    $tanggal = $_POST['tanggal'];
    $waktu   = $_POST['waktu'];
    $lokasi  = $_POST['lokasi'];
    $suhu    = $_POST['suhu'];
    $fotoNama = $catatan['foto']; // default, jika tidak ada foto baru

    // Jika ada foto baru yang diunggah, proses upload foto
    if ($_FILES['foto']['name']) {
        $fotoNama = time() . '_' . basename($_FILES['foto']['name']);
        $fotoPath = 'uploads/' . $fotoNama;
        move_uploaded_file($_FILES['foto']['tmp_name'], $fotoPath);
    }

    $stmt = $conn->prepare("UPDATE catatan SET tanggal = ?, waktu = ?, lokasi = ?, suhu = ?, foto = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $tanggal, $waktu, $lokasi, $suhu, $fotoNama, $id);

    if ($stmt->execute()) {
        $_SESSION['alert'] = ['type'=>'success', 'message'=>'Catatan berhasil diperbarui!'];
        header("Location: catatan.php");
        exit();
    } else {
        $_SESSION['alert'] = ['type'=>'error', 'message'=>'Gagal memperbarui catatan!'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Catatan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Catatan Perjalanan</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal:</label>
                <input type="date" name="tanggal" class="form-control" value="<?= $catatan['tanggal'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="waktu" class="form-label">Jam:</label>
                <input type="time" name="waktu" class="form-control" value="<?= $catatan['waktu'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="lokasi" class="form-label">Lokasi:</label>
                <input type="text" name="lokasi" class="form-control" value="<?= $catatan['lokasi'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="suhu" class="form-label">Suhu Tubuh:</label>
                <input type="text" name="suhu" class="form-control" value="<?= $catatan['suhu'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="foto" class="form-label">Foto:</label><br>
                <?php
                if ($catatan['foto']) {
                    echo "<img src='uploads/{$catatan['foto']}' width='100' alt='Foto'><br>";
                }
                ?>
                <input type="file" name="foto" class="form-control">
            </div>
            <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
        </form>
    </div>
</body>
</html>
