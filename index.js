const map = L.map('map').setView([-7.264861, 112.743282], 13);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors'
}).addTo(map);

const markerList = document.getElementById("markerList");
const sidebar = document.getElementById("sidebar");
const mapDiv = document.getElementById("map");

let markerRefs = [];

fetch('marker-data.json')
  .then(res => res.json())
  .then(data => {
    const grouped = {};

    data.forEach(m => {
      const category = m.category || "Lainnya";
      if (!grouped[category]) grouped[category] = [];
      grouped[category].push(m);
    });

    for (const category in grouped) {
      const groupWrapper = document.createElement("div");
      groupWrapper.className = "group-wrapper";

      const groupTitle = document.createElement("div");
      groupTitle.className = "group-title";
      groupTitle.innerText = category;

      const groupList = document.createElement("ul");
      groupList.className = "group-list";
      groupList.style.maxHeight = "0px";

      // Buat semua marker tapi belum ditampilkan di map
      grouped[category].forEach(m => {
        const marker = L.marker(m.coords).bindPopup(`<h3>${m.name}</h3>`);
        markerRefs.push({ marker, name: m.name.toLowerCase(), category });

        const li = document.createElement("li");
        li.className = "marker-item";
        li.setAttribute("data-name", m.name.toLowerCase());

        li.innerHTML = `
          <div class="marker-icon">${m.icon || "📍"}</div>
          <div class="marker-info">${m.name}</div>
        `;

        li.addEventListener("click", () => {
          const ref = markerRefs.find(ref => ref.name === m.name.toLowerCase());
          if (ref) {
            map.setView(m.coords, 17);
            ref.marker.openPopup();
            toggleSidebar(false);
          }
        });

        groupList.appendChild(li);
      });

      groupTitle.addEventListener("click", () => {
        const isExpanded = groupList.classList.contains("expanded");

        // Tutup semua kategori
        document.querySelectorAll(".group-list").forEach(list => {
          list.style.maxHeight = "0px";
          list.classList.remove("expanded");
        });

        // Hapus semua marker dari map
        markerRefs.forEach(ref => {
          if (map.hasLayer(ref.marker)) map.removeLayer(ref.marker);
        });

        if (!isExpanded) {
          // Expand dan tambahkan marker kategori ini
          groupList.style.maxHeight = groupList.scrollHeight + "px";
          groupList.classList.add("expanded");

          markerRefs
            .filter(ref => ref.category === category)
            .forEach(ref => ref.marker.addTo(map));
        }
      });

      groupWrapper.appendChild(groupTitle);
      groupWrapper.appendChild(groupList);
      markerList.appendChild(groupWrapper);
    }
  });

function toggleSidebar(force = null) {
  const open = sidebar.classList.contains("active");
  const shouldOpen = force !== null ? force : !open;
  if (shouldOpen) {
    sidebar.classList.add("active");
    mapDiv.classList.add("sidebar-open");
  } else {
    sidebar.classList.remove("active");
    mapDiv.classList.remove("sidebar-open");
  }
}

function filterMarkers() {
  const input = document.getElementById("searchInput").value.toLowerCase();
  document.querySelectorAll(".marker-item").forEach(item => {
    const name = item.getAttribute("data-name");
    item.style.display = name.includes(input) ? "flex" : "none";
  });
}

document.addEventListener('click', function (e) {
  const isSidebar = sidebar.contains(e.target);
  const isToggle = e.target.classList.contains("sidebar-toggle");
  if (!isSidebar && !isToggle) {
    toggleSidebar(false);
  }
});
