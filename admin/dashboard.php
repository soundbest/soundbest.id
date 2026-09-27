<?php
session_start();
require_once "../config/database.php";

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Hanya admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Hitung jumlah barang
$query_barang = mysqli_query($conn, "SELECT COUNT(*) AS total FROM barang");
$data_barang = mysqli_fetch_assoc($query_barang);
$total_barang = $data_barang['total'];

// Hitung jumlah pengguna
$query_pengguna = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
$data_pengguna = mysqli_fetch_assoc($query_pengguna);
$total_pengguna = $data_pengguna['total'];

// Hitung peminjaman
$query_peminjaman = mysqli_query($conn, "SELECT COUNT(*) AS total FROM peminjaman");
$data_peminjaman = mysqli_fetch_assoc($query_peminjaman);
$total_peminjaman = $data_peminjaman['total'];

// Hitung antrean
$query_antrean = mysqli_query($conn, "SELECT COUNT(*) AS total FROM peminjaman WHERE status = 'Menunggu'");
$data_antrean = mysqli_fetch_assoc($query_antrean);
$total_antrean = $data_antrean['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f6f9; color: #111827; }
        header { display: flex; justify-content: space-between; align-items: center; padding: 18px 30px; background: #111827; color: #fff; }
        header a { color: #fff; background: #dc2626; padding: 9px 15px; text-decoration: none; border-radius: 6px; }
        main { padding: 30px; }
        .cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin: 25px 0; }
        .card, .menu { padding: 24px; background: #fff; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,.08); }
        .card h2 { margin: 0 0 12px; font-size: 15px; color: #555; }
        .number { font-size: 32px; font-weight: bold; }
        .menu a { display: inline-block; margin: 6px; padding: 12px 16px; color: #fff; background: #111827; text-decoration: none; border-radius: 6px; }
        @media (max-width: 800px) { .cards { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 500px) { main { padding: 15px; } .cards { grid-template-columns: 1fr; } header { padding: 15px; } }
    </style>
</head>
<body>
    <header>
        <strong>SOUNDBEST - Admin</strong>
        <span>Halo, <?php echo htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8'); ?>
            <a href="../auth/logout.php">Logout</a>
        </span>
    </header>
    <main>
        <h1>Dashboard Admin</h1>
        <p>Selamat datang di sistem peminjaman barang Soundbest.</p>
        <section class="cards">
            <div class="card"><h2>Total Barang</h2><div class="number"><?php echo $total_barang; ?></div></div>
            <div class="card"><h2>Total Pengguna</h2><div class="number"><?php echo $total_pengguna; ?></div></div>
            <div class="card"><h2>Total Peminjaman</h2><div class="number"><?php echo $total_peminjaman; ?></div></div>
            <div class="card"><h2>Antrean Menunggu</h2><div class="number"><?php echo $total_antrean; ?></div></div>
        </section>
        <section class="menu">
            <h2>Menu Admin</h2>
            <a href="barang.php">Data Barang</a>
            <a href="pengguna.php">Data Pengguna</a>
            <a href="peminjaman.php">Peminjaman</a>
            <a href="antrean.php">Antrean</a>
            <a href="pengembalian.php">Pengembalian</a>
            <a href="laporan.php">Laporan</a>
        </section>
    </main>
</body>
</html>
