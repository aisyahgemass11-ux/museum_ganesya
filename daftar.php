<?php
session_start();
require_once "koneksi.php";
// Kalau sudah login, langsung masuk katalog
if (!empty($_SESSION["pengguna_id"])) {
    header("Location: katalog.php");
    exit;
}
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") 

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama = trim($_POST["nama_lengkap"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $keterangan = $_POST["keterangan"] ?? "";

    $pilihanKeterangan = [
        "Mahasiswa",
        "Pelajar",
        "Guru/Dosen",
        "Peneliti",
        "Umum"
    ];

    if (
        $nama === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($password) < 8 ||
        !in_array($keterangan, $pilihanKeterangan, true)
    ) {
        $error = "Lengkapi semua data dengan benar. Password minimal 8 karakter.";
    } else {
        $cek = mysqli_prepare(
            $conn,
            "SELECT id FROM pengguna WHERE email = ? LIMIT 1"
        );

        if (!$cek) {
            $error = "Terjadi kesalahan saat memeriksa email.";
        } else {
            mysqli_stmt_bind_param($cek, "s", $email);
            mysqli_stmt_execute($cek);
            mysqli_stmt_store_result($cek);

            if (mysqli_stmt_num_rows($cek) > 0) {
                $error = "Email sudah terdaftar. Silakan gunakan email lain.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = mysqli_prepare(
                    $conn,
                    "INSERT INTO pengguna
                    (nama_lengkap, email, password, keterangan)
                    VALUES (?, ?, ?, ?)"
                );

                if (!$stmt) {
                    $error = "Pendaftaran gagal diproses.";
                } else {
                    mysqli_stmt_bind_param(
                        $stmt,
                        "ssss",
                        $nama,
                        $email,
                        $hash,
                        $keterangan
                    );

                    if (mysqli_stmt_execute($stmt)) {
                        $pengguna_id = mysqli_insert_id($conn);

                        session_regenerate_id(true);
                        $_SESSION["pengguna_id"] = $pengguna_id;
                        $_SESSION["pengguna_nama"] = $nama;
                        $_SESSION["pengguna_email"] = $email;
                        $_SESSION["pengguna_keterangan"] = $keterangan;

                        mysqli_stmt_close($stmt);
                        mysqli_stmt_close($cek);

                        header("Location: katalog.php");
                        exit;
                    } else {
                        $error = "Pendaftaran gagal. Silakan coba kembali.";
                    }

                    mysqli_stmt_close($stmt);
                }
            }

            mysqli_stmt_close($cek);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Museum Ganesya</title>
    <style>
        * { box-sizing: border-box; }

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

        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #d8c7b5;
            border-radius: 7px;
            font-size: 15px;
            background: white;
        }

        input:focus, select:focus {
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

        button:hover { background: #704a35; }

        .error {
            background: #f9e5e2;
            color: #963b30;
            padding: 11px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .link {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #76553f;
            text-decoration: none;
            font-size: 14px;
        }

        .link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">✦</div>
        <h1>Museum Ganesya</h1>
        <p class="subtitle">Daftar Akun Pengguna</p>

        <?php if ($error !== ""): ?>
            <div class="error">
                <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="daftar.php">
            <label for="nama_lengkap">Nama lengkap</label>
            <input
                type="text"
                id="nama_lengkap"
                name="nama_lengkap"
                maxlength="100"
                placeholder="Masukkan nama lengkap"
                value="<?= htmlspecialchars($_POST["nama_lengkap"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                autocomplete="name"
                required
            >

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
                minlength="8"
                placeholder="Minimal 8 karakter"
                autocomplete="new-password"
                required
            >

            <label for="keterangan">Keterangan pengguna</label>
            <select id="keterangan" name="keterangan" required>
                <option value="">-- Pilih keterangan --</option>
                <?php
                $pilihan = [
                    "Mahasiswa",
                    "Pelajar",
                    "Guru/Dosen",
                    "Peneliti",
                    "Umum"
                ];

                foreach ($pilihan as $item):
                ?>
                    <option
                        value="<?= htmlspecialchars($item, ENT_QUOTES, "UTF-8") ?>"
                        <?= (($_POST["keterangan"] ?? "") === $item) ? "selected" : "" ?>
                    >
                        <?= htmlspecialchars($item, ENT_QUOTES, "UTF-8") ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Daftar Sekarang</button>
        </form>

        <a class="link" href="login_pengguna.php">
            Sudah punya akun? Login
        </a>
    </div>
</body>
</html>
