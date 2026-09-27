<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'teknisi') {
    header("Location: ../index.php");
    exit;
}

$username = $_SESSION['username'];

/* Total peminjaman yang disetujui */
$query_disetujui = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM peminjaman
    WHERE status = 'Diproses'
");

$data_disetujui = mysqli_fetch_assoc($query_disetujui);
$total_disetujui = $data_disetujui['total'];


/* Total serah terima */
$query_serah = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM peminjaman
    WHERE status = 'Dipinjam'
");

$data_serah = mysqli_fetch_assoc($query_serah);
$total_serah = $data_serah['total'];


/* Total pengembalian */
$query_kembali = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM peminjaman
    WHERE status = 'Dikembalikan'
");

$data_kembali = mysqli_fetch_assoc($query_kembali);
$total_kembali = $data_kembali['total'];

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Teknisi</title>

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

.header {
    background: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 25px;
}

.cards {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.card {
    background: white;
    width: 30%;
    min-width: 200px;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.card h3 {
    color: #64748b;
    margin: 0;
}

.card p {
    font-size: 30px;
    font-weight: bold;
    margin: 15px 0 0;
}

@media (max-width: 700px) {
    .sidebar { position: static; width: auto; height: auto; padding: 15px; }
    .sidebar a { display: inline-block; padding: 8px; }
    .content { margin-left: 0; padding: 15px; }
    .card { width: 100%; min-width: 0; }
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

    <div class="header">

        <h2>Dashboard Teknisi</h2>

        <p>
            Selamat datang,
            <b><?php echo htmlspecialchars($username); ?></b>
        </p>

    </div>


    <div class="cards">

        <div class="card">

            <h3>Peminjaman Disetujui</h3>

            <p>
                <?php echo $total_disetujui; ?>
            </p>

        </div>


        <div class="card">

            <h3>Sudah Diserahkan</h3>

            <p>
                <?php echo $total_serah; ?>
            </p>

        </div>


        <div class="card">

            <h3>Sudah Dikembalikan</h3>

            <p>
                <?php echo $total_kembali; ?>
            </p>

        </div>

    </div>

</div>

</body>

</html>