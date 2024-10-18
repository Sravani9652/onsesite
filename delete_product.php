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

// Check if the form was submitted to delete a product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = htmlspecialchars($_POST['product_id']);

    // Prepare SQL query to delete the product from the database
    $sql = "DELETE FROM products WHERE id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':product_id' => $productId]);

    // Redirect back to the main page or display a success message
    header('Location: submit_product.php'); // Redirect to the product list page
    exit;
} else {
    echo "Invalid request.";
}
?>
