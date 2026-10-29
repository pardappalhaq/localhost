<?php
session_start();

function caesarCipher($teks, $kunci, $modus) {
    $hasil = '';
    $alfabetBesar = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $alfabetKecil = 'abcdefghijklmnopqrstuvwxyz';

    for ($i = 0; $i < strlen($teks); $i++) {
        $char = $teks[$i];

        if (strpos($alfabetBesar, $char) !== false) {
            $index = strpos($alfabetBesar, $char);
            if ($modus === 'enkripsi') {
                $newIndex = ($index + $kunci) % 26;
            } else {
                $newIndex = ($index - $kunci) % 26;
                if ($newIndex < 0) $newIndex += 26;
            }
            $hasil .= $alfabetBesar[$newIndex];
        }
        elseif (strpos($alfabetKecil, $char) !== false) {
            $index = strpos($alfabetKecil, $char);
            if ($modus === 'enkripsi') {
                $newIndex = ($index + $kunci) % 26;
            } else {
                $newIndex = ($index - $kunci) % 26;
                if ($newIndex < 0) $newIndex += 26;
            }
            $hasil .= $alfabetKecil[$newIndex];
        }
        else {
            $hasil .= $char;
        }
    }
    return $hasil;
}

/**
 * Daftar kata umum bahasa Indonesia & Inggris untuk pendeteksian plaintext.
 */
function getKataUmum() {
    return [
        // Kata Indonesia umum
        'dan','di','yang','ini','itu','dengan','untuk','dari','pada','ke',
        'tidak','ada','adalah','akan','juga','sudah','saya','kita','kami','mereka',
        'dia','anda','bisa','bukan','hanya','lebih','lagi','atau','jika','kalau',
        'apa','siapa','mana','kapan','bagaimana','mengapa','kenapa','karena','oleh','dalam',
        'atas','bawah','antara','sebelum','sesudah','ketika','saat','waktu','hari','tahun',
        'bulan','minggu','jam','menit','detik','orang','anak','ibu','bapak','ayah',
        'nama','saya','kamu','kopi','suka','makan','minum','pergi','datang','pulang',
        'besar','kecil','tinggi','rendah','panjang','pendek','baru','lama','muda','tua',
        'baik','buruk','bagus','jelek','cantik','tampan','pintar','bodoh','kaya','miskin',
        'satu','dua','tiga','empat','lima','enam','tujuh','delapan','sembilan','sepuluh',
        'halo','hai','selamat','pagi','siang','sore','malam','terima','kasih','maaf',
        'tolong','silakan','ya','bukan','belum','masih','harus','boleh','mau','ingin',
        'rumah','sekolah','kantor','toko','pasar','jalan','kota','desa','negara','dunia',
        'air','api','tanah','angin','langit','bumi','laut','gunung','sungai','hutan',
        'kucing','anjing','burung','ikan','ayam','kuda','sapi','kambing','domba','gajah',
        'merah','biru','hijau','kuning','putih','hitam','coklat','ungu','abu','emas',
        'makan','tidur','bangun','belajar','bekerja','bermain','berlari','berjalan','duduk','berdiri',
        'buku','pensil','pena','kertas','meja','kursi','papan','tulis','kelas','guru',
        'murid','siswa','mahasiswa','dosen','profesor','ilmu','pelajaran','ujian','nilai','lulus',
        'sangat','sekali','paling','cukup','agak','sedikit','banyak','semua','setiap','beberapa',
        'teman','sahabat','keluarga','tetangga','warga','rakyat','bangsa','bahasa','budaya','adat',
        'cinta','kasih','sayang','rindu','senang','gembira','bahagia','sedih','marah','takut',
        'praktikum','kriptografi','enkripsi','dekripsi','cipher','algoritma','program','komputer','data','informasi',

        // Kata Inggris umum
        'the','be','to','of','and','in','that','have','it','for',
        'not','on','with','he','as','you','do','at','this','but',
        'his','by','from','they','we','say','her','she','or','an',
        'will','my','one','all','would','there','their','what','so','up',
        'out','if','about','who','get','which','go','me','when','make',
        'can','like','time','no','just','him','know','take','people','into',
        'year','your','good','some','could','them','see','other','than','then',
        'now','look','only','come','its','over','think','also','back','after',
        'use','two','how','our','work','first','well','way','even','new',
        'want','because','any','these','give','day','most','us','hello','world',
        'cat','dog','love','hate','eat','drink','sleep','walk','run','play',
        'big','small','old','young','fast','slow','hot','cold','happy','sad',
    ];
}

/**
 * Hitung skor gabungan: chi-squared + bonus kata yang cocok.
 * Semakin KECIL skor = semakin mirip bahasa alami (semakin bagus).
 */
function hitungSkorGabungan($teks) {
    // 1. Chi-squared (frekuensi huruf)
    $freqID = [
        'a'=>19.0,'b'=>2.1,'c'=>1.4,'d'=>3.6,'e'=>8.5,
        'f'=>0.3,'g'=>2.5,'h'=>2.0,'i'=>7.5,'j'=>0.6,
        'k'=>3.7,'l'=>3.4,'m'=>3.8,'n'=>9.3,'o'=>2.7,
        'p'=>2.8,'q'=>0.0,'r'=>4.3,'s'=>3.5,'t'=>4.5,
        'u'=>4.5,'v'=>0.1,'w'=>0.6,'x'=>0.0,'y'=>1.5,
        'z'=>0.1
    ];
    $freqEN = [
        'a'=>8.2,'b'=>1.5,'c'=>2.8,'d'=>4.3,'e'=>12.7,
        'f'=>2.2,'g'=>2.0,'h'=>6.1,'i'=>7.0,'j'=>0.2,
        'k'=>0.8,'l'=>4.0,'m'=>2.4,'n'=>6.7,'o'=>7.5,
        'p'=>1.9,'q'=>0.1,'r'=>6.0,'s'=>6.3,'t'=>9.1,
        'u'=>2.8,'v'=>1.0,'w'=>2.4,'x'=>0.2,'y'=>2.0,
        'z'=>0.1
    ];

    $lower = strtolower($teks);
    $letterCount = [];
    $totalLetters = 0;
    for ($i = 0; $i < strlen($lower); $i++) {
        $c = $lower[$i];
        if ($c >= 'a' && $c <= 'z') {
            if (!isset($letterCount[$c])) $letterCount[$c] = 0;
            $letterCount[$c]++;
            $totalLetters++;
        }
    }
    if ($totalLetters === 0) return PHP_FLOAT_MAX;

    $chiID = 0; $chiEN = 0;
    foreach (range('a','z') as $letter) {
        $observed = isset($letterCount[$letter]) ? $letterCount[$letter] : 0;
        $expectedID = ($freqID[$letter] / 100) * $totalLetters;
        if ($expectedID > 0) $chiID += pow($observed - $expectedID, 2) / $expectedID;
        $expectedEN = ($freqEN[$letter] / 100) * $totalLetters;
        if ($expectedEN > 0) $chiEN += pow($observed - $expectedEN, 2) / $expectedEN;
    }
    $chiScore = min($chiID, $chiEN);

    // 2. Pengecekan kata (dictionary check)
    $kataUmum = getKataUmum();
    $words = preg_split('/[^a-z]+/', $lower, -1, PREG_SPLIT_NO_EMPTY);
    $totalWords = count($words);
    $matchedWords = 0;
    if ($totalWords > 0) {
        foreach ($words as $word) {
            if (in_array($word, $kataUmum)) {
                $matchedWords++;
            }
        }
        $matchRatio = $matchedWords / $totalWords; // 0.0 - 1.0
    } else {
        $matchRatio = 0;
    }

    // 3. Gabungkan: skor akhir = chiScore - (bonus dari kata yg cocok)
    // Bonus besar jika banyak kata cocok → skor jadi jauh lebih kecil
    // Bonus = matchRatio * 200 * (jumlah kata cocok) → makin banyak kata cocok makin kuat
    $bonus = $matchRatio * 200 * max($matchedWords, 1);
    $skorAkhir = $chiScore - $bonus;

    return $skorAkhir;
}

/**
 * Deteksi otomatis: aksi + kunci.
 *
 * Cara kerja:
 * 1. Hitung skor SEMUA 26 shift (0-25).
 *    Shift 0 = teks asli, shift K = dekripsi kunci K.
 * 2. Cari shift mana yang skornya paling kecil.
 * 3. Shift 0 terbaik → teks sudah plaintext → ENKRIPSI.
 *    Shift K terbaik → teks adalah ciphertext → DEKRIPSI kunci K.
 */
function autoDetectAll($teks) {
    // Hitung skor semua 26 shift
    $allShifts = [];
    for ($k = 0; $k <= 25; $k++) {
        if ($k === 0) {
            $shifted = $teks;
        } else {
            $shifted = caesarCipher($teks, $k, 'dekripsi');
        }
        $skor = hitungSkorGabungan($shifted);
        $allShifts[] = ['kunci' => $k, 'hasil' => $shifted, 'skor' => $skor];
    }

    // Cari shift dengan skor terkecil
    $bestShift = 0;
    $bestSkor  = $allShifts[0]['skor'];
    for ($k = 1; $k <= 25; $k++) {
        if ($allShifts[$k]['skor'] < $bestSkor) {
            $bestSkor  = $allShifts[$k]['skor'];
            $bestShift = $k;
        }
    }

    if ($bestShift === 0) {
        // === Teks plaintext → ENKRIPSI ===
        // Urutkan hasil enkripsi: skor TERBESAR = paling acak = terkuat
        $hasilEnkripsi = [];
        for ($k = 1; $k <= 25; $k++) {
            $enc  = caesarCipher($teks, $k, 'enkripsi');
            $skor = hitungSkorGabungan($enc);
            $hasilEnkripsi[] = ['kunci' => $k, 'hasil' => $enc, 'skor' => $skor];
        }
        usort($hasilEnkripsi, function($a, $b) { return $b['skor'] <=> $a['skor']; });
        return ['aksi' => 'enkripsi', 'results' => $hasilEnkripsi];
    } else {
        // === Teks ciphertext → DEKRIPSI ===
        // Urutkan hasil dekripsi: skor TERKECIL = paling mirip bahasa = terbaik
        $hasilDekripsi = [];
        for ($k = 1; $k <= 25; $k++) {
            $hasilDekripsi[] = $allShifts[$k];
        }
        usort($hasilDekripsi, function($a, $b) { return $a['skor'] <=> $b['skor']; });
        return ['aksi' => 'dekripsi', 'results' => $hasilDekripsi];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teks_input = isset($_POST['teks']) ? trim($_POST['teks']) : '';

    if ($teks_input === '') {
        header("Location: index.php?error=" . urlencode("Teks tidak boleh kosong!"));
        exit();
    }

    $detection  = autoDetectAll($teks_input);
    $aksi       = $detection['aksi'];
    $allResults = $detection['results'];

    $_SESSION['bruteforce'] = $allResults;
    $_SESSION['teks_asli']  = $teks_input;

    $bestKey    = $allResults[0]['kunci'];
    $bestResult = $allResults[0]['hasil'];

    $isi_file  = "Waktu     : " . date("Y-m-d H:i:s") . "\n";
    $isi_file .= "Aksi      : " . ucfirst($aksi) . " (Auto-Detect)\n";
    $isi_file .= "Kunci     : " . $bestKey . " (terdeteksi otomatis)\n";
    $isi_file .= "Teks Asli : " . $teks_input . "\n";
    $isi_file .= "Hasil     : " . $bestResult . "\n";
    $isi_file .= "----------------------------------------\n";
    file_put_contents("file.txt", $isi_file, FILE_APPEND);

    header("Location: index.php?hasil=" . urlencode($bestResult)
        . "&teks=" . urlencode($teks_input)
        . "&kunci=" . urlencode($bestKey)
        . "&aksi=" . urlencode($aksi));
    exit();
}
?>
