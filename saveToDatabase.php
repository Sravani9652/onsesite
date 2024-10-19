<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$host = 'localhost';
$db = 'company_info';
$user = 'root';
$pass = '';
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve input data
    $companyName = htmlspecialchars($_POST['company_name']);
    $year = htmlspecialchars($_POST['year_established']);
    $logo = htmlspecialchars($_POST['logo']);
    $phoneNumber = htmlspecialchars($_POST['phoneNumber']);
    $description = htmlspecialchars($_POST['description']);
    $industry = htmlspecialchars($_POST['industry']);
    $location = htmlspecialchars($_POST['location']);

    // Prepare and execute the insertion into the 'companies' table
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

        // Prepare data for API requests
        $apiKey = 'AIzaSyCxz8nQkAAYStq6MNcRpXf3nJfjggyR9Ec';
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey;

        // Prepare field array for API requests
        $fields = [
            'Company Name' => $companyName,
            'Year Established' => $year,
            'Logo' => $logo,
            'Phone Number' => $phoneNumber,
            'Description' => $description,
            'Industry' => $industry,
            'Location' => $location,
        ];

        $queryParams = []; // Initialize an array for query parameters

        // Execute API requests for each field
        foreach ($fields as $field => $value) {
            $data = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => "Generate a description based on the following information: $field: $value."
                            ]
                        ]
                    ]
                ]
            ];

            // Use CURL for the API request
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo json_encode(['error' => 'API request failed: ' . curl_error($ch)]);
                exit;
            }
            curl_close($ch);

            // Decode the API response
            $response = json_decode($result, true);

            // Collect the text response, limiting to 30 words
            if (isset($response['candidates']) && count($response['candidates']) > 0) {
                $candidate = $response['candidates'][0];
                if (isset($candidate['content']['parts'][0]['text'])) {
                    $responseText = $candidate['content']['parts'][0]['text'];
                    // Limit response to 30 words
                    $limitedResponse = implode(' ', array_slice(explode(' ', $responseText), 0, 30));
                    $queryParams[$field] = $limitedResponse; // Store response for each field
                } else {
                    $queryParams[$field] = "Error: Invalid response structure.";
                }
            } else {
                $queryParams[$field] = "Error: No candidates found.";
            }
        }

        // Redirect to response.php with the responses as a query string
        $queryString = http_build_query($queryParams);
        header("Location: response.php?$queryString");
        exit;
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }

    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method Not Allowed']);
exit;
?>
