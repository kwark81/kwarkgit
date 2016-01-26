<?php
namespace lk;

class lkSkin{
	private $user;
	private $userRights;
	public $servers;
	public $skins;
	public $result;
	private $gl_skin_height = array(1000,1024,64);  //размеры скина
	private $gl_skin_width = array(513,500,512,32);
	private $gl_cloak_height = array(22,512,500);  //размеры клока
	private $gl_cloak_width = array(17,250,256);
	
	

	function __construct()//lkSetup $s
	{
		$this->user = lk::get('User')->getUserData();
		$this->userRights  = lk::get('User')->getUserRights();
		$this->servers = lk::get('Remote')->getServerList();// false, $this->user['default_server']
	}
	
	function getSkin($server = 'all'){
		$i = 0;
		foreach($this->servers as $server)
		{
			$this->skins[$i]['name'] = $server['server_name'];
			$this->skins[$i]['id'] = $server['id'];
			$this->skins[$i]['skin'] = array();
			$this->skins[$i]['cloak'] = array();
			if(file_exists(SKIN_DIR.$server['server_name'].'/'.$this->user['name'].'.png'))
			{
				$this->skins[$i]['skin']['img'] = $this->getImg(SKIN_DIR.$server['server_name'].'/'.$this->user['name'].'.png');
				$this->skins[$i]['skin']['default'] = false;
			}
			else
			{
                        	$this->skins[$i]['skin']['img'] = SKIN_URL.'default_skin.png';
				$this->skins[$i]['skin']['default'] = 1;
			}
			if(file_exists(CLOAK_DIR.$server['server_name'].'/'.$this->user['name'].'.png'))
			{
				$this->skins[$i]['cloak']['img'] = $this->getImg(CLOAK_DIR.$server['server_name'].'/'.$this->user['name'].'.png');
				$this->skins[$i]['cloak']['default'] = false;
			}
			else{
				$this->skins[$i]['cloak']['default'] = 1;
			}
			if($this->user['default_server']==$server['id'])
			{
				$this->skins[$i]['default'] = 'Y';	
			}
			$i++;
		}
		return $this->skins;
	}
	
	function getImg($url){
        	$imageData = base64_encode(file_get_contents($url));
                return 'data: '.mime_content_type($url).';base64,'.$imageData;
	}

	function addSkin(){
		$result = array();
		
		$sid = intval($_REQUEST['serverid']);
		
		if(!$sid or !$this->servers[$sid]){$result['error'][] = 'Сервер не найден или не активен';return $result;}
		
		$dir = SKIN_DIR.$this->servers[$sid]['server_name'].'/';
		
		if(!is_dir($dir)){mkdir($dir);}
		
		if(move_uploaded_file($_FILES["file"]["tmp_name"], $dir.$this->user['name'].'tmp.png'))
		{
			$finfo = getimagesize($dir.$this->user['name'].'tmp.png');
		        
			$result['$finfo']['img'] = $finfo;
			$result['$finfo']['stats'] = $this->userRights;
			
			if(!in_array($finfo[0],$this->gl_skin_height) or !in_array($finfo[1],$this->gl_skin_width))
			{
		        	$result['error'][] = 'Не верные размеры скина';
			}
                        elseif(($finfo[0] > 64 or $finfo[1] > 32))
			{
			       	if(!$this->userRights[$sid]['hd_skin'])
				{
			        	$result['error'][] = 'Не достаточно прав статуса для HD скина';
				}
			}
			
			if(!$result['error'])
			{
				copy($dir.$this->user['name'].'tmp.png',$dir.$this->user['name'].'.png');
				$result['skins'] = $this->getSkin();
			}
			unlink($dir.$this->user['name'].'tmp.png');
		}
		else
		{
			$result['error'][] = 'Не удалось скопировать файл';
		}
		return $result;
        }
        
	function addCloak(){
		$result = array();
		
		$sid = intval($_REQUEST['serverid']);
		if(!$sid or !$this->servers[$sid]){$result['error'][] = 'Сервер не найден или не активен';return $result;}
		
		$dir = CLOAK_DIR.$this->servers[$_REQUEST['serverid']]['server_name'].'/';
		
		if(!is_dir($dir)){mkdir($dir);}
		
		if(move_uploaded_file($_FILES["file"]["tmp_name"], $dir.$this->user['name'].'tmp.png'))
		{
			$finfo = getimagesize($dir.$this->user['name'].'tmp.png');
		        
			$result['$finfo']['img'] = $finfo;
			$result['$finfo']['stats'] = $this->userRights;
			
		
			if(!in_array($finfo[0],$this->gl_cloak_height) or !in_array($finfo[1],$this->gl_cloak_width))
			{
		        	$result['error'][] = 'Не верные размеры скина';
			}
                        elseif(($finfo[0] > 22 or $finfo[1] > 17))
			{
			       	if(!$this->userRights[$sid]['hd_cloak'])
				{
			        	$result['error'][] = 'Не достаточно прав статуса для HD плаща';
				}
			}
			
			if($result['error'])
			{
				unlink($dir.$this->user['name'].'tmp.png');
			}
			else
			{
				copy($dir.$this->user['name'].'tmp.png',$dir.$this->user['name'].'.png');
				unlink($dir.$this->user['name'].'tmp.png');
				$result['skins'] = $this->getSkin();
			}
		}
		else
		{
			$result['error'][] = 'Не удалось скопировать файл';
		}
		return $result;
	}
	
	function delUserFile($serverid, $fileType){
		$url = $fileType == 'Skin' ? SKIN_DIR : CLOAK_DIR;
		
		$result = array();
		
		$sid = intval($serverid);
		if(!$sid or !$this->servers[$sid]){$result['error'][] = 'Сервер не найден или не активен';return $result;}
		
		if(!$this->servers[$serverid]['server_name']){$result['error'][] = 'Сервер не найден или не активен';return $result;}
		$file = $url.$this->servers[$_REQUEST['serverid']]['server_name'].'/'.$this->user['name'].'.png';
		if(!file_exists($file)){$result['error'][] = 'Сервер не найден или не активен';}
		unlink($file);
		$result['skins'] = $this->getSkin();
		return $result;		
        }
        
}