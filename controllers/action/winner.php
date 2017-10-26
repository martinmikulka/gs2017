<?php

defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');

$code = value($_REQUEST, 'code', '');
$firstname = value($_REQUEST, 'firstname', '');
$lastname = value($_REQUEST, 'lastname', '');
$phone = value($_REQUEST, 'phone', '');
$email = value($_REQUEST, 'email', '');
$street = value($_REQUEST, 'street', '');
$city = value($_REQUEST, 'city', '');
$zip = value($_REQUEST, 'zip', '');
$birthday = value($_REQUEST, 'birthday', '');
$file = value($_FILES, 'files', array());

$result = Winner::Create(array(
    'code' => $code,
    'firstname' => $firstname,
    'lastname' => $lastname,
    'phone' => $phone,
    'email' => $email,
    'street' => $street,
    'city' => $city,
    'zip' => $zip,
    'birthday' => $birthday,
), $file);

if (!isAjax()) {
    $form = new Form('FormWinner');
    if ($result->OK()) {
        $form->success = true;
    } else {
        $form->data = array(
            'code' => $code,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'phone' => $phone,
            'email' => $email,
            'street' => $street,
            'city' => $city,
            'zip' => $zip,
            'birthday' => $birthday,
        );
    }
    $form->messages = $result->GetMessages();
    $form->StoreToTmp();
}

if (isAjax()) {
    header('Content-type: application/json');
    echo $result->ToJSON();
} else {
    header('Location: /#WinnerForm');
}
exit;
