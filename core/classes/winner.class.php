<?php

class Winner extends BDbIdObject {
	const SUCCESS = 'SUCCESS';
	const E_FIRSTNAME_EMPTY = 'E_FIRSTNAME_EMPTY';
	const E_LASTNAME_EMPTY = 'E_LASTNAME_EMPTY';
	const E_PHONE_EMPTY = 'E_PHONE_EMPTY';
	const E_PHONE_INVALID = 'E_PHONE_INVALID';
	const E_PHONE_EXISTS = 'E_PHONE_EXISTS';
	const E_EMAIL_EMPTY = 'E_EMAIL_EMPTY';
	const E_EMAIL_INVALID = 'E_EMAIL_INVALID';
	const E_EMAIL_EXISTS = 'E_EMAIL_EXISTS';
	const E_STREET_EMPTY = 'E_STREET_EMPTY';
	const E_CITY_EMPTY = 'E_CITY_EMPTY';
	const E_ZIP_EMPTY = 'E_ZIP_EMPTY';
	const E_BIRTHDAY_EMPTY = 'E_BIRTHDAY_EMPTY';
	const E_BIRTHDAY_INVALID = 'E_BIRTHDAY_INVALID';
	const E_CODE_EMPTY = 'E_CODE_EMPTY';
	const E_CODE_INVALID = 'E_CODE_INVALID';
	const E_CODE_EXISTS = 'E_CODE_EXISTS';
	const E_FILE_EMPTY = 'E_FILE_EMPTY';
	const E_FILE_OVERSIZED = 'E_FILE_OVERSIZED';
	const E_FILE_MOVE_ERROR = 'E_FILE_MOVE_ERROR';
	const E_FILE_UNKNOWN_ERROR = 'E_FILE_UNKNOWN_ERROR';
	const E_SAVE_FAILED = 'E_SAVE_FAILED';

	/**
	 * Get list of winner codes
	 */
	private static function CheckCode($code) {
		global $BDatabase;
		$q = 'SELECT * FROM `code` WHERE `code` = %s AND `winner_id` IS NULL';
		$res = $BDatabase->Query($q, array($code));
		return $BDatabase->NumRows($res) > 0;
	}


	/**
	 * Create new registration
	 */
	public static function Create($data, $file) {
		$data = array_merge(array(
            'code' => '',
            'firstname' => '',
            'lastname' => '',
            'phone' => '',
            'email' => '',
            'street' => '',
            'city' => '',
            'zip' => '',
            'birthday' => '',
		), $data);

		$file = array_merge(array(
			'error' => UPLOAD_ERR_NO_FILE
		), $file);

        $result = new Result;
		if (!$data['code']) {
			$result->AddError(self::E_CODE_EMPTY);
		} else if (!self::CheckCode($data['code'])) {
			$result->AddError(self::E_CODE_INVALID);
		}
		if (!$data['firstname']) {
			$result->AddError(self::E_FIRSTNAME_EMPTY);
		}
		if (!$data['lastname']) {
			$result->AddError(self::E_LASTNAME_EMPTY);
		}
		if (!$data['phone']) {
			$result->AddError(self::E_PHONE_EMPTY);
		} else if (!preg_match('#^(((\+|00)42(0|1))|0)?(\d{9})$#', $data['phone'], $m)) {
			$result->AddError(self::E_PHONE_INVALID);
		}
		if (!$data['email']) {
			$result->AddError(self::E_EMAIL_EMPTY);
		} else if (!validateEmail($data['email'])) {
			$result->AddError(self::E_EMAIL_INVALID);
		}
		if (!$data['street']) {
			$result->AddError(self::E_STREET_EMPTY);
		}
		if (!$data['city']) {
			$result->AddError(self::E_CITY_EMPTY);
		}
		if (!$data['zip']) {
			$result->AddError(self::E_ZIP_EMPTY);
		} else {
			$data['zip'] = str_replace(' ', '', $data['zip']);
		}
		if (!$data['birthday']) {
			$result->AddError(self::E_BIRTHDAY_EMPTY);
		} else if (!preg_match('#^(\d{1,2})\.(\d{1,2})\.(\d{4})$#', $data['birthday'], $m) || !checkdate($m[2], $m[1], $m[3])) {
			$result->AddError(self::E_BIRTHDAY_INVALID);
		} else {
			$data['birthday'] = $m[3] . '-' . $m[2] . '-' . $m[1];
		}

		switch ($file['error']) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				$result->AddError(self::E_FILE_EMPTY);
				break;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$result->AddError(self::E_FILE_OVERSIZED);
				break;
			default:
				$result->AddError(self::E_FILE_UNKNOWN_ERROR);
				break;
		}

		if ($result->OK()) {
            global $BDatabase;
            $BDatabase->Begin();

            $item = new Winner;
            @$item->Save($data);

            switch ($BDatabase->Errno()) {
                case 0:
                    $item = new Winner($item->id); // needed for data reload
                    $filename = $item->id . '_' . $file['name'];
					if (!move_uploaded_file($file['tmp_name'], ROOT_DIR . BILLS_DIR . '/' . $filename)) {
						$result->AddError(self::E_FILE_MOVE_ERROR);
					} else {
	                    $item->SendMailToAdmin();
	                    $item->SendMailToWinner();
	                    $item->Save(array(
	                    	'bill' => $filename
	                    ));

	                    $q = 'UPDATE `code` SET `winner_id` = %d WHERE `code` = %s';
	                    $BDatabase->Query($q, array($item->id, $data['code']));

						$BDatabase->Commit();
					}
					$result->AddMessage(self::SUCCESS);
                    break;
                case 1062:
					$result->AddError(self::E_PHONE_EXISTS);
                    break;
                default:
                	$result->AddError(self::E_SAVE_FAILED);
                	error_log($BDatabase->Errno() . ': ' . $BDatabase->Error());
                	break;
            }
			$BDatabase->Rollback();
		}

		return $result;
	}


	/**
	 * Send email notification to winner
	 */
	private function SendMailToWinner() {
		// process mail template
		ob_start();
		include ROOT_DIR . '/views/partial/mail_winner.php';
		$body = ob_get_clean();

		// replace placeholders
		$body = str_replace('#SITE_URL#', siteUrl(), $body);
		
		$email = new EcsEmail;
		$email->Save(array(
			'subject' => 'Coca-Cola soutěž - Ověření účtenky',
			'body' => $body,
			'email' => $this->email,
		));
	}


	/**
	 * Send email notification to administrator
	 */
	private function SendMailToAdmin() {
		// process mail template
		ob_start();
		include ROOT_DIR . '/views/partial/mail_admin.php';
		$body = ob_get_clean();

		// replace placeholders
		$body = str_replace('#SITE_URL#', siteUrl(), $body);
		$body = str_replace('#FIRSTNAME#', $this->firstname, $body);
		$body = str_replace('#LASTNAME#', $this->lastname, $body);
		$body = str_replace('#PHONE#', $this->phone, $body);
		$body = str_replace('#EMAIL#', $this->email, $body);
		$body = str_replace('#STREET#', $this->street, $body);
		$body = str_replace('#ZIP#', $this->zip, $body);
		$body = str_replace('#CITY#', $this->city, $body);
		$body = str_replace('#CODE#', $this->code, $body);
		$body = str_replace('#BIRTHDAY#', $this->birthday, $body);

		$email = new EcsEmail;
		$email->Save(array(
			'subject' => 'Coca-Cola soutěž - Registrace výherce ' . $this->firstname . ' ' . $this->lastname,
			'body' => $body,
			'email' => ADMIN_EMAIL,
		));
	}
}
