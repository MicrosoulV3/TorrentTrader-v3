TorrentTrader 2.8 updated to use mysqli and php 7.4. This script is not tested on 8+. If you wanna test it, feel free. Debugging code added to the top of backend/functions.php. Uncomment to use. This release is now named TorrentTrader 3.0
![](gitimage/1.jpg)

**OPTIONAL EDIT**
install phpmailer into your backend/ directory. I used composer to do that. You will have a folder afterwards that says "vendor" and inside will be a few files and folders. Thats all you have to do with that. Get that done first and be sure you did it correctly. Any example I post here will be used on ubuntu, but may work on other distros. First off, install composer. I wont go into that here, its easy, just google how to do that. Then afterwards do the following

Putty/ssh into your server and change dir to the backend folder
cd /var/www/yoursitefolder/backend/
install to that dir
composer require phpmailer/phpmailer
You may need to chmod your site to $USER:www-data to allow the installation of phpmailer if you see a error of "permission denied". After the install, you can chmod back to www-data:www:data and you dont have to think about that anymore.

Add this to your config.php replacing the current mail section

// BELOW IS PHPMAILER SETTINGS </br>
$site_config["mail_type"] = "phpmailer"; </br>
$site_config["mail_smtp_host"] = "smtp.gmail.com"; </br>
$site_config["mail_smtp_port"] = 587;              // Use 587 for TLS (465 is for SSL) </br>
$site_config["mail_smtp_ssl"] = true; </br>
$site_config["mail_smtp_auth"] = true; </br>
$site_config["mail_smtp_user"] = "yourmail@gmail.com"; // Your Gmail address </br>
$site_config["mail_smtp_pass"] = "16 characters with no spaces";   // Your Gmail App Password, Not your password to log into your email. Get an APP key, its 16 digits with spaces, remove the spaces. </br>

replace your entire backend/mail.php </br>

 [code]<?php
// Require Composer's autoloader for PHPMailer
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$GLOBALS["TTMail"] = new TTMail;

class TTMail {
    var $type;
    var $smtp_host;
    var $smtp_port;
    var $smtp_ssl;
    var $smtp_auth;
    var $smtp_user;
    var $smtp_pass;

    function __construct() {
        GLOBAL $site_config;

        // Initialize with minimal defaults
        $this->type = "php"; // Fallback to basic PHP mail if config fails
        $this->smtp_host = "";
        $this->smtp_port = 0;
        $this->smtp_ssl = false;
        $this->smtp_auth = false;
        $this->smtp_user = "";
        $this->smtp_pass = "";

        if (isset($site_config["mail_type"])) {
            switch (strtolower($site_config["mail_type"])) {
                case "pear":
                    $this->type = "pear";
                    $this->smtp_host = $site_config["mail_smtp_host"];
                    $this->smtp_port = $site_config["mail_smtp_port"];
                    $this->smtp_ssl = $site_config["mail_smtp_ssl"];
                    $this->smtp_auth = $site_config["mail_smtp_auth"];
                    $this->smtp_user = $site_config["mail_smtp_user"];
                    $this->smtp_pass = $site_config["mail_smtp_pass"];
                    if (!@include_once("Mail.php")) {
                        trigger_error("PEAR Mail not installed.", E_USER_WARNING);
                        $this->type = "php";
                    }
                    break;
                case "phpmailer":
                    $this->type = "phpmailer";
                    $this->smtp_host = $site_config["mail_smtp_host"];
                    $this->smtp_port = $site_config["mail_smtp_port"];
                    $this->smtp_ssl = $site_config["mail_smtp_ssl"];
                    $this->smtp_auth = $site_config["mail_smtp_auth"];
                    $this->smtp_user = $site_config["mail_smtp_user"];
                    $this->smtp_pass = $site_config["mail_smtp_pass"];
                    break;
                case "php":
                default:
                    $this->type = "php";
            }
        }

        // Validate critical fields for PHPMailer
        if ($this->type === "phpmailer" && (empty($this->smtp_user) || empty($this->smtp_pass))) {
            trigger_error("SMTP username or password not provided in config.", E_USER_WARNING);
            $this->type = "php"; // Fallback to basic PHP mail
        }
    }

    function Send($to, $subject, $message, $additional_headers = "", $additional_parameters = "") {
        GLOBAL $site_config;

        if (preg_match("!^From:(.*)!m", $additional_headers, $matches)) {
            $from = trim($matches[1]);
        } else {
            $from = $site_config["SITEEMAIL"] ?? $this->smtp_user;
        }

        $additional_headers = preg_replace("!^From:(.*)!m", "", $additional_headers);
        $additional_headers .= "\nFrom: $from\nReturn-Path: $from";
        $additional_headers = trim($additional_headers);
        $additional_headers = preg_replace("!\n+!", "\n", $additional_headers);

        switch ($this->type) {
            case "pear":
                $headers = array("From" => $from, "Return-Path" => $from, "To" => $to, "Subject" => $subject);
                $params = array("host" => $this->smtp_host, "port" => $this->smtp_port, "auth" => $this->smtp_auth, "username" => $this->smtp_user, "password" => $this->smtp_pass);
                $smtp = Mail::Factory("smtp", $params);
                $smtp->send($to, $headers, $message);
                break;

            case "phpmailer":
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = $this->smtp_host;
                    $mail->Port = $this->smtp_port;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS on 587
                    $mail->SMTPAuth = $this->smtp_auth;
                    $mail->Username = $this->smtp_user;
                    $mail->Password = $this->smtp_pass;
                    $mail->SMTPDebug = SMTP::DEBUG_OFF; // Change to SMTP::DEBUG_SERVER for testing

                    $mail->setFrom($from);
                    $mail->addAddress($to);
                    $mail->Subject = $subject;
                    $mail->Body = $message;

                    $mail->send();
                } catch (Exception $e) {
                    trigger_error("PHPMailer Error: " . $mail->ErrorInfo, E_USER_WARNING);
                }
                break;

            case "php":
                @mail($to, $subject, $message, $additional_headers, $additional_parameters);
                break;
        }
    }
}

function sendmail($to, $subject, $message, $additional_headers = "", $additional_parameters = "") {
    $GLOBALS["TTMail"]->Send($to, $subject, $message, $additional_headers, $additional_parameters);
}

// Optional: Test the mail function. If you uncomment this, it will start sending emails in rapid fashion if everything is set properly. Leave commented unless testing
// sendmail("email@somesite.com", "Test", "This is a test email from Gmail!");
?> [/code]
</br>
Go to your google gmail account that you want to use. https://myaccount.google.com and in the search bar, type "app password" and select that option, create an app password. It will have 16 digits with spaces after every 4 chars, just copy/paste that into your config and remove the spaces so its just one long line of 16 chars with no spaces wrapped with quotes ("). </br>
