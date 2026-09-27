<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'karyawan') {
    header("Location: ../index.php");
    exit;
}

$username = $_SESSION['username'];

// Jumlah barang tersedia
$query_barang = mysqli_query($conn, "SELECT COUNT(*) AS total FROM barang WHERE jumlah > 0");
$data_barang = mysqli_fetch_assoc($query_barang);
$total_barang = $data_barang['total'];

// Jumlah peminjaman karyawan
$query_pinjam = mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM peminjaman
    WHERE user_id = " . (int) $_SESSION['user_id']);
$data_pinjam = mysqli_fetch_assoc($query_pinjam);
$total_pinjam = $data_pinjam['total'];

// Peminjaman menunggu
$query_menunggu = mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM peminjaman
    WHERE user_id = " . (int) $_SESSION['user_id'] . "
    AND status = 'Menunggu'
");
$data_menunggu = mysqli_fetch_assoc($query_menunggu);
$total_menunggu = $data_menunggu['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Karyawan</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            background: #f4f6f9;
        }

        .sidebar {
            width: 230px;
            height: 100vh;
            background: #1e293b;
            color: white;
            position: fixed;
            padding: 25px 15px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px;
            margin: 5px 0;
            border-radius: 6px;
        }

        .sidebar a:hover {
            background: #334155;
        }

        .content {
            margin-left: 230px;
            padding: 30px;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card {
            background: white;
            width: 30%;
            min-width: 200px;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .card h3 {
            margin: 0;
            color: #64748b;
        }

        .card p {
            font-size: 30px;
            font-weight: bold;
            margin: 15px 0 0;
        }

        @media (max-width: 700px) {
            .sidebar { position: static; width: auto; height: auto; padding: 15px; }
            .sidebar a { display: inline-block; padding: 8px; }
            .content { margin-left: 0; padding: 15px; }
            .card { width: 100%; min-width: 0; }
        }
    </style>
</head>

<body>

<div class="sidebar">
    <h2>Soundbest</h2>

    <a href="dashboard.php">Dashboard</a>
    <a href="barang.php">Data Barang</a>
    <a href="peminjaman.php">Peminjaman</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<div class="content">

    <div class="header">
        <h2>Dashboard Karyawan</h2>
        <p>Selamat datang, <b><?php echo htmlspecialchars($username); ?></b></p>
    </div>

    <div class="cards">

        <div class="card">
            <h3>Barang Tersedia</h3>
            <p><?php echo $total_barang; ?></p>
        </div>

        <div class="card">
            <h3>Total Peminjaman</h3>
            <p><?php echo $total_pinjam; ?></p>
        </div>

        <div class="card">
            <h3>Menunggu</h3>
            <p><?php echo $total_menunggu; ?></p>
        </div>

    </div>

</div>

</body>
</html>