<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Rumah Sakit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="text-center mb-4">
        <h2>Dashboard Sistem Informasi Rumah Sakit</h2>
        <p class="text-muted">CRUD dokter dan ruangan melalui REST API: <code>API.php</code></p>
    </div>

    <div id="alertBox"></div>

    <!-- RUANGAN -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Ruangan</h5>
            <button class="btn btn-light btn-sm" onclick="bukaFormRuangan()">+ Tambah Ruangan</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr><th>ID</th><th>Kode</th><th>Nama Ruangan</th><th>Lokasi</th><th>Daya Tampung</th><th style="width:140px">Aksi</th></tr>
                    </thead>
                    <tbody id="tbodyRuangan"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- DOKTER -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Dokter</h5>
            <button class="btn btn-light btn-sm" onclick="bukaFormDokter()">+ Tambah Dokter</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr><th>ID</th><th>Kode</th><th>Nama Dokter</th><th>Spesialisasi</th><th>No. Telepon</th><th style="width:140px">Aksi</th></tr>
                    </thead>
                    <tbody id="tbodyDokter"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PASIEN (baca saja) -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Data Pasien</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr><th>ID</th><th>No. RM</th><th>Nama Pasien</th><th>JK</th><th>Gol. Darah</th><th>No. Telepon</th></tr>
                    </thead>
                    <tbody id="tbodyPasien"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL RUANGAN -->
<div class="modal fade" id="modalRuangan" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" onsubmit="simpanRuangan(event)">
            <div class="modal-header">
                <h5 class="modal-title" id="judulRuangan">Tambah Ruangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="r_id">
                <div class="mb-3"><label class="form-label">Kode Ruangan</label>
                    <input type="text" class="form-control" id="r_kode" required></div>
                <div class="mb-3"><label class="form-label">Nama Ruangan</label>
                    <input type="text" class="form-control" id="r_nama" required></div>
                <div class="mb-3"><label class="form-label">Lokasi</label>
                    <input type="text" class="form-control" id="r_lokasi" required></div>
                <div class="mb-3"><label class="form-label">Daya Tampung</label>
                    <input type="number" min="0" class="form-control" id="r_daya" required></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DOKTER -->
<div class="modal fade" id="modalDokter" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" onsubmit="simpanDokter(event)">
            <div class="modal-header">
                <h5 class="modal-title" id="judulDokter">Tambah Dokter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="d_id">
                <div class="mb-3"><label class="form-label">Kode Dokter</label>
                    <input type="text" class="form-control" id="d_kode" required></div>
                <div class="mb-3"><label class="form-label">Nama Dokter</label>
                    <input type="text" class="form-control" id="d_nama" required></div>
                <div class="mb-3"><label class="form-label">Spesialisasi</label>
                    <input type="text" class="form-control" id="d_spes" required></div>
                <div class="mb-3"><label class="form-label">No. Izin Praktek</label>
                    <input type="text" class="form-control" id="d_izin"></div>
                <div class="mb-3"><label class="form-label">No. Telepon</label>
                    <input type="text" class="form-control" id="d_telp"></div>
                <div class="mb-3"><label class="form-label">Alamat</label>
                    <textarea class="form-control" id="d_alamat" rows="2"></textarea></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const API = 'API.php';
let dataRuangan = [], dataDokter = [];
const modalRuangan = new bootstrap.Modal(document.getElementById('modalRuangan'));
const modalDokter  = new bootstrap.Modal(document.getElementById('modalDokter'));

// Mencegah XSS: semua teks dari database di-escape sebelum ditampilkan
function esc(teks) {
    const el = document.createElement('div');
    el.textContent = teks ?? '';
    return el.innerHTML;
}

