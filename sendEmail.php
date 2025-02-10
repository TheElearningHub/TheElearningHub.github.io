<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = strip_tags(trim($_POST['name']));
    $name = str_replace(array("\r","\n"),array(" "," "),$name);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (empty($name) OR !filter_var($email, FILTER_VALIDATE_EMAIL) OR empty($message)) {
        $response['message'] = 'There was a problem with your submission. Please complete the form and try again.';
    } else {
        $recipient = "contact.info.e.learning@gmail.com";
        $subject = "New contact from $name";
        $email_content = "Name: $name\n\n";
        $email_content .= "Email: $email\n\n";
        $email_content .= "Subject: $subject\n\n";
        $email_content .= "Message:\n$message\n\n";
        $email_headers = "From: $name <$email>\n\n";
        $email_headers .= "From: $name <$email> \r\n\n Reply-To: $email \r\n\n";

        // SMTP Configuration
        $smtp_host = 'smtp.gmail.com';
        $smtp_port = 587;
        $smtp_username = 'contact.info.e.learning@gmail.com'; // Replace with your email
        $smtp_password = 'unya sohg yofv niwh'; // Replace with your app password

        // Send email via SMTP
        $smtp_response = sendEmailViaSMTP($smtp_host, $smtp_port, $smtp_username, $smtp_password, $recipient, $subject, $email_content, $email_headers);

        if ($smtp_response === true) {
            // Auto-reply content
            $auto_reply_subject = "Thank you for contacting us!!";
            $auto_reply_message = "Hello $name,\n\n";
            $auto_reply_message .= "Thank you for contacting us. We have received your message and will contact you as soon as possible.\n\n";
            $auto_reply_message .= "Sincerely yours,\n";
            $auto_reply_message .= "E-Learning";

            $auto_reply_headers = "From: E-Learning <contact.info.e.learning@gmail.com>\r\n";
            $auto_reply_headers .= "Content-type: text/plain\r\n";

            // Send auto-reply email
            $auto_reply_response = sendEmailViaSMTP($smtp_host, $smtp_port, $smtp_username, $smtp_password, $email, $auto_reply_subject, $auto_reply_message, $auto_reply_headers);

            if ($auto_reply_response === true) {
                $response['success'] = true;
                $response['message'] = 'Thank you very much. Your message has been sent.';
            } else {
                error_log('Failed to send auto-reply email: ' . $auto_reply_response);
                $response['message'] = 'Oops! A problem occurred with the automatic reply, but your message was sent.';
            }
        } else {
            error_log('Failed to send email: ' . $smtp_response);
            $response['message'] = 'Oops! A problem occurred with the automatic reply, but your message was sent.';
        }
    }
} else {
    $response['message'] = 'There was a problem with your submission. Please try again.';
}

header('Content-Type: application/json');
echo json_encode($response);

function sendEmailViaSMTP($host, $port, $username, $password, $to, $subject, $message, $headers) {
    $socket = fsockopen($host, $port, $errno, $errstr, 10);
    if (!$socket) {
        return "Could not open socket: $errstr ($errno)";
    }

    $response = getSMTPResponse($socket);
    if (strpos($response, '220') === false) {
        return "Error connecting to SMTP server: $response";
    }

    fputs($socket, "EHLO $host\r\n");
    $response = getSMTPResponse($socket);
    if (strpos($response, '250') === false) {
        return "Error during EHLO: $response";
    }

    fputs($socket, "STARTTLS\r\n");
    $response = getSMTPResponse($socket);
    if (strpos($response, '220') === false) {
        return "Error starting TLS: $response";
    }

    stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

    fputs($socket, "EHLO $host\r\n");
    $response = getSMTPResponse($socket);
    if (strpos($response, '250') === false) {
        return "Error during EHLO after STARTTLS: $response";
    }

    fputs($socket, "AUTH LOGIN\r\n");
    $response = getSMTPResponse($socket);
    if (strpos($response, '334') === false) {
        return "Error initiating authentication: $response";
    }

    fputs($socket, base64_encode($username) . "\r\n");
    $response = getSMTPResponse($socket);
    if (strpos($response, '334') === false) {
        return "Error sending username: $response";
    }

    fputs($socket, base64_encode($password) . "\r\n");
    $response = getSMTPResponse($socket);
    if (strpos($response, '235') === false) {
        return "Error sending password: $response";
    }

    fputs($socket, "MAIL FROM: <$username>\r\n");
    $response = getSMTPResponse($socket);
    if (strpos($response, '250') === false) {
        return "Error setting sender: $response";
    }

    fputs($socket, "RCPT TO: <$to>\r\n");
    $response = getSMTPResponse($socket);
    if (strpos($response, '250') === false) {
        return "Error setting recipient: $response";
    }

    fputs($socket, "DATA\r\n");
    $response = getSMTPResponse($socket);
    if (strpos($response, '354') === false) {
        return "Error initiating data: $response";
    }

    fputs($socket, "Subject: $subject\r\n$headers\r\n\r\n$message\r\n.\r\n");
    $response = getSMTPResponse($socket);
    if (strpos($response, '250') === false) {
        return "Error sending message: $response";
    }

    fputs($socket, "QUIT\r\n");
    fclose($socket);

    return true;
}

function getSMTPResponse($socket) {
    $response = '';
    while ($str = fgets($socket, 515)) {
        $response .= $str;
        if (substr($str, 3, 1) == ' ') {
            break;
        }
    }
    return $response;
}
?>
