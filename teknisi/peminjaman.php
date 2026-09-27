<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'teknisi') {
    header("Location: ../index.php");
    exit;
}

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
    WHERE p.status = 'Diproses'
    ORDER BY p.id ASC
");

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Peminjaman</title>

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

.status {
    color: green;
    font-weight: bold;
}

@media (max-width: 700px) {
    .sidebar { position: static; width: auto; height: auto; padding: 15px; }
    .sidebar a { display: inline-block; padding: 8px; }
    .content { margin-left: 0; padding: 15px; }
    .box { overflow-x: auto; }
    table { min-width: 900px; }
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

<h2>Daftar Peminjaman</h2>

<p>
Daftar peminjaman yang telah disetujui Admin dan siap diproses.
</p>


<table>

<tr>

    <th>No</th>

    <th>ID Peminjaman</th>

    <th>Peminjam</th>

    <th>Barang</th>

    <th>Jumlah</th>

    <th>Tanggal Pinjam</th>

    <th>Tanggal Kembali</th>

    <th>Status</th>

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
        <?php echo $data['tanggal_pinjam']; ?>
    </td>

    <td>
        <?php echo $data['tanggal_kembali']; ?>
    </td>

    <td class="status">
        <?php echo htmlspecialchars($data['status']); ?>
    </td>

</tr>

<?php

    }

} else {

?>

<tr>

<td colspan="8" style="text-align:center;">

Belum ada peminjaman yang disetujui.

</td>

</tr>

<?php

}

?>

</table>

</div>

</div>

</body>

</html>