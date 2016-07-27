var dataBox = {
action : '',
data : ''
}
$(document).ready(function(){
	
	function checkPlayerLoop(){
		
		if (typeof player.account != 'undefined' || typeof player.nickname != 'undefined'){
			alert("無法取得您的訊息，請重新整理或重新登入。");
			location.reload(true);
		}
		setTimeout(logoLoop, 5000);
	}
	
	$(window).bind('beforeunload', function(){
		return '離開此頁面，將與遊戲大廳中斷連線，並不會儲存目前的遊戲紀錄。';
	});
	
	function resetGameRoom(){
		$('div #playRoom .gameController ul li').each(function(){
			$(this).text("");
		});
		$("#chatMessage").html("");
		$('#you_img').attr("src", imgRoot + "head/head_0.jpg");
		$('#other_img').attr("src", imgRoot + "head/head_0.jpg");
		$('#you_nickname').text("--等待對手加入--");
		$('#other_nickname').text("--等待對手加入--");
		$('#loser').hide();
		$('#winner').hide();
		$("#tie").hide();
		$('#iwantsay').val("");
	}
	
	$('#noConnection').show();
	$('#playRoom').dialog({ autoOpen: false});
	
	var conn = new WebSocket('ws://localhost:8080');
	conn.onopen = function(e) {
		alertMsg("提示訊息", "遊戲伺服器連接成功！");
		$('#noConnection').hide();

		dataBox.action = 'setPlayer';
		dataBox.data = player;
		sendData(dataBox);
	};

	conn.onmessage = function(e) {
		var dataBox = $.parseJSON( e.data );
		var data = null;
		console.log("GET------dataBox---"+ dataBox.action);	
		if(dataBox.data != 'null'){
			data = $.parseJSON( dataBox.data );
			console.log("GET------data---" + dataBox.data);	
			console.log(data);
		}
		console.log("----------------------");	
		
		if(data.action == 'roomIsFull'){
			alerMsg("警告訊息", "該遊戲室人員已塞滿，無法進入。");
		}

		if(dataBox.action == 'refreshGameRoom'){
			if(data == "null" || data.length <= 0){
				$('#noConnection').hide();
				$('li[id^=room_]').each(function(){
					$(this).remove();
				});
				$('#noGameRoom').show();
			}else{
				$('#noConnection').hide();
				$('#noGameRoom').hide();
				$('li[id^=room_]').each(function(){
					$(this).remove();
				});
				$.each(data, function () {
					if(this.player1 == null || this.player2 == null){
						var playerName = "";
						var palyerAccount = "";
						if(this.player1 != null){
							palyerAccount = this.player1.account;
							playerName = this.player1.nickname;
						}
						if(this.player2 != null){
							palyerAccount = this.player2.account;
							playerName = this.player2.nickname;
						}
						
						var newGameRoom = $('#ex_room_0000').clone();
						$(newGameRoom).attr('id', 'room_' + this.roomID);
						$(newGameRoom).find('label[class=title]').text('第 '+ this.roomID +' 室  '+ this.gameName);
						$(newGameRoom).find('label[class=playerName]').text(playerName);
						$(newGameRoom).find('#btn_createGameRoom').text('進入挑戰');
						$(newGameRoom).find('#btn_createGameRoom').attr('id', "btn_joinGameRoom");
						$('#gameList').append(newGameRoom);
						$('#room_'+ this.roomID).show();
					}
				});
			}
		}
		
		if(dataBox.action == 'winner'){
			if(data.account == 'tie'){
				$("#tie").show("slow", "swing");
			}else if(data.account == player.account){
				$("#winner").show("slow", "swing");
			}else{
				$("#loser").show("slow", "swing");
			}
		}
		
		if(dataBox.action == 'oneMoreTime'){
			$('#playRoom').find('li[id^=table_]').each(function(){
				$(this).text("");
			});
			$('#playRoom #tie').hide();
			$('#playRoom #winner').hide();
			$('#playRoom #loser').hide();
			
			if(data.whosturn == player.account){
				$('#whosturn').text("該你/妳囉");
			}else{
				$('#whosturn').text("等待對手!");
			}
		}
		
		if(dataBox.action == 'sendMessage'){
			$('#chatMessage').append("<label class='guestName'>"+data.nickname+"</label><label class='message'>"+data.msg+"</label>");
			$("#chatMessage").animate({ scrollTop:  $('#chatMessage').prop("scrollHeight") }, 500);
		}
		
		if(dataBox.action == 'joinGameRoom'){
			if ($('#playRoom').dialog('isOpen') === true) {
				$('#playRoom').find('#other_nickname').text(data.player2.nickname);
				$('#playRoom').find('#other_img').attr("src", imgRoot + "head/"+data.player2.account+".jpg");
				$('#btn_sendMessage').button({disabled: false});
			} else {
				$("#playRoom").dialog({
					title: "第 " + data.roomID + " 遊戲室 - " + data.gameName,
					modal: true,
					resizable: false,
					draggable: false,
					autoOpen: true,
					height: 750,
					width: 950,
					open: function() {
							$(this).find('#you_nickname').text(data.player2.nickname);
							$(this).find('#you_img').attr("src", imgRoot + "head/"+data.player2.account+".jpg");
							$(this).find('#other_nickname').text(data.player1.nickname);
							$(this).find('#other_img').attr("src", imgRoot + "head/"+data.player1.account+".jpg");
							
							$(this).find('#btn_sendMessage').button({disabled: false});
							$(this).find('#btn_sendMessage').attr("name", "roomID_" +data.roomID);
							
							var gameBoard = drawGame(data.gameName);
							$(".gameController").html(gameBoard);
					},
					buttons: {
						"離開": function() {
							dataBox.action = 'leaveRoom';
							dataBox.data = {'roomID' : data.roomID , 'player' : player };
							sendData(dataBox);
							$( this ).dialog( "close" );
							resetGameRoom();
						}
					}
				});
			}

			if(data.whosturn == player.account){
				$('#whosturn').text("該你/妳囉");
			}else{
				$('#whosturn').text("等待對手!");
			}
		}
		
		if(dataBox.action == 'createRoom'){			
			$("#playRoom").dialog({
				title: "第 " + data.roomID + " 遊戲室 - " + data.gameName,
				modal: true,
				resizable: false,
				draggable: false,
				autoOpen: true,
				height: 750,
				width: 950,
				open: function() {
						$(this).find('#you_nickname').text(data.player1.nickname);
						$(this).find('#you_img').attr("src", imgRoot + "head/"+data.player1.account+".jpg");
						$(this).find('#btn_sendMessage').button({disabled: true});
						$(this).find('#btn_sendMessage').attr("name", "roomID_" +data.roomID);	
						var gameBoard = drawGame(data.gameName);
						
						$(".gameController").html(gameBoard);
				},
				buttons: {
					"再來一局": function() {
						if($('#other_nickname').text() != "--等待對手加入--"){
							if($('#playRoom #loser').is(':visible') ||
								$('#playRoom #tie').is(':visible') ||
								$('#playRoom #winner').is(':visible')){
								
								dataBox.action = 'oneMoreTime';
								dataBox.data = {'roomID' : data.roomID };
								sendData(dataBox);
							}else{
								alertMsg("提示訊息","請先完成本局遊戲。");
							}
						}else{
							alertMsg("提示訊息","尚未有對手。");
						}
						
					},
					"離開": function() {
						dataBox.action = 'leaveRoom';
						dataBox.data = {'roomID' : data.roomID , 'player' : player };
						sendData(dataBox);
						$( this ).dialog( "close" );
						resetGameRoom();
					}
				}
			});
		}
		
		if(dataBox.action == 'anotherOneleft'){
			if(data.leaveAccount != player.account){
				alert("對手已離開遊戲室");
			}
			$("#playRoom").dialog('close');
			resetGameRoom();
		}
		
		if(dataBox.action == 'gameStep'){
			if(data.whosturn != player.account){
				$('#whosturn').text("該你/妳囉");
			}else{
				$('#whosturn').text("該對手囉");
			}
			
			var tableID = 'table_' + data.tableID;
			var mark = data.mark;
			$('#'+tableID).text(mark);
		}
		
		if(dataBox.action == 'saveGameRecord'){
			$.ajax({
				method: "POST",
				url: root + "game/saveGameRecord",
				data: data
			}).done(function(msg){
				console.log("saveGameRecord");
				console.log(data);
				console.log("----------------------");
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
		dataBox = {
			action : '',
			data : ''
		}
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
				dataBox.action = 'createRoom';
				dataBox.data = {'gameName' : $('#CGR_gameName').val()};
				sendData(dataBox);
				$( this ).dialog( "close" );
			}
		}
	});
		
	$('#gameList').delegate('a', 'click', function(){
		if($(this).attr('id') == 'btn_createGameRoom'){
			$("#createGameRoomSetting").dialog("open");
			$('#CGR_gameName').selectmenu({width: 250});
		}else{
			var roomID = $(this).closest("li").attr("id").split("_")[1];
			dataBox.action = 'joinGameRoom';
			dataBox.data = {'roomID' : roomID, "player" : player};
			sendData(dataBox);
		}
	});
	
	
	$("#btn_sendMessage").click(function() {
		if($('#iwantsay').val().length > 0){
			var now = new Date(Date.now());
			var formatted = now.getHours() + ":" + now.getMinutes() + ":" + now.getSeconds();
			
			var msg = escapeHtml($('#iwantsay').val());
			var roomID = $(this).attr('name').split("_")[1];
			$('#iwantsay').val("");
			$('#chatMessage').append("<label class='hostName'>"+player.nickname + " (" +formatted+")</label><label class='message'>"+msg+"</label>");
			dataBox.action = 'sendMessage';
			dataBox.data = {'roomID' : roomID, "player" : player, "msg": msg};
			sendData(dataBox);
			
			$("#chatMessage").animate({ scrollTop:  $('#chatMessage').prop("scrollHeight") }, 500);
		}
		$('textarea').focus();
	});
	
	$('textarea').keypress(function (e) {
		if (e.which == 13) {
			if(!$("#btn_sendMessage").is(":disabled")){
				$('#btn_sendMessage').trigger('click');
				$('textarea').focus();
			}else{
				$('textarea').val("");
				$('textarea').focus();
			}
			return false;
		}
	});
	
	$('div #playRoom .gameController ul li').click(function(){
		if($(this).text() == "" && $('#other_nickname').text() != "--等待對手加入--"){
			if($('#whosturn').text() == "該你/妳囉"){
				$('#whosturn').text("該對手囉");
			}
			var id = $(this).attr('id').split("_")[1];
			var roomID = $('#btn_sendMessage').attr('name').split("_")[1];
			dataBox.action = 'gameStep';
			dataBox.data = {'roomID' : roomID, "player" : player, "tableID": id};
			sendData(dataBox);
		}
	});
	
	function drawGame(gameName){
		var gameBoard = "";
		var draw = {
					"row" : 0,
					"col" : 0,
					"class" : ''
		}
		if(gameName == '井字遊戲'){
			draw.row = 3;
			draw.col = 3;
			draw.class = 'TicTacToe';
		}else if(gameName == '暗棋'){
			draw.row = 4;
			draw.col = 8;
			draw.class = 'DarkChess';
		}
		
		for(var row=1; row<=draw.row; row++){
			gameBoard += "<ul>";
			for(var col=1; col<=draw.col; col++){
				gameBoard += "<li class='"+draw.class+"' id='table_"+row+"-"+col+"'></li>";
			}
			gameBoard += "</ul>";
		}
		
		return gameBoard;
	}
});