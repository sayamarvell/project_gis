<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $koneksi = new mysqli("localhost", "root", "", "data_teritori");
    if ($koneksi->connect_error) {
        die("Koneksi gagal: " . $koneksi->connect_error);
    }

    $nama = $_POST["nama_marker"];
    $provider = $_POST["provider_pemilik"];
    $latitude = (float)$_POST["latitude"];
    $longitude = (float)$_POST["longitude"];
    $provinsi = $_POST["provinsi"];
    $kabupaten = $_POST["kabupaten"];
    $kecamatan = $_POST["kecamatan"];
    $kelurahan = $_POST["kelurahan"];
    $category = $_POST["category"];

    $stmt = $koneksi->prepare("INSERT INTO teritory (nama_marker, provider_pemilik, latitude, longitude, provinsi, kabupaten, kecamatan, kelurahan, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddsssss", $nama, $provider, $latitude, $longitude, $provinsi, $kabupaten, $kecamatan, $kelurahan, $category);

    if ($stmt->execute()) {
        echo "<p style='color:green'>✅ Data berhasil ditambahkan.</p>";
    } else {
        echo "<p style='color:red'>❌ Gagal menambahkan data: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $koneksi->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Marker</title>
  <script>
    async function loadProvinces() {
      const response = await fetch("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json");
      const provinces = await response.json();
      const select = document.getElementById("provinsi");
      provinces.forEach(p => {
        const opt = document.createElement("option");
        opt.value = p.name;
        opt.textContent = p.name;
        opt.dataset.id = p.id;
        select.appendChild(opt);
      });
    }

    async function loadRegencies(provName) {
      const prov = [...document.getElementById("provinsi").options].find(opt => opt.value === provName);
      if (!prov) return;
      const id = prov.dataset.id;
      const response = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${id}.json`);
      const regencies = await response.json();
      const select = document.getElementById("kabupaten");
      select.innerHTML = '<option value="">-- Pilih Kabupaten/Kota --</option>';
      regencies.forEach(k => {
        const opt = document.createElement("option");
        opt.value = k.name;
        opt.textContent = k.name;
        opt.dataset.id = k.id;
        select.appendChild(opt);
      });
      select.disabled = false;
    }

    async function loadDistricts(kabName) {
      const kab = [...document.getElementById("kabupaten").options].find(opt => opt.value === kabName);
      if (!kab) return;
      const id = kab.dataset.id;
      const response = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${id}.json`);
      const districts = await response.json();
      const select = document.getElementById("kecamatan");
      select.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
      districts.forEach(kec => {
        const opt = document.createElement("option");
        opt.value = kec.name;
        opt.textContent = kec.name;
        opt.dataset.id = kec.id;
        select.appendChild(opt);
      });
      select.disabled = false;
    }

    async function loadVillages(kecName) {
      const kec = [...document.getElementById("kecamatan").options].find(opt => opt.value === kecName);
      if (!kec) return;
      const id = kec.dataset.id;
      const response = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${id}.json`);
      const villages = await response.json();
      const select = document.getElementById("kelurahan");
      select.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
      villages.forEach(kel => {
        const opt = document.createElement("option");
        opt.value = kel.name;
        opt.textContent = kel.name;
        select.appendChild(opt);
      });
      select.disabled = false;
    }

    window.onload = loadProvinces;
  </script>
</head>
<body>
  <h2>Tambah Data Marker Baru</h2>
  <form method="post" action="">
    <label>Nama Marker:</label><br>
    <input type="text" name="nama_marker" required><br>

    <label>Provider:</label><br>
    <select name="provider_pemilik" required>
      <option value="Telkomsel">Telkomsel</option>
      <option value="Indosat">Indosat</option>
      <option value="XL Axiata">XL Axiata</option>
      <option value="Smartfren">Smartfren</option>
      <option value="3 (Tri)">3 (Tri)</option>
    </select><br>

    <label>Latitude:</label><br>
    <input type="text" name="latitude" required><br>

    <label>Longitude:</label><br>
    <input type="text" name="longitude" required><br>

    <label>Provinsi:</label><br>
    <select name="provinsi" id="provinsi" required onchange="loadRegencies(this.value)">
      <option value="">-- Pilih Provinsi --</option>
    </select><br>

    <label>Kabupaten/Kota:</label><br>
    <select name="kabupaten" id="kabupaten" required onchange="loadDistricts(this.value)" disabled>
      <option value="">-- Pilih Kabupaten --</option>
    </select><br>

    <label>Kecamatan:</label><br>
    <select name="kecamatan" id="kecamatan" required onchange="loadVillages(this.value)" disabled>
      <option value="">-- Pilih Kecamatan --</option>
    </select><br>

    <label>Kelurahan/Desa:</label><br>
    <select name="kelurahan" id="kelurahan" required disabled>
      <option value="">-- Pilih Kelurahan --</option>
    </select><br>

    <label>Kategori:</label><br>
    <input type="text" name="category" required><br><br>

    <button type="submit">Tambah Data</button>
  </form>
</body>
</html>
