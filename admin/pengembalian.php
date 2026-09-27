```php
<?php
session_start();
require_once "../config/database.php";

// ==========================
// CEK LOGIN
// ==========================
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit;
}

// ==========================
// CEK ROLE
// ==========================
if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$pesan = "";


// ==========================
// UBAH STATUS MENJADI DIPINJAM
// ==========================
if (isset($_GET['pinjam'])) {

    $id = (int) $_GET['pinjam'];

    $cek = mysqli_query(
        $conn,
        "SELECT * FROM peminjaman WHERE id=$id"
    );

    if (mysqli_num_rows($cek) == 1) {

        $data = mysqli_fetch_assoc($cek);

        if ($data['status'] == 'Diproses') {

            mysqli_query(
                $conn,
                "UPDATE peminjaman
                 SET status='Dipinjam'
                 WHERE id=$id"
            );
        }
    }

    header("Location: pengembalian.php");
    exit;
}


// ==========================
// PROSES PENGEMBALIAN
// ==========================
if (isset($_GET['kembali'])) {

    $id = (int) $_GET['kembali'];

    $cek = mysqli_query(
        $conn,
        "SELECT * FROM peminjaman WHERE id=$id"
    );

    if (mysqli_num_rows($cek) == 1) {

        $data = mysqli_fetch_assoc($cek);

        if ($data['status'] == 'Dipinjam') {

            // Kembalikan stok barang
            mysqli_query(
                $conn,
                "UPDATE barang
                 SET jumlah = jumlah + {$data['jumlah']}
                 WHERE id = {$data['barang_id']}"
            );

            // Ubah status
            mysqli_query(
                $conn,
                "UPDATE peminjaman
                 SET status='Dikembalikan'
                 WHERE id=$id"
            );
        }
    }

    header("Location: pengembalian.php");
    exit;
}


// ==========================
// DATA PEMINJAMAN DIPROSES
// ==========================
$data_proses = mysqli_query(
    $conn,
    "SELECT
        p.id,
        u.nama AS nama_user,
        b.nama_barang,
        p.jumlah,
        p.tanggal_pinjam,
        p.tanggal_kembali,
        p.status
     FROM peminjaman p
     JOIN users u
        ON p.user_id = u.id
     JOIN barang b
        ON p.barang_id = b.id
     WHERE p.status='Diproses'
     ORDER BY p.created_at ASC"
);


// ==========================
// DATA BARANG YANG DIPINJAM
// ==========================
$data_dipinjam = mysqli_query(
    $conn,
    "SELECT
        p.id,
        u.nama AS nama_user,
        b.nama_barang,
        p.jumlah,
        p.tanggal_pinjam,
        p.tanggal_kembali,
        p.status
     FROM peminjaman p
     JOIN users u
        ON p.user_id = u.id
     JOIN barang b
        ON p.barang_id = b.id
     WHERE p.status='Dipinjam'
     ORDER BY p.tanggal_kembali ASC"
);


// ==========================
// DATA YANG SUDAH KEMBALI
// ==========================
$data_kembali = mysqli_query(
    $conn,
    "SELECT
        p.id,
        u.nama AS nama_user,
        b.nama_barang,
        p.jumlah,
        p.tanggal_pinjam,
        p.tanggal_kembali,
        p.status
     FROM peminjaman p
     JOIN users u
        ON p.user_id = u.id
     JOIN barang b
        ON p.barang_id = b.id
     WHERE p.status='Dikembalikan'
     ORDER BY p.id DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Pengembalian Barang - Soundbest</title>

    <style>

        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            background: #f4f6f9;
        }

        .header {
            background: #111827;
            color: white;
            padding: 20px 30px;
        }

        .container {
            padding: 30px;
        }

        .kembali {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #111827;
            font-weight: bold;
        }

        .box {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background: #111827;
            color: white;
        }

        tr:nth-child(even) {
            background: #f9fafb;
        }

        .pinjam {
            background: #2563eb;
            color: white;
            padding: 7px 11px;
            text-decoration: none;
            border-radius: 5px;
        }

        .kembali-btn {
            background: #16a34a;
            color: white;
            padding: 7px 11px;
            text-decoration: none;
            border-radius: 5px;
        }

        .status {
            padding: 5px 9px;
            border-radius: 5px;
            font-weight: bold;
        }

        .diproses {
            background: #dbeafe;
            color: #1e40af;
        }

        .dipinjam {
            background: #fef3c7;
            color: #92400e;
        }

        .dikembalikan {
            background: #dcfce7;
            color: #166534;
        }

        @media (max-width: 800px) {

            .container {
                padding: 15px;
            }

            .box {
                overflow-x: auto;
            }

            table {
                min-width: 900px;
            }
        }

    </style>

</head>

<body>


<div class="header">

    <h2>
        Pengembalian Barang Soundbest
    </h2>

</div>


<div class="container">


    <a href="dashboard.php" class="kembali">
        ← Kembali ke Dashboard
    </a>


    <!-- DIPROSES -->

    <div class="box">

        <h3>
            Peminjaman Siap Diserahkan
        </h3>

        <br>


        <table>

            <tr>

                <th>No</th>
                <th>Pengguna</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Tanggal Pinjam</th>
                <th>Status</th>
                <th>Aksi</th>

            </tr>


            <?php

            $no = 1;

            while ($data = mysqli_fetch_assoc($data_proses)) {

            ?>

            <tr>

                <td>
                    <?= $no++; ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $data['nama_user']
                    ); ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $data['nama_barang']
                    ); ?>
                </td>

                <td>
                    <?= $data['jumlah']; ?>
                </td>

                <td>
                    <?= $data['tanggal_pinjam']; ?>
                </td>

                <td>

                    <span class="status diproses">
                        Diproses
                    </span>

                </td>

                <td>

                    <a
                        href="pengembalian.php?pinjam=<?= $data['id']; ?>"
                        class="pinjam"
                        onclick="return confirm(
                            'Konfirmasi barang diserahkan kepada pengguna?'
                        )"
                    >
                        Serahkan Barang
                    </a>

                </td>

            </tr>

            <?php } ?>


        </table>

    </div>


    <!-- DIPINJAM -->

    <div class="box">

        <h3>
            Barang Sedang Dipinjam
        </h3>

        <br>


        <table>

            <tr>

                <th>No</th>
                <th>Pengguna</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Tanggal Pinjam</th>
                <th>Rencana Kembali</th>
                <th>Status</th>
                <th>Aksi</th>

            </tr>


            <?php

            $no = 1;

            while ($data = mysqli_fetch_assoc($data_dipinjam)) {

            ?>

            <tr>

                <td>
                    <?= $no++; ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $data['nama_user']
                    ); ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $data['nama_barang']
                    ); ?>
                </td>

                <td>
                    <?= $data['jumlah']; ?>
                </td>

                <td>
                    <?= $data['tanggal_pinjam']; ?>
                </td>

                <td>
                    <?= $data['tanggal_kembali']; ?>
                </td>

                <td>

                    <span class="status dipinjam">
                        Dipinjam
                    </span>

                </td>

                <td>

                    <a
                        href="pengembalian.php?kembali=<?= $data['id']; ?>"
                        class="kembali-btn"
                        onclick="return confirm(
                            'Konfirmasi barang sudah dikembalikan?'
                        )"
                    >
                        Kembalikan
                    </a>

                </td>

            </tr>

            <?php } ?>


        </table>

    </div>


    <!-- SUDAH DIKEMBALIKAN -->

    <div class="box">

        <h3>
            Riwayat Pengembalian
        </h3>

        <br>


        <table>

            <tr>

                <th>No</th>
                <th>Pengguna</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Tanggal Pinjam</th>
                <th>Status</th>

            </tr>


            <?php

            $no = 1;

            while ($data = mysqli_fetch_assoc($data_kembali)) {

            ?>

            <tr>

                <td>
                    <?= $no++; ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $data['nama_user']
                    ); ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $data['nama_barang']
                    ); ?>
                </td>

                <td>
                    <?= $data['jumlah']; ?>
                </td>

                <td>
                    <?= $data['tanggal_pinjam']; ?>
                </td>

                <td>

                    <span class="status dikembalikan">
                        Dikembalikan
                    </span>

                </td>

            </tr>

            <?php } ?>


        </table>

    </div>


</div>

</body>

</html>
```
