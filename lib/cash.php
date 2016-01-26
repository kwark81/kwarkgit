<?
namespace lk;

class lkCash
{
	public $data;
	private $servers;
	private $userName;
	
	function getUserCash($type = false)
	{
		//if(!$this->UserCash){
			$query = "SELECT gc.id currency, gc.name, IFNULL(gum.value,0) value, gc.description currency_name 
				FROM gl_user_money gum 
				JOIN gl_currency gc on gc.id = gum.currency_id
				WHERE gum.user_id = ".lk::$userID;
			if($type){$query .= " AND currency_id = {$type}";}
			$userCash = lk::get('DB')->data($query,'name');
		//}
		//return array('ok'=>$query);
		return $userCash;
	}
	
	function setUserCash($cash,$type)
	{
		if(!in_array(intval($type),array(1,2))){throw new \Exception('Wrong currency type');}
		else if(!intval($cash)){throw new \Exception('Wrong cash size');}
		return lk::get('DB')->data("INSERT INTO gl_user_money (user_id, currency_id, value) VALUES (".lk::$userID.",{$type},{$cash}) ON DUPLICATE KEY UPDATE value = value + {$cash}");
	}

        function getUserCashFromServers()
	{
		$userData = lk::get('User')->getUserData();
		$this->userName = $userData['name'];
		$this->servers = lk::get('remote')->getServerList();
		foreach($this->servers as $server)
		{
			$result[$server['id']]['id'] = $server['id'];
			$result[$server['id']]['name'] = $server['server_name'];
			$result[$server['id']]['cash'] = $this->getUserCashFromServer($server['id']); //$server
		}
                return $result;
	}

	function getUserCashFromServer($sid)
	{
		lk::get('Remote')->connect($sid);
                $result = lk::get('Remote')->setQuery("SELECT `money` FROM `economy` WHERE `name` = '{$this->userName}'");
                //$result = lk::get('Remote')->setQuery("INSERT `money` FROM `economy` WHERE `name` = '{$this->userName}'");
                
		return intval($result[0]['money']);
	}

	function transferFunds($data)
	{
	        
	        if(!$data['data']){ throw new \Exception('No data'); }
		$udata = json_decode($data['data']);
		
		if( $udata->name->id == lk::$userID ){ throw new \Exception('Нельзя передать самому себе'); }
		
		if( !intval($udata->val) ){  throw new \Exception('Не верно указано количество');  }
		
		if($data['type'] == 'servers')
		{
			$name = lk::get('User')->getUserData();
			if(!$udata->sid){ throw new \Exception('Не указан сервер'); }
			lk::get('Remote')->connect($udata->sid);
			lk::get('Remote')->setQuery("UPDATE economy SET money = money - {$udata->val} WHERE name = '{$name['name']}'");
			lk::get('Remote')->setQuery("UPDATE economy SET money = money + {$udata->val} WHERE name = '{$udata->name->name}'");	
		}
		else
		{
                	$cashType = key(lk::get('DB')->data("SELECT id FROM gl_currency WHERE name = '{$data['type']}'",'id'));
                	$this->setUserCash(-$udata->val,$cashType);
			lk::$userID = $udata->name->id;
			$this->setUserCash($udata->val,$cashType);
		}
		return array('Передано успешно');
		
	}

}
?>