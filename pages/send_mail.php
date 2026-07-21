<?php
// error_reporting(E_ALL); // Report all errors
// ini_set('display_errors', 1); // Display errors on screen

// // Debugging $_POST data
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// Exit to see the output before mail() attempts to send
// exit(); // You can uncomment this to stop script execution here

$name = $_POST['name']?? '';
$email = $_POST['email']?? '';
$subject = $_POST['subject']?? '';
$message = $_POST['message']?? '';

$to = "sexymosquito08@gmail.com";
$from = $subject;
$headers = "From: $from\r\nReply-To: $email\r\n";
$body = "Name: $name\nEmail: $email\nSubject: $subject\nMessage:\n$message";

if(mail($to, "New Contact: $from", $body, $headers)){
    echo "<b>Success!</b> Message sent <br>
    Go back to <a href='../index.html'>Home Page</a>";

} else {
    echo "Error: Mail could not be sent. Check C:/xampp/sendmail/error.log";
}
?>