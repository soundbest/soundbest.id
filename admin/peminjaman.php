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
// TAMBAH PEMINJAMAN
// ==========================
if (isset($_POST['tambah'])) {

    $user_id = (int) $_POST['user_id'];
    $barang_id = (int) $_POST['barang_id'];
    $jumlah = (int) $_POST['jumlah'];

    $tanggal_pinjam = mysqli_real_escape_string(
        $conn,
        $_POST['tanggal_pinjam']
    );

    $tanggal_kembali = mysqli_real_escape_string(
        $conn,
        $_POST['tanggal_kembali']
    );

    // Cek jumlah
    if ($jumlah <= 0) {

        $pesan = "Jumlah peminjaman harus lebih dari 0.";

    } else {

        // Ambil jumlah barang
        $cek_barang = mysqli_query(
            $conn,
            "SELECT * FROM barang WHERE id=$barang_id"
        );

        if (mysqli_num_rows($cek_barang) == 0) {

            $pesan = "Barang tidak ditemukan.";

        } else {

            $barang = mysqli_fetch_assoc($cek_barang);

            if ($jumlah > $barang['jumlah']) {

                $pesan = "Jumlah barang yang dipinjam melebihi stok.";

            } else {

                // Masukkan peminjaman
                $query = mysqli_query(
                    $conn,
                    "INSERT INTO peminjaman
                    (
                        user_id,
                        barang_id,
                        jumlah,
                        tanggal_pinjam,
                        tanggal_kembali,
                        status
                    )
                    VALUES
                    (
                        $user_id,
                        $barang_id,
                        $jumlah,
                        '$tanggal_pinjam',
                        '$tanggal_kembali',
                        'Menunggu'
                    )"
                );

                if ($query) {

                    header("Location: peminjaman.php");
                    exit;

                } else {

                    $pesan = "Gagal menyimpan data peminjaman.";

                }
            }
        }
    }
}


// ==========================
// HAPUS PEMINJAMAN
// ==========================
if (isset($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];

    mysqli_query(
        $conn,
        "DELETE FROM peminjaman WHERE id=$id"
    );

    header("Location: peminjaman.php");
    exit;
}


// ==========================
// DATA USER
// ==========================
$data_user = mysqli_query(
    $conn,
    "SELECT id, nama, username
     FROM users
     WHERE role != 'admin'
     ORDER BY nama ASC"
);


// ==========================
// DATA BARANG
// ==========================
$data_barang = mysqli_query(
    $conn,
    "SELECT id, kode_barang, nama_barang, jumlah
     FROM barang
     WHERE jumlah > 0
     ORDER BY nama_barang ASC"
);


// ==========================
// DATA PEMINJAMAN
// ==========================
$data_peminjaman = mysqli_query(
    $conn,
    "SELECT
        p.id,
        u.nama AS nama_user,
        b.kode_barang,
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
     ORDER BY p.id DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Peminjaman Barang - Soundbest</title>

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

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 11px;
            margin-bottom: 15px;
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

        button:hover {
            background: #374151;
        }

        .pesan {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 5px;
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

        tr:nth-child(even) {
            background: #f9fafb;
        }

        .hapus {
            background: #dc2626;
            color: white;
            padding: 7px 11px;
            text-decoration: none;
            border-radius: 5px;
        }

        .status {
            padding: 5px 9px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: bold;
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
            background: #dcfce7;
            color: #166534;
        }

        .dikembalikan {
            background: #e5e7eb;
            color: #374151;
        }

        @media (max-width: 800px) {

            .container {
                padding: 15px;
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
        Peminjaman Barang Soundbest
    </h2>

</div>


<div class="container">


    <a href="dashboard.php" class="kembali">
        ← Kembali ke Dashboard
    </a>


    <?php if ($pesan != "") { ?>

        <div class="pesan">
            <?= htmlspecialchars($pesan); ?>
        </div>

    <?php } ?>


    <!-- FORM PEMINJAMAN -->

    <div class="box">

        <h3>
            Tambah Peminjaman
        </h3>

        <br>


        <form method="POST">


            <label>
                Pengguna
            </label>

            <select name="user_id" required>

                <option value="">
                    -- Pilih Pengguna --
                </option>

                <?php while ($user = mysqli_fetch_assoc($data_user)) { ?>

                    <option value="<?= $user['id']; ?>">

                        <?= htmlspecialchars($user['nama']); ?>

                        -
                        <?= htmlspecialchars($user['username']); ?>

                    </option>

                <?php } ?>

            </select>


            <label>
                Barang
            </label>

            <select name="barang_id" required>

                <option value="">
                    -- Pilih Barang --
                </option>

                <?php while ($barang = mysqli_fetch_assoc($data_barang)) { ?>

                    <option value="<?= $barang['id']; ?>">

                        <?= htmlspecialchars($barang['kode_barang']); ?>

                        -

                        <?= htmlspecialchars($barang['nama_barang']); ?>

                        (Stok:
                        <?= $barang['jumlah']; ?>
                        )

                    </option>

                <?php } ?>

            </select>


            <label>
                Jumlah
            </label>

            <input
                type="number"
                name="jumlah"
                min="1"
                required
            >


            <label>
                Tanggal Pinjam
            </label>

            <input
                type="date"
                name="tanggal_pinjam"
                required
            >


            <label>
                Rencana Tanggal Kembali
            </label>

            <input
                type="date"
                name="tanggal_kembali"
                required
            >


            <button
                type="submit"
                name="tambah"
            >
                + Simpan Peminjaman
            </button>


        </form>

    </div>


    <!-- DAFTAR PEMINJAMAN -->

    <div class="box">

        <h3>
            Daftar Peminjaman
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

                <th>Rencana Kembali</th>

                <th>Status</th>

                <th>Aksi</th>

            </tr>


            <?php

            $no = 1;

            while ($pinjam = mysqli_fetch_assoc($data_peminjaman)) {

                $status_class = strtolower(
                    $pinjam['status']
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
                        $pinjam['nama_user']
                    ); ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $pinjam['kode_barang']
                    ); ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $pinjam['nama_barang']
                    ); ?>
                </td>

                <td>
                    <?= $pinjam['jumlah']; ?>
                </td>

                <td>
                    <?= $pinjam['tanggal_pinjam']; ?>
                </td>

                <td>
                    <?= $pinjam['tanggal_kembali']; ?>
                </td>

                <td>

                    <span class="status <?= $status_class; ?>">

                        <?= htmlspecialchars(
                            $pinjam['status']
                        ); ?>

                    </span>

                </td>

                <td>

                    <a
                        href="peminjaman.php?hapus=<?= $pinjam['id']; ?>"
                        class="hapus"
                        onclick="return confirm(
                            'Yakin ingin menghapus data peminjaman ini?'
                        )"
                    >
                        Hapus
                    </a>

                </td>

            </tr>

            <?php } ?>


        </table>

    </div>


</div>

</body>

</html>