<?php
header('Content-Type: application/json; charset=utf-8');

$host = "localhost";
$user = "root";
$pass = "";
$db   = "rumahsakit";

$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) {
    http_response_code(500);
    die(json_encode(["status" => "error", "pesan" => "Tidak bisa terkoneksi ke database"]));
}
mysqli_set_charset($koneksi, "utf8mb4");

$op     = $_GET['op'] ?? "";
$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Data untuk PUT dibaca dari body request
$put = [];
if ($method === 'PUT') {
    parse_str(file_get_contents("php://input"), $put);
}

function kirim($status, $pesan = null, $data = null, $kode = 200) {
    http_response_code($kode);
    $out = ['status' => $status];
    if ($pesan !== null) $out['pesan'] = $pesan;
    if ($data !== null)  $out['data']  = $data;
    echo json_encode($out);
    exit;
}

function ambilSemua($koneksi, $sql) {
    $q = mysqli_query($koneksi, $sql);
    return mysqli_fetch_all($q, MYSQLI_ASSOC);
}

switch ($op) {

    // ==================== RUANGAN ====================
    case 'ruangan':
        if ($method === 'GET') {
            kirim('success', null, ambilSemua($koneksi, "SELECT * FROM ruangans"));
        }
        if ($method === 'POST') {
            $stmt = mysqli_prepare($koneksi,
                "INSERT INTO ruangans (kodeRuangan, namaRuangan, lokasi, dayaTampung) VALUES (?, ?, ?, ?)");
            $dt = (int) ($_POST['dayaTampung'] ?? 0);
            mysqli_stmt_bind_param($stmt, "sssi",
                $_POST['kodeRuangan'], $_POST['namaRuangan'], $_POST['lokasi'], $dt);
            mysqli_stmt_execute($stmt)
                ? kirim('success', 'Data ruangan berhasil ditambahkan', null, 201)
                : kirim('error', 'Gagal menambahkan data ruangan', null, 400);
        }
        if ($method === 'PUT' && $id > 0) {
            $stmt = mysqli_prepare($koneksi,
                "UPDATE ruangans SET kodeRuangan=?, namaRuangan=?, lokasi=?, dayaTampung=? WHERE id=?");
            $dt = (int) ($put['dayaTampung'] ?? 0);
            mysqli_stmt_bind_param($stmt, "sssii",
                $put['kodeRuangan'], $put['namaRuangan'], $put['lokasi'], $dt, $id);
            mysqli_stmt_execute($stmt)
                ? kirim('success', 'Data ruangan berhasil diubah')
                : kirim('error', 'Gagal mengubah data ruangan', null, 400);
        }
        if ($method === 'DELETE' && $id > 0) {
            $stmt = mysqli_prepare($koneksi, "DELETE FROM ruangans WHERE id=?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt)
                ? kirim('success', 'Data ruangan berhasil dihapus')
                : kirim('error', 'Gagal menghapus data ruangan', null, 400);
        }
        kirim('error', 'Metode atau parameter tidak valid', null, 405);
        break;

    // ==================== DOKTER ====================
    case 'dokter':
        if ($method === 'GET') {
            kirim('success', null, ambilSemua($koneksi, "SELECT * FROM dokter"));
        }
        if ($method === 'POST') {
            $stmt = mysqli_prepare($koneksi,
                "INSERT INTO dokter (kode_dokter, nama_dokter, spesialisasi, no_izin_praktek, no_telepon, alamat)
                 VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssssss",
                $_POST['kode_dokter'], $_POST['nama_dokter'], $_POST['spesialisasi'],
                $_POST['no_izin_praktek'], $_POST['no_telepon'], $_POST['alamat']);
            mysqli_stmt_execute($stmt)
                ? kirim('success', 'Data dokter berhasil ditambahkan', null, 201)
                : kirim('error', 'Gagal menambahkan data dokter', null, 400);
        }
        if ($method === 'PUT' && $id > 0) {
            $stmt = mysqli_prepare($koneksi,
                "UPDATE dokter SET kode_dokter=?, nama_dokter=?, spesialisasi=?, no_izin_praktek=?, no_telepon=?, alamat=?
                 WHERE id_dokter=?");
            mysqli_stmt_bind_param($stmt, "ssssssi",
                $put['kode_dokter'], $put['nama_dokter'], $put['spesialisasi'],
                $put['no_izin_praktek'], $put['no_telepon'], $put['alamat'], $id);
            mysqli_stmt_execute($stmt)
                ? kirim('success', 'Data dokter berhasil diubah')
                : kirim('error', 'Gagal mengubah data dokter', null, 400);
        }
        if ($method === 'DELETE' && $id > 0) {
            $stmt = mysqli_prepare($koneksi, "DELETE FROM dokter WHERE id_dokter=?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt)
                ? kirim('success', 'Data dokter berhasil dihapus')
                : kirim('error', 'Gagal menghapus data dokter', null, 400);
        }
        kirim('error', 'Metode atau parameter tidak valid', null, 405);
        break;

    // ==================== PASIEN (baca saja, CRUD menyusul) ====================
    case 'pasien':
        if ($method === 'GET') {
            kirim('success', null, ambilSemua($koneksi, "SELECT * FROM pasien"));
        }
        kirim('error', 'Metode tidak didukung', null, 405);
        break;

    // ==================== KUNJUNGAN PASIEN ====================
    case 'kunjungan':
        if ($method === 'GET') {
            $sql = "SELECT k.*, p.nama_pasien, d.nama_dokter, r.namaRuangan
                    FROM kunjungan_pasien k
                    JOIN pasien p ON k.id_pasien = p.id_pasien
                    JOIN dokter d ON k.id_dokter = d.id_dokter
                    JOIN ruangans r ON k.id_ruangan = r.id";
            kirim('success', null, ambilSemua($koneksi, $sql));
        }
        kirim('error', 'Metode tidak didukung', null, 405);
        break;

    case 'normal':
        kirim('success', 'Koneksi API Rumah Sakit Berhasil');
        break;

    default:
        kirim('error', 'Operasi tidak ditemukan', null, 404);
}