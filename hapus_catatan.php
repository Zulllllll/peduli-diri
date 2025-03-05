<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID tidak ditemukan atau kosong!");
}

$id = (int) $_GET['id'];

$query = "DELETE FROM catatan WHERE id_catatan = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>
        setTimeout(function() {
            Swal.fire({
                icon: 'success',
                title: 'Data berhasil dihapus!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'catatan.php';
            });
        }, 100);
    </script>";
} else {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal menghapus data!',
            text: '" . $stmt->error . "'
        });
    </script>";
}

$stmt->close();
$conn->close();
?>



<!-- Tambahkan SweetAlert2 -->
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>