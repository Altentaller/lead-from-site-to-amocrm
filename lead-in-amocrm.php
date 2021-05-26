<?php
if ( (!empty($_POST['phone'])) && (!empty($_POST['name'])) && (!empty($_POST['email'])) )
{
	$name_f = $_POST['name'];
	$phone_f = $_POST['phone'];
	$email_f = $_POST['email'];

// Отправка заявки на почту
	$to = "name1@domain.com";
	$subject = "Заявка с сайта";
	$message = 
			'<p>Имя: ' . $name_f . '</p>' .
			'<p>Email: ' . $email_f . '</p>' .
			'<p>Телефон: ' . $phone_f . '</p>';	

	$headers = "Content-type: text/html; charset=utf-8 \r\n";
	$headers .= "From: noreply@domain.com\r\n";
	$headers .= "Reply-To: noreply@domain.com\r\n";

	mail($to, $subject, $message, $headers);
	
// Создание сделки в AmoCRM
	try
	{
		// Авторизация
		$subdomain = 'mycrm'; // имя аккаунта
		$login = 'name2@domain.com'; // логин
		$apikey = 'your api key'; //api ключ
		$amo = new \AmoCRM\Client($subdomain, $login, $apikey);

		// Вывод информации об аккаунте)
		echo '<pre>';
		print_r($amo
			->account
			->apiCurrent());
		echo '</pre>';

		$lead = $amo->lead; //Получение экземпляра модели для работы со сделками
		$lead['name'] = 'Lead from site'; // Название сделки
		$lead['responsible_user_id'] = 6945088; // ID ответсвенного
		$lead['pipeline_id'] = 42972559; // ID воронки	
		$id = $lead->apiAdd(); // Добавление и получение ID новой сделки


		$contact = $amo->contact; // Получение экземпляра модели для работы с контактами)
		$contact['name'] = $_POST['name']; // Имя контакта
		$contact['linked_leads_id'] = [(int)$id]; // ID связанной сделки
		$contact->addCustomField(191385, [[$_POST['email'], 'PRIV'], ]); // Добавление email в соотв. поле
		$contact->addCustomField(191383, [[$_POST['phone'], 'MOB'], ]); // Добавление телефона в соотв. поле
		$id = $contact->apiAdd(); // Добавление и получение ID нового контакт

	}
	catch(\AmoCRM\Exception $e)
	{
		printf('Error (%d): %s' . PHP_EOL, $e->getCode() , $e->getMessage());
	}

}

