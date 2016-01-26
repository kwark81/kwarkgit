<?php

namespace lk;


class lkNickname{

	public function getUserSettings(){
		$result = array();
		$status = lk::get('Status')->getUserServerStatus();
		
		foreach($status as $s){
			lk::get('Remote')->connect($s['server_id']);
			
			$query = 'SELECT * FROM permissions_inheritance ';
		        //permissions_inheritance
			//permissions_entity

			
			$data = lk::get('Remote')->setQuery($query);
			//lk::dump($data);
		}
		//$servers = lk::get('Remote')->getServerList();
		
	}
}







/*
			if(in_array($data['prefix_color'], $this->gl_mc_color_codes)){$_prefix_color = $data['prefix_color'];}
			else{$_prefix_color = '&f';}
			if(in_array($data['nickname_color'], $this->gl_mc_color_codes)){$_nickname_color = $data['nickname_color'];}
			else{$_nickname_color = '&f';}
			if(in_array($data['text_color'], $this->gl_mc_color_codes)){$_text_color = $data['text_color'];}
			else{ $_text_color = '&0';}
			if($data['prefix']){
				if(preg_match('/^[a-z0-9_]+$/i',$data['prefix'])){
					$_prefix = '['.substr($data['prefix'],0,10).']';
				}
				else{echo $this->msg['bad_text'];exit();}
			}
			else{
				$_prefix = '[]';
			}
			$_prefix = $_prefix_color.$_prefix.$_nickname_color;
                        
			$servers = $this->getServers();
			foreach($servers as $srv){
		        	if($data['server'] == 'all' or $data['server']==$srv){
					$pm = new pm($srv);
					$chk = $pm->setQuery("SELECT * FROM `{$this->tbl_PRMS_entity}` WHERE `name`='{$this->gl_user_name}' AND `type`=1");
					if($chk){
						$pm->setQuery("UPDATE `{$this->tbl_PRMS_entity}` SET `prefix`='{$_prefix}',`suffix`='{$_text_color}' WHERE `name`='{$this->gl_user_name}'"); 
					}
					else{
						$pm->setQuery("INSERT INTO `{$this->tbl_PRMS_entity}` (`name`,`type`,`prefix`,`suffix`,`default`) VALUES ('{$this->gl_user_name}',1,'{$_prefix}','{$_text_color}',0)");
					}
					unset($pm);
				}
			}
			echo $this->msg['save_prefix'];

*/

?>