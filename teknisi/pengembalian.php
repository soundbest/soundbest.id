<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'teknisi') {
    header("Location: ../index.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Proses Pengembalian
|--------------------------------------------------------------------------
*/

if (isset($_POST['kembalikan'])) {

    $id_peminjaman = (int) $_POST['id_peminjaman'];


    /*
    |--------------------------------------------------------------------------
    | Ambil data peminjaman
    |--------------------------------------------------------------------------
    */

    $cek = mysqli_query($conn, "

        SELECT *

        FROM peminjaman

        WHERE id = $id_peminjaman

        AND status = 'Dipinjam'

    ");

    $data = mysqli_fetch_assoc($cek);


    if ($data) {

        $barang_id = (int) $data['barang_id'];
        $jumlah = $data['jumlah'];


        /*
        |--------------------------------------------------------------------------
        | Update stok barang
        |--------------------------------------------------------------------------
        */

        $update_stok = mysqli_query($conn, "

            UPDATE barang

            SET jumlah = jumlah + $jumlah

            WHERE id = $barang_id

        ");


        /*
        |--------------------------------------------------------------------------
        | Update status peminjaman
        |--------------------------------------------------------------------------
        */

        $update_status = mysqli_query($conn, "

            UPDATE peminjaman

            SET status = 'Dikembalikan'

            WHERE id = $id_peminjaman

        ");


        if ($update_stok && $update_status) {

            echo "<script>

                alert('Barang berhasil dikembalikan dan stok diperbarui.');

                window.location='pengembalian.php';

            </script>";

            exit;

        } else {

            echo "<script>

                alert('Proses pengembalian gagal.');

            </script>";

        }

    } else {

        echo "<script>

            alert('Data peminjaman tidak ditemukan.');

        </script>";

    }

}


/*
|--------------------------------------------------------------------------
| Data barang yang sedang dipinjam
|--------------------------------------------------------------------------
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

    WHERE p.status = 'Dipinjam'

    ORDER BY p.id ASC

");

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Pengembalian Barang</title>

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

button {
    background: #2563eb;
    color: white;
    border: none;
    padding: 9px 14px;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background: #1d4ed8;
}

@media (max-width: 700px) {
    .sidebar { position: static; width: auto; height: auto; padding: 15px; }
    .sidebar a { display: inline-block; padding: 8px; }
    .content { margin-left: 0; padding: 15px; }
    .box { overflow-x: auto; }
    table { min-width: 850px; }
}

</style>

</head>

<body>


<div class="sidebar">

    <h2>Soundbest</h2>

    <a href="dashboard.php">Dashboard</a>

    <a href="peminjaman.php">Peminjaman</a>

    <a href="serah-terima.php">Serah-Terima</a>

    <a href="pengembalian.php">Pengembalian</a>

    <a href="../auth/logout.php">Logout</a>

</div>


<div class="content">

<div class="box">

<h2>Pengembalian Barang</h2>

<p>
Daftar barang yang sedang dipinjam dan menunggu pengembalian.
</p>


<table>

<tr>

    <th>No</th>

    <th>ID Peminjaman</th>

    <th>Peminjam</th>

    <th>Barang</th>

    <th>Jumlah</th>

    <th>Tanggal Kembali</th>

    <th>Aksi</th>

</tr>


<?php

$no = 1;

if (mysqli_num_rows($query) > 0) {

    while ($data = mysqli_fetch_assoc($query)) {

?>

<tr>

    <td>
        <?php echo $no++; ?>
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
        <?php echo $data['tanggal_kembali']; ?>
    </td>

    <td>

        <form method="POST">

            <input
                type="hidden"
                name="id_peminjaman"
                value="<?php echo $data['id']; ?>"
            >

            <button
                type="submit"
                name="kembalikan"
                onclick="return confirm('Apakah barang sudah benar-benar dikembalikan?')"
            >
                Terima Pengembalian
            </button>

        </form>

    </td>

</tr>

<?php

    }

} else {

?>

<tr>

<td colspan="7" style="text-align:center;">

Tidak ada barang yang sedang dipinjam.

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</body>

</html>