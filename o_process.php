<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $description = $_POST['description'];
    $file = $_FILES['file'];

    // Initialize cURL
    $ch = curl_init();

    // Check if a file is uploaded
    if ($file['size'] > 0) {
        // Process the file if uploaded
        $file_path = $file['tmp_name'];
        $file_mime = mime_content_type($file_path);
        $file_url = 'https://example.com/uploads/' . basename($file['name']); // Change to your actual file URL

        // Move the uploaded file to your server (you need to implement the upload directory)
        if (move_uploaded_file($file_path, '/path/to/uploads/' . basename($file['name']))) { // Update with actual path
            $file_data = [
                "mimetype" => $file_mime,
                "filename" => $file['name'],
                "url" => $file_url
            ];

            // Create the payload for sending image
            $data = [
                "chatId" => $chatId,
                "caption" => $description,
                "session" => $sessionName,
                "file" => $file_data
            ];

            $payload = json_encode($data);

            // Set cURL options for sending image
            curl_setopt($ch, CURLOPT_URL, $whatsappApiUrlSendImage);
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

        $payload = json_encode($data);

        // Set cURL options for sending text
        curl_setopt($ch, CURLOPT_URL, $whatsappApiUrlSendText);
    }

    // Common cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    // Execute the request
    $response = curl_exec($ch);

    // Check for cURL errors
    if(curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
    }

    // Close cURL session
    curl_close($ch);

    // Output the response
    echo $response;

    // Provide feedback to the user
    echo '<a href="index.html">Back to Home</a>';
}
?>

