<?php
session_start();
require 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['username'];

// Ambil NIK pengguna berdasarkan nama
$stmt = $conn->prepare("SELECT nik FROM users WHERE nama = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo '<script>alert("Data pengguna tidak ditemukan!");</script>';
    exit;
}

$nik = $data['nik'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan Perjalanan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background: rgb(36, 149, 255);
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 15px;
            display: block;
        }
        .sidebar a:hover {
            background: rgb(36, 149, 255);
        }
        .content {
            margin-left: 260px;
            padding: 20px;
        }
        .navbar {
            background: rgb(36, 149, 255);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="text-center mb-4">
            <img src="assets/img/icon_main.png" alt="Logo" width="120">
        </div>
        <a href="home.php"><i class="fa fa-home me-2"></i>Home</a>
        <hr>
        <a href="catatan.php"><i class="fa fa-book me-2"></i>Catatan Perjalanan</a>
        <hr>
        <a href="isidata.php"><i class="fa fa-edit me-2"></i>Isi Data</a>
        <hr>
        <!-- Sidebar Footer -->
        <div class="sidebar-footer text-center">
            <a href="logout.php" class="link" data-toggle="tooltip" title="Logout">
                <i class="mdi mdi-power"></i> Logout
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Peduli Diri</a>
                <div class="ms-auto text-white">
                    <i class="fa fa-user-circle"></i> <?php echo $_SESSION['username']; ?>
                </div>
            </div>
        </nav>
        
        <div class="container mt-4">
            <h4 class="mb-3">Catatan Perjalanan</h4>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead style="background-color: rgb(36, 149, 255); color: white;">
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Lokasi</th>
                            <th>Suhu</th>
                            <th>Foto</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php
                                $stmt = $conn->prepare("SELECT * FROM catatan WHERE nik = ?");
                                $stmt->bind_param("s", $nik);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row['tanggal'] . "</td>";
                                    echo "<td>" . $row['waktu'] . "</td>";
                                    echo "<td>" . $row['lokasi'] . "</td>";
                                    echo "<td>" . $row['suhu'] . "</td>";
                                    echo "<td><button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editModal' onclick='editData(" . $row['id_catatan'] . ", \"" . $row['tanggal'] . "\", \"" . $row['waktu'] . "\", \"" . $row['lokasi'] . "\", \"" . $row['suhu'] . "\")'>Edit</button></td>";
                                    echo "<td> <button class='btn btn-danger btn-sm' onclick='hapusData(" . $row['id_catatan'] . ")'>Hapus</button></td>";
                                    echo "</tr>";
                                }
                             ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
