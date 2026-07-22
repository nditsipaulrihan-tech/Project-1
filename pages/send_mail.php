<?php
// error_reporting(E_ALL); // Report all errors
// ini_set('display_errors', 1); // Display errors on screen

// // Debugging $_POST data
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// Exit to see the output before mail() attempts to send
// exit(); // You can uncomment this to stop script execution here

$name = $email = $subject = $message = '';

if(!($_SERVER['REQUEST_METHOD']=='POST')){

}
else{

    if(empty($_POST['name'])){
        echo "Input Name<br>";
    }else{
        // $name = sanitize($_POST['name'])
        $name = trim($_POST['name']);
        $name = stripslashes($_POST['name']);
        $name = htmlspecialchars($_POST['name']);
    } if(!preg_match("/^[a-zA-Z-' ]*$/",$name)){
        echo "Invalid Name<br>";
    }

    if(empty($_POST['email'])){
        echo "Required Email<br>";
    } else{
        $email = trim($_POST['email']);
        $email = stripslashes($_POST['email']);
        $email = htmlspecialchars($_POST['email']);
    } if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo "Invalid Email<br>";
    }

    // if(){

    // }else{

    // }

    if(empty($_POST['message'])){
        echo "Your Message Objective Isn't Mentioned <br>";
    }else{
        $message = trim($_POST['message']);
        $message = stripslashes($_POST['message']);
        $message = htmlspecialchars($_POST['message']);
    }

$to = "sexymosquito08@gmail.com";
$from = $subject;
$headers = "From: $from\r\nReply-To: $email\r\n";
$body = "Name: $name\nEmail: $email\nSubject: $subject\nMessage:\n$message";

if(mail($to, "New Contact: $from", $body, $headers)){
    header("Location: ../index.html");
    echo "<script>alert('Mail Sent, We will get back to you');</script>";
    exit;

} else {
    echo "Error: Mail could not be sent.";
}}
?>