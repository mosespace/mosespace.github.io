<?php

require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable("../"); // env folder path
$dotenv->load();

// $contact->recaptcha_secret_key = '6LfjUCwjAAAAAHBCMJRtINRHhPSvuOgQ6yCMASAb'; // not sure

$firstname = isset($_POST['firstname']) ? $_POST["firstname"] : null;
$lastname = isset($_POST['lastname']) ? $_POST["lastname"] : null;
$email = isset($_POST["email"]) ? $_POST["email"] : null;
$contact = isset($_POST["contact"]) ? $_POST["contact"] : "";
$message = isset($_POST["message"]) ? $_POST["message"] : "";


// validate required values
if(!($firstname && $lastname && $email && $message)){
    die("Required values missing");
}

// check if email is valid
if($email == "email"){
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Email not valid");
    }
}

// create full name
$fullname = $firstname . " " .  $lastname;

// mail setup
$subject =  isset($_ENV['CONTACT_FROM_SUBJECT']) ? $_ENV['CONTACT_FROM_SUBJECT'] : "";

// email body
$body = "A user has sent you a message<br>";
$body .= "Fullname: " . $fullname . "<br>";
$body .= "Email Contact: " . $email . "<br>";
$body .= "Phone Contact: " . $contact . "<br>";
$body .= "The user says: " . $message;


$mail = new PHPMailer(false);

try{
    // mail config

    $mail->isSMTP();
    $mail->Host       = isset($_ENV['MAIL_HOST']) ? $_ENV['MAIL_HOST'] : "";
    $mail->SMTPAuth   = true;
    $mail->Username   = isset($_ENV['MAIL_USERNAME']) ? $_ENV['MAIL_USERNAME'] : "";
    $mail->Password   = isset($_ENV['MAIL_PASSWORD']) ? $_ENV['MAIL_PASSWORD'] : "";
    $mail->Port       = isset($_ENV['MAIL_PORT']) ? $_ENV['MAIL_PORT'] : "";

    $from = isset($_ENV['MAIL_FROM_NAME']) ? $_ENV['MAIL_FROM_NAME'] : "";
    $fromEmail = isset($_ENV['MAIL_FROM_ADDRESS']) ? $_ENV['MAIL_FROM_ADDRESS'] : "";
    $to =  isset($_ENV['MAIL_TO_ADDRESS']) ? $_ENV['MAIL_TO_ADDRESS'] : "";

    //Recipients
    $mail->setFrom($fromEmail, $from);
    $mail->addAddress($to, $to);     // Name is optional
    $mail->addReplyTo($fromEmail, $fromEmail);


    // Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $body;
    $mail->AltBody = $body;

    $mail->send();

    echo 'Message has been sent';

}catch (Exception $e){

    // die on error
    die($e->getMessage());
}



?>
