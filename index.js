const map = L.map('map').setView([-7.264861, 112.743282], 13);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors'
}).addTo(map);

const markerList = document.getElementById("markerList");
const sidebar = document.getElementById("sidebar");
const mapDiv = document.getElementById("map");

const markerRefs = [];

fetch('marker-data.json')
  .then(res => res.json())
  .then(data => {
    data.forEach((m, i) => {
      const marker = L.marker(m.coords).addTo(map).bindPopup(`<h3>${m.name}</h3>`);
      markerRefs.push({ marker, name: m.name.toLowerCase() });

      const li = document.createElement("li");
      li.className = "marker-item";
      li.setAttribute("data-name", m.name.toLowerCase());

      li.innerHTML = `
        <div class="marker-icon">${m.icon || "📍"}</div>
        <div class="marker-info">${m.name}</div>
      `;

      li.addEventListener("click", () => {
        map.setView(m.coords, 17);
        marker.openPopup();
        toggleSidebar(false);
      });

      markerList.appendChild(li);
    });
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
