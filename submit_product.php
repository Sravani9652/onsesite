<?php
// Database connection settings
$host = 'localhost';
$dbname = 'company_info';
$username = 'root';
$password = '';

try {
    // Establish a database connection using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form has products
    if (isset($_POST['product_name']) && is_array($_POST['product_name'])) {
        foreach ($_POST['product_name'] as $index => $productName) {
            // Sanitize and retrieve form input
            $description = htmlspecialchars($_POST['description'][$index]);
            $price = htmlspecialchars($_POST['price'][$index]);
            $category = htmlspecialchars($_POST['category'][$index]);

            // Handle image upload
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'][$index] === UPLOAD_ERR_OK) {
                $imageTmpName = $_FILES['product_image']['tmp_name'][$index];
                $imageName = basename($_FILES['product_image']['name'][$index]);
                $uploadDir = 'uploads/';
                $uploadFilePath = $uploadDir . $imageName;

                // Move the uploaded file to the designated directory
                if (move_uploaded_file($imageTmpName, $uploadFilePath)) {
                    // Prepare SQL query to insert the product into the database
                    $sql = "INSERT INTO products (product_name, description, price, category, image_path) VALUES (:product_name, :description, :price, :category, :image_path)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':product_name' => $productName,
                        ':description' => $description,
                        ':price' => $price,
                        ':category' => $category,
                        ':image_path' => $uploadFilePath
                    ]);
                } else {
                    echo "Failed to upload image for product: $productName";
                }
            } else {
                echo "Image upload error for product: $productName - " . $_FILES['product_image']['error'][$index];
            }
        }
    } else {
        echo "No products to add.";
    }
}

// Fetch all existing products from the database
$sql = "SELECT * FROM products";
$stmt = $pdo->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Display all products
if ($products) {
    echo "<h1>All Products:</h1><div style='display: flex; flex-wrap: wrap;'>"; // Start flex container

    foreach ($products as $product) {
        echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px; width: 200px;'>";
        echo "<strong>Product Name:</strong> " . htmlspecialchars($product['product_name']) . "<br>";
        echo "<strong>Description:</strong> " . htmlspecialchars($product['description']) . "<br>";
        echo "<strong>Price:</strong> " . htmlspecialchars($product['price']) . "<br>";
        echo "<strong>Category:</strong> " . htmlspecialchars($product['category']) . "<br>";
        echo "<strong>Image:</strong><br><img src='" . htmlspecialchars($product['image_path']) . "' alt='Product Image' style='max-width:150px;'><br>";
        
        // Add a delete button
        echo "<form method='POST' action='delete_product.php' style='display:inline;'>";
        echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($product['id']) . "'>"; // Assuming 'id' is the primary key
        echo "<input type='submit' value='Delete' onclick='return confirm(\"Are you sure you want to delete this product?\");'>";
        echo "</form>";
        
        echo "</div>"; // End of product details box
    }

    echo "</div>"; // End flex container
} else {
    echo "No products found.";
}
?>
