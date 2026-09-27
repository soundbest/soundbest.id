<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'karyawan') {
    header("Location: ../index.php");
    exit;
}

$username = $_SESSION['username'];

/*
|--------------------------------------------------------------------------
| Ambil semua antrean peminjaman
|--------------------------------------------------------------------------
| Hanya peminjaman dengan status Menunggu yang masuk ke antrean.
| Urutan berdasarkan ID peminjaman dari yang paling kecil.
*/

$query = mysqli_query($conn, "
    SELECT 
        p.*,
        b.nama_barang,
        u.nama AS nama_user
    FROM peminjaman p
    LEFT JOIN barang b 
        ON p.barang_id = b.id
    LEFT JOIN users u
        ON p.user_id = u.id
    WHERE p.status = 'Menunggu'
    ORDER BY p.id ASC
");

$posisi = 1;
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrean Peminjaman</title>

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

        .box {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .info {
            background: #eff6ff;
            padding: 15px;
            border-left: 5px solid #2563eb;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #1e293b;
            color: white;
        }

        .posisi {
            font-weight: bold;
            font-size: 18px;
            text-align: center;
        }

        .saya {
            background: #eff6ff;
        }

        .status {
            color: #d97706;
            font-weight: bold;
        }

        @media (max-width: 700px) {
            .sidebar { position: static; width: auto; height: auto; padding: 15px; }
            .sidebar a { display: inline-block; padding: 8px; }
            .content { margin-left: 0; padding: 15px; }
            .box { overflow-x: auto; }
            table { min-width: 800px; }
        }

    </style>

</head>

<body>

<div class="sidebar">

    <h2>Soundbest</h2>

    <a href="dashboard.php">Dashboard</a>

    <a href="barang.php">Data Barang</a>

    <a href="peminjaman.php">Peminjaman</a>

    <a href="antrean.php">Antrean</a>

    <a href="riwayat.php">Riwayat</a>

    <a href="../auth/logout.php">Logout</a>

</div>


<div class="content">

    <div class="box">

        <h2>Antrean Peminjaman</h2>

        <div class="info">

            Sistem menggunakan metode
            <b>First Come First Served (FCFS)</b>.

            Peminjaman yang diajukan lebih dahulu akan diproses lebih dahulu.

        </div>


        <table>

            <tr>

                <th>Posisi</th>

                <th>No. Peminjaman</th>

                <th>Peminjam</th>

                <th>Barang</th>

                <th>Jumlah</th>

                <th>Tanggal Pinjam</th>

                <th>Status</th>

            </tr>


            <?php

            if (mysqli_num_rows($query) > 0) {

                while ($data = mysqli_fetch_assoc($query)) {

                    $class = "";

                    if ((int) $data['user_id'] === (int) $_SESSION['user_id']) {
                        $class = "saya";
                    }

            ?>

            <tr class="<?php echo $class; ?>">

                <td class="posisi">
                    <?php echo $posisi; ?>
                </td>

                <td>
                    <?php echo $data['id']; ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($data['nama_user']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($data['nama_barang']); ?>
                </td>

                <td>
                    <?php echo $data['jumlah']; ?>
                </td>

                <td>
                    <?php echo $data['tanggal_pinjam']; ?>
                </td>

                <td class="status">
                    <?php echo htmlspecialchars($data['status']); ?>
                </td>

            </tr>

            <?php

                    $posisi++;

                }

            } else {

            ?>

            <tr>

                <td colspan="7" style="text-align:center;">
                    Tidak ada antrean peminjaman saat ini.
                </td>

            </tr>

            <?php } ?>

        </table>

    </div>

</div>

</body>

</html>