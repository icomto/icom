<?php

class imail {
	public static $config;
	
	public static function mail($to, $subject, $message, $args) {
		if(empty(self::$config['host']) {
			mail(
				$to,
				"=?UTF-8?B?".base64_encode($subject)."?=",
				base64_encode($message),
				"From: iCom.to <".self::$config['noreply'].">\r\n".
				"MIME-Version: 1.0\r\n".
				"Content-Type: text/plain; charset=UTF-8\r\n".
				"Content-Transfer-Encoding: base64\r\n");
		}
		else {
			$s = self::smtp_mail_start();
			self::smtp_mail_send($s, $to, '', $subject, $message);
			self::smtp_mail_end($s);
		}
	}


	protected static function smtp_mail_start() {
		$s = fsockopen('tcp://'.self::$config['host'], self::$config['port']);
		if(!$s) return 0;
		if(self::smtp_mail_fetch($s)) { fclose($s); return 1; }
		
		self::smtp_fputs($s, "STARTTLS\r\n");
		if(self::smtp_mail_fetch($s)) { fclose($s); return 21; }
		if(!stream_socket_enable_crypto($s, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) return 22;
		
		self::smtp_fputs($s, "EHLO icom.to\r\n");
		if(self::smtp_mail_fetch($s)) { fclose($s); return 23; }
		
		self::smtp_fputs($s, "AUTH LOGIN\r\n");
		if(self::smtp_mail_fetch($s)) { fclose($s); return 3; }
		self::smtp_fputs($s, base64_encode(self::$config['user'])."\r\n");
		if(self::smtp_mail_fetch($s)) { fclose($s); return 4; }
		self::smtp_fputs($s, base64_encode(self::$config['pass'])."\r\n");
		if(self::smtp_mail_fetch($s)) { fclose($s); return 5; }
		
		return $s;
	}
	protected static function smtp_mail_send($s, $to, $to_name, $subject, $message) {
		self::smtp_fputs($s, "MAIL FROM: <".self::$config['noreply'].">\r\n");
		if(self::smtp_mail_fetch($s)) { fclose($s); return 6; }
		self::smtp_fputs($s, "RCPT TO: <".$to.">\r\n");
		if(self::smtp_mail_fetch($s)) { fclose($s); return 7; }
		self::smtp_fputs($s, "DATA\r\n");
		$gah = "";
		if(self::smtp_mail_fetch($s, $gah)) { fclose($s); return 8; }
		self::smtp_fputs($s, "From: iCom.to <".self::$config['noreply'].">\r\n");
		if($to_name) self::smtp_fputs($s, "To: ".$to_name." <".$to.">\r\n");
		else self::smtp_fputs($s, "To: ".$to."\r\n");
		self::smtp_fputs($s, "MIME-Version: 1.0\r\n");
		self::smtp_fputs($s, "Content-Type: text/plain; charset=UTF-8\r\n");
		self::smtp_fputs($s, "Content-Transfer-Encoding: base64\r\n");
		self::smtp_fputs($s, "Subject: =?UTF-8?B?".base64_encode($subject)."?=\r\n");
		self::smtp_fputs($s, "\r\n");
		self::smtp_fputs($s, base64_encode($message)."\r\n");
		self::smtp_fputs($s, ".\r\n");
		if(self::smtp_mail_fetch($s)) { fclose($s); return 9; }
	}
	protected static function smtp_mail_end($s) {
		self::smtp_fputs($s, "QUIT\r\n");
		if(self::smtp_mail_fetch($s)) { fclose($s); return 10; }
		fclose($s);
	}

	protected static function smtp_fputs($s, $str) {
		#echo '<< '.$str;
		return fputs($s, $str);
	}
	protected static function smtp_mail_fetch($s, $val = NULL) {
		$error = false;
		while(!feof($s) and $l = fgets($s)) {
			#echo '>> '.$l;
			if(preg_match("/^354 .+ ([^ ]+)$/", trim($l), $out)) $gah = $out[1];;
			if(preg_match("/^5.. /", $l)) $error = true;;
			if(preg_match("/^... /", $l)) break;
		}
		return $error;
	}
}

imail::$config = (empty($CONFIG['mail']) ? NULL : $CONFIG['mail']);

?>
