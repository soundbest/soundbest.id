<?php
session_start();
require_once "../config/database.php";

$error = "";

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);

    $query = mysqli_query(
        $conn,
        "SELECT * FROM users
         WHERE username='$username'"
    );

    if (mysqli_num_rows($query) == 1) {

        $user = mysqli_fetch_assoc($query);
        $passwordValid = password_verify($_POST['password'], $user['password']);

        // Compatibility for accounts created with the old MD5 format.
        if (!$passwordValid && strlen($user['password']) === 32) {
            $passwordValid = hash_equals($user['password'], md5($_POST['password']));

            if ($passwordValid) {
                $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $newPassword = mysqli_real_escape_string($conn, $newPassword);
                mysqli_query($conn, "UPDATE users SET password='$newPassword' WHERE id=" . (int) $user['id']);
            }
        }

        if (!$passwordValid) {
            $error = "Username atau password salah!";
        } else {
            // Simpan session
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['id']       = $user['id'];
            $_SESSION['nama']     = $user['nama'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            // Arahkan sesuai role
            if ($user['role'] == 'admin') {

                header("Location: ../admin/dashboard.php");
                exit;

            } elseif ($user['role'] == 'karyawan') {

                header("Location: ../karyawan/dashboard.php");
                exit;

            } elseif ($user['role'] == 'teknisi') {

                header("Location: ../teknisi/dashboard.php");
                exit;

            }
        }

    } else {

        $error = "Username atau password salah!";

    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Soundbest</title>
    <style>
        body { font-family: Arial, sans-serif; background: #111; }
        .login-box { width: 350px; margin: 100px auto; padding: 30px; background: #fff; border-radius: 10px; }
        input { width: 100%; padding: 10px; margin: 8px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #000; color: #fff; border: 0; cursor: pointer; }
        .error { color: #c00; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>SOUNDBEST</h2>
        <p>Sistem Peminjaman Barang</p>

        <?php if ($error !== "") { ?>
            <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php } ?>

        <form method="POST" action="">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>

            <button type="submit" name="login" value="1">LOGIN</button>
        </form>
    </div>
</body>
</html>