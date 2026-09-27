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
| Ambil riwayat peminjaman karyawan
|--------------------------------------------------------------------------
*/

$query = mysqli_query($conn, "
    SELECT
        p.*,
        b.nama_barang
    FROM peminjaman p
    LEFT JOIN barang b
        ON p.barang_id = b.id
    WHERE p.user_id = " . (int) $_SESSION['user_id'] . "
    ORDER BY p.id DESC
");

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Riwayat Peminjaman</title>

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

        .menunggu {
            color: #d97706;
            font-weight: bold;
        }

        .disetujui {
            color: #16a34a;
            font-weight: bold;
        }

        .ditolak {
            color: #dc2626;
            font-weight: bold;
        }

        .dikembalikan {
            color: #2563eb;
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

        <h2>Riwayat Peminjaman</h2>

        <p>
            Riwayat peminjaman untuk akun:
            <b><?php echo htmlspecialchars($username); ?></b>
        </p>


        <table>

            <tr>

                <th>No</th>

                <th>ID Peminjaman</th>

                <th>Barang</th>

                <th>Jumlah</th>

                <th>Tanggal Pinjam</th>

                <th>Tanggal Kembali</th>

                <th>Keperluan</th>

                <th>Status</th>

            </tr>


            <?php

            $no = 1;

            if (mysqli_num_rows($query) > 0) {

                while ($data = mysqli_fetch_assoc($query)) {

                    $status = strtolower($data['status']);

                    $class = "";

                    if ($status == "menunggu") {
                        $class = "menunggu";
                    } elseif ($status == "disetujui") {
                        $class = "disetujui";
                    } elseif ($status == "ditolak") {
                        $class = "ditolak";
                    } elseif ($status == "dikembalikan") {
                        $class = "dikembalikan";
                    }

            ?>

            <tr>

                <td>
                    <?php echo $no++; ?>
                </td>

                <td>
                    <?php echo $data['id']; ?>
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

                <td>
                    <?php echo $data['tanggal_kembali']; ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($data['keperluan']); ?>
                </td>

                <td class="<?php echo $class; ?>">
                    <?php echo htmlspecialchars($data['status']); ?>
                </td>

            </tr>

            <?php

                }

            } else {

            ?>

            <tr>

                <td colspan="8" style="text-align:center;">

                    Belum ada riwayat peminjaman.

                </td>

            </tr>

            <?php } ?>

        </table>

    </div>

</div>


</body>

</html>