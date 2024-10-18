<?php
header('Content-Type: application/json');

// Database connection settings
$host = 'localhost';
$dbname = 'company_info'; // Your database name
$username = 'root'; // Your MySQL username
$password = ''; // Your MySQL password

// Establish a database connection using PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
    exit;
}

// Prepare the SQL insert statement
$sql = "INSERT INTO services (service_name, description, category, pricing) 
        VALUES (:service_name, :description, :category, :pricing)";

$stmt = $pdo->prepare($sql);

// Initialize an array to store errors
$errors = [];

// Loop through the data to insert each service
for ($i = 0; $i < count($_POST['service_name']); $i++) {
    // Bind parameters
    $stmt->bindParam(':service_name', $_POST['service_name'][$i]);
    $stmt->bindParam(':description', $_POST['description'][$i]);
    $stmt->bindParam(':category', $_POST['category'][$i]);
    $stmt->bindParam(':pricing', $_POST['pricing'][$i]);

    // Execute the statement
    if (!$stmt->execute()) {
        $errors[] = "Error saving service: " . implode(", ", $stmt->errorInfo());
    }
}

// Return a response
if (empty($errors)) {
    echo json_encode(['message' => 'Services saved successfully!']);
} else {
    echo json_encode(['error' => 'Some services could not be saved: ' . implode(" | ", $errors)]);
}
?>
