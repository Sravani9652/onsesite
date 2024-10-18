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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form input
    $companyName = htmlspecialchars($_POST['company_name']);
    $year = htmlspecialchars($_POST['year_established']);
    $logo = htmlspecialchars($_POST['logo']);
    $phoneNumber = htmlspecialchars($_POST['phoneNumber']);
    $description = htmlspecialchars($_POST['description']);
    $industry = htmlspecialchars($_POST['industry']);
    $location = htmlspecialchars($_POST['location']);

    // Insert data into the 'companies' table
    $sql = "INSERT INTO companies (company_name, year_established, logo, phone_number, description, industry, location)
            VALUES (:company_name, :year_established, :logo, :phone_number, :description, :industry, :location)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':company_name' => $companyName,
            ':year_established' => $year,
            ':logo' => $logo,
            ':phone_number' => $phoneNumber,
            ':description' => $description,
            ':industry' => $industry,
            ':location' => $location
        ]);

        // Get the ID of the inserted company record (This will be used in the API response table)
        $companyId = $pdo->lastInsertId();

        // Prepare the data for the API request
        $apiKey = 'AIzaSyCxz8nQkAAYStq6MNcRpXf3nJfjggyR9Ec'; // Replace with your actual API key
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey;

        $data = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => "Company Name: $companyName, Established in: $year, Logo: $logo, Phone Number: $phoneNumber, Description: $description, Industry: $industry, Location: $location."
                        ]
                    ]
                ]
            ]
        ];

        // Prepare options for the API request
        $options = [
            'http' => [
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
            ]
        ];

        // Execute the request
        $context  = stream_context_create($options);
        $result = @file_get_contents($url, false, $context); // Suppress warnings

        // Handle errors in API request
        if ($result === FALSE) {
            $error = error_get_last(); // Capture the last error
            echo json_encode(['error' => 'API request failed: ' . $error['message']]);
            exit;
        }

        // Decode the JSON response
        $response = json_decode($result, true);

        // Output the generated paragraph
        if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
            $generatedText = htmlspecialchars($response['candidates'][0]['content']['parts'][0]['text']);
            
            // Store the API response in the 'api_responses' table
            $responseSql = "INSERT INTO api_responses (id, api_response) VALUES (:id, :api_response)";
            $responseStmt = $pdo->prepare($responseSql);
            $responseStmt->execute([
                ':id' => $companyId,  // Use company id as the foreign key reference
                ':api_response' => $generatedText
            ]);

            // Return the response as JSON
            echo json_encode(['message' => $generatedText]);
        } else {
            echo json_encode(['error' => 'API Error: ' . json_encode($response)]);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }

    exit;
}

// If the request method is not POST, return a 405 Method Not Allowed response
http_response_code(405);
echo json_encode(['error' => 'Method Not Allowed']);
exit;
