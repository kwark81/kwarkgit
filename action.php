<?
session_start();

if($_SESSION['dle_user_id'])
{
        $userID = $_SESSION['dle_user_id'];
}

require('lkconfig.php');

use lk\lk;

$result = false;

if($userID){

lk::$userID = $userID; 

try
{
	if($_REQUEST['action']/* and $_REQUEST['serverid']*/)
	{
		switch($_REQUEST['action'])
		{
			case 'getSkins': $result = array('skins' => lk::get('Skin')->getSkin()); break;
			case 'delSkin': $result = lk::get('Skin')->delUserFile($_REQUEST['serverid'],'Skin'); break;
			case 'delCloak': $result = lk::get('Skin')->delUserFile($_REQUEST['serverid'],'Cloak'); break;
			case 'getFunds': $result = array('servers' => lk::get('Cash')->getUserCashFromServers(),'funds' => lk::get('Cash')->getUserCash()); break;
			case 'getServerStatus': $result = lk::get('Status')->getStatus(); break;
			case 'getUserList': $result = lk::get('User')->getUserList($_REQUEST['word']); break;
			case 'transfer': $result = lk::get('Cash')->transferFunds($_REQUEST); break;
			case 'pay': $result = urlencode(lk::get('Payclass')->pay_form( $_REQUEST['amount'], lk::get('Payclass')->config['user_param'], 'unitpay' )); break;
	                case 'setDS': $result = lk::get('User')->setUserDefaultServer($_REQUEST['serverid'],$_REQUEST['val']); break;
	        	case 'getDS': $result = lk::get('User')->getUserDefaultServer(); break;
	        	//getDS
		}
	}
	else if($_FILES['file'] and $_REQUEST['type'] and $_REQUEST['serverid'])
	{
		if ($_FILES['file']['type'] != 'image/png')
		{
			$result['error'] = 'не верное расширение файла.';
		}
		elseif($_FILES["file"]["size"] > 1024 * FILE_MAX_SIZE)
		{
			$result['error'] = 'не верный размер файла.';
		} 
		elseif(!is_uploaded_file($_FILES["file"]["tmp_name"]))
		{
			$result['error'] = 'файл не загружен';
		}

		if(!$result)
		{
			$result = call_user_func_array(array(lk::get('Skin'),'add'.$_REQUEST['type']),array());
		} 
	
	}
}
catch(\Exception $e)
{
	$result = array('error'=>$e->getMessage());
}

}
else{
	$result['error'] = 'NO USER ID IN CURRENT SESSION!';
}

if(!$result){ $result = 'Не верное действие'; }
if(!is_array($result)){ $result = array($result); }

$result['uid'] = $userID;

echo json_encode($result);

exit();

/*

if (isset($_REQUEST['amount']) && isset($_REQUEST['payment'])) 
	{
		echo 'eweewe';
		//echo json_encode(lk::get('Payment')->pay_form($_REQUEST['amount'], lk::get('Payment')->config['user_param'], $_REQUEST['payment']));
		//header ("Location: ".$pay->pay_form($_REQUEST['amount'], $pay->config['user_param'], $_REQUEST['payment']));
	}
	else 
*/
		

?>