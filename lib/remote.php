<?php

namespace lk;

class lkRemote{
	protected  $servers;
	private $DBuser = 'teo';
	private $DBpass = 'rxPSnB1UtB';
	private $pdo;
	
	function connect($serverID){
		
		if(!$serverID){return false;}
                if(!$this->servers){$this->getServerList();}
		
		$this->pdo = null;

		$dsn = "mysql:dbname={$this->servers[$serverID]['server_name']};host={$this->servers[$serverID]['server_ip']};charset=cp1251;port=3306";
		
                $opt = array(
   			 \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
    			 \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
		);
		
		$this->pdo = new \PDO($dsn, $this->DBuser, $this->DBpass, $opt);
	}
        
	function setQuery($query){
		try{
	                $tmp = $this->pdo->prepare($query);
			$tmp->execute();
			if($tmp->columnCount()>0){
				return $tmp->fetchAll();
			}
		}
		catch (\PDOException $e) {//PDOException
    			return $e->getMessage(); //выведет \\\"Exception message\\\"
		}	
	}
	

	public function getServerList($server = ''){
		if(!$this->servers){
			$this->servers = lk::get('DB')->data('SELECT * FROM servers_list'.($server?" WHERE name = '{$server}'":" WHERE active = 1"),'id');
		}
		return $this->servers;
	}

	function __destruct(){$this->pdo = null;}

}

?>