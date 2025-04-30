<?php
session_start();
include 'database.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Handle image upload
    $targetDir = "products/";
    $imageName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $imageName;
    $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Validate image type (optional, but recommended)
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedTypes)) {
        echo "Only JPG, JPEG, PNG & GIF files are allowed.";
        exit;
    }

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
        // Insert into DB with image filename
        $stmt = $conn->prepare("INSERT INTO products (name, price, image, stock) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdsi", $name, $price, $imageName, $stock);
        if ($stmt->execute()) {
            echo "<script>alert('Product added successfully!'); window.location.href = 'productlist.php';</script>";
        } else {
            echo "Database error: " . $conn->error;
        }
    } else {
        echo "Failed to upload image.";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product - Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .add-product-container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 3em 1em;
    background: #f7e5e0;
}

.add-product-box {
    background: white;
    padding: 2em;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 600px;
}

h2 {
    text-align: center;
    color: #f78ca2;
    margin-bottom: 1.5em;
}

label {
    display: block;
    margin-bottom: 0.5em;
    color: #f56c8d;
    font-weight: bold;
}

input[type="text"], input[type="number"], input[type="file"] {
    width: 100%;
    padding: 0.8em;
    margin: 0.6em 0;
    border: 1px solid #f0c7d1;
    border-radius: 8px;
    font-size: 1em;
}

textarea {
    width: 100%;
    padding: 0.8em;
    margin: 0.6em 0;
    border: 1px solid #f0c7d1;
    border-radius: 8px;
    font-size: 1em;
    height: 120px;
    resize: none;
}

button[type="submit"] {
    width: 100%;
    padding: 1em;
    background-color: #f78ca2;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1.2em;
}

button[type="submit"]:hover {
    background-color: #f56c8d;
}

/* Make it responsive */
@media (max-width: 600px) {
    .add-product-box {
        padding: 1.5em;
    }
}
    </style>
</head>
<body>
<div class="add-product-container">
    <div class="add-product-box">
    <header>
        <h1>Add a New Cake</h1>
        <nav>
            <a href="productlist.php">View Products</a>
            <a href="admin_dashb.php">Go Back?</a>
        </nav>
    </header>
    <main>
        <form method="post" enctype="multipart/form-data">
            <label for="name">Product Name:</label>
            <input type="text" name="name" required>

            <label for="price">Price:</label>
            <input type="number" name="price" required>

            <label for="stock">Stock:</label>
            <input type="number" name="stock" required>

            <label for="image">Product Image:</label>
            <input type="file" name="image" accept="image/*" required>

            <button type="submit">Add Product</button>
        </form>
    </main>
    </div>
</div>
</body>
</html>