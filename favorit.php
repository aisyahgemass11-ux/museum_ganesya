
<?php
session_start();
require_once "koneksi.php";

if (empty($_SESSION["pengguna_id"])) {
    header("Location: daftar.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: katalog.php");
    exit;
}

$penggunaId = (int)$_SESSION["pengguna_id"];
$koleksiId = filter_input(INPUT_POST, "koleksi_id", FILTER_VALIDATE_INT);
$aksi = $_POST["aksi"] ?? "";

if (!$koleksiId || !in_array($aksi, ["tambah", "hapus"], true)) {
    header("Location: katalog.php?status=gagal");
    exit;
}

/* Pastikan koleksi memang ada */
$stmt = mysqli_prepare($conn, "SELECT id FROM koleksi WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $koleksiId);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$koleksiAda = mysqli_fetch_assoc($hasil);
mysqli_stmt_close($stmt);

if (!$koleksiAda) {
    header("Location: katalog.php?status=gagal");
    exit;
}

if ($aksi === "tambah") {
    /* INSERT IGNORE mencegah favorit ganda jika indeks unik tersedia */
    $stmt = mysqli_prepare(
        $conn,
        "INSERT IGNORE INTO favorit (pengguna_id, koleksi_id)
         VALUES (?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "ii", $penggunaId, $koleksiId);
    $berhasil = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: katalog.php?status=" . ($berhasil ? "tersimpan" : "gagal"));
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM favorit WHERE pengguna_id = ? AND koleksi_id = ?"
);
mysqli_stmt_bind_param($stmt, "ii", $penggunaId, $koleksiId);
$berhasil = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: katalog.php?status=" . ($berhasil ? "dihapus" : "gagal"));
exit;
?>
