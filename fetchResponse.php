<?php
// Database connection settings
$host = 'localhost';
$dbname = 'company_info';
$username = 'root';
$password = '';

// Establish a database connection using PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
    exit;
}

// Get the company ID from the request (this can be passed as a query parameter)
$companyId = $_GET['id'] ?? null;

if ($companyId) {
    // Fetch the API response from the database
    $sql = "SELECT api_response FROM api_responses WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $companyId]);

    $response = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($response) {
        echo json_encode(['message' => $response['api_response']]);
    } else {
        echo json_encode(['error' => 'No response found.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request.']);
}
