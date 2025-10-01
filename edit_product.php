<?php
include 'config/conn_db.php';

$id = $_GET['id'];
$sql = "SELECT * FROM products WHERE id=$id";
$result = $conn->query($sql);

// Cek apakah ada data produk
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Produk tidak ditemukan!<br>";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
</head>
<body>
    <h2>Form Edit Produk</h2>
    <form action="update.php" method="POST">
        <!-- ID produk disimpan tersembunyi -->
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

        <label for="name">Nama Produk:</label><br>
        <input type="text" id="name" name="name" 
               value="<?php echo htmlspecialchars($row['name']); ?>" required><br><br>

        <label for="description">Deskripsi Produk:</label><br>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($row['description']); ?></textarea><br><br>

        <label for="price">Harga Produk:</label><br>
        <input type="number" id="price" name="price" 
               value="<?php echo $row['price']; ?>" required><br><br>

        <button type="submit">Update Produk</button>
    </form>
</body>
</html>
