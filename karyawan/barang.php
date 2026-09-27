<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'karyawan') {
    header("Location: ../index.php");
    exit;
}

$query = mysqli_query($conn, "
    SELECT * FROM barang
    ORDER BY nama_barang ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang</title>

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
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #1e293b;
            color: white;
        }

        .tersedia {
            color: green;
            font-weight: bold;
        }

        .habis {
            color: red;
            font-weight: bold;
        }

        @media (max-width: 700px) {
            .sidebar { position: static; width: auto; height: auto; padding: 15px; }
            .sidebar a { display: inline-block; padding: 8px; }
            .content { margin-left: 0; padding: 15px; overflow-x: auto; }
            table { min-width: 600px; }
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

    <div class="box">

        <h2>Data Barang</h2>
        <p>Daftar barang yang dapat digunakan untuk peminjaman.</p>

        <table>

            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Status</th>
            </tr>

            <?php
            $no = 1;

            while ($data = mysqli_fetch_assoc($query)) {
            ?>

            <tr>

                <td><?php echo $no++; ?></td>

                <td>
                    <?php echo htmlspecialchars($data['nama_barang']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($data['kategori']); ?>
                </td>

                <td>
                    <?php echo $data['jumlah']; ?>
                </td>

                <td>

                    <?php if ($data['jumlah'] > 0) { ?>

                        <span class="tersedia">Tersedia</span>

                    <?php } else { ?>

                        <span class="habis">Habis</span>

                    <?php } ?>

                </td>

            </tr>

            <?php } ?>

        </table>

    </div>

</div>

</body>
</html>