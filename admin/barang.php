<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// TAMBAH BARANG
if (isset($_POST['tambah'])) {

    $kode_barang = mysqli_real_escape_string($conn, $_POST['kode_barang']);
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $jumlah = (int) $_POST['jumlah'];
    $kondisi = mysqli_real_escape_string($conn, $_POST['kondisi']);

    mysqli_query($conn, "
        INSERT INTO barang
        (kode_barang, nama_barang, kategori, jumlah, kondisi)
        VALUES
        ('$kode_barang', '$nama_barang', '$kategori', '$jumlah', '$kondisi')
    ");

    header("Location: barang.php");
    exit;
}

// HAPUS BARANG
if (isset($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];

    mysqli_query($conn, "DELETE FROM barang WHERE id = $id");

    header("Location: barang.php");
    exit;
}

// DATA BARANG
$data_barang = mysqli_query($conn, "SELECT * FROM barang ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f6f9; color: #111827; }
        header { padding: 20px 30px; background: #111827; color: #fff; }
        main { padding: 30px; }
        .box { margin-bottom: 24px; padding: 24px; background: #fff; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,.08); }
        input, select { width: 100%; padding: 10px; margin: 7px 0 15px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 5px; }
        button { padding: 10px 18px; border: 0; border-radius: 5px; background: #111827; color: #fff; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #111827; color: #fff; }
        .back, .delete { display: inline-block; text-decoration: none; }
        .back { margin-bottom: 20px; color: #111827; }
        .delete { padding: 7px 10px; border-radius: 4px; background: #dc2626; color: #fff; }
        @media (max-width: 700px) { main { padding: 15px; overflow-x: auto; } table { min-width: 700px; } }
    </style>
</head>
<body>
    <header><h2>Data Barang Soundbest</h2></header>
    <main>
        <a class="back" href="dashboard.php">&larr; Kembali ke Dashboard</a>

        <section class="box">
            <h3>Tambah Barang</h3>
            <form method="POST">
                <label>Kode Barang</label>
                <input type="text" name="kode_barang" required>
                <label>Nama Barang</label>
                <input type="text" name="nama_barang" required>
                <label>Kategori</label>
                <input type="text" name="kategori" required>
                <label>Jumlah</label>
                <input type="number" name="jumlah" min="1" required>
                <label>Kondisi</label>
                <select name="kondisi" required>
                    <option value="">-- Pilih Kondisi --</option>
                    <option value="Baik">Baik</option>
                    <option value="Rusak Ringan">Rusak Ringan</option>
                    <option value="Rusak Berat">Rusak Berat</option>
                </select>
                <button type="submit" name="tambah">+ Tambah Barang</button>
            </form>
        </section>

        <section class="box">
            <h3>Daftar Barang</h3>
            <table>
                <tr><th>No</th><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Jumlah</th><th>Kondisi</th><th>Aksi</th></tr>
                <?php $no = 1; while ($barang = mysqli_fetch_assoc($data_barang)) { ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($barang['kode_barang'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($barang['nama_barang'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($barang['kategori'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo $barang['jumlah']; ?></td>
                        <td><?php echo htmlspecialchars($barang['kondisi'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><a class="delete" href="barang.php?hapus=<?php echo (int) $barang['id']; ?>" onclick="return confirm('Yakin ingin menghapus barang ini?')">Hapus</a></td>
                    </tr>
                <?php } ?>
            </table>
        </section>
    </main>
</body>
</html>


