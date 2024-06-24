<?php
// Assuming this is your PHP script handling form submission

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $description = htmlspecialchars($_POST['description']); // Sanitize input
    $file = $_FILES['file'];

    // Check if a file is uploaded
    if ($file['size'] > 0) {
        // Process the file if uploaded
        $file_path = $file['tmp_name'];
        $file_mime = mime_content_type($file_path);
        $file_name = basename($file['name']);
        $file_url = 'http://192.168.29.157:8003/uploads/' . $file_name; // Adjust with your domain and path

        // Move the uploaded file to your server
        if (move_uploaded_file($file_path, '/var/www/html/wpc/uploads/' . $file_name)) {
            $file_data = [
                "mimetype" => $file_mime,
                "filename" => $file_name,
                "url" => $file_url
            ];

            // Create the payload for sending image
            $data = [
                "chatId" => $chatId,
                "caption" => $description,
                "session" => $sessionName,
                "file" => $file_data
            ];

            $whatsappApiUrl = $whatsappApiUrlSendImage; // Use send image API endpoint
        } else {
            echo "Failed to upload file.";
            exit;
        }
    } else {
        // Create the payload for sending text
        $data = [
            "chatId" => $chatId,
            "text" => $description,
            "session" => $sessionName
        ];

        $whatsappApiUrl = $whatsappApiUrlSendText; // Use send text API endpoint
    }

    // Convert data to JSON
    $payload = json_encode($data);

    // Initialize cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $whatsappApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    // Execute the request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
    }

    // Close cURL session
    curl_close($ch);

    // Decode the response JSON
    $responseData = json_decode($response, true);

    // Check if message was successfully sent
    if (isset($responseData['_data']) && isset($responseData['_data']['id'])) {
        echo 'Message sent successfully to ' . $responseData['_data']['id']['remote'];
    } else {
        echo 'Failed to send message.';
    }

    // Provide feedback to the user
    echo '<br><a href="index.html">Back to Home</a>';
}
?>

