<?php
header('Content-Type: text/html; charset=utf-8');

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
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Retrieve services from the database
$sql = "SELECT service_name, description, category, pricing FROM services"; // Modified SQL to fetch only the necessary fields
$stmt = $pdo->prepare($sql);
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Services</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .service {
            border: 1px solid #ccc;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        h2 {
            color: #333;
        }
        strong {
            color: #555;
        }
    </style>
</head>
<body>

<h2>Available Services</h2>

<?php if (empty($services)): ?>
    <p>No services available.</p>
<?php else: ?>
    <?php foreach ($services as $service): ?>
        <div class="service">
            <strong>Service Name:</strong> <?= htmlspecialchars($service['service_name']) ?><br>
            <strong>Description:</strong> <?= htmlspecialchars($service['description']) ?><br>
            <strong>Category:</strong> <?= htmlspecialchars($service['category']) ?><br>
            <strong>Pricing:</strong> $<?= htmlspecialchars($service['pricing']) ?><br>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
