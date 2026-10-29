<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caesar Cipher</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>
<body>
<div class="card">

    <!-- Header -->
    <div class="header">
        <div class="mascot">🦉</div>
        <h1>Caesar Cipher</h1>
        <p>Masukkan teks — kunci & aksi terdeteksi otomatis!</p>
    </div>

    <!-- Progress bar -->
    <div class="progress-bar">
        <div class="progress-fill" id="progressFill"></div>
    </div>

    <!-- Error -->
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
            ❌ <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <!-- Form: hanya textarea + tombol -->
    <form action="cipher-proses.php" method="post" autocomplete="off" id="cipherForm">
        <div class="form-group">
            <label for="teks">Masukkan Teks</label>
            <textarea id="teks" name="teks"
                placeholder="Ketik teks di sini... Sistem akan otomatis mendeteksi apakah perlu dienkripsi atau didekripsi beserta kuncinya."
            ><?= isset($_GET['teks']) ? htmlspecialchars($_GET['teks']) : '' ?></textarea>
        </div>

        <button type="submit" class="btn btn-green">Deteksi & Proses Otomatis</button>
    </form>

    <!-- Result -->
    <?php if (isset($_GET['hasil'])): ?>
        <?php
            $aksiDeteksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';
            $isEnkripsi  = $aksiDeteksi === 'enkripsi';
            $kunciDeteksi = isset($_GET['kunci']) ? (int)$_GET['kunci'] : '?';
        ?>
        <div class="result-section">

            <!-- Info deteksi -->
            <div class="detection-info <?= $aksiDeteksi ?>">
                <span class="detection-pill">
                    <?= $isEnkripsi ? 'Enkripsi' : 'Dekripsi' ?>
                </span>
                <span>
                    Teks terdeteksi sebagai
                    <strong><?= $isEnkripsi ? 'plaintext' : 'ciphertext' ?></strong>
                    — kunci otomatis: <strong><?= $kunciDeteksi ?></strong>
                </span>
            </div>

            <div class="result-label">Hasil</div>
            <div class="result-box">
                <button class="copy-btn" onclick="copyResult()" id="copyBtn">Salin</button>
                <span id="resultText"><?= htmlspecialchars($_GET['hasil']) ?></span>
            </div>

            <div class="alert alert-success" style="margin-top: 0.85rem; margin-bottom: 0;">
                Tersimpan di <strong>file.txt</strong> — <a href="file.txt" target="_blank">Lihat</a>
            </div>

            <!-- Tabel semua kemungkinan -->
            <?php if (isset($_SESSION['bruteforce'])): ?>
                <div class="bruteforce-section">
                    <button class="bruteforce-toggle" onclick="toggleBruteforce()" id="bfToggle">
                        Lihat Semua 25 Kemungkinan
                    </button>
                    <div class="bruteforce-table-wrap" id="bfTable">
                        <table class="bruteforce-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Kunci</th>
                                    <th>Hasil <?= $isEnkripsi ? 'Enkripsi' : 'Dekripsi' ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_SESSION['bruteforce'] as $idx => $item): ?>
                                    <tr class="<?= $idx === 0 ? 'best-match' : '' ?>">
                                        <td><span class="rank-badge"><?= $idx + 1 ?></span></td>
                                        <td><?= $item['kunci'] ?></td>
                                        <td><?= htmlspecialchars($item['hasil']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <div class="streak-bar">
                Hebat! Cipher berhasil diproses otomatis!
            </div>
        </div>
    <?php endif; ?>

    <div class="footer">Praktikum Kriptografi</div>
</div>

<script>
    // Progress bar
    (function () {
        const fill = document.getElementById('progressFill');
        const hasResult = <?= isset($_GET['hasil']) ? 'true' : 'false' ?>;
        setTimeout(() => { fill.style.width = hasResult ? '100%' : '20%'; }, 300);
    })();

    document.getElementById('teks').addEventListener('input', function () {
        const fill = document.getElementById('progressFill');
        fill.style.width = this.value.trim() ? '60%' : '20%';
    });

    // Copy to clipboard
    function copyResult() {
        const text = document.getElementById('resultText').innerText;
        navigator.clipboard.writeText(text).then(() => {
            const btn = document.getElementById('copyBtn');
            btn.textContent = '✓ OK!';
            setTimeout(() => { btn.textContent = 'Salin'; }, 1500);
        });
    }

    // Toggle brute-force table
    function toggleBruteforce() {
        const table = document.getElementById('bfTable');
        const btn   = document.getElementById('bfToggle');
        table.classList.toggle('show');
        btn.textContent = table.classList.contains('show')
            ? 'Sembunyikan Tabel'
            : 'Lihat Semua 25 Kemungkinan';
    }
</script>
</body>
</html>
