<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = htmlspecialchars($_POST['description']); // Sanitize input
    $file = $_FILES['file'];

    $whatsappApiUrl = $whatsappApiUrlSendText;
    $data = [
        "chatId" => $chatId,
        "text" => $description,
        "session" => $sessionName
    ];

    if ($file['size'] > 0) {
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        $file_path = $file['tmp_name'];
        $file_mime = mime_content_type($file_path);
        $file_name = basename($file['name']);
        $uploadDir = '/var/www/html/wgc/uploads/';
        $file_url = 'http://139.84.213.199/wgc/uploads/' . $file_name;

        if (!in_array($file_mime, $allowedMimeTypes)) {
            echo "Invalid file type.";
            exit;
        }

        if (move_uploaded_file($file_path, $uploadDir . $file_name)) {
            $file_data = [
                "mimetype" => $file_mime,
                "filename" => $file_name,
                "url" => $file_url
            ];

            $data = [
                "chatId" => $chatId,
                "caption" => $description,
                "session" => $sessionName,
                "file" => $file_data
            ];

            $whatsappApiUrl = $whatsappApiUrlSendImage;
        } else {
            echo "Failed to upload file.";
            exit;
        }
    }

    $payload = json_encode($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $whatsappApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
    }
    curl_close($ch);

    $responseData = json_decode($response, true);

    if (isset($responseData['_data']) && isset($responseData['_data']['id'])) {
        echo 'Message sent successfully to ' . $responseData['_data']['id']['remote'];
    } else {
        echo 'Failed to send message.';
    }

    echo '<br><a href="index.html">Back to Home</a>';
}
?>

