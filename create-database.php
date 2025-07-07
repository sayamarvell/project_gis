<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah Marker</title>
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&display=swap" rel="stylesheet">
  <style>
      body {
    background: linear-gradient(135deg, #b71c1c, #ffffff);
    font-family: 'Raleway', sans-serif;
    color: #b71c1c;
    padding: 40px 20px;
    opacity: 0;
    overflow-y: auto; /* ✅ ubah dari hidden ke auto */
    transition: opacity 1s ease-in-out;
  }
    .back-button {
      display: inline-block;
      margin-bottom: 20px;
      padding: 10px 20px;
      background-color: #ccc;
      color: #333;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .back-button:hover {
      background-color: #aaa;
    }

    body.show {
      opacity: 1;
      overflow: auto;
    }

    .container {
      background: #ffffff;
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
      max-width: 600px;
      margin: auto;
    }

    h2 {
      text-align: center;
      font-weight: 700;
      margin-bottom: 30px;
      color: #b71c1c;
    }

    label {
      font-weight: 600;
      margin-top: 15px;
    }
    select {
    appearance: none;
    background-color: white;
    position: relative;
    z-index: 1;
  }

    .select-group {
    display: flex;
    flex-direction: column;
    gap: 1px;
    margin-top: 10px;
    }


    select, input[type="text"], button {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 8px;
      border: 1px solid #ccc;
      transition: 0.3s;
    }
    select:focus {
    border-color: #b71c1c;
    box-shadow: 0 0 5px rgba(183, 28, 28, 0.5);
    z-index: 10; /* ✅ untuk memastikan menu dropdown tampil di atas */
    }

    select:focus, input:focus {
      border-color: #b71c1c;
      box-shadow: 0 0 5px rgba(183, 28, 28, 0.5);
    }

    button[type="submit"] {
      background-color: #b71c1c;
      color: white;
      font-weight: bold;
      margin-top: 25px;
      cursor: pointer;
    }

    button[type="submit"]:hover {
      background-color: #a31212;
    }

    select:disabled, input:disabled {
      background-color: #f0eae3;
    }
  </style>
</head>
<body>

  <div class="container">
  <a href="admin.php" class="back-button">🔙 Kembali ke Admin</a>
    <h2>🗺️ Tambah Data Marker Baru</h2>
   <form method="post" action="">
  <label>Nama Marker:</label>
  <input type="text" name="nama_marker" required />

  <label>Provider:</label>
  <select name="provider_pemilik" required>
    <option value="Telkomsel">Telkomsel</option>
    <option value="Indosat">Indosat</option>
    <option value="XL Axiata">XL Axiata</option>
    <option value="Smartfren">Smartfren</option>
    <option value="3 (Tri)">3 (Tri)</option>
  </select>

  <label>Latitude:</label>
  <input type="text" name="latitude" required />

  <label>Longitude:</label>
  <input type="text" name="longitude" required />

  <!-- ✅ Dibungkus dalam .select-group -->
  <div class="select-group">
    <label>Provinsi:</label>
    <select name="provinsi" id="provinsi" required onchange="loadRegencies(this.value)">
      <option value="">-- Pilih Provinsi --</option>
    </select>

    <label>Kabupaten/Kota:</label>
    <select name="kabupaten" id="kabupaten" required onchange="loadDistricts(this.value)" disabled>
      <option value="">-- Pilih Kabupaten --</option>
    </select>

    <label>Kecamatan:</label>
    <select name="kecamatan" id="kecamatan" required onchange="loadVillages(this.value)" disabled>
      <option value="">-- Pilih Kecamatan --</option>
    </select>

    <label>Kelurahan/Desa:</label>
    <select name="kelurahan" id="kelurahan" required disabled>
      <option value="">-- Pilih Kelurahan --</option>
    </select>
  </div>

  <label>Kategori:</label>
  <input type="text" name="category" required />

  <button type="submit">📌 Tambah Marker</button>
</form>
  </div>

  <script>
  // Efek Fade-In
  window.addEventListener("load", () => {
    document.body.classList.add("show");
    loadProvinces(); // Pindah ke sini agar tidak overwrite
  });

  // Load Provinsi
  async function loadProvinces() {
    const res = await fetch("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json");
    const data = await res.json();
    const select = document.getElementById("provinsi");
    data.forEach(p => {
      const opt = document.createElement("option");
      opt.value = p.name;
      opt.textContent = p.name;
      opt.dataset.id = p.id;
      select.appendChild(opt);
    });
  }

  // Load Kabupaten/Kota saat pilih Provinsi
  async function loadRegencies(provName) {
    const prov = [...document.getElementById("provinsi").options].find(opt => opt.value === provName);
    if (!prov) return;

    // Reset dropdown bawahnya
    resetSelect("kabupaten");
    resetSelect("kecamatan");
    resetSelect("kelurahan");

    const id = prov.dataset.id;
    const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${id}.json`);
    const data = await res.json();
    const select = document.getElementById("kabupaten");
    data.forEach(k => {
      const opt = document.createElement("option");
      opt.value = k.name;
      opt.textContent = k.name;
      opt.dataset.id = k.id;
      select.appendChild(opt);
    });
    select.disabled = false;
  }

  // Load Kecamatan saat pilih Kabupaten
  async function loadDistricts(kabName) {
    const kab = [...document.getElementById("kabupaten").options].find(opt => opt.value === kabName);
    if (!kab) return;

    resetSelect("kecamatan");
    resetSelect("kelurahan");

    const id = kab.dataset.id;
    const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${id}.json`);
    const data = await res.json();
    const select = document.getElementById("kecamatan");
    data.forEach(kec => {
      const opt = document.createElement("option");
      opt.value = kec.name;
      opt.textContent = kec.name;
      opt.dataset.id = kec.id;
      select.appendChild(opt);
    });
    select.disabled = false;
  }

  // Load Kelurahan saat pilih Kecamatan
  async function loadVillages(kecName) {
    const kec = [...document.getElementById("kecamatan").options].find(opt => opt.value === kecName);
    if (!kec) return;

    resetSelect("kelurahan");

    const id = kec.dataset.id;
    const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${id}.json`);
    const data = await res.json();
    const select = document.getElementById("kelurahan");
    data.forEach(kel => {
      const opt = document.createElement("option");
      opt.value = kel.name;
      opt.textContent = kel.name;
      select.appendChild(opt);
    });
    select.disabled = false;
  }

  // Fungsi reset & disable dropdown
  function resetSelect(id) {
    const select = document.getElementById(id);
    if (select) {
      select.innerHTML = `<option value="">-- Pilih ${select.name.charAt(0).toUpperCase() + select.name.slice(1)} --</option>`;
      select.disabled = true;
    }
  }
</script>
</body>
</html>
