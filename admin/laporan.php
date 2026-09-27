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


// ==========================
// FILTER TANGGAL
// ==========================
$tanggal_awal = isset($_GET['tanggal_awal'])
    ? $_GET['tanggal_awal']
    : '';

$tanggal_akhir = isset($_GET['tanggal_akhir'])
    ? $_GET['tanggal_akhir']
    : '';


// ==========================
// KONDISI FILTER
// ==========================
$where = "";

if ($tanggal_awal != "" && $tanggal_akhir != "") {

    $tanggal_awal = mysqli_real_escape_string(
        $conn,
        $tanggal_awal
    );

    $tanggal_akhir = mysqli_real_escape_string(
        $conn,
        $tanggal_akhir
    );

    $where = "
        WHERE p.tanggal_pinjam
        BETWEEN '$tanggal_awal'
        AND '$tanggal_akhir'
    ";
}


// ==========================
// TOTAL DATA
// ==========================
$query_total = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM peminjaman p
     $where"
);

$total = mysqli_fetch_assoc($query_total)['total'];


// ==========================
// TOTAL MENUNGGU
// ==========================
$query_menunggu = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM peminjaman p
     $where
     " . ($where != ""
        ? "AND p.status='Menunggu'"
        : "WHERE p.status='Menunggu'")
);

$total_menunggu =
    mysqli_fetch_assoc($query_menunggu)['total'];


// ==========================
// TOTAL DIPROSES
// ==========================
$query_diproses = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM peminjaman p
     $where
     " . ($where != ""
        ? "AND p.status='Diproses'"
        : "WHERE p.status='Diproses'")
);

$total_diproses =
    mysqli_fetch_assoc($query_diproses)['total'];


// ==========================
// TOTAL DIPINJAM
// ==========================
$query_dipinjam = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM peminjaman p
     $where
     " . ($where != ""
        ? "AND p.status='Dipinjam'"
        : "WHERE p.status='Dipinjam'")
);

$total_dipinjam =
    mysqli_fetch_assoc($query_dipinjam)['total'];


// ==========================
// TOTAL DIKEMBALIKAN
// ==========================
$query_kembali = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM peminjaman p
     $where
     " . ($where != ""
        ? "AND p.status='Dikembalikan'"
        : "WHERE p.status='Dikembalikan'")
);

$total_kembali =
    mysqli_fetch_assoc($query_kembali)['total'];


// ==========================
// DATA LAPORAN
// ==========================
$data_laporan = mysqli_query(
    $conn,
    "SELECT
        p.id,
        u.nama AS nama_user,
        b.kode_barang,
        b.nama_barang,
        p.jumlah,
        p.tanggal_pinjam,
        p.tanggal_kembali,
        p.status,
        p.created_at
     FROM peminjaman p
     JOIN users u
        ON p.user_id = u.id
     JOIN barang b
        ON p.barang_id = b.id
     $where
     ORDER BY p.created_at DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Laporan Peminjaman - Soundbest</title>

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

        .filter {
            display: grid;
            grid-template-columns: 1fr 1fr auto auto;
            gap: 15px;
            align-items: end;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background: #111827;
            color: white;
            border: none;
            padding: 11px 18px;
            border-radius: 5px;
            cursor: pointer;
        }

        .reset {
            background: #6b7280;
            color: white;
            padding: 11px 18px;
            text-decoration: none;
            border-radius: 5px;
        }

        .statistik {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
        }

        .card {
            padding: 20px;
            border-radius: 8px;
            background: #f9fafb;
            border: 1px solid #ddd;
        }

        .card h4 {
            margin-bottom: 10px;
            color: #555;
        }

        .angka {
            font-size: 28px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 11px;
            text-align: left;
        }

        th {
            background: #111827;
            color: white;
        }

        tr:nth-child(even) {
            background: #f9fafb;
        }

        .status {
            padding: 5px 8px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 12px;
        }

        .menunggu {
            background: #fef3c7;
            color: #92400e;
        }

        .diproses {
            background: #dbeafe;
            color: #1e40af;
        }

        .dipinjam {
            background: #fed7aa;
            color: #9a3412;
        }

        .dikembalikan {
            background: #dcfce7;
            color: #166534;
        }

        @media (max-width: 900px) {

            .statistik {
                grid-template-columns: repeat(2, 1fr);
            }

            .filter {
                grid-template-columns: 1fr;
            }

            .box {
                overflow-x: auto;
            }

            table {
                min-width: 1000px;
            }
        }

    </style>

</head>

<body>


<div class="header">

    <h2>
        Laporan Peminjaman Soundbest
    </h2>

</div>


<div class="container">


    <a href="dashboard.php" class="kembali">
        ← Kembali ke Dashboard
    </a>


    <!-- FILTER -->

    <div class="box">

        <h3>
            Filter Laporan
        </h3>

        <br>

        <form method="GET">

            <div class="filter">

                <div>

                    <label>
                        Tanggal Awal
                    </label>

                    <input
                        type="date"
                        name="tanggal_awal"
                        value="<?= htmlspecialchars(
                            $tanggal_awal
                        ); ?>"
                    >

                </div>


                <div>

                    <label>
                        Tanggal Akhir
                    </label>

                    <input
                        type="date"
                        name="tanggal_akhir"
                        value="<?= htmlspecialchars(
                            $tanggal_akhir
                        ); ?>"
                    >

                </div>


                <button type="submit">
                    Tampilkan
                </button>


                <a
                    href="laporan.php"
                    class="reset"
                >
                    Reset
                </a>

            </div>

        </form>

    </div>


    <!-- STATISTIK -->

    <div class="box">

        <h3>
            Ringkasan Peminjaman
        </h3>

        <br>


        <div class="statistik">


            <div class="card">

                <h4>
                    Total
                </h4>

                <div class="angka">
                    <?= $total; ?>
                </div>

            </div>


            <div class="card">

                <h4>
                    Menunggu
                </h4>

                <div class="angka">
                    <?= $total_menunggu; ?>
                </div>

            </div>


            <div class="card">

                <h4>
                    Diproses
                </h4>

                <div class="angka">
                    <?= $total_diproses; ?>
                </div>

            </div>


            <div class="card">

                <h4>
                    Dipinjam
                </h4>

                <div class="angka">
                    <?= $total_dipinjam; ?>
                </div>

            </div>


            <div class="card">

                <h4>
                    Dikembalikan
                </h4>

                <div class="angka">
                    <?= $total_kembali; ?>
                </div>

            </div>


        </div>

    </div>


    <!-- DETAIL LAPORAN -->

    <div class="box">

        <h3>
            Detail Laporan
        </h3>

        <br>


        <table>

            <tr>

                <th>No</th>

                <th>Pengguna</th>

                <th>Kode Barang</th>

                <th>Nama Barang</th>

                <th>Jumlah</th>

                <th>Tanggal Pinjam</th>

                <th>Tanggal Kembali</th>

                <th>Status</th>

                <th>Waktu Pengajuan</th>

            </tr>


            <?php

            $no = 1;

            while ($laporan = mysqli_fetch_assoc($data_laporan)) {

                $status_class = strtolower(
                    $laporan['status']
                );

                $status_class = str_replace(
                    ' ',
                    '',
                    $status_class
                );

            ?>

            <tr>

                <td>
                    <?= $no++; ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $laporan['nama_user']
                    ); ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $laporan['kode_barang']
                    ); ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $laporan['nama_barang']
                    ); ?>
                </td>

                <td>
                    <?= $laporan['jumlah']; ?>
                </td>

                <td>
                    <?= $laporan['tanggal_pinjam']; ?>
                </td>

                <td>
                    <?= $laporan['tanggal_kembali']; ?>
                </td>

                <td>

                    <span class="status <?= $status_class; ?>">

                        <?= htmlspecialchars(
                            $laporan['status']
                        ); ?>

                    </span>

                </td>

                <td>
                    <?= $laporan['created_at']; ?>
                </td>

            </tr>

            <?php } ?>


        </table>

    </div>


</div>

</body>

</html>
```
