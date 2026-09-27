```php
<?php
session_start();

// Jika sudah login, arahkan sesuai role
if (isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    }

    if ($_SESSION['role'] == 'karyawan') {
        header("Location: karyawan/dashboard.php");
        exit;
    }

    if ($_SESSION['role'] == 'teknisi') {
        header("Location: teknisi/dashboard.php");
        exit;
    }
}

// Jika belum login, arahkan ke halaman login
header("Location: auth/login.php");
exit;
?>
```
