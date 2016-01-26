<?php
namespace lk;

class lkStatus{
	public $servers;
	public function getServerDefaults(){
	        $def = array(
                        'group_name' => 'Обычный игрок',
            		'start_date' => -1,
            		'expiration_date' => -1,
            		'discount' => 0,
            		'delta' => 'Без ограничений',
            		
		);
		$servers = lk::get('Remote')->getServerList();
                foreach($servers as $serv){
		      	$result[$serv['id']] = $def;
			$result[$serv['id']]['name'] = $serv['server_name'];
			$result[$serv['id']]['server_id'] = $serv['id'];
		}
        	return $result;
	}

	public function getUserServerStatus(){
                $query = "SELECT du.group_name, gpd.server_id, sl.server_name name, IFNULL(gpd.start_date,0) start_date, IFNULL(gpd.expiration_date,0) expiration_date, 
				CASE WHEN gpd.start_date > 0 THEN gs.discount_renew ELSE gs.discount END discount
				FROM gl_permissions_duration gpd
  				JOIN gl_status gs ON gs.id = gpd.status_id
  				JOIN dle_usergroups du ON du.id = gs.group_id
  				JOIN servers_list sl ON sl.id = gpd.server_id AND sl.active = 1
				WHERE gpd.user_id = ".lk::$userID;
  		$data = lk::get('DB')->data($query,'server_id');
		return $data ? $data : array();
	}


	public function getStatus()
	{
                $stat = array_replace($this->getServerDefaults(),$this->getUserServerStatus());
		foreach($stat as &$s){
			$end = ($s['expiration_date'] > 0 and $s['start_date'] > 0) ? round(($s['expiration_date'] - $s['start_date'])/86400, 1) : 'Без ограничений';
			if(intval($end) > 0)
			{       
				if($end < 1){ $end = round(($s['expiration_date'] - $s['start_date'])/3600).' часов'; }
				else{$end = round($end).' дней';}
				 
			}
			//$s['discount'].=' %';
			$s['delta'] = $end;
			$s['expiration_date'] = $s['expiration_date'] < 0 ? 'Не играл' : ($s['expiration_date'] == 0 ? 'Без ограничений' : date('d.m.Y H:i:s',$s['expiration_date']));
			$s['start_date'] = $s['start_date'] < 0 ? 'Не играл' : ($s['start_date'] == 0 ? 'Без ограничений' : date('d.m.Y H:i:s',$s['start_date']));
	
		}
		return array('servers'=>$stat);
	}


}

?>