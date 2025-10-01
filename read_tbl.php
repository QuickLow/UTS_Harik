<?php
include 'config/conn_db.php';

$sql = "SELECT id, name, description, price FROM products";
$result = $conn->query($sql);

// Cek apakah ada data produk

echo "<a href='form_product.html'>Tambah Produk</a><br><br>";

if ($result->num_rows > 0) {
    // buka tabel
    echo "<table border='1'>";
    echo "<tr>
            <th>No</th> 
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Action</th>
          </tr>";
    
    // looping data produk
    $no = 1;
   while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>".$no++."</td>
            <td>".$row["name"]."</td>
            <td>".$row["description"]."</td>
            <td>".$row["price"]."</td>
            <td>
                <a href='edit_product.php?id=".$row["id"]."'>Edit</a> | 
                <a href='delete.php?id=".$row["id"]."' onclick='return confirm(\"Yakin hapus?\")'>Delete</a>
            </td>
          </tr>";

    
    }

    // tutup tabel
    echo "</table>";
} else {
    echo "0 result - Data not found";
}

// Close connection
$conn->close();
?>
