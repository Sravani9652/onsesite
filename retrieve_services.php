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

// Retrieve specified fields from the services table
try {
    $sql = "SELECT service_name, description, category, pricing FROM services";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if any services are found
    if ($services) {
        echo json_encode($services);
    } else {
        echo json_encode(['message' => 'No services found.']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
    exit;
}
?>
