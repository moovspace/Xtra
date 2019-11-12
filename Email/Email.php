<?php
namespace Xtra\Email;

use \PDO;
use \Exception;
use Xtra\Mysql\MysqlConnect;

// Php mailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Email extends MysqlConnect
{
	public $Error = 0;
	public $ErrorMsg = "";

	function GetTheme($file = 'content/email/email.html', $data = array('html' => '<h1>how day?</h1>')) : string {
		$f = $_SERVER['DOCUMENT_ROOT'].'/'.ltrim($file, '/');
		if(file_exists($f)){
			$h = file_get_contents($f);

			foreach ($data as $k => $v) {
				$h = str_replace('{'.$k.'}', $v, $h);
			}
		}else{
			$h = '<h3>Error template path!</h3>';
		}
		return $h;
	}

	function Send($email, $subject, $html, $files = array()){
		if(filter_var($email,FILTER_VALIDATE_EMAIL)){
			return $this->MailerSmtp($email, $subject, $html, $files, self::SMTP_FROM_EMAIL, self::SMTP_FROM_USER, self::SMTP_USER, self::SMTP_PASS, self::SMTP_HOST, self::SMTP_TLS, self::SMTP_AUTH, self::SMTP_PORT, self::SMTP_DEBUG);
		}else{
			$this->ErrorMsg = 'Error email address.';
			$this->Error = 0;
			return 0;
	        }
	}

	function MailerSmtp($email, $subject, $html, $files, $from_email, $from_user, $smtpUser, $smtpPass, $smtpHost, $smtpTls = false, $smtpAuth = false, $smtpPort = 25, $smtpDebug = 0){
		$m = new PHPMailer(true); // Passing `true` enables exceptions
		try {
			//Server settings
			$m->SMTPDebug = (int) $smtpDebug;
			$m->isSMTP();
			$m->SMTPOptions = array('ssl' => array('verify_peer' => false,'verify_peer_name' => false,'allow_self_signed' => true));
			$m->CharSet = "UTF-8";
			// Host, port
			$m->Host = $smtpHost;
			$m->Port = $smtpPort;
			// Auth
			$m->SMTPAuth = $smtpAuth;
			$m->Username = $smtpUser;
			$m->Password = $smtpPass;
			// Ssl
			if($smtpTls == true){
				$m->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
			}
			// Sender
			$m->setFrom($from_email, $from_user);
			$m->addReplyTo($from_email, $from_user);
			// Add a recipient
			$m->addAddress($email);
			// Subject
			$m->Subject = $subject;
			// Html
			$m->isHTML(true); // Set email format to HTML
			$m->Body    = $html;
			$m->AltBody = 'Change client display type to html content.';
			// Add files from array
			foreach ($files as $path) {
				if(file_exists($path)){
					$m->addAttachment($path);
				}
			}
			// Send
			if (!$m->send()) {
				$this->ErrorMsg = $m->ErrorInfo;
				$this->Error = 0;
				return 0;
			}else{
				$this->ErrorMsg = "Email was sent.";
				$this->Error = 1;
				return 1;
			}
		} catch (Exception $e) {
			// echo $e->getMessage();
			$this->ErrorMsg = $m->ErrorInfo;
			$this->Error = 0;
			return 0;
		}
	}
}
?>