function tampilPesan(pesan, jenis = 'success') {
    document.getElementById('alertBox').innerHTML =
        `<div class="alert alert-${jenis} alert-dismissible fade show">${esc(pesan)}
         <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
}

async function ambil(op) {
    const res = await fetch(`${API}?op=${op}`);
    const json = await res.json();
    return json.data || [];
}

async function kirim(op, method, body, id) {
    const url = id ? `${API}?op=${op}&id=${id}` : `${API}?op=${op}`;
    const opsi = { method };
    if (body) {
        opsi.headers = { 'Content-Type': 'application/x-www-form-urlencoded' };
        opsi.body = new URLSearchParams(body).toString();
    }
    const res = await fetch(url, opsi);
    return res.json();
}

// ---------- RENDER ----------
function renderRuangan() {
    const tb = document.getElementById('tbodyRuangan');
    if (!dataRuangan.length) {
        tb.innerHTML = '<tr><td colspan="6" class="text-center">Tidak ada data ruangan.</td></tr>';
        return;
    }
    tb.innerHTML = dataRuangan.map(r => `
        <tr>
            <td>${esc(r.id)}</td><td>${esc(r.kodeRuangan)}</td><td>${esc(r.namaRuangan)}</td>
            <td>${esc(r.lokasi)}</td><td>${esc(r.dayaTampung)}</td>
            <td>
                <button class="btn btn-warning btn-sm" onclick="bukaFormRuangan(${Number(r.id)})">Edit</button>
                <button class="btn btn-danger btn-sm" onclick="hapusRuangan(${Number(r.id)})">Hapus</button>
            </td>
        </tr>`).join('');
}

function renderDokter() {
    const tb = document.getElementById('tbodyDokter');
    if (!dataDokter.length) {
        tb.innerHTML = '<tr><td colspan="6" class="text-center">Tidak ada data dokter.</td></tr>';
        return;
    }
    tb.innerHTML = dataDokter.map(d => `
        <tr>
            <td>${esc(d.id_dokter)}</td><td>${esc(d.kode_dokter)}</td><td>${esc(d.nama_dokter)}</td>
            <td>${esc(d.spesialisasi)}</td><td>${esc(d.no_telepon)}</td>
            <td>
                <button class="btn btn-warning btn-sm" onclick="bukaFormDokter(${Number(d.id_dokter)})">Edit</button>
                <button class="btn btn-danger btn-sm" onclick="hapusDokter(${Number(d.id_dokter)})">Hapus</button>
            </td>
        </tr>`).join('');
}

function renderPasien(data) {
    const tb = document.getElementById('tbodyPasien');
    if (!data.length) {
        tb.innerHTML = '<tr><td colspan="6" class="text-center">Tidak ada data pasien.</td></tr>';
        return;
    }
    tb.innerHTML = data.map(p => `
        <tr>
            <td>${esc(p.id_pasien)}</td><td>${esc(p.no_rm)}</td><td>${esc(p.nama_pasien)}</td>
            <td>${esc(p.jenis_kelamin)}</td><td>${esc(p.golongan_darah)}</td><td>${esc(p.no_telepon)}</td>
        </tr>`).join('');
}

async function muatSemua() {
    try {
        [dataRuangan, dataDokter] = await Promise.all([ambil('ruangan'), ambil('dokter')]);
        renderRuangan();
        renderDokter();
        renderPasien(await ambil('pasien'));
    } catch (e) {
        tampilPesan('Gagal memuat data dari API. Pastikan Apache dan MySQL menyala.', 'danger');
    }
}

// ---------- RUANGAN ----------
function bukaFormRuangan(id) {
    const r = dataRuangan.find(x => Number(x.id) === id) || {};
    document.getElementById('judulRuangan').textContent = id ? 'Edit Ruangan' : 'Tambah Ruangan';
    document.getElementById('r_id').value    = id || '';
    document.getElementById('r_kode').value  = r.kodeRuangan || '';
    document.getElementById('r_nama').value  = r.namaRuangan || '';
    document.getElementById('r_lokasi').value = r.lokasi || '';
    document.getElementById('r_daya').value  = r.dayaTampung || '';
    modalRuangan.show();
}

async function simpanRuangan(e) {
    e.preventDefault();
    const id = document.getElementById('r_id').value;
    const body = {
        kodeRuangan: document.getElementById('r_kode').value,
        namaRuangan: document.getElementById('r_nama').value,
        lokasi:      document.getElementById('r_lokasi').value,
        dayaTampung: document.getElementById('r_daya').value
    };
    const hasil = await kirim('ruangan', id ? 'PUT' : 'POST', body, id);
    modalRuangan.hide();
    tampilPesan(hasil.pesan, hasil.status === 'success' ? 'success' : 'danger');
    muatSemua();
}

async function hapusRuangan(id) {
    if (!confirm('Yakin ingin menghapus ruangan ini?')) return;
    const hasil = await kirim('ruangan', 'DELETE', null, id);
    tampilPesan(hasil.pesan, hasil.status === 'success' ? 'success' : 'danger');
    muatSemua();
}

// ---------- DOKTER ----------
function bukaFormDokter(id) {
    const d = dataDokter.find(x => Number(x.id_dokter) === id) || {};
    document.getElementById('judulDokter').textContent = id ? 'Edit Dokter' : 'Tambah Dokter';
    document.getElementById('d_id').value     = id || '';
    document.getElementById('d_kode').value   = d.kode_dokter || '';
    document.getElementById('d_nama').value   = d.nama_dokter || '';
    document.getElementById('d_spes').value   = d.spesialisasi || '';
    document.getElementById('d_izin').value   = d.no_izin_praktek || '';
    document.getElementById('d_telp').value   = d.no_telepon || '';
    document.getElementById('d_alamat').value = d.alamat || '';
    modalDokter.show();
}

async function simpanDokter(e) {
    e.preventDefault();
    const id = document.getElementById('d_id').value;
    const body = {
        kode_dokter:     document.getElementById('d_kode').value,
        nama_dokter:     document.getElementById('d_nama').value,
        spesialisasi:    document.getElementById('d_spes').value,
        no_izin_praktek: document.getElementById('d_izin').value,
        no_telepon:      document.getElementById('d_telp').value,
        alamat:          document.getElementById('d_alamat').value
    };
    const hasil = await kirim('dokter', id ? 'PUT' : 'POST', body, id);
    modalDokter.hide();
    tampilPesan(hasil.pesan, hasil.status === 'success' ? 'success' : 'danger');
    muatSemua();
}

async function hapusDokter(id) {
    if (!confirm('Yakin ingin menghapus dokter ini?')) return;
    const hasil = await kirim('dokter', 'DELETE', null, id);
    tampilPesan(hasil.pesan, hasil.status === 'success' ? 'success' : 'danger');
    muatSemua();
}

muatSemua();
</script>
</body>
</html>