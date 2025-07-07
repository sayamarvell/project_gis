<?php
include 'koneksi.php'; // atau koneksi ke database

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $query = mysqli_query($conn, "DELETE FROM teritory WHERE id = $id");

  if ($query) {
    echo "✅ Marker berhasil dihapus.";
  } else {
    echo "❌ Gagal menghapus marker.";
  }
} else {
  echo "❗ ID tidak ditemukan.";
}
?>
