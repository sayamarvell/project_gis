const map = L.map('map').setView([-7.264861, 112.743282], 13);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors'
}).addTo(map);

let markerRefs = [];
let grouped = {};

const provinsiSelect = document.getElementById("provinsiSelect");
const kotaSelect = document.getElementById("kotaSelect");
const kecamatanSelect = document.getElementById("kecamatanSelect");
const kelurahanSelect = document.getElementById("kelurahanSelect");
const markerListContainer = document.getElementById("markerListContainer");

fetch('marker-data.json')
  .then(res => res.json())
  .then(data => {
    data.forEach(m => {
      const category = m.category || "Lainnya";
      if (!grouped[category]) grouped[category] = [];
      grouped[category].push(m);
    });
  });

fetch("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json")
  .then(res => res.json())
  .then(data => {
    data.forEach(prov => {
      const option = document.createElement("option");
      option.value = prov.id;
      option.textContent = prov.name;
      provinsiSelect.appendChild(option);
    });
  });

provinsiSelect.addEventListener("change", function () {
  const provId = this.value;
  kotaSelect.innerHTML = `<option value="">-- Pilih Kota/Kabupaten --</option>`;
  kecamatanSelect.innerHTML = `<option value="">-- Pilih Kecamatan --</option>`;
  kelurahanSelect.innerHTML = `<option value="">-- Pilih Kelurahan --</option>`;
  kotaSelect.disabled = true;
  kecamatanSelect.disabled = true;
  kelurahanSelect.disabled = true;

  if (!provId) return;

  fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provId}.json`)
    .then(res => res.json())
    .then(data => {
      data.forEach(kota => {
        const option = document.createElement("option");
        option.value = kota.id;
        option.textContent = kota.name;
        kotaSelect.appendChild(option);
      });
      kotaSelect.disabled = false;
    });
});

kotaSelect.addEventListener("change", function () {
  const kotaId = this.value;
  kecamatanSelect.innerHTML = `<option value="">-- Pilih Kecamatan --</option>`;
  kelurahanSelect.innerHTML = `<option value="">-- Pilih Kelurahan --</option>`;
  kecamatanSelect.disabled = true;
  kelurahanSelect.disabled = true;

  if (!kotaId) return;

  fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${kotaId}.json`)
    .then(res => res.json())
    .then(data => {
      data.forEach(kec => {
        const option = document.createElement("option");
        option.value = kec.id;
        option.textContent = kec.name;
        kecamatanSelect.appendChild(option);
      });
      kecamatanSelect.disabled = false;
    });
});

kecamatanSelect.addEventListener("change", function () {
  const kecId = this.value;
  kelurahanSelect.innerHTML = `<option value="">-- Pilih Kelurahan --</option>`;
  kelurahanSelect.disabled = true;

  if (!kecId) return;

  fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${kecId}.json`)
    .then(res => res.json())
    .then(data => {
      data.forEach(kel => {
        const option = document.createElement("option");
        option.value = kel.name; 
        option.textContent = kel.name;
        kelurahanSelect.appendChild(option);
      });
      kelurahanSelect.disabled = false;
    });
});

kelurahanSelect.addEventListener("change", function () {
  const selectedKelurahan = this.value;
  markerListContainer.innerHTML = "";

  markerRefs.forEach(ref => {
    if (map.hasLayer(ref.marker)) map.removeLayer(ref.marker);
  });
  markerRefs = [];

  if (!selectedKelurahan) return;

  for (const category in grouped) {
    grouped[category].forEach(m => {
      if (m.category === selectedKelurahan) {
        const marker = L.marker(m.coords).bindPopup(`<h3>${m.name}</h3>`);
        marker.addTo(map);

        markerRefs.push({ marker, name: m.name.toLowerCase(), category });

        const itemDiv = document.createElement("div");
        itemDiv.className = "marker-item";
        itemDiv.setAttribute("data-name", m.name.toLowerCase());
        itemDiv.innerHTML = `<span style="margin-right: 8px;">${m.icon || "📍"}</span><strong>${m.name}</strong>`;
        itemDiv.onclick = () => {
          map.setView(m.coords, 17);
          marker.openPopup();
        };

        markerListContainer.appendChild(itemDiv);
      }
    });
  }
});
