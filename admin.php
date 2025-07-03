<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - WebGIS</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
      padding: 20px;
    }
    .table-wrapper {
      max-height: 500px;
      overflow-y: auto;
    }
  </style>
</head>
<body>
  <h1 class="mb-4">Admin Panel</h1>

  <div class="mb-3">
    <button onclick="createmarker()" class="btn btn-success">Create marker</button>
    <button onclick="deletemarker()" class="btn btn-danger">Delete marker</button>
  </div>

  <h3>Data Tabel `teritory`</h3>
  <div class="table-wrapper">
    <table class="table table-bordered" id="dataTable">
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
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <script>
    function loadTable() {
      fetch("get-markers.php")
        .then(res => res.json())
        .then(data => {
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
            `;
            tbody.appendChild(tr);
          });
        });
    }

    function createmarker() {
    // Redirect ke halaman form input marker
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

    loadTable();
  </script>
</body>
</html>
