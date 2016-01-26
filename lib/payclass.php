<?php

namespace lk;

class lkPayclass
{
	
	public $config = array (
		'path' => 'lk', // Путь до файла payment.php от корня, без / (payment - поумолчанию)
		'user_param' => '', // Уникальный параметр, напримаер логин или id игрока, зависит от параметра COLUMN_USER
		'description' => 'Пополнение счёта игрока - ', // Описание платежа. DESCRIPTION USER_PARAM
		'max_pay' => 1000000, // Максимальная сумма пополнения
		'message' => array (
			'success' => 'Успешно.', // сообщение в случае успешной оплаты
			'fail' => 'Ошибка!', // сообщение в случае ошибки или неудачной оплаты.
		),
		'pay_system' => array (
			'interkassa' => array( // Настройки InterKassa
				'enable' => false, // Принимать платежи данной системы?
				'shop_id' => '', // ID магазина InterKassa
				'cur' => 'RUB', // Тип валюты (RUB UAH USD EUR)
				'key' => '', // Секретный ключ
				'test_key' => '', // Местовый секретный ключ
				'testing' => false // Включить режим тестирования?
			),
		
			'unitpay' => array( // Настройки UnitPay
				'enable' => true, // Принимать платежи данной системы?
				'project_id' => '5220-3acb9', // ID(Номер) проекта || demo - в случае тестого платежа
				'key' => '70b8c3d48b1dbcfe84a2834fecfbe8e8', // Секретный ключ
			),
		
			/*'robokassa' => array( // Настройки RoboKassa
				'enable' => false, // Принимать платежи данной системы?
				'password1' => '', // Пароль #1								В следующей версии.
				'password2' => '', // Пароль #2
				'login' => '', // Логин (Имя проекта) || demo - в случае тестого платежа
			),*/
		)
	);

	public function __construct()
	{
		//$this->ik = $this->config['pay_system']['interkassa'];
		$this->up = $this->config['pay_system']['unitpay'];
		$this->config['user_param'] = $this->getUserName();
	}

	//public function test(){ return $this->config['user_param']; }
	

	public function getUserName($id){
		$name = lk::get('DB')->data('SELECT name FROM dle_users WHERE user_id = '.lk::$userID);
		return $name[0]['name'];
	}


	public function ik_sort($param)
	{
		$data['ik_co_id'] = $this->ik['shop_id'];
		foreach ($param as $key => $value) // убирает параметры без /ik_/
		{
			if (!preg_match('/ik_/', $key)) continue;
			$data[$key] = $value; // сохраняем параметры
		}
		return $data;
	}
	
	public function ik_sign($param) // интеркасса генератор контрольной цифровой подписи со стороны сервера
	{
		$data = $this->ik_sort($param);
		$ikSign = $data['ik_sign']; // сохраняем приходящую переменную
		unset($data['ik_sign']); // удаляем придодащую переменную, для генирации подписи
		$key = ($data['ik_pw_via'] == 'test_interkassa_test_xts') ? $this->ik['test_key'] : $this->ik['key'];
		if ($data['ik_pw_via'] == 'test_interkassa_test_xts' && !$this->ik['testing']) return false;
		ksort ($data, SORT_STRING); // сортируем массив
		array_push($data, $key); // внедряем переменуую $key в массив
		$signStr = implode(':', $data); // записываем массив в формат @string через : 
		$sign = base64_encode(md5($signStr, true)); // хешируем подпись
		return ($sign == $ikSign) ? true : false;
	}
	
	
	public function up_json_reply($type = "error", $params) // системный ответ для сервера unitpay, json
	{
		if ($type == "check" || $type == "pay") $type = "success";
		$reply = array( // системный массив
			'error' => array(
				"jsonrpc" => "2.0",
				"error" => array("code" => -32000, "message" => $this->config['message']['fail']),
				'id' => $params['projectId']
			),
			'success' => array(
				"jsonrpc" => "2.0",
				"result" => array("message" => $this->config['message']['success']),
				'id' => $params['projectId']
			)
		);
		return json_encode($reply[$type]); // возвращаем json
    }
	
