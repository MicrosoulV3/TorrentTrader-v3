<?php

$GLOBALS["TTMail"] = new TTMail;

class TTMail {
	var $type;

	var $smtp_host;
	var $smtp_port = 25;
	var $smtp_ssl = false;
	var $smtp_auth = false;

	var $smtp_user;
	var $smtp_pass;

	function __construct () {
		GLOBAL $site_config;

		switch (strtolower($site_config["mail_type"])) {
			case "pear":
				$this->smtp_ssl = $site_config["mail_smtp_ssl"];

				if ($this->smtp_ssl) {
					$this->smtp_host = "ssl://".$site_config["mail_smtp_host"];
				} else {
					$this->smtp_host = $site_config["mail_smtp_host"];
				}
                
                $this->type = "pear";
				$this->smtp_port = $site_config["mail_smtp_port"];
				$this->smtp_auth = $site_config["mail_smtp_auth"];
				$this->smtp_user = $site_config["mail_smtp_user"];
				$this->smtp_pass = $site_config["mail_smtp_pass"];

				if (!@include_once("Mail.php")) {
					trigger_error("Config is set to use PEAR Mail but it is not installed (or include_path is wrong).", E_USER_WARNING);
					$this->type = "php";
				}
			break;
			case "php":
			default:
				$this->type = "php";
		}
	}

	function Send ($to, $subject, $message, $additional_headers = "", $additional_parameters = "") {
		GLOBAL $site_config;

		if (preg_match("!^From:(.*)!m", $additional_headers, $matches)) {
			$from = trim($matches[1]);
		} else {
			$from = "$site_config[SITEEMAIL]";
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

				$mail = $smtp->send($to, $headers, $message);

			break;
			case "php":
				@mail($to, $subject, $message, $additional_headers, $additional_parameters);
			break;
		}
	}
}

function sendmail ($to, $subject, $message, $additional_headers = "", $additional_parameters = "") {
	$GLOBALS["TTMail"]->Send($to, $subject, $message, $additional_headers, $additional_parameters);
}
?>