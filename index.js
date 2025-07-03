const map = L.map('map').setView([-7.264861, 112.743282], 13);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors'
}).addTo(map);

let markerRefs = [];
let allMarkers = [];

// Gunakan marker custom (ikon rumah sakit berwarna merah)
const hospitalIcon = L.icon({
  iconUrl: 'https://cdn3.iconfinder.com/data/icons/technology-135/100/tower-512.png',  // Gambar rumah sakit yang kamu upload
  iconSize: [45, 45],       // Ukuran marker (ubah sesuai kebutuhan)
  iconAnchor: [15, 45],     // Titik anchor pada marker
});

const provinsiSelect = document.getElementById("provinsiSelect");
const kotaSelect = document.getElementById("kotaSelect");
const kecamatanSelect = document.getElementById("kecamatanSelect");
const kelurahanSelect = document.getElementById("kelurahanSelect");
const markerListContainer = document.getElementById("markerListContainer");

// Ambil data marker
fetch('get-markers.php')
  .then(res => res.json())
  .then(data => {
    allMarkers = data;
    renderMarkers(allMarkers);
  });

// Ambil daftar provinsi
fetch("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json")
  .then(res => res.json())
  .then(data => {
    data.forEach(prov => {
      const option = document.createElement("option");
      option.value = prov.name;
      option.textContent = prov.name;
      provinsiSelect.appendChild(option);
    });
  });

  function renderMarkers(data) {
    markerRefs.forEach(ref => map.removeLayer(ref));
    markerRefs = [];
    markerListContainer.innerHTML = "";
  
    data.forEach(m => {
      const marker = L.marker([m.latitude, m.longitude], { icon: hospitalIcon })
        .bindPopup(`<h3>${m.nama_marker}</h3><p>${m.provider_pemilik}</p>`);
      marker.addTo(map);
      markerRefs.push(marker);
  
      const item = document.createElement("div");
      item.className = "marker-item";
      item.innerHTML = `<span style="margin-right:8px;">📍</span><strong>${m.nama_marker}</strong>`;
      item.onclick = () => {
        map.setView([m.latitude, m.longitude], 17);
        marker.openPopup();
      };
      markerListContainer.appendChild(item);
    });
  }

provinsiSelect.addEventListener("change", function () {
  const selectedProv = this.value;

  kotaSelect.innerHTML = `<option value="">-- Pilih Kota/Kabupaten --</option>`;
  kecamatanSelect.innerHTML = `<option value="">-- Pilih Kecamatan --</option>`;
  kelurahanSelect.innerHTML = `<option value="">-- Pilih Kelurahan --</option>`;
  kotaSelect.disabled = kecamatanSelect.disabled = kelurahanSelect.disabled = true;

  markerRefs.forEach(ref => map.removeLayer(ref));
  markerRefs = [];
  markerListContainer.innerHTML = "";

  const filtered = allMarkers.filter(m => m.provinsi === selectedProv);
  filtered.forEach(m => {
    const marker = L.marker([m.latitude, m.longitude], { icon: hospitalIcon })
      .bindPopup(`<h3>${m.nama_marker}</h3><p>${m.provider_pemilik}</p>`);
    marker.addTo(map);
    markerRefs.push(marker);

    const item = document.createElement("div");
    item.className = "marker-item";
    item.innerHTML = `<span style="margin-right:8px;">📍</span><strong>${m.nama_marker}</strong>`;
    item.onclick = () => {
      map.setView([m.latitude, m.longitude], 17);
      marker.openPopup();
    };
    markerListContainer.appendChild(item);
  });

  const kotaList = [...new Set(filtered.map(m => m.kabupaten))];
  kotaList.forEach(kota => {
    const option = document.createElement("option");
    option.value = kota;
    option.textContent = kota;
    kotaSelect.appendChild(option);
  });
  kotaSelect.disabled = false;
});

kotaSelect.addEventListener("change", function () {
  const selectedKota = this.value;

  kecamatanSelect.innerHTML = `<option value="">-- Pilih Kecamatan --</option>`;
  kelurahanSelect.innerHTML = `<option value="">-- Pilih Kelurahan --</option>`;
  kecamatanSelect.disabled = kelurahanSelect.disabled = true;

  const kecList = [...new Set(allMarkers.filter(m => m.kabupaten === selectedKota).map(m => m.kecamatan))];
  kecList.forEach(kec => {
    const option = document.createElement("option");
    option.value = kec;
    option.textContent = kec;
    kecamatanSelect.appendChild(option);
  });
  kecamatanSelect.disabled = false;
});

kecamatanSelect.addEventListener("change", function () {
  const selectedKec = this.value;

  kelurahanSelect.innerHTML = `<option value="">-- Pilih Kelurahan --</option>`;
  kelurahanSelect.disabled = true;

  const kelList = [...new Set(allMarkers.filter(m => m.kecamatan === selectedKec).map(m => m.kelurahan))];
  kelList.forEach(kel => {
    const option = document.createElement("option");
    option.value = kel;
    option.textContent = kel;
    kelurahanSelect.appendChild(option);
  });
  kelurahanSelect.disabled = false;
});

kelurahanSelect.addEventListener("change", function () {
  const selectedKelurahan = this.value;

  markerRefs.forEach(ref => map.removeLayer(ref));
  markerRefs = [];
  markerListContainer.innerHTML = "";

  const filtered = allMarkers.filter(m => m.kelurahan === selectedKelurahan);
  filtered.forEach(m => {
    const marker = L.marker([m.latitude, m.longitude], { icon: hospitalIcon })
      .bindPopup(`<h3>${m.nama_marker}</h3><p>${m.provider_pemilik}</p>`);
    marker.addTo(map);
    markerRefs.push(marker);

    const item = document.createElement("div");
    item.className = "marker-item";
    item.innerHTML = `<span style="margin-right:8px;">📍</span><strong>${m.nama_marker}</strong>`;
    item.onclick = () => {
      map.setView([m.latitude, m.longitude], 17);
      marker.openPopup();
    };
    markerListContainer.appendChild(item);
  });
});
