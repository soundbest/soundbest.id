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
// PROSES ANTREAN FCFS
// ==========================
if (isset($_GET['proses'])) {

    $id = (int) $_GET['proses'];

    // Ambil data peminjaman
    $cek = mysqli_query(
        $conn,
        "SELECT * FROM peminjaman WHERE id=$id"
    );

    if (mysqli_num_rows($cek) == 1) {

        $data = mysqli_fetch_assoc($cek);

        if ($data['status'] == 'Menunggu') {

            // Cek apakah masih ada antrean
            // yang datang lebih dahulu
            $cek_antrian = mysqli_query(
                $conn,
                "SELECT id
                 FROM peminjaman
                 WHERE status='Menunggu'
                 AND created_at < '{$data['created_at']}'
                 ORDER BY created_at ASC
                 LIMIT 1"
            );

            if (mysqli_num_rows($cek_antrian) > 0) {

                $pesan =
                    "Peminjaman ini belum dapat diproses karena masih ada antrean yang lebih dahulu.";

            } else {

                mysqli_query(
                    $conn,
                    "UPDATE peminjaman
                     SET status='Diproses'
                     WHERE id=$id"
                );

                header("Location: antrean.php");
                exit;
            }
        }
    }
}


// ==========================
// DATA ANTREAN FCFS
// ==========================
$data_antrean = mysqli_query(
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
     WHERE p.status='Menunggu'
     ORDER BY p.created_at ASC"
);


// ==========================
// DATA YANG SEDANG DIPROSES
// ==========================
$data_proses = mysqli_query(
    $conn,
    "SELECT
        p.id,
        u.nama AS nama_user,
        b.nama_barang,
        p.jumlah,
        p.status,
        p.created_at
     FROM peminjaman p
     JOIN users u
        ON p.user_id = u.id
     JOIN barang b
        ON p.barang_id = b.id
     WHERE p.status='Diproses'
     ORDER BY p.created_at ASC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Antrean FCFS - Soundbest</title>

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

        .info {
            background: #dbeafe;
            color: #1e3a8a;
            padding: 15px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .pesan {
            background: #fee2e2;
            color: #991b1b;
            padding: 15px;
            border-radius: 7px;
            margin-bottom: 20px;
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

        tr:first-child td {
            background: #fff7ed;
        }

        .nomor {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
        }

        .proses {
            background: #2563eb;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
        }

        .status {
            background: #fef3c7;
            color: #92400e;
            padding: 5px 9px;
            border-radius: 5px;
            font-weight: bold;
        }

        .status-proses {
            background: #dbeafe;
            color: #1e40af;
            padding: 5px 9px;
            border-radius: 5px;
            font-weight: bold;
        }

        @media (max-width: 800px) {

            .container {
                padding: 15px;
            }

            .box {
                overflow-x: auto;
            }

            table {
                min-width: 950px;
            }
        }

    </style>

</head>

<body>


<div class="header">

    <h2>
        Antrean Peminjaman - Algoritma FCFS
    </h2>

</div>


<div class="container">


    <a href="dashboard.php" class="kembali">
        ← Kembali ke Dashboard
    </a>


    <div class="info">

        <strong>Algoritma FCFS:</strong>

        Peminjaman yang masuk lebih dahulu
        akan diproses terlebih dahulu berdasarkan
        waktu pengajuan (`created_at`).

    </div>


    <?php if ($pesan != "") { ?>

        <div class="pesan">
            <?= htmlspecialchars($pesan); ?>
        </div>

    <?php } ?>


    <!-- ANTREAN MENUNGGU -->

    <div class="box">

        <h3>
            Antrean Menunggu
        </h3>

        <br>


        <table>

            <tr>

                <th>
                    No. Antrean
                </th>

                <th>
                    Pengguna
                </th>

                <th>
                    Barang
                </th>

                <th>
                    Jumlah
                </th>

                <th>
                    Tanggal Pinjam
                </th>

                <th>
                    Waktu Pengajuan
                </th>

                <th>
                    Status
                </th>

                <th>
                    Aksi
                </th>

            </tr>


            <?php

            $no = 1;

            while ($antrian = mysqli_fetch_assoc($data_antrean)) {

            ?>

            <tr>

                <td class="nomor">

                    <?= $no; ?>

                    <?php if ($no == 1) { ?>

                        <br>
                        <small>Prioritas FCFS</small>

                    <?php } ?>

                </td>


                <td>
                    <?= htmlspecialchars(
                        $antrian['nama_user']
                    ); ?>
                </td>


                <td>

                    <?= htmlspecialchars(
                        $antrian['kode_barang']
                    ); ?>

                    -

                    <?= htmlspecialchars(
                        $antrian['nama_barang']
                    ); ?>

                </td>


                <td>
                    <?= $antrian['jumlah']; ?>
                </td>


                <td>
                    <?= $antrian['tanggal_pinjam']; ?>
                </td>


                <td>
                    <?= $antrian['created_at']; ?>
                </td>


                <td>

                    <span class="status">
                        Menunggu
                    </span>

                </td>


                <td>

                    <?php if ($no == 1) { ?>

                        <a
                            href="antrean.php?proses=<?= $antrian['id']; ?>"
                            class="proses"
                            onclick="return confirm(
                                'Proses antrean pertama sesuai FCFS?'
                            )"
                        >
                            Proses
                        </a>

                    <?php } else { ?>

                        Menunggu giliran

                    <?php } ?>

                </td>

            </tr>

            <?php

                $no++;

            }

            ?>


        </table>

    </div>


    <!-- SEDANG DIPROSES -->

    <div class="box">

        <h3>
            Sedang Diproses
        </h3>

        <br>


        <table>

            <tr>

                <th>No</th>

                <th>Pengguna</th>

                <th>Barang</th>

                <th>Jumlah</th>

                <th>Waktu Pengajuan</th>

                <th>Status</th>

            </tr>


            <?php

            $no_proses = 1;

            while ($proses = mysqli_fetch_assoc($data_proses)) {

            ?>

            <tr>

                <td>
                    <?= $no_proses++; ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $proses['nama_user']
                    ); ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $proses['nama_barang']
                    ); ?>
                </td>

                <td>
                    <?= $proses['jumlah']; ?>
                </td>

                <td>
                    <?= $proses['created_at']; ?>
                </td>

                <td>

                    <span class="status-proses">
                        Diproses
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
