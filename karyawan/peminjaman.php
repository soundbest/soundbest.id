<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'karyawan') {
    header("Location: ../index.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

if (isset($_POST['submit'])) {

    $barang_id = (int) $_POST['id_barang'];
    $jumlah = (int) $_POST['jumlah'];
    $tanggal_pinjam = mysqli_real_escape_string($conn, $_POST['tanggal_pinjam']);
    $tanggal_kembali = mysqli_real_escape_string($conn, $_POST['tanggal_kembali']);
    $keperluan = mysqli_real_escape_string($conn, $_POST['keperluan']);

    // Ambil data barang
    $cek = mysqli_query($conn, "
        SELECT * FROM barang 
        WHERE id = $barang_id
    ");

    $barang = mysqli_fetch_assoc($cek);

    if (!$barang) {

        echo "<script>
                alert('Barang tidak ditemukan!');
                window.location='peminjaman.php';
              </script>";
        exit;

    }

    // Cek stok
    if ($jumlah > $barang['jumlah']) {

        echo "<script>
                alert('Jumlah barang melebihi stok tersedia!');
                window.location='peminjaman.php';
              </script>";
        exit;

    }

    // Simpan peminjaman
    $insert = mysqli_query($conn, "
        INSERT INTO peminjaman
        (
            user_id,
            barang_id,
            jumlah,
            tanggal_pinjam,
            tanggal_kembali,
            keperluan,
            status
        )
        VALUES
        (
            $user_id,
            $barang_id,
            '$jumlah',
            '$tanggal_pinjam',
            '$tanggal_kembali',
            '$keperluan',
            'Menunggu'
        )
    ");

    if ($insert) {

        echo "<script>
                alert('Peminjaman berhasil diajukan!');
                window.location='peminjaman.php';
              </script>";

    } else {

        echo "<script>
                alert('Peminjaman gagal disimpan!');
              </script>";

    }
}

// Ambil daftar barang
$barang_query = mysqli_query($conn, "
    SELECT * FROM barang
    WHERE jumlah > 0
    ORDER BY nama_barang ASC
");

// Riwayat peminjaman
$riwayat = mysqli_query($conn, "
    SELECT 
        p.*,
        b.nama_barang
    FROM peminjaman p
    LEFT JOIN barang b 
        ON p.barang_id = b.id
    WHERE p.user_id = $user_id
    ORDER BY p.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Barang</title>

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
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            height: 80px;
            resize: none;
        }

        button {
            margin-top: 20px;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            background: #2563eb;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
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

        .status {
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
    <a href="../auth/logout.php">Logout</a>

</div>

<div class="content">

    <div class="box">

        <h2>Form Peminjaman Barang</h2>

        <form method="POST">

            <label>Nama Barang</label>

            <select name="id_barang" required>

                <option value="">-- Pilih Barang --</option>

                <?php while ($data = mysqli_fetch_assoc($barang_query)) { ?>

                    <option value="<?php echo $data['id']; ?>">

                        <?php
                        echo htmlspecialchars($data['nama_barang']);
                        echo " - Stok: " . $data['jumlah'];
                        ?>

                    </option>

                <?php } ?>

            </select>


            <label>Jumlah</label>

            <input
                type="number"
                name="jumlah"
                min="1"
                required
            >


            <label>Tanggal Peminjaman</label>

            <input
                type="date"
                name="tanggal_pinjam"
                required
            >


            <label>Tanggal Pengembalian</label>

            <input
                type="date"
                name="tanggal_kembali"
                required
            >


            <label>Keperluan</label>

            <textarea
                name="keperluan"
                placeholder="Masukkan keperluan peminjaman..."
                required
            ></textarea>


            <button type="submit" name="submit">
                Ajukan Peminjaman
            </button>

        </form>

    </div>


    <div class="box">

        <h2>Riwayat Peminjaman Saya</h2>

        <table>

            <tr>
                <th>No</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Keperluan</th>
                <th>Status</th>
            </tr>

            <?php

            $no = 1;

            while ($data = mysqli_fetch_assoc($riwayat)) {

            ?>

            <tr>

                <td><?php echo $no++; ?></td>

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

                <td class="status">
                    <?php echo htmlspecialchars($data['status']); ?>
                </td>

            </tr>

            <?php } ?>

        </table>

    </div>

</div>

</body>
</html>