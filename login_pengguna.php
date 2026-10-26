<?php
session_start();
require_once "koneksi.php";
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === "") {
        $error = "Masukkan email dan password dengan benar.";
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, nama_lengkap, email, password, keterangan
             FROM pengguna
             WHERE email = ?
             LIMIT 1"
        );
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) === 1) {
            mysqli_stmt_bind_result(
                $stmt,
                $id,
                $nama,
                $emailTersimpan,
                $hashPassword,
                $keterangan
            );
            mysqli_stmt_fetch($stmt);
            if (password_verify($password, $hashPassword)) {
                session_regenerate_id(true);
                $_SESSION["pengguna_id"] = $id;
                $_SESSION["pengguna_nama"] = $nama;
                $_SESSION["pengguna_email"] = $emailTersimpan;
                $_SESSION["pengguna_keterangan"] = $keterangan;
                header("Location: katalog.php");
                exit;
            }
        }
        $error = "Email atau password salah. Silakan coba lagi.";
        mysqli_stmt_close($stmt);
    }
}
$pesanDaftar = isset($_GET["daftar"])
    && $_GET["daftar"] === "berhasil";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pengguna - Museum Ganesya</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            background: #f6f0e5;
            color: #5a3827;
        }

        .container {
            width: 100%;
            max-width: 430px;
            padding: 30px;
            background: #fffdf9;
            border-radius: 14px;
            box-shadow: 0 5px 20px rgba(70,45,25,.12);
        }

        .logo {
            text-align: center;
            font-size: 36px;
            margin-bottom: 8px;
        }

        h1 {
            text-align: center;
            font-size: 25px;
            margin: 0;
        }

        .subtitle {
            text-align: center;
            color: #806b5b;
            font-size: 14px;
            margin: 10px 0 24px;
        }

        label {
            display: block;
            margin: 15px 0 7px;
            font-weight: bold;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d8c7b5;
            border-radius: 7px;
            font-size: 15px;
            background: white;
        }

        input:focus {
            outline: 2px solid #a98465;
            border-color: #a98465;
        }

        button {
            width: 100%;
            margin-top: 23px;
            padding: 13px;
            border: none;
            border-radius: 7px;
            background: #5a3827;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #704a35;
        }
        .error, .success {
            padding: 11px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .error {
            background: #f9e5e2;
            color: #963b30;
        }

        .success {
            background: #e4f4e5;
            color: #286b35;
        }

        .link {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #76553f;
            text-decoration: none;
            font-size: 14px;
        }

        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">✦</div>
        <h1>Museum Ganesya</h1>
        <p class="subtitle">Login Akun Pengguna</p>

        <?php if ($pesanDaftar): ?>
            <div class="success">
                Pendaftaran berhasil! Silakan login menggunakan akun kamu.
            </div>
        <?php endif; ?>

        <?php if ($error !== ""): ?>
            <div class="error">
                <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login_pengguna.php">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                maxlength="150"
                placeholder="nama@email.com"
                value="<?= htmlspecialchars($_POST["email"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                autocomplete="email"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Masukkan password"
                autocomplete="current-password"
                required
            >

            <button type="submit">Login</button>
        </form>

        <a class="link" href="daftar.php">
            Belum punya akun? Daftar Pengguna
        </a>
    </div>
</body>
</html>