	public function up_sign($reply) { // Проверка цифровой подписи unitpay
		ksort($reply); // сортируем массив
		$exp = explode("-", $this->up['project_id']);
		$Sign = $reply['sign']; // сохраняем подпись
		unset($reply['sign']); // удаляем подпись
		$reply['projectId'] = $exp[0]; // заменяем существующий ид проекта на свой, дабы убедиться, что запрос от нашего UP
		$return = (md5(join(null, $reply).$this->up['key']) != $Sign) ? "error" : "success"; // генирация и проверка подписи
		return $this->up_json_reply($return, $reply);
	}
	
	public function mysql_prepare($sql, $db, $binds)
	{
		foreach($binds as $key => $bind) {
			$a[] = $key;
			$b[] = "'{$bind}'";
		}
		$query = mysql_query(str_replace($a, $b, $sql), $db);
		return (!$query) ? false : true;
	}
	
	public function pay_systems($pay_system)
	{
		foreach($this->config['pay_system'] as $system => $options)
		{
			if ($pay_system == $system && $options['enable']) return true;
		}
		return false;
	}
	
	public function pay_form($amount, $user, $pay_system) // генерация GET запроса
	{
		$amount = (int) $amount;
		if ( $amount > $this->config['max_pay'] || $amount <= 0 || !$this->pay_systems($pay_system) ){ return "/{$this->config['path']}/payment.php?reply=fail"; }
		$desc = "{$this->config['description']} {$user}";
		switch ($pay_system) {
			case "interkassa" :
				return "https://sci.interkassa.com/?ik_co_id={$this->ik['shop_id']}&ik_pm_no={$user}&ik_am={$amount}&ik_cur={$this->ik['cur']}&ik_desc={$desc}";
			break;
			case "unitpay" :
				return "https://unitpay.ru/pay/{$this->up['project_id']}?sum={$amount}&account={$user}&desc={$desc}";
			break;
			case "robokassa" :
				// Ждём следующую версию
			break;
			default:
				return "/{$this->config['path']}/payment.php?reply=fail";
			break;
		}
	}
	
	public function pay($amount,$payid,$user) // пополнение счета
	{
		$amount = intval($amount);
		$info = lk::get('DB')->data("SELECT du.user_id,du.name,gr.name payname,IFNULL(gr.cash,0) cash,gr.user_pay_id 
					FROM {$this->config['table_users']} du
  					LEFT JOIN {$this->config['table_money']} gr ON gr.id = du.user_id
  					WHERE du.name ='{$user}' LIMIT 1");
		
                if(!$info or ($info[0]['user_pay_id'] && $info[0]['user_pay_id']==$payid) or !$amount){
                	lk::get('DB')->safedata("INSERT INTO `gl_donation_error_log` VALUES('',".date('Y.m.d H:i:s').",'дубликат или не найден {$user}')");	
			return false;
                }
                
                $cash = $info[0]['cash'];
                $id = $info[0]['user_id'];
		$name = $info[0]['name'];
                $payname = $info[0]['payname'];
		
                if(!$payname){
			$ch = lk::get('DB')->safedata("INSERT INTO `{$this->config['table_money']}` VALUES({$id},'{$name}',{$amount},{$payid},{$amount})");
		}
		else{
                       	$sum = ($cash + $amount);
			$ch = lk::get('DB')->safedata("UPDATE `{$this->config['table_money']}` SET `cash`={$sum},`user_pay_id`={$payid},`last_donate_cnt`={$amount} WHERE `id`={$id}");
		}
		if($ch){
			lk::get('DB')->safedata("INSERT INTO `{$this->config['table_history']}` VALUES('{$name}',".time().",{$amount})");
			lk::get('DB')->safedata("INSERT INTO `gl_donation_history` VALUES('{$name}',".date('Y.m.d H:i:s').",{$amount})");
		}
		else{
	        	lk::get('DB')->safedata("INSERT INTO `gl_donation_error_log` VALUES('',".date('Y.m.d H:i:s').",'не удалось записать в таблицу реалмани для userid ".$id."')");	
		}

		return true;
	}
	
}
?>