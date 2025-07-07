<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Panel - WebGIS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes typing {
      from { width: 0; }
      to { width: 100%; }
    }

    .splash {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: #fff;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  transition: opacity 0.5s ease;
}


    .splash.fade-out {
      opacity: 0;
      visibility: hidden;
    }

    body {
      background-color: #b71c1c;
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
      padding: 20px;
      opacity: 0;
      transition: opacity 1s ease-in-out;
      overflow: hidden;
      animation: fadeIn 1s forwards;
    }

    body.show {
      opacity: 1;
      overflow: auto;
    }

    h1.typing {
      width: 20ch;
      overflow: hidden;
      white-space: nowrap;
      border-right: 2px solid #fff;
      animation: typing 2s steps(20) 1s forwards;
    }

    .table-wrapper {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      padding: 15px;
      backdrop-filter: blur(6px);
      max-height: 500px;
      overflow-x: auto;
    }

    .btn {
      margin: 5px;
      transition: transform 0.2s ease-in-out;
    }

    .btn:hover {
      transform: scale(1.05);
    }

    canvas {
      background: #fff;
      border-radius: 10px;
      padding: 10px;
    }

    #searchInput {
  width: 100%;
  max-width: 300px;
  margin: 30px auto 15px; /* Tambahkan margin-top agar turun ke bawah */
  padding: 8px;
  border-radius: 8px;
  border: none;
  display: block;
}

.section-title {
  text-align: center;
  margin-bottom: 20px;
  margin-top: 40px;
  font-size: 1.5rem;
  font-weight: bold;
}
.title-center {
  text-align: center;
  margin-bottom: 30px;
  font-weight: bold;
  font-size: 2.5rem;
}

    .table-dark th {
      background-color: #8e0000;
    }
  </style>
</head>
<body>

  <!-- Splash Screen -->
  <div id="splash" class="splash">
    <div class="text-center">
      <div class="spinner-border text-danger" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2 text-danger fw-bold">Loading WebGIS Admin...</p>
    </div>
  </div>

  <h1 class="title-center mb-4">🛰️ Admin Panel - WebGIS</h1>

  <div class="text-center mb-4">
    <button onclick="createmarker()" class="btn btn-light">➕ Create Marker</button>
    <button onclick="deletemarker()" class="btn btn-dark">🗑️ Delete All Markers</button>
    <button onclick="exportCSV()" class="btn btn-warning text-dark">📥 Export CSV</button>
    <a href="logout.php" class="btn btn-outline-light">🚪 Logout</a>
  </div>

  <h3>📊 Jumlah Marker per Provider</h3>
  <canvas id="providerChart" height="150"></canvas>

  <input type="text" id="searchInput" placeholder="🔍 Cari marker...">

  <h3 class="section-title">📍 Data Tabel <code>teritory</code></h3>
  <div class="table-wrapper">
    <table class="table table-bordered table-hover text-white" id="dataTable">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nama Marker</th>
          <th>Provider</th>
          <th>Latitude</th>
          <th>Longitude</th>
          <th>Provinsi</th>
          <th>Kabupaten</th>
          <th>Kecamatan</th>
          <th>Kelurahan</th>
          <th>Category</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <script>
    window.addEventListener("load", () => {
      setTimeout(() => {
        document.getElementById("splash").classList.add("fade-out");
        document.body.classList.add("show");
      }, 1500);
    });

    let allData = [];

    function loadTable() {
      fetch("get-markers.php")
        .then(res => res.json())
        .then(data => {
          allData = data;
          const tbody = document.querySelector("#dataTable tbody");
          tbody.innerHTML = "";
          data.forEach((row, index) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
            <td>${index + 1}</td>
            <td>${row.nama_marker}</td>
            <td>${row.provider_pemilik}</td>
            <td>${row.latitude}</td>
            <td>${row.longitude}</td>
            <td>${row.provinsi}</td>
            <td>${row.kabupaten}</td>
            <td>${row.kecamatan}</td>
            <td>${row.kelurahan}</td>
            <td>${row.category}</td>
            <td>
            <button onclick="deleteSingleMarker('${row.id}')" class="btn btn-sm btn-danger">🗑️</button>
          </td>
        `;

            tbody.appendChild(tr);
          });
          drawChart(data);
        });
    }

    function drawChart(data) {
      const count = {};
      data.forEach(row => {
        const provider = row.provider_pemilik;
        count[provider] = (count[provider] || 0) + 1;
      });

      new Chart(document.getElementById('providerChart').getContext('2d'), {
        type: 'bar',
        data: {
          labels: Object.keys(count),
          datasets: [{
            label: 'Jumlah Marker',
            data: Object.values(count),
            backgroundColor: '#ffffff',
            borderColor: '#b71c1c',
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false }
          }
        }
      });
    }

    function createmarker() {
      window.location.href = "create-database.php";
    }

    function deletemarker() {
      if (confirm("Yakin ingin menghapus seluruh marker?")) {
        fetch("delete-marker.php")
          .then(res => res.text())
          .then(alert)
          .then(loadTable);
      }
    }

    function deleteSingleMarker(id) {
      if (confirm("Yakin ingin menghapus marker ini?")) {
      fetch(`delete-marker.php?id=${id}`)
        .then(res => res.text())
        .then(response => {
          alert(response);
         loadTable();
          });
       }
     }


    function exportCSV() {
      let csv = "ID,Nama Marker,Provider,Latitude,Longitude,Provinsi,Kabupaten,Kecamatan,Kelurahan,Category\n";
      allData.forEach((row, index) => {
        csv += `${index + 1},"${row.nama_marker}","${row.provider_pemilik}",${row.latitude},${row.longitude},"${row.provinsi}","${row.kabupaten}","${row.kecamatan}","${row.kelurahan}","${row.category}"\n`;
      });
      const blob = new Blob([csv], { type: "text/csv" });
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = "marker-data.csv";
      a.click();
    }

    document.getElementById("searchInput").addEventListener("input", function () {
      const value = this.value.toLowerCase();
      const rows = document.querySelectorAll("#dataTable tbody tr");
      rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(value) ? "" : "none";
      });
    });

    loadTable();
  </script>
</body>
</html>
