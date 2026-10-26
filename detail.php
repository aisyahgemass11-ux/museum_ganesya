
<?php
require_once "koneksi.php";

function e($teks) {
    return htmlspecialchars((string)$teks, ENT_QUOTES, "UTF-8");
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
$item = null;

if ($id && $id > 0) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT nama, kategori, deskripsi
         FROM koleksi
         WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $hasil = mysqli_stmt_get_result($stmt);
    $item = mysqli_fetch_assoc($hasil);

    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Koleksi - Museum Ganesya</title>

    <link rel="stylesheet" href="style.css">

    <style>
        body {
            background: #f7f0e6;
        }

        .detail-container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 45px 38px;
            background: white;
            border-radius: 18px;
            box-sizing: border-box;
        }

        .detail-container h1 {
            color: #512c18;
            font-size: 38px;
            margin: 0 0 12px;
            text-transform: none;
        }

        .detail-kategori {
            color: #512c18;
            font-size: 21px;
            margin-bottom: 18px;
        }

        .detail-container h2 {
            color: #512c18;
            font-size: 28px;
            margin: 15px 0 10px;
        }

        .detail-deskripsi {
            color: #493323;
            font-size: 18px;
            line-height: 1.8;
            white-space: pre-line;
        }

        .kembali {
            display: inline-block;
            margin-top: 25px;
            color: #6522a0;
            font-size: 20px;
        }

        .tidak-ditemukan {
            color: #512c18;
            font-size: 18px;
        }

        @media (max-width: 600px) {
            .detail-container {
                margin: 18px 12px;
                padding: 28px 22px;
            }

            .detail-container h1 {
                font-size: 30px;
                overflow-wrap: anywhere;
            }
        }
    </style>
</head>

<body>

<main class="detail-container">

    <?php if ($item): ?>

        <h1><?= e($item["nama"]) ?></h1>

        <p class="detail-kategori">
            <strong>Kategori:</strong>
            <?= e($item["kategori"]) ?>
        </p>

        <h2>Deskripsi</h2>

        <div class="detail-deskripsi"><?php
            if (trim((string)$item["deskripsi"]) !== "") {
                echo e($item["deskripsi"]);
            } else {
                echo "Belum ada deskripsi untuk koleksi ini.";
            }
        ?></div>

    <?php else: ?>

        <h1>Koleksi tidak ditemukan</h1>

        <p class="tidak-ditemukan">
            Data koleksi tidak tersedia. Silakan kembali ke katalog.
        </p>

    <?php endif; ?>

    <a href="koleksi_tersimpan.php" class="kembali">
        ← Kembali ke Koleksi Tersimpan
    </a>

</main>

</body>
</html>
