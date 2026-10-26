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
$kataKunci = trim($_GET["search"] ?? "");
$status = $_GET["status"] ?? "";
$koleksiLama = [
    ["bagian"=>"Artefak", "nama"=>"Arca Figur Manusia", "deskripsi"=>"Koleksi arca berbentuk figur manusia yang menjadi bagian dari peninggalan sejarah dan budaya.", "gambar"=>"gambar/artefak 1.jpg", "item"=>"artefak1"],
    ["bagian"=>"Artefak", "nama"=>"Koleksi Perhiasan dan Kalung", "deskripsi"=>"Koleksi perhiasan dan kalung yang menunjukkan bentuk seni serta budaya masyarakat pada masa lalu.", "gambar"=>"gambar/artefak 2.jpg", "item"=>"artefak2"],
    ["bagian"=>"Artefak", "nama"=>"Arca dan Miniatur Bangunan", "deskripsi"=>"Koleksi arca dan miniatur yang menggambarkan bentuk peninggalan budaya masa lampau.", "gambar"=>"gambar/artefak 3.jpg", "item"=>"artefak3"],
    ["bagian"=>"Artefak", "nama"=>"Celengan Babi", "deskripsi"=>"Koleksi wadah berbentuk babi yang menjadi salah satu benda peninggalan dalam koleksi museum.", "gambar"=>"gambar/artefak 4.jpg", "item"=>"artefak4"],
    ["bagian"=>"Artefak", "nama"=>"Koleksi Gerabah dan Wadah", "deskripsi"=>"Berbagai bentuk gerabah dan wadah yang memperlihatkan penggunaan benda dalam kehidupan masyarakat masa lalu.", "gambar"=>"gambar/artefak 5.jpg", "item"=>"artefak5"],
    ["bagian"=>"Artefak", "nama"=>"Guci dan Wadah Kuno", "deskripsi"=>"Koleksi guci dan wadah kuno yang menjadi bagian dari benda budaya dan sejarah museum.", "gambar"=>"gambar/artefak 6.jpg", "item"=>"artefak6"],
    ["bagian"=>"Topeng", "nama"=>"Topeng Mahesa Sura", "deskripsi"=>"Salah satu koleksi topeng yang menggambarkan karakter dalam tradisi seni pertunjukan Nusantara.", "gambar"=>"gambar/topeng 1.jpg", "item"=>"topeng1"],
    ["bagian"=>"Topeng", "nama"=>"Topeng Menak", "deskripsi"=>"Koleksi topeng tradisional yang menjadi bagian dari kekayaan seni dan budaya Indonesia.", "gambar"=>"gambar/topeng 2.jpg", "item"=>"topeng2"],
    ["bagian"=>"Topeng", "nama"=>"Topeng Jaya Samba", "deskripsi"=>"Topeng dengan karakter khas yang menjadi bagian dari koleksi seni tradisional museum.", "gambar"=>"gambar/topeng 3.jpg", "item"=>"topeng3"],
    ["bagian"=>"Topeng", "nama"=>"Topeng Tangsen", "deskripsi"=>"Koleksi topeng tradisional yang memperlihatkan karakter dan bentuk seni budaya Nusantara.", "gambar"=>"gambar/topeng 4.jpg", "item"=>"topeng4"],
    ["bagian"=>"Topeng", "nama"=>"Topeng Pintèn", "deskripsi"=>"Salah satu koleksi topeng tradisional yang memiliki bentuk dan karakter khas.", "gambar"=>"gambar/topeng 5.jpg", "item"=>"topeng5"],
    ["bagian"=>"Topeng", "nama"=>"Topeng Demit", "deskripsi"=>"Koleksi topeng dengan karakter khas yang memperlihatkan keberagaman seni tradisional.", "gambar"=>"gambar/topeng 6.jpg", "item"=>"topeng6"],
    ["bagian"=>"Wayang", "nama"=>"Wayang Golek Panakawan", "deskripsi"=>"Koleksi wayang golek yang menampilkan tokoh Panakawan dalam tradisi seni pertunjukan.", "gambar"=>"gambar/wayang 1.jpg", "item"=>"wayang1"],
    ["bagian"=>"Wayang", "nama"=>"Punokawan Cirebon", "deskripsi"=>"Koleksi tokoh Punokawan yang memperlihatkan keberagaman tradisi wayang di Indonesia.", "gambar"=>"gambar/wayang 2.jpg", "item"=>"wayang2"],
    ["bagian"=>"Wayang", "nama"=>"Wayang Thegel", "deskripsi"=>"Koleksi wayang tradisional yang menjadi bagian dari kekayaan seni pertunjukan Nusantara.", "gambar"=>"gambar/wayang 3.jpg", "item"=>"wayang3"],
    ["bagian"=>"Wayang", "nama"=>"Wayang Golek Tokoh Wayang", "deskripsi"=>"Koleksi wayang golek dengan bentuk tokoh yang memperlihatkan karakter seni tradisional.", "gambar"=>"gambar/wayang 4.jpg", "item"=>"wayang4"],
    ["bagian"=>"Wayang", "nama"=>"Koleksi Wayang Tradisional", "deskripsi"=>"Berbagai bentuk wayang tradisional yang menjadi bagian dari warisan budaya Indonesia.", "gambar"=>"gambar/wayang 5.jpg", "item"=>"wayang5"],
    ["bagian"=>"Wayang", "nama"=>"Wayang Boneka Tiga Dimensi", "deskripsi"=>"Koleksi wayang berbentuk tiga dimensi yang menunjukkan variasi bentuk dalam seni pertunjukan.", "gambar"=>"gambar/wayang 6.jpg", "item"=>"wayang6"]
];
/* Cocokkan koleksi lama dengan tabel koleksi. */
foreach ($koleksiLama as &$item) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id FROM koleksi WHERE nama = ? LIMIT 1"
    );
    mysqli_stmt_bind_param($stmt, "s", $item["nama"]);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    $baris = mysqli_fetch_assoc($hasil);
    mysqli_stmt_close($stmt);

    if (!$baris) {
        $kategori = $item["bagian"];
        $nama = $item["nama"];
        $deskripsi = $item["deskripsi"];
        $gambar = $item["gambar"];

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO koleksi (nama, kategori, deskripsi, gambar)
             VALUES (?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $nama,
            $kategori,
            $deskripsi,
            $gambar
        );
        mysqli_stmt_execute($stmt);
        $item["id"] = (int)mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
    } else {
        $item["id"] = (int)$baris["id"];
    }
}
unset($item);

/* Ambil favorit milik pengguna yang login. */
$favoritSaya = [];

$stmtFav = mysqli_prepare(
    $conn,
    "SELECT koleksi_id FROM favorit WHERE pengguna_id = ?"
);
mysqli_stmt_bind_param($stmtFav, "i", $penggunaId);
mysqli_stmt_execute($stmtFav);
$resultFav = mysqli_stmt_get_result($stmtFav);

while ($rowFav = mysqli_fetch_assoc($resultFav)) {
    $favoritSaya[] = (int)$rowFav["koleksi_id"];
}
mysqli_stmt_close($stmtFav);

function cocokKata($item, $kataKunci) {
    if ($kataKunci === "") {
        return true;
    }

    $teks = $item["bagian"] . " " . $item["nama"] . " " . $item["deskripsi"];
    return stripos($teks, $kataKunci) !== false;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Koleksi - Museum Ganesya</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .koleksi-atas {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin: 25px 8%;
        }

        .judul-koleksi-tersimpan {
            color: #7b241c;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }

        .judul-koleksi-tersimpan:hover {
            text-decoration: underline;
        }

        .pesan-katalog {
            margin: 15px 8%;
            padding: 12px 15px;
            border-radius: 6px;
            background: #f5eee8;
            color: #7b241c;
        }

        /* Tata letak kartu katalog */
        .koleksi-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            align-items: stretch;
            gap: 25px;
        }

        .koleksi-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            min-width: 0;
            box-sizing: border-box;
            overflow: hidden;
        }

        .koleksi-card > img {
            display: block;
            width: 100%;
            height: 220px;
            flex-shrink: 0;
            object-fit: cover;
        }

        .koleksi-info {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 0;
            box-sizing: border-box;
        }

        .koleksi-info h3 {
            min-height: 60px;
            margin-bottom: 12px;
        }

        .koleksi-info p {
            flex: 1;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .kartu-tombol {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            margin-top: auto;
        }

        .kartu-tombol form {
            display: flex;
            margin: 0;
        }

        .tombol-favorit,
        .tombol-detail {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-height: 44px;
            box-sizing: border-box;
            border: 0;
            border-radius: 6px;
            padding: 10px 13px;
            background: #7b241c;
            color: #fff;
            font-size: 14px;
            font-weight: bold;
            font-family: inherit;
            text-decoration: none;
            cursor: pointer;
        }

        .tombol-favorit:hover,
        .tombol-detail:hover {
            background: #5e1913;
        }

        .tombol-favorit.sudah {
            background: #555;
        }

        .tombol-favorit.sudah:hover {
            background: #333;
        }

        @media (max-width: 900px) {
            .koleksi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 20px;
            }
        }

        @media (max-width: 600px) {
            .koleksi-grid {
                grid-template-columns: minmax(0, 1fr);
                gap: 20px;
            }

            .koleksi-atas {
                margin: 20px 5%;
            }

            .judul-koleksi-tersimpan {
                font-size: 21px;
            }

            .koleksi-card > img {
                height: 220px;
            }

            .koleksi-info h3 {
                min-height: 0;
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
        <form class="nav-search" action="katalog.php" method="GET">
            <input
                type="text"
                name="search"
                placeholder="Search"
                aria-label="Cari koleksi"
                value="<?= e($kataKunci) ?>"
            >
            <button type="submit" aria-label="Cari">🔍</button>
        </form>

        <a href="index.html">Home</a>
        <a href="index.html#tentang">Tentang Museum</a>
        <a href="index.html#visi-misi">Visi &amp; Misi</a>
        <a href="katalog.php">Koleksi</a>
        <a
            href="https://www.google.com/maps/search/?api=1&query=Museum+Ganesya+Malang"
            target="_blank"
            rel="noopener noreferrer"
        >Lokasi Museum</a>
    </nav>
</header>

<section class="katalog-header">
    <span>KOLEKSI MUSEUM</span>
    <h2>Katalog Koleksi</h2>
    <p>Jelajahi berbagai koleksi budaya yang terdapat di Museum Ganesya.</p>
</section>

<?php if ($status === "tersimpan"): ?>
    <p class="pesan-katalog">Koleksi berhasil disimpan ke favorit kamu.</p>
<?php elseif ($status === "dihapus"): ?>
    <p class="pesan-katalog">Koleksi berhasil dihapus dari favorit.</p>
<?php elseif ($status === "gagal"): ?>
    <p class="pesan-katalog">Favorit gagal diproses. Silakan coba lagi.</p>
<?php endif; ?>

<div class="koleksi-atas">
    <a href="koleksi_tersimpan.php" class="judul-koleksi-tersimpan">
        ♥ Favorit Saya
    </a>
</div>

<?php
$bagianList = ["Artefak", "Topeng", "Wayang"];

foreach ($bagianList as $bagian):
    $hasilBagian = array_filter(
        $koleksiLama,
        function ($item) use ($bagian, $kataKunci) {
            return $item["bagian"] === $bagian
                && cocokKata($item, $kataKunci);
        }
    );

    if (count($hasilBagian) === 0) {
        continue;
    }
?>
<section class="koleksi-section" id="<?= strtolower(e($bagian)) ?>">
    <h2><?= e($bagian) ?></h2>

    <div class="koleksi-grid">
        <?php foreach ($hasilBagian as $item): ?>
            <?php
                $sudahFavorit = in_array(
                    (int)$item["id"],
                    $favoritSaya,
                    true
                );
            ?>

            <div class="koleksi-card">
                <img
                    src="<?= e($item["gambar"]) ?>"
                    alt="<?= e($item["nama"]) ?>"
                >

                <div class="koleksi-info">
                    <h3><?= e($item["nama"]) ?></h3>
                    <p><?= e($item["deskripsi"]) ?></p>

                    <div class="kartu-tombol">
                        <a
                            class="tombol-detail"
                            href="detail.html?item=<?= rawurlencode($item["item"]) ?>"
                        >Lihat Detail</a>

                        <form action="favorit.php" method="POST">
                            <input
                                type="hidden"
                                name="koleksi_id"
                                value="<?= (int)$item["id"] ?>"
                            >

                            <?php if (!$sudahFavorit): ?>
                                <input type="hidden" name="aksi" value="tambah">
                                <button class="tombol-favorit" type="submit">
                                    ♡ Simpan Favorit
                                </button>
                            <?php else: ?>
                                <input type="hidden" name="aksi" value="hapus">
                                <button class="tombol-favorit sudah" type="submit">
                                    ♥ Hapus Favorit
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endforeach; ?>

<?php
$jumlahHasil = count(
    array_filter(
        $koleksiLama,
        function ($item) use ($kataKunci) {
            return cocokKata($item, $kataKunci);
        }
    )
);

if ($kataKunci !== "" && $jumlahHasil === 0):
?>
    <p style="text-align:center;padding:20px;">
        Koleksi yang dicari tidak ditemukan.
    </p>
<?php endif; ?>

<footer>
    <p>&copy; 2026 Museum Ganesya. Website Galeri Indonesia Budaya.</p>
</footer>

</body>
</html>
