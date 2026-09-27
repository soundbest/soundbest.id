<?php
session_start();
require_once "../config/database.php";

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Cek role
if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$pesan = "";

// ==========================
// TAMBAH PENGGUNA
// ==========================
if (isset($_POST['tambah'])) {

    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Cek username
    $cek = mysqli_query(
        $conn,
        "SELECT * FROM users WHERE username='$username'"
    );

    if (mysqli_num_rows($cek) > 0) {

        $pesan = "Username sudah digunakan!";

    } else {

        $query = mysqli_query(
            $conn,
            "INSERT INTO users
            (nama, username, password, role)
            VALUES
            ('$nama', '$username', '$password', '$role')"
        );

        if ($query) {
            header("Location: pengguna.php");
            exit;
        } else {
            $pesan = "Gagal menambahkan pengguna!";
        }
    }
}


// ==========================
// HAPUS PENGGUNA
// ==========================
if (isset($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];

    // Tidak boleh menghapus akun sendiri
    if ($id == $_SESSION['id']) {

        $pesan = "Akun yang sedang digunakan tidak dapat dihapus!";

    } else {

        mysqli_query(
            $conn,
            "DELETE FROM users WHERE id=$id"
        );

        header("Location: pengguna.php");
        exit;
    }
}


// ==========================
// AMBIL DATA PENGGUNA
// ==========================
$data_pengguna = mysqli_query(
    $conn,
    "SELECT id, nama, username, role
     FROM users
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Data Pengguna - Soundbest</title>

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

        .hapus:hover {
            background: #b91c1c;
        }

        @media (max-width: 700px) {

            .container {
                padding: 15px;
            }

            .box {
                overflow-x: auto;
            }

            table {
                min-width: 650px;
            }
        }

    </style>

</head>

<body>

<div class="header">

    <h2>Data Pengguna Soundbest</h2>

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


    <!-- FORM TAMBAH -->

    <div class="box">

        <h3>Tambah Pengguna</h3>

        <br>

        <form method="POST">

            <label>Nama Lengkap</label>

            <input
                type="text"
                name="nama"
                placeholder="Masukkan nama lengkap"
                required
            >


            <label>Username</label>

            <input
                type="text"
                name="username"
                placeholder="Masukkan username"
                required
            >


            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Masukkan password"
                required
            >


            <label>Role</label>

            <select name="role" required>

                <option value="">
                    -- Pilih Role --
                </option>

                <option value="admin">
                    Admin
                </option>

                <option value="karyawan">
                    Karyawan
                </option>

                <option value="teknisi">
                    Teknisi
                </option>

            </select>


            <button type="submit" name="tambah">
                + Tambah Pengguna
            </button>

        </form>

    </div>


    <!-- DATA PENGGUNA -->

    <div class="box">

        <h3>Daftar Pengguna</h3>

        <br>

        <table>

            <tr>

                <th>No</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Role</th>
                <th>Aksi</th>

            </tr>

            <?php

            $no = 1;

            while ($user = mysqli_fetch_assoc($data_pengguna)) {

            ?>

            <tr>

                <td>
                    <?= $no++; ?>
                </td>

                <td>
                    <?= htmlspecialchars($user['nama']); ?>
                </td>

                <td>
                    <?= htmlspecialchars($user['username']); ?>
                </td>

                <td>
                    <?= htmlspecialchars($user['role']); ?>
                </td>

                <td>

                    <?php if ($user['id'] != $_SESSION['id']) { ?>

                        <a
                            href="pengguna.php?hapus=<?= $user['id']; ?>"
                            class="hapus"
                            onclick="return confirm(
                                'Yakin ingin menghapus pengguna ini?'
                            )"
                        >
                            Hapus
                        </a>

                    <?php } else { ?>

                        Akun Anda

                    <?php } ?>

                </td>

            </tr>

            <?php } ?>

        </table>

    </div>

</div>

</body>

</html>