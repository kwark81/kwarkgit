<?
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//session_start();

require_once("lkconfig.php");
use lk\lk;

if(isset($_REQUEST['amount']) or isset($_REQUEST['method']) or isset($_GET['reply']))
{	
	if (isset($_REQUEST['method']) && isset($_REQUEST['params']))
	{
		switch (strtolower($_REQUEST['method']))
		{
			case "check" :
				echo lk::get('Payclass')->up_sign($_REQUEST['params']); 
			break;
			case "pay" :
				echo lk::get('Payclass')->up_sign($_REQUEST['params']);
				$dir = dirname(dirname(__FILE__));
				/*foreach($_REQUEST['params'] as $k=>$p){
					file_put_contents($dir.'/debug_payment.txt',$k.'---'.$p,FILE_APPEND);
				}*/
				lk::get('Payclass')->pay($_REQUEST['params']['sum'],$_REQUEST['params']['unitpayId'], $_REQUEST['params']['account']);
			break;
			default :
				lk::get('Payclass')->up_json_reply("error", $_REQUEST['params']);
			break;
		}
		exit();
	}
	else
	{
		if (isset($_GET['reply']))
		{
			echo lk::get('Payclass')->config['message'][$_GET['reply']];
		}
		else
		{
			if(!lk::get('Payclass')->ik_sign($_REQUEST)) exit("403");
			lk::get('Payclass')->pay($_REQUEST['ik_am'], $_REQUEST['ik_pm_no']);
		}
	}
}

?>