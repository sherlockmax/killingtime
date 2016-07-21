<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$pageName = 'gameLobby'; 
?>
<!DOCTYPE HTML>
<html>
<head>
	<?PHP include_once('init.php'); ?>
	<script>
		var player = {
			account : '<?= $_SESSION["player"]["account"] ?>',
			nickname : '<?= $_SESSION["player"]["nickname"] ?>'
		};
		$(document).ready(function(){			
			$("#show_findGameRoom").button({
				icons: { primary: "ui-icon-search" }
			});
			$("#btn_createGameRoom").button({
				icons: { primary: "ui-icon-plus" }
			});
			
			$( "#gameName" ).selectmenu({width: 150});
			
			$('.toolBar input:text').button().css({
			       'outline' : 'none',
			        'cursor' : 'text',
			});
		});
	</script>
	<?= $this->script('game') ?>
</head>
<body>
	<div id="background">
		<?php include_once("header.php") ?>
		<div id="body">
			<div>
				<div>
					<div class="toolBar">
						<select id="gameName" name="gameName">
							<option disabled selected>依遊戲篩選</option>
							<option value="TicTacToe">井字遊戲</option>
							<!--<option value="猜數字">猜數字</option>-->
						</select>
						<input id="gameId" type="text" placeholder="輸入遊戲室編號">
						<button id="show_findGameRoom" type="button">搜尋遊戲室</button>
						<button id="btn_createGameRoom" type="button">建立遊戲室</button>
					</div>
					
					<div class="games">
						<div class="aside">
							<h3>遊戲室列表</h3>
							<ul id="gameList">
								<li id="noConnection" class="messageBox">
									<label class="title">正在連接至遊戲伺服器...</label>
								</li>
								<li id="noGameRoom" class="messageBox">
									<label class="title">目前尚無遊戲室</label>
								</li>
								<li id="ex_room_0000" style="display: none">
									<label class="title">第&nbsp;0001&nbsp;室&nbsp;&nbsp;井字遊戲</label>
									<label class="playerName">殺很大殺很大</label>
									<span><a id="btn_joinGameRoom" href="javascript:void(0);">進入挑戰</a></span>
								</li>
							</ul>
						</div>
					</div>
					
				</div>
			</div>
		</div>
		<?php include_once("footer.php"); ?>
	</div>
	
	<div id="createGameRoomSetting" title="建立遊戲室">
		<label>選擇遊戲</label>
		<select name="gameName" id="CGR_gameName">
			<option value="井字遊戲">井字遊戲</option>
			<!--<option value="猜數字">猜數字</option>-->
		</select>
	</div>
	
	<div id="playRoom" title="第 0001 遊戲室 - 井字遊戲" style="display: none">
		<img id="loser" src="images/loser.jpg">
		<img id="winner" src="images/winner.jpg">
		<div class="playerBox">
			<label id="you_nickname">--等待對手加入--</label>
			<img id="you_img" src="images/head/head_0.jpg">
			<div id="whosturn">該你/妳囉</div>
			<img id="other_img" src="images/head/head_0.jpg">
			<label id="other_nickname">--等待對手加入--</label>
		</div>
		<div class="gameController">
			<ul>
				<li id="table_1-1"></li>
				<li id="table_1-2"></li>
				<li id="table_1-3"></li>
			</ul>
			<ul>
				<li id="table_2-1"></li>
				<li id="table_2-2"></li>
				<li id="table_2-3"></li>
			</ul>
			<ul>
				<li id="table_3-1"></li>
				<li id="table_3-2"></li>
				<li id="table_3-3"></li>
			</ul>
		</div>
		<div class="chatBox">
			<div id="chatMessage"></div>
			<textarea id="iwantsay"></textarea>
			<input id="btn_sendMessage" name="room_" type="button" value="送出訊息">
		</div>
	</div>
</body>
</html>