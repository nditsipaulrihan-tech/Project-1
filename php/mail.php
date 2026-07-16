<?php

    $name = htmlspecialchars($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

if(isset($_POST['submit'])) {

    // 1. GET ALL FORM DATA
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $text = $_POST['text'];

    // 2. SET EMAIL DETAILS
    $to = "sexymosquito08@gmail.com"; // <- PUT YOUR EMAIL HERE
    $email_subject = "New Contact : " . $subject;
    
    // 3. BUILD THE EMAIL BODY
    $email_body = "You have received a new message from your website.\n\n";
    $email_body .= "Name: " . $name . "\n";
    $email_body .= "Email: " . $email . "\n";
    $email_body .= "Subject: " . $subject . "\n";
    $email_body .= "Message:\n" . $message . "\n";

    // 4. EMAIL HEADERS
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";

    // 5. SEND EMAIL
    if(mail($to, $email_subject, $email_body, $headers)) {
        echo "Message sent successfully! We will get back to you soon.";
        // header("Location: thankyou.html"); // Optional: redirect after sending
    } else {
        echo "Sorry, something went wrong. Please try again.";
    }

    } else {
    echo "Form not submitted.";
}
?>
