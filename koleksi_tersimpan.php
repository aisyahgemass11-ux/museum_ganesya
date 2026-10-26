
<?php
session_start();
require_once "koneksi.php";

if (empty($_SESSION["pengguna_id"])) {
    header("Location: daftar.php");
    exit;
}

function e($teks) {
    return htmlspecialchars((string)$teks, ENT_QUOTES, "UTF-8");
}

$penggunaId = (int)$_SESSION["pengguna_id"];

$stmt = mysqli_prepare(
    $conn,
    "SELECT k.id, k.nama, k.kategori, k.deskripsi, k.gambar
     FROM favorit f
     JOIN koleksi k ON f.koleksi_id = k.id
     WHERE f.pengguna_id = ?
     ORDER BY f.created_at DESC"
);

mysqli_stmt_bind_param($stmt, "i", $penggunaId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

/* Kode item untuk halaman detail.html */
function kodeDetail($nama) {
    $pemetaan = [
        "Arca Figur Manusia" => "artefak1",
        "Koleksi Perhiasan dan Kalung" => "artefak2",
        "Arca dan Miniatur Bangunan" => "artefak3",
        "Celengan Babi" => "artefak4",
        "Koleksi Gerabah dan Wadah" => "artefak5",
        "Guci dan Wadah Kuno" => "artefak6",

        "Topeng Mahesa Sura" => "topeng1",
        "Topeng Menak" => "topeng2",
        "Topeng Jaya Samba" => "topeng3",
        "Topeng Tangsen" => "topeng4",
        "Topeng Pintèn" => "topeng5",
        "Topeng Demit" => "topeng6",

        "Wayang Golek Panakawan" => "wayang1",
        "Punokawan Cirebon" => "wayang2",
        "Wayang Thegel" => "wayang3",
        "Wayang Golek Tokoh Wayang" => "wayang4",
        "Koleksi Wayang Tradisional" => "wayang5",
        "Wayang Boneka Tiga Dimensi" => "wayang6"
    ];

    return $pemetaan[$nama] ?? "";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorit Saya - Museum Ganesya</title>
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            background: #f7efe4;
        }

        .favorit-header {
            text-align: center;
            padding: 30px 15px 20px;
        }

        .favorit-header h2 {
            color: #703b25;
            margin-bottom: 10px;
        }

        .favorit-aksi {
            max-width: 1100px;
            margin: 0 auto 25px;
            padding: 0 20px;
        }

        .favorit-aksi a {
            color: #8b251b;
            font-size: 20px;
            font-weight: bold;
            text-decoration: none;
        }

        .favorit-aksi a:hover {
            text-decoration: underline;
        }

        .favorit-grid {
            display: grid;
            grid-template-columns: repeat(
                auto-fit,
                minmax(min(100%, 300px), 530px)
            );
            justify-content: center;
            align-items: start;
            gap: 25px;
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px 40px;
        }

        .favorit-card {
            width: 100%;
            max-width: 530px;
            min-width: 0;
            overflow: hidden;
            border-radius: 12px;
            background: #fffaf4;
            box-shadow: 0 3px 12px rgba(70, 40, 20, 0.10);
        }

        .favorit-card img {
            display: block;
            width: 100%;
            height: 260px;
            object-fit: cover;
        }

        .favorit-info {
            padding: 22px 25px 25px;
        }

        .favorit-info h3 {
            margin: 8px 0 12px;
            color: #703b25;
            font-family: Georgia, serif;
            font-size: 25px;
        }

        .favorit-info p {
            color: #684c3b;
            font-size: 17px;
            line-height: 1.6;
            overflow-wrap: anywhere;
        }

        .favorit-tombol-aksi {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-top: 22px;
        }

        .favorit-tombol-aksi form {
            margin: 0;
        }

        .favorit-tombol {
            display: inline-block;
            box-sizing: border-box;
            border: none;
            border-radius: 8px;
            padding: 11px 14px;
            background: #81271d;
            color: white;
            font-family: inherit;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .favorit-tombol:hover {
            background: #5e1913;
        }

        .favorit-kosong {
            grid-column: 1 / -1;
            padding: 35px 15px;
            color: #684c3b;
            text-align: center;
            font-size: 18px;
        }

        .favorit-kosong a {
            color: #81271d;
            font-weight: bold;
        }

        @media (max-width: 600px) {
            .favorit-grid {
                grid-template-columns: minmax(0, 1fr);
                padding: 0 15px 30px;
            }

            .favorit-card img {
                height: 220px;
            }

            .favorit-info {
                padding: 20px;
            }

            .favorit-info h3 {
                font-size: 23px;
            }

            .favorit-tombol-aksi {
                gap: 8px;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <h1>Museum Ganesya</h1>
    </div>

    <nav>
        <a href="index.html">Home</a>
        <a href="katalog.php">Koleksi</a>
        <a href="koleksi_tersimpan.php">Favorit Saya</a>
    </nav>
</header>

<section class="favorit-header">
    <h2>♥ Favorit Saya</h2>
    <p>Koleksi Museum Ganesya yang kamu simpan.</p>
</section>

<div class="favorit-aksi">
    <a href="katalog.php">← Kembali ke Katalog</a>
</div>

<section class="favorit-grid">
    <?php if (mysqli_num_rows($result) === 0): ?>

        <div class="favorit-kosong">
            <p>Kamu belum menyimpan koleksi favorit.</p>
            <a href="katalog.php">Jelajahi Katalog</a>
        </div>

    <?php else: ?>

        <?php while ($item = mysqli_fetch_assoc($result)): ?>
            <?php $kode = kodeDetail($item["nama"]); ?>

            <article class="favorit-card">
                <?php if (!empty($item["gambar"])): ?>
                    <img
                        src="<?= e($item["gambar"]) ?>"
                        alt="<?= e($item["nama"]) ?>"
                    >
                <?php endif; ?>

                <div class="favorit-info">
                    <h3><?= e($item["nama"]) ?></h3>

                    <p><?= e($item["deskripsi"] ?? "") ?></p>

                    <div class="favorit-tombol-aksi">
                        <?php if ($kode !== ""): ?>
                            <a
                                class="favorit-tombol"
                                href="detail.html?item=<?= rawurlencode($kode) ?>"
                            >
                                Lihat Detail
                            </a>
                        <?php endif; ?>

                        <form action="favorit.php" method="POST">
                            <input
                                type="hidden"
                                name="koleksi_id"
                                value="<?= (int)$item["id"] ?>"
                            >
                            <input
                                type="hidden"
                                name="aksi"
                                value="hapus"
                            >

                            <button
                                class="favorit-tombol"
                                type="submit"
                            >
                                ♥ Hapus Favorit
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        <?php endwhile; ?>

    <?php endif; ?>
</section>

<footer>
    <p>&copy; 2026 Museum Ganesya. Website Galeri Indonesia Budaya.</p>
</footer>

</body>
</html>

<?php
mysqli_stmt_close($stmt);
?>
