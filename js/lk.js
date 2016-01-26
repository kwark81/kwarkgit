angular.module('lk', ['ngFileUpload','angucomplete'])
.controller('glk', ['$window','$http','$scope', 
	function (w,h,s){
		s.dir = '/persacc/action.php';
		s.server = {
			id : '',
			name:'',
			defaultID: 1,
			action:{},
		};
		s.load = false;
                s.getData = function(params){
			s.load = true;
			params.serverid = s.server.id;
			console.log('http params', params);
			return(h.get(s.dir,{params:params}));
		};
		/*s.setDS = function(){
			//s.getData({action:'setDefServer', sid: s.server.id, status:n})
			//.then(function(responce){console.log(responce);},function(responce){console.log(responce);});
			console.log('default', arguments, this);
		};*/
		
		s.dialog = function(text){
                        angular.element('#lkBody').html(text); 
			angular.element('#lkModal').modal();
		}
		s.getData({action:'getDS'}).then(
		function(resp){s.load = false;s.server.defaultID = resp.data[0]; },function(){s.load = false;});
	}
])	
.controller('lkskin',['$scope',	function(s){
    	s.skins = {servers:{}};
   	s.Max = 0;
	//s.options = {};
	s.cur = 0;
   	
	s.setSkin = function(){
		console.log('set skin', s.skins.servers[s.cur]);
		$('#skins').empty();
		$('<div />',{id:'server_'+s.server.id, class:'skin'}).appendTo('#skins');
		
        	new Skin("server_"+s.server.id, s.skins.servers[s.cur]['skin']['img'], s.skins.servers[s.cur]['cloak']['img']?s.skins.servers[s.cur]['cloak']['img']:'');
		s.skindefault = s.skins.servers[s.cur]['skin']['default'];
		s.cloakdefault = s.skins.servers[s.cur]['cloak']['default'];
	};
	
	s.changeServer = function(dir){
	   	console.log('chdir', dir);
		if(dir == 'prev')
		{
			s.cur = (s.cur-1) > 0 ? s.cur - 1 : s.Max; 
			s.server.id = s.skins.servers[s.cur]['id'];	
		}
		else if(dir == 'next')
		{
			s.cur = (s.cur+1) > s.Max ? 0 : s.cur + 1; 
			s.server.id = s.skins.servers[s.cur]['id'];	
		}
		else
		{
			if(!s.server.id){
				angular.forEach(s.skins.servers, function(value, key) {
			   		if(value.id == s.server.defaultID){
			   			s.cur = key;
						s.server.id = s.server.defaultID;
					}
				});
			}
		}
		console.log('cur server ID', s.server.id);
		s.server.name = s.skins.servers[s.cur]['name'];
		s.setSkin();
	};
	   
	s.getSkin = function(action){
		s.getData({'action':action})
		.then(
			function(responce){
					console.log('skins responce',responce);
					if(angular.isDefined(responce.data.error)){
                                        	s.dialog(responce.data.error);
					}
	    				else{
	    					s.skins.servers = responce.data.skins;
						s.Max = Object.keys(responce.data.skins).length - 1;
						s.changeServer();
	    				}
					s.load = false;
			},
                        function(responce){
					console.log('error',responce);
					s.load = false;
			}
		);
	};
	s.getSkin('getSkins');		   
}])
.controller('upload', ['$scope', 'Upload', function (s, u) {
    console.log(s);
    s.load = false;
    s.loadPercent = 0;
    s.submit = function() {
      	if (form.file.$valid && s.file) {
       		 s.upload(s.file);
      	}
    };
    s.upload = function (f,t) {
        s.load = true;
	s.loadPercent = 0;
	console.log('addSkin', s.server.id, t, f);
	u.upload({
            	url: s.dir,
            	data: {file: f, type: t, serverid: s.server.id}
        }).then(function (responce) 
	{
		console.log('after addSkin', responce);
		if(angular.isDefined(responce.data.error))
		{ 
			s.dialog(responce.data.error);
		}
	    	else{
	    		s.skins.servers = responce.data.skins;
			s.changeServer();
	    	}
		f = s.load = false;
		s.loadPercent = 0;
        }, 
	function (responce){
	        $('<div />',{text:responce.status}).dialog();
        	s.load = false;
		s.loadPercent = 0;
        }, 
	function (evt){
            	s.loadPercent = parseInt(100.0 * evt.loaded / evt.total);
        });
    };
    s.uploadFiles = function (f) {
      if (f && f.length) {
        	u.upload({data: {file: f}});
      }
    }
}])
.controller('finance',['$scope',function(s){
	s.pay = '';
	s.funds = { real:0, virtual:0, servers:{} };
	s.transfer = { real:{val: '', name: ''}, virtual:{val:'', name:''}, servers:{sid:'',name:'',val:''}};
	
	s.$on('real',function(e){
		if(!s.transfer.real.name){ angular.element('#user_real_value').focus(); return false;}
		s.trans('real');
	});
	
	s.$on('virtual',function(e){
		if(!s.transfer.virtual.name){ angular.element('#user_virtual_value').focus(); return false;}
		s.trans('virtual');
	});
	
        s.$on('servers',function(e){
		if(!s.transfer.servers.name){ angular.element('#user_servers_value').focus(); return false;}
		s.trans('servers');
	});
	
        s.trans = function(type){
		
		console.log('transfer request', s.transfer[type]);
		
        	s.getData({action:'transfer', type:type, data: s.transfer[type]})
		.then(
			function(responce){
				console.log(responce);
				var r = responce.data.error ? responce.data.error : responce.data[0];
				s.dialog(r);
				if(angular.isUndefined(responce.data.error))
				{
					s.getFunds('getFunds');
					angular.element('[name='+type+']').find('input').val('');
				}
				s.load = false;
        		},
			function(responce){
                        	console.log(responce);
				s.load = false;
        		}
		);
	}
		
        s.$on('pay',function(e){
		s.getData({action:'pay', amount: s.pay})
		.then(
		       function(responce){console.log(responce);
		             //if(angular.isDefined(responce.data)){
		                    window.location.href = responce.data;
			     //}
			     s.load = false;
        	       },
		       function(responce){
				console.log(responce);
				s.load = false;
        			
			}
			
		);//e.preventDefault();
		//if(!s.transfer.real.name){angular.element('#user_real_value').focus();return false;}
		//else if(!angular.isNumber(s.transfer.real.val) || s.transfer.real.val > s.funds.real){return false;}
		console.log('event pay', s.pay);
	});
	
	s.getFunds = function(action){
		s.getData({action: action})
		.then(
	  		function(responce){
				console.log('funds',responce);
				if(angular.isDefined(responce.data.funds)){
					s.funds.real = responce.data.funds.real.value;
					s.funds.virtual = responce.data.funds.virtual.value;
				}
				if(angular.isDefined(responce.data.servers)){
					console.log('funds servers',responce.data.servers);
					s.funds.servers = responce.data.servers;
				}
				if(angular.isDefined(responce.data.error)){
					s.dialog(responce.data.error);
				}
				s.load = false;
        				
			},
	  		function(responce){
                                s.load = false;
        		}
		);
	}

	s.getFunds('getFunds');

}])
.controller('status',['$scope',
	function(s){
		console.log('status start',s);
		s.data = {};
		s.getStatus = function(action)
		{
		
			console.log('status...');
			s.getData({action: action})
			.then(
	  			function(responce){
					if(!angular.isUndefined(responce.data)){
						s.data = responce.data;
					}
					s.load = false;
        				console.log('status', responce , s.data);
				},
	  			function(responce){
                                	s.load = false;
        			}
			);
		};
		s.getStatus('getServerStatus');
	}
])
.controller('nickname',['$scope',
	function(s){
		
	}
])
.directive('summ', [function(){
  return {
    restrict: 'A', // only activate on element attribute
    require: '?ngModel', // get a hold of NgModelController
    link: function(scope, element, attrs, ngModel) {
       	ngModel.$validators.real = function(modelValue, viewValue) {
        	//console.log('direct', scope, element, attrs, attrs.ngModel);
		if(attrs.ngModel == 'pay'){
			var c = parseInt(modelValue);
		        if (c > 0) {
				return true;
        		}
		}
                else if(attrs.ngModel == 'transfer.real.val'){
			var c = parseInt(modelValue);
		        var f = parseInt(scope.funds.real);
			if (c > 0 && f >= c) {
				return true;
        		}
		}
		else if(attrs.ngModel == 'transfer.virtual.val'){
			var c = parseInt(modelValue);
		        var f = parseInt(scope.funds.virtual);
			if (c > 0 && f >= c) {
				return true;
        		}
		}
		else if(attrs.ngModel == 'transfer.servers.val'){
			var c = parseInt(modelValue);
			if(scope.transfer.servers.sid == ''){return false;}
		        var f = parseInt(scope.funds.servers[scope.transfer.servers.sid]['cash']);
			if (c > 0 && f >= c) {
				return true;
        		}
		}
		return false;
        };
    }
  };
}])
.directive('default', [function(){
  return {
    restrict: 'E', // only activate on element attribute
    link: function(scope, element, attrs) {
    	element.on('click', function(event) {
		scope.getData({ action:'setDS', val: scope.checkbox });
	});
    },
    controller: function($scope, $element){
	$scope.checkbox = Number($scope.checkbox);
    },	
    template: '<label><input type="checkbox" ng-model="checkbox" ng-checked="server.defaultID == server.id" ng-true-value="1" ng-false-value="0">По умолчанию</label>',
  };
}])
;


$(function(){
var m = ['<div class="modal" id="lkModal">',
  '<div class="modal-dialog">',
    '<div class="modal-content">',
      '<div class="modal-header">',
        '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>',
      '</div>',
      '<div class="modal-body" id="lkBody"></div>',
    '</div>',
  '</div>',
'</div>'].join('');
$(m).appendTo('body');

});