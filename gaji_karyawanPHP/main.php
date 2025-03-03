<?php
//File model/gaji.php untuk menyimpan data karyawan
$filePath = __DIR__ . '/gaji.php';

//Fungsi untuk membaca data ke file
function bacaData($filePath) {
    if (!file_exists($filePath)) {
        file_put_contents($filePath, '<?php return [];');
    }

    // gunakan include untuk membaca data
    $data = include $filePath;

    // jika data bukan array (format file salah), kembalikan array kosong
    if (!is_array($data)) {
        return [];
    }

    return $data;
}


//Fungsi untuk menyimpan data ke file
function simpanData($filePath, $data) {
    file_put_contents($filePath, '<?php return ' . var_export($data, true) . ';');
}

//Fungsi untuk menampilkan menu
function tampilkanMenu() {
    echo "\n=== Sistem Manajemen Gaji Karyawan ===\n";
    echo "1. Lihat karyawan\n";
    echo "2. Tambah karyawan\n";
    echo "3. Update karyawan\n";
    echo "4. Hapus karyawan\n";
    echo "5. Hitung Gaji Karyawan\n";
    echo "6. Keluar aplikasi\n";
    echo "Pilih aksi: ";
}

//Fungsi Untuk Menampilkan Daftar Karyawan
function lihatKaryawan($karyawan) {
    echo "\n=== Daftar Karyawan ===\n";
    if (empty($karyawan)) {
        echo "Belum ada karyawan yang terdaftar.\n";
        return;
    }
    foreach ($karyawan as $index => $dataKaryawan) {
        echo ($index + 1) . ". {$dataKaryawan['name']} -  {$dataKaryawan['position']}\n";
    }
}

//Fungsi Untuk Tambah Karyawan
function tambahKaryawan($karyawan, $filePath) {
    echo "\nMasukkan nama karyawan: ";
    $nama = trim(fgets(STDIN));

    echo "\nPilih jabatan karyawan (Manajer/Supervisor/Staf): ";
    $jabatan = trim(fgets(STDIN));
    $jabatanValid = ['Manajer', 'Supervisor', 'Staf'];

    if (!in_array($jabatan, $jabatanValid)) {
        echo "\nError: Jabatan tidak valid.\n";
        return;
    }

    $karyawan[] = ['name' => $nama, 'position' => $jabatan];
    simpanData($filePath, $karyawan);
    echo "\nKaryawan berhasil ditambahkan.\n";
}

//Fungsi Untuk Update Karyawan
function updateKaryawan($karyawan, $filePath) {
    lihatKaryawan($karyawan);
    echo "\nMasukkan nomor karyawan yang ingin diupdate: ";
    $index = (int)trim(fgets(STDIN)) - 1;

    if (!isset($karyawan[$index])) {
        echo "\nError: karyawan tidak valid.\n";
        return;
    }

    echo "\nMasukkan nama baru: ";
    $nama = trim(fgets(STDIN));

    echo "\nMasukkan jabatan baru (Manajer/Supervisor/Staf): ";
    $jabatan = trim(fgets(STDIN));
    $jabatanValid = ['Manajer', 'Supervisor', 'Staf'];

    if (!in_array($jabatan, $jabatanValid)) {
        echo "\nError: Jabatan tidak valid.\n";
        return;
    }

    $karyawan[$index] = ['name' => $nama, 'position' => $jabatan];
    simpanData($filePath, $karyawan);
    echo "\nKaryawan berhasil diupdate.\n";
}

//Fungsi Untuk Hapus Karyawan
function hapusKaryawan($karyawan, $filePath) {
    lihatKaryawan($karyawan);
    echo "\nMasukkan nomor karyawan yang ingin dihapus: ";
    $index = (int)trim(fgets(STDIN)) - 1;

    if (!isset($karyawan[$index])) {
        echo "\nKaryawan tidak ditemukan.\n";
        return;
    }

    echo "Yakin ingin menghapus {$karyawan[$index]['name']}? (y/n): ";
    $konfirmasi = trim(fgets(STDIN));

    if (strtolower($konfirmasi) === 'y') {
        unset($karyawan[$index]);
        $karyawan = array_values($karyawan);
        simpanData($filePath, $karyawan);
        echo "\nKaryawan berhasil dihapus.\n";
    } else {
        echo "Penghapusan dibatalkan.\n";
    }
}

//Fungsi Hitung Gaji Karyawan
function hitungGaji($karyawan) {
    lihatKaryawan($karyawan);
    echo "\nMasukkan nomor karyawan untuk menghitung gaji: ";
    $index = (int)trim(fgets(STDIN)) - 1;

    if (!isset($karyawan[$index])) {
        echo "\nError: Nomor karyawan tidak ditemukan.\n";
        return;
    }

    $gajiPokok = [
        'Manajer' => 5000000,
        'Supervisor' => 4000000,
        'Staf' => 3000000,
    ];

    echo "\nMasukkan jumlah jam lembur: ";
    $jamLembur = (int)trim(fgets(STDIN));
    echo "\nMasukkan rating kinerja (1-5): ";
    $rating = (int)trim(fgets(STDIN));

    if ($rating < 1 || $rating > 5) {
        echo "\nError: Rating kinerja tidak valid.\n";
        return;
    }

    $bayarLembur = $jamLembur * 20000;
    $bonusKinerja = $rating * 50000;
    $totalGaji = $gajiPokok[$karyawan[$index]['position']] + $bayarLembur + $bonusKinerja;

    echo "\n=== Detail Gaji ===\n";
    echo "Nama: {$karyawan[$index]['name']}\n";
    echo "Jabatan: {$karyawan[$index]['position']}\n";
    echo "Gaji Pokok: Rp{$gajiPokok[$karyawan[$index]['position']]}\n";
    echo "Lembur: Rp$bayarLembur\n";
    echo "Bonus Kinerja: Rp$bonusKinerja\n";
    echo "Total Gaji: Rp$totalGaji\n";
}

// main program
$karyawan = bacaData($filePath);
while (true) {
    tampilkanMenu();
    $pilihan = trim(fgets(STDIN));

    switch ($pilihan) {
        case '1':
            lihatKaryawan($karyawan);
            break;
        case '2':
            tambahKaryawan($karyawan, $filePath);
            break;
        case '3':
            updateKaryawan($karyawan, $filePath);
            break;
        case '4':
            hapusKaryawan($karyawan, $filePath);
            break;
        case '5':
            hitungGaji($karyawan);
            break;
        case '6':
            echo "Terima kasih telah datang!\n";
            exit;
        default:
            echo "Input tidak valid. Pilih angka 1-6.\n";
    }
}
