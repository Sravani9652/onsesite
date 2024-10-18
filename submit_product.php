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
    // Sanitize and retrieve form input
    $productName = htmlspecialchars($_POST['product_name']);
    $description = htmlspecialchars($_POST['description']);
    $price = htmlspecialchars($_POST['price']);
    $category = htmlspecialchars($_POST['category']);
    
    // Handle image upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpName = $_FILES['product_image']['tmp_name'];
        $imageName = basename($_FILES['product_image']['name']);
        $imageSize = $_FILES['product_image']['size'];
        $imageType = $_FILES['product_image']['type'];
        
        // Set a directory to store the uploaded images
        $uploadDir = 'uploads/';
        $uploadFilePath = $uploadDir . $imageName;
        
        // Move the uploaded file to the designated directory
        if (move_uploaded_file($imageTmpName, $uploadFilePath)) {
            // Prepare SQL query to insert the product into the database
            $sql = "INSERT INTO products (product_name, description, price, category, image_path)
                    VALUES (:product_name, :description, :price, :category, :image_path)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':product_name' => $productName,
                ':description' => $description,
                ':price' => $price,
                ':category' => $category,
                ':image_path' => $uploadFilePath
            ]);
            
            // Display the submitted product details
            echo "<h1>Product Added Successfully!</h1>";
            echo "<p><strong>Product Name:</strong> $productName</p>";
            echo "<p><strong>Description:</strong> $description</p>";
            echo "<p><strong>Price:</strong> $price</p>";
            echo "<p><strong>Category:</strong> $category</p>";
            echo "<p><strong>Image:</strong></p><img src='$uploadFilePath' alt='Product Image' style='max-width:300px;'><br>";
        } else {
            echo "Failed to upload image.";
        }
    } else {
        echo "Image upload error: " . $_FILES['product_image']['error'];
    }
}
