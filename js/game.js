var dataArray = {
action : '',
data : ''
}
$(document).ready(function(){
	$(window).bind('beforeunload', function(){
		return '離開此頁面，將與遊戲大廳中斷連線，並不會儲存目前的遊戲紀錄。';
	});
	
	$('#noConnection').show();
	
	var conn = new WebSocket('ws://localhost:8080');
	conn.onopen = function(e) {
		alertMsg("提示訊息", "遊戲伺服器連接成功！");
		$('#noConnection').hide();

		dataArray.action = 'setPlayer';
		dataArray.data = player;		
		sendData(dataArray);
	};

	conn.onmessage = function(e) {
		var data = $.parseJSON( e.data );
		console.log(data);	
		
		if(data.action == 'gameRoomList'){
			if(data.gameRoomList == "null"){
				$('#noConnection').hide();
				$('#noGameRoom').show();
			}else{
				
			}
		}
		
		if(data.action == 'createRoom'){
			$("#playRoom").dialog({
				title: "第 " + data.roomID + " 遊戲室 - " + data.gameName,
				modal: true,
				resizable: false,
				draggable: false,
				height: 750,
				width: 950,
				buttons: {
					"離開": function() {
						dataArray.action = 'leaveRoom';
						dataArray.data = {'roomID' : data.roomID , 'player' : player };
						sendData(dataArray);
						$( this ).dialog( "close" );
					}
				}
			});
		}
	};
	
	conn.onerror = function(e){
		alertMsg("錯誤訊息", "無法連接至遊戲伺服器");
		$('#noConnection label').text("無法連接至遊戲伺服器");
		$('#noConnection').show();
		$('#noGameRoom').hide();
		$('li[id^=room_]').each(function(){
			$(this).remove();
		});
	}
	
	function sendData(data){
		conn.send(JSON.stringify(data));
	}
	
	$("#createGameRoomSetting").dialog({
		modal: true,
		autoOpen: false,
		resizable: false,
		height: 250,
		buttons: {
			"取消": function() {
				$( this ).dialog( "close" );
			},
			"建立": function() {
				dataArray.action = 'createRoom';
				dataArray.data = {'gameName' : $('#CGR_gameName').val()};
				sendData(dataArray);
				$( this ).dialog( "close" );
			}
		}
	});
	
	$('#btn_createGameRoom').click(function(){
		$("#createGameRoomSetting").dialog("open");
		$('#CGR_gameName').selectmenu({width: 250});
	});
});