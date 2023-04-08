<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $postData = json_encode($data);


    $url = 'https://us17.api.mailchimp.com/3.0/lists/' . MAILCHIMP_LIST_ID . '/members';
    $apiKey = MAILCHIMP_API_KEY;
    try {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: apikey ' . $apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);

        $response = curl_exec($ch);

        if ($response === false) {
            printf("cURL error: %s\n", curl_error($ch));
        }

        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        //echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode >= 400) {
            error_log("Errore durante la chiamata a Mailchimp API: " . $response);
        }

        http_response_code($statusCode);
        echo $response;
    } catch (Exception $e) {
        error_log("Errore nell'esecuzione del codice PHP: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error', 'message' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}