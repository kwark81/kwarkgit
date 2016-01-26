<?
//error_reporting(E_ALL);
//ini_set('display_errors', 1);


//if($_SESSION['dle_user_id']){
//           $userID = $_SESSION['dle_user_id'];
//}
//elseif($_COOKIE['dle_user_id']){
//           $userID = $_COOKIE['dle_user_id'];
//}

//echo 'юзер айди - '.$userID.', должно быть 1(teo) 2(kwark) 3(seno)';

//lk::dump($userID, '$userID');


require('lkconfig.php');
use lk\lk;
lk::$userID = 2;

$data = lk::get('Status')->getStatus();
$data2 = lk::get('User')->getUserRights();
lk::dump($data, '$data');
lk::dump($data2, '$data2');


//lk::$userID = 2;

//lk::get('Remote')->connect(1);
//lk::get('Remote')->setQuery("UPDATE economy SET money = 1000 WHERE name = 'kwark'");

//$t = lk::get('User')->getUserData();

//lk::dump($t, '$def');



/*
lk::$userID = 2;

lk::get('Remote')->getServerList();

lk::dump($t, 'res $t');

$data = lk::get('User')->getUserData();
lk::dump($data, 'res data');
*/

//$user = lk::get('User')->getUserData();
//lk::dump($user, 'res udata');

//$user = lk::get('User')->getUserRights();
//lk::dump($user,'res rights');

//$user = lk::get('Skin')->getSkin();
//lk::dump($user,'res Skin');

?>
<div ng-app="lk">
<div class="container" ng-controller="glk">
	<div class="row">
	<div class="col-xs-5" ng-controller="lkskin" ng-cloak>
		<div class="title">Скин и плащъ</div>
		<div id="skins"></div>
		<div class="row">
			<div class="col-xs-2" ng-click="changeServer('prev')"><i class="fa fa-arrow-left"></i></div>
			<div class="col-xs-3">{{server.name}}</div>
			<div class="col-xs-2" ng-click="changeServer('next')"><i class="fa fa-arrow-right"></i></div>
			<div class="col-xs-5"><default /></div>
		</div>
		<div class="row" ng-controller="upload">
			<div class="col-xs-6">
				<button ng-show="!skindefault" ng-click="getSkin('delSkin')" class="btn btn-default" ng-disabled="load==true">Удалить скин</button>
				<button ng-show="skindefault" class="btn btn-default" ngf-select="upload($file,'Skin')" name="skin" ngf-pattern="'image/*'" accept="image/png" ng-disabled="load==true">Загрузить скин</button>
			</div>
			<div class="col-xs-6">
				<button ng-show="!cloakdefault" ng-click="getSkin('delCloak')" class="btn btn-default" ng-disabled="load==true">Удалить плащ</button>
				<button ng-show="cloakdefault" class="btn btn-default" ngf-select="upload($file,'Cloak')" name="cloak" ngf-pattern="'image/*'" accept="image/png" ng-disabled="load==true">Загрузить плащ</button>
			</div>
			<div class="progress" ng-show="load">
  				<div class="progress-bar" role="progressbar" aria-valuenow="{{loadPercent}}" aria-valuemin="0" aria-valuemax="100" style="width: {{loadPercent}}%;">
    					{{loadPercent}}%
  				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-7" ng-controller="finance" id="finance" ng-cloak>
		<div class="title">Управление счетами</div>
		<div class="title_inn col-xs-12">Рублевый счет</div>
		<div class="row">
				<form id="transfer_real" novalidate name="payform">
				<div class="col-xs-3">Баланс <i class="fa fa-rub"></i></div>
				<div class="col-xs-3"><label>{{funds.real}}</label></div>
                        	<div class="col-xs-2"><input type="text" class="form-control" placeholder="Сумма" ng-pattern="/^[0-9]{1,7}$/" ng-model="pay" summ></div>
				<div class="col-xs-3"><button class="btn btn-default col-xs-12" ng-click="$emit('pay')" ng-disabled="payform.$invalid">Пополнить</button></div>
				<div class="col-xs-1"><i class="fa fa-clock-o"></i></div>
				</form>
		</div>
		<div class="row">
				<form id="transfer_real" novalidate name="real">
				<div class="col-xs-3">Передать <i class="fa fa-rub"></i></div>
	                	<div class="col-xs-3">
	    	 		<angucomplete id="user_real"
              				placeholder="Введите ник"
              				pause="400"
              				selectedobject="transfer.real.name"
              				url="lkact/?action=getUserList&word="
              				datafield="results"
              				titlefield="name"
              				descriptionfield="id"
              				inputclass="form-control form-control-small"/>
				</div>
				<div class="col-xs-2"><input type="text" class="form-control" placeholder="Сумма" ng-model="transfer.real.val" ng-pattern="/^[0-9]{1,7}$/" summ></div>
				<div class="col-xs-3"><button class="btn btn-default col-xs-12" ng-disabled="real.$invalid" ng-click="$emit('real')">Передать</button></div>
				<div class="col-xs-1"><i class="fa fa-clock-o"></i></div>
				</form>
		</div>
		<div class="title_inn col-xs-12">Баланс голосов</div>
		<div class="row">
				<div class="col-xs-3">Баланс <i class="fa fa-thumbs-o-up"></i></div>
				<div class="col-xs-3"><label>{{funds.virtual}}</label></div>
                        	<div class="col-xs-5"><a href="/" class="btn btn-default col-xs-12">Проголосовать</a></div>
				<div class="col-xs-1"><i class="fa fa-clock-o"></i></div>
		</div>
		<div class="row">
				<form id="transfer_virtual" novalidate name="virtual">
				<div class="col-xs-3">Передать <i class="fa fa-thumbs-o-up"></i></div>
	                	<div class="col-xs-3">
		     		<angucomplete id="user_virtual"
        	      			placeholder="Введите ник"
              				pause="400"
              				selectedobject="transfer.virtual.name"
              				url="lkact/?action=getUserList&word="
              				datafield="results"
              				titlefield="name"
              				descriptionfield="id"
              				inputclass="form-control form-control-small"/>
				</div>
				<div class="col-xs-2"><input type="text" class="form-control" placeholder="Кол-во" ng-pattern="/^[0-9]{1,7}$/" ng-model="transfer.virtual.val" summ></div>
				<div class="col-xs-3"><button class="btn btn-default col-xs-12" ng-disabled="virtual.$invalid" ng-click="$emit('virtual')">Передать</button></div>
				<div class="col-xs-1"><i class="fa fa-clock-o"></i></div>
				</form>
		</div>
		<div class="title_inn col-xs-12">Баланс на серверах</div>
		<div class="row">
			<div class="col-xs-6" ng-repeat = "srv in funds.servers"><input type="radio" name="servers" ng-model="transfer.servers.sid" ng-value="{{srv.id}}" ng-disabled="{{srv.cash<=0}}"> {{srv.name}} <span class="right yell">{{srv.cash}}</span></div>
		</div>
		<div class="row">
		<form name="servers" novalidate id="transfer_servers">
			<div class="col-xs-3">Передать <i class="fa fa-dot-circle-o"></i></div>
	        	<div class="col-xs-3">
	    		<angucomplete id="user_servers"
              		placeholder="Введите ник"
              		pause="400"
              		selectedobject="transfer.servers.name"
              		url="lkact/?action=getUserList&word="
              		datafield="results"
              		titlefield="name"
              		descriptionfield="id"
              		inputclass="form-control form-control-small"/>
			</div>
			<div class="col-xs-2"><input type="text" class="form-control" placeholder="Сумма" ng-model="transfer.servers.val" ng-pattern="/^[0-9]{1,7}$/" summ></div>
			<div class="col-xs-3"><button type="submit" class="btn btn-default col-xs-12" ng-disabled="servers.$invalid" ng-click="$emit('servers')">Передать</button></div>
			<div class="col-xs-1"><i class="fa fa-clock-o"></i></div>
		</form>
		</div>
	</div>
	</div>
	<div class="row">
		<div class="col-xs-12" ng-controller="status" ng-cloak>
		<div class="title">Управление статусами</div>
		<div class="col-xs-12">
		<table id="stats" width="100%">
			<tr><th>Сервер</th><th>Статус</th><th>Начало</th><th>Окончание</th><th>Осталось</th><th>Скидка</th><th>Продление</th><th>Управление</th></tr>
			<tr ng-repeat="serv in data.servers">
				<td>{{serv.name}}</td>
				<td>{{serv.group_name}}</td>
				<td>{{serv.start_date}}</td>
				<td>{{serv.expiration_date}}</td>
				<td>{{serv.delta}}</td>
				<td>{{serv.discount}} %</td>
				<td><button class="btn btn-default col-xs-12">Продлить</button></td>
				<td><button class="btn btn-default col-xs-12">Апгрейд</button></td>
			</tr>
		</table>
		</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12" ng-controller="nickname" ng-cloak>
		<div class="title">Управление ником и приставкой</div>
		<div class="col-xs-12">
		<table id="stats" width="100%">
			<tr><th>Сервер</th><th>Приставка</th><th>Ник</th><th>Сообщение</th><th colspan="2">Управление</th></tr>
			<tr ng-repeat="serv in servers">
				<td>{{serv.name}}</td>
				<td>{{serv.group_name}}</td>
				<td>{{serv.start_date}}</td>
				<td>{{serv.expiration_date}}</td>
				<td><button class="btn btn-default col-xs-12">Применить</button></td>
				<td><button class="btn btn-default col-xs-12">Сбросить</button></td>
			</tr>
		</table>
		</div>
		</div>
	</div>
	

</div>
</div>


