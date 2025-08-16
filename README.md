## 📢 TorrentTrader 3.0 Release

TorrentTrader 2.8 has been upgraded to **TorrentTrader 3.0**, featuring:

- ✅ Updated to use **MySQLi**
- ✅ Compatible with **PHP 7.4**
- ⚠️ **Not yet tested on PHP 8+** — feel free to test and report any issues!

For debugging, code has been added to the top of `backend/functions.php`.  
Just **uncomment** the relevant lines to enable it.

---

![TorrentTrader Screenshot](gitimage/1.jpg)
## Below is optional, but recommended
## Configure PHPMailer for your project to send emails using Gmail Secure Apps. You need to have 2 Factor Authentication turned on in your gmail account that you are using. If you dont have this turned on, you will not be able to do this

## Step 1: Install PHPMailer using Composer

Install PHPMailer into your `backend/` directory. This guide assumes you're using **Ubuntu**, but it may work on other Linux distros too.

First, install [Composer](https://getcomposer.org/) if you haven’t already. You can Google how to install it for your system.

Then SSH into your server (make sure you are in the backend/ folder. Thats where the phpmailer files need to be, because your mail.php is there. Its just easier):

```bash
cd /var/www/yoursitefolder/backend/
```
Run this command while in the backend folder
```bash
composer require phpmailer/phpmailer
```

> **Note:** If you get a "permission denied" error, you may need to temporarily change ownership:
>
> ```bash
> sudo chown -R $USER:www-data /var/www/yoursitefolder
> ```
>
> After installation, you can revert it:
>
> ```bash
> sudo chown -R www-data:www-data /var/www/yoursitefolder
> ```

---

## Step 2: Update `config.php`

Replace your current mail settings with:

```php
// BELOW IS PHPMAILER SETTINGS
$site_config["mail_type"] = "phpmailer";
$site_config["mail_smtp_host"] = "smtp.gmail.com";
$site_config["mail_smtp_port"] = 587;
$site_config["mail_smtp_ssl"] = true;
$site_config["mail_smtp_auth"] = true;
$site_config["mail_smtp_user"] = "yourmail@gmail.com"; // Your Gmail address
$site_config["mail_smtp_pass"] = "16characterswithnospaces"; // Gmail App Password (not your login password)
```

---

## Step 3: Replace `backend/mail.php`

Replace the entire contents of `backend/mail.php` with the following:

```php
<?php
// Require Composer's autoloader for PHPMailer
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class TTMail {
    var $type;
    var $smtp_host;
    var $smtp_port;
    var $smtp_ssl;
    var $smtp_auth;
    var $smtp_user;
    var $smtp_pass;
    var $daily_limit = 500; // Gmail's free account limit
    var $sent_today = 0; // Track emails sent today

    function __construct() {
        global $site_config;

        // Initialize defaults
        $this->type = "php"; // Fallback to PHP mail
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
                    $this->smtp_host = $site_config["mail_smtp_host"] ?? "";
                    $this->smtp_port = $site_config["mail_smtp_port"] ?? 0;
                    $this->smtp_ssl = $site_config["mail_smtp_ssl"] ?? false;
                    $this->smtp_auth = $site_config["mail_smtp_auth"] ?? false;
                    $this->smtp_user = $site_config["mail_smtp_user"] ?? "";
                    $this->smtp_pass = $site_config["mail_smtp_pass"] ?? "";
                    if (!@include_once("Mail.php")) {
                        trigger_error("PEAR Mail not installed.", E_USER_WARNING);
                        $this->type = "php";
                    }
                    break;
                case "phpmailer":
                    $this->type = "phpmailer";
                    $this->smtp_host = $site_config["mail_smtp_host"] ?? "smtp.gmail.com";
                    $this->smtp_port = $site_config["mail_smtp_port"] ?? 587;
                    $this->smtp_ssl = $site_config["mail_smtp_ssl"] ?? true;
                    $this->smtp_auth = $site_config["mail_smtp_auth"] ?? true;
                    $this->smtp_user = $site_config["mail_smtp_user"] ?? "";
                    $this->smtp_pass = $site_config["mail_smtp_pass"] ?? "";
                    // Validate Gmail settings
                    if ($this->smtp_host === "smtp.gmail.com") {
                        if ($this->smtp_port != 587) {
                            trigger_error("Gmail requires port 587 for TLS.", E_USER_WARNING);
                            $this->smtp_port = 587;
                        }
                        if (empty($this->smtp_user) || empty($this->smtp_pass)) {
                            trigger_error("Gmail SMTP requires username and App Password.", E_USER_WARNING);
                            $this->type = "php"; // Fallback to PHP mail
                        }
                    }
                    break;
                case "php":
                default:
                    $this->type = "php";
            }
        }

        // Load sent email count for today (placeholder; implement if needed)
        $this->sent_today = $this->getSentEmailCount();
    }

    /**
     * Get the number of emails sent today (placeholder; implement with your storage)
     * @return int
     */
    private function getSentEmailCount() {
        // Users should implement database or file storage to track emails sent today
        // Example: SELECT email_count FROM email_log WHERE sent_date = CURDATE()
        return 0; // Placeholder
    }

    /**
     * Increment the sent email count (placeholder; implement with your storage)
     */
    private function incrementSentEmailCount() {
        // Users should implement database or file storage to update count
        // Example: INSERT INTO email_log (sent_date, email_count) VALUES (CURDATE(), 1) ON DUPLICATE KEY UPDATE email_count = email_count + 1
        $this->sent_today++;
    }

    /**
     * Wrap plain-text message in a responsive HTML email template
     * @param string $message The email body content
     * @param string $subject The email subject
     * @return array [formatted_message, is_html]
     */
    private function formatEmail($message, $subject) {
        global $site_config;
        $is_html = preg_match('/<!DOCTYPE|<html|<body|<div|<p/i', $message);
        if ($is_html) {
            return [$message, true];
        }

        // Use $site_config["SITEURL"] for domain-agnostic "Visit Site" link
        $site_url = $site_config["SITEURL"] ?? "https://example.com";
        $html_message = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($subject) . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { background-color: #007bff; padding: 20px; text-align: center; border-top-left-radius: 8px; border-top-right-radius: 8px; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; }
        .content { padding: 20px; }
        .content p { color: #333333; font-size: 16px; line-height: 1.5; }
        .button { display: inline-block; padding: 10px 20px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px; font-size: 16px; }
    </style>
</head>
<body>
    <table class="container" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td class="header">
                <h1>' . htmlspecialchars($subject) . '</h1>
            </td>
        </tr>
        <tr>
            <td class="content">
                <p>' . nl2br(htmlspecialchars($message)) . '</p>
                <p style="text-align: center; margin: 20px 0;">
                    <a href="' . htmlspecialchars($site_url) . '" class="button">Visit Site</a>
                </p>
            </td>
        </tr>
    </table>
</body>
</html>';

        return [$html_message, true];
    }

    /**
     * Send a single email
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $message Email body
     * @param string $additional_headers Additional headers
     * @param string $additional_parameters Additional parameters
     * @return bool Success or failure
     */
    function Send($to, $subject, $message, $additional_headers = "", $additional_parameters = "") {
        global $site_config;

        // Validate recipient email
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            error_log("Invalid recipient email: $to");
            trigger_error("Invalid recipient email: $to", E_USER_WARNING);
            return false;
        }

        // Sanitize subject to prevent header injection
        $subject = str_replace(["\r", "\n"], '', $subject);

        // Set From address
        $from = $site_config["SITEEMAIL"] ?? $this->smtp_user;
        if (empty($from)) {
            error_log("No From address specified in site_config[SITEEMAIL] or smtp_user");
            trigger_error("No From address specified.", E_USER_WARNING);
            return false;
        }
        if (preg_match("!^From:(.*)!m", $additional_headers, $matches)) {
            $from = trim($matches[1]);
        }

        // For Gmail, ensure From matches SMTP user
        if ($this->type === "phpmailer" && $this->smtp_host === "smtp.gmail.com" && $from !== $this->smtp_user) {
            error_log("From address must match SMTP user for Gmail: using $this->smtp_user");
            $from = $this->smtp_user;
        }

        $additional_headers = preg_replace("!^From:(.*)!m", "", $additional_headers);
        $additional_headers .= "\nFrom: $from\nReturn-Path: $from\nReply-To: $from";
        $additional_headers = trim(preg_replace("!\n+!", "\n", $additional_headers));

        // Format message for PHPMailer
        $is_html = false;
        if ($this->type === "phpmailer") {
            [$formatted_message, $is_html] = $this->formatEmail($message, $subject);
        } else {
            $formatted_message = $message;
        }

        switch ($this->type) {
            case "pear":
                $headers = ["From" => $from, "Return-Path" => $from, "To" => $to, "Subject" => $subject];
                $params = [
                    "host" => $this->smtp_host,
                    "port" => $this->smtp_port,
                    "auth" => $this->smtp_auth,
                    "username" => $this->smtp_user,
                    "password" => $this->smtp_pass,
                ];
                try {
                    $smtp = Mail::factory("smtp", $params);
                    $result = $smtp->send($to, $headers, $message);
                    if (PEAR::isError($result)) {
                        error_log("PEAR Mail Error: " . $result->getMessage());
                        trigger_error("PEAR Mail Error: " . $result->getMessage(), E_USER_WARNING);
                        return false;
                    }
                    error_log("Email sent to $to via PEAR: $subject");
                    return true;
                } catch (Exception $e) {
                    error_log("PEAR Mail Exception: " . $e->getMessage());
                    trigger_error("PEAR Mail Exception: " . $e->getMessage(), E_USER_WARNING);
                    return false;
                }
                break;

            case "phpmailer":
                // Check daily sending limit for Gmail
                if ($this->smtp_host === "smtp.gmail.com" && $this->sent_today >= $this->daily_limit) {
                    error_log("Daily sending limit of {$this->daily_limit} emails reached.");
                    trigger_error("Daily sending limit reached.", E_USER_WARNING);
                    return false;
                }

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = $this->smtp_host;
                    $mail->Port = $this->smtp_port;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->SMTPAuth = $this->smtp_auth;
                    $mail->Username = $this->smtp_user;
                    $mail->Password = $this->smtp_pass;
                    $mail->SMTPDebug = SMTP::DEBUG_OFF;

                    $mail->setFrom($from, $site_config["SITENAME"] ?? "Site");
                    $mail->addAddress($to);
                    $mail->addReplyTo($from, $site_config["SITENAME"] ?? "Site");
                    $mail->Subject = $subject;
                    $mail->MessageID = '<' . uniqid() . '@' . parse_url($site_url, PHP_URL_HOST) . '>';

                    if ($is_html) {
                        $mail->isHTML(true);
                        $mail->Body = $formatted_message;
                        $mail->AltBody = strip_tags($message);
                    } else {
                        $mail->isHTML(false);
                        $mail->Body = $formatted_message;
                    }

                    $mail->send();
                    $this->incrementSentEmailCount();
                    error_log("Email sent to $to via PHPMailer: $subject");
                    return true;
                } catch (Exception $e) {
                    error_log("PHPMailer Error: " . $mail->ErrorInfo);
                    trigger_error("PHPMailer Error: " . $mail->ErrorInfo, E_USER_WARNING);
                    return false;
                }
                break;

            case "php":
                $result = @mail($to, $subject, $message, $additional_headers, $additional_parameters);
                if ($result) {
                    error_log("Email sent to $to via PHP mail: $subject");
                } else {
                    error_log("Failed to send email to $to via PHP mail");
                }
                return $result;
                break;

            default:
                error_log("Invalid mail type: $this->type");
                return false;
        }
    }

    /**
     * Send mass emails with throttling for Gmail
     * @param array $recipients List of recipient emails
     * @param string $subject Email subject
     * @param string $message Email body
     * @return bool Success or failure
     */
    function SendMassEmails($recipients, $subject, $message) {
        foreach ($recipients as $to) {
            if ($this->type === "phpmailer" && $this->smtp_host === "smtp.gmail.com" && $this->sent_today >= $this->daily_limit) {
                error_log("Daily sending limit of {$this->daily_limit} emails reached.");
                trigger_error("Daily sending limit reached.", E_USER_WARNING);
                return false;
            }
            $this->Send($to, $subject, $message);
            if ($this->type === "phpmailer" && $this->smtp_host === "smtp.gmail.com") {
                usleep(100000); // 100ms delay for Gmail to avoid rate limits
            }
        }
        return true;
    }

    /**
     * Test email functionality
     * @param string $to Recipient email
     * @return bool Success or failure
     */
    function testMail($to) {
        global $site_config;
        $site_url = $site_config["SITEURL"] ?? "https://example.com";
        return $this->Send($to, "Test Email", "This is a test email to verify your mail configuration.\nVisit: " . $site_url);
    }
}

function sendmail($to, $subject, $message, $additional_headers = "", $additional_parameters = "") {
    $GLOBALS["TTMail"] = new TTMail;
    return $GLOBALS["TTMail"]->Send($to, $subject, $message, $additional_headers, $additional_parameters);
}
?>
```

---

## Step 4: Set Up Gmail App Password

Make sure 2 factor authentication is on for the gmail account you want to use.

Go to [https://myaccount.google.com](https://myaccount.google.com), type **"app password"** into the search bar, and create a new app password. DO NOT use the password that logs into the email account

Google will give you a 16-character password **with spaces**. Remove the spaces and use it as your `mail_smtp_pass` in `config.php`.

> **Example:**
> ```text
> abcd efgh ijkl mnop → "abcdefghijklmnop"
> ```
