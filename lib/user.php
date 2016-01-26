<?php   namespace lk;

class lkUser{
	public $UserData, $UserRights;

	function __construct(){}

	function getUserData(){
		//lk::dump(lk::$userID,'UID');
		if(!$this->UserData){
			$tmp = lk::get('DB')->data("SELECT name, user_group, lastdate, banned, default_server FROM dle_users WHERE user_id = ".lk::$userID);
			$this->UserData = $tmp[0];
		}
		return $this->UserData;
	}
	
	function getUserRights(){
		if(!$this->UserRights){
			$this->UserRights = lk::get('DB')->data("SELECT 
							sl.id server, gs.id status, gpd.start_date, gpd.expiration_date, gs.hd_skin, gs.hd_cloak, gs.prefix, gs.name_color
						FROM gl_permissions_duration gpd 
				                JOIN servers_list sl on sl.id = gpd.server_id
						JOIN gl_status gs on gs.id = gpd.status_id
						WHERE gpd.user_id = ".lk::$userID,'server');
		}
		return $this->UserRights;
	}
	
	function getUserDefaultServer(){
		$res = lk::get('DB')->data('SELECT default_server FROM dle_users WHERE user_id = '.lk::$userID);
		return $res[0]['default_server'];
	}

        function setUserDefaultServer($sid,$val){
		if(!intval($sid)){throw new \Exception('Нет Ид сервера'); return false;}
		$servers = lk::get('Remote')->getServerList();
		if(!$servers[$sid]){throw new \Exception('Не верный Ид сервера'); return false;}
		if($val){
			lk::get('DB')->data("UPDATE dle_users SET default_server = {$sid} WHERE user_id = ".lk::$userID);
		}
		else{
                	lk::get('DB')->data("UPDATE dle_users SET default_server = NULL WHERE user_id = ".lk::$userID);
		}
		return array('ok'=>'Выбран сервер по умолчанию.');
	}
	
	function getUserList($word){
		if(!$word or !preg_match('/[\d|\w|_|-]+/',$word)){return false;}
		return array('results'=>lk::get('DB')->data("SELECT user_id id,name FROM dle_users WHERE name LIKE '$word%'")); 
	}

	function setUserRights($rights=array()){
		
	}

	function checkUserRights(){
		
	}
	
	

	
}