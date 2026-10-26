<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Koleksi - Museum Ganesya</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<header>
    <div class="logo">
        <h1>Museum Ganesya</h1>
    </div>

    <nav>
        <form class="nav-search" action="../katalog.php" method="GET">
            <input
                type="text"
                name="search"
                placeholder="Search"
                aria-label="Cari koleksi"
            >
            <button type="submit" aria-label="Cari">🔍</button>
        </form>

        <a href="../index.html">Home</a>
        <a href="../index.html#tentang">Tentang Museum</a>
        <a href="../index.html#visi-misi">Visi &amp; Misi</a>
        <a href="../katalog.php">Koleksi</a>
        <a
            href="https://www.google.com/maps/search/?api=1&query=Museum+Ganesya+Malang"
            target="_blank"
            rel="noopener noreferrer"
        >Lokasi Museum</a>
    </nav>
</header>
