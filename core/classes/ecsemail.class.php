<?php

class EcsEmail extends BDbIdObject {
	/*
	 *
	 */
	public function Send() {
		// create mail instance
		require_once ROOT_DIR . '/lib/phpmailer/class.phpmailer.php';
		$mail = new PHPMailer;
		$mail->AddAddress($this->email);
		$mail->Body = $this->body;
		$mail->CharSet = 'utf-8';
		$mail->Encoding = 'quoted-printable';
		$mail->IsHTML(false);
		$mail->IsSMTP();
		$mail->SetFrom(EMAIL_SENDER_EMAIL, EMAIL_SENDER_NAME);
		$mail->SetLanguage('en', ROOT_DIR . '/lib/phpmailer/language/');
		$mail->Subject = $this->subject;
		$mail->WordWrap = 90;

		$status = @$mail->Send();

		$this->Save(array(
			'status' => $status,
			'sentstamp' => date('YmdHis')
		));
	}

	/**
	 *
	 */
	public static function SendUnsent() {
		global $BDatabase;

		$q = 'SELECT * FROM `ecs_email` WHERE `sentstamp` IS NULL';
		$res = $BDatabase->Query($q);
		while ($row = $BDatabase->FetchAssoc($res)) {
			$item = new EcsEmail;
			$item->LoadFromRow($row);
			$item->Send();
		}
	}
}
