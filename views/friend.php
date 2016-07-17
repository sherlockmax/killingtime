<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$pageName = 'friendManage';
?>
<!DOCTYPE HTML>
<html>
<head>
	<?PHP include_once('init.php'); ?>
	<script>
	$(document).ready(function(){
		var availableTags = [];
		
		$("#show_findPlayer").button({
			icons: { primary: "ui-icon-search" }
		});
		$('#show_friendApply').button({
			icons: { primary: "ui-icon-plus"}
		});
		$('#show_friendInvite').button({
			icons: { primary: "ui-icon-mail-closed"}
		});
		$('#show_friendList').button({
			icons: { primary: "ui-icon-person"}
		});
		$('.toolBar input:text').button().css({
		       'outline' : 'none',
		        'cursor' : 'text',
		});
		$('.friendListBox input').each(function(){
			$(this).button();
		});
		$('#nickname').on("change blur keyup keypress", function(){
			$.ajax({
				method: "POST",
				url: "/friend/queryNicname",
				data: { keyname: $(this).val() }
			}).done(function( nicknames ) {
				if(nicknames.length > 0){
					availableTags = nicknames.split(",");
					$( "#nickname" ).autocomplete("option", { source: availableTags });
				}
			});
		});

		$( "#nickname" ).autocomplete({
			source: availableTags
		});
		
		$('');
	});
	</script>
</head>
<body>
	<div id="background">
		<?php include_once("header.php") ?>
		<div id="body">
			<div>
				<div>
					<div class="toolBar">
						<button id="show_friendList" type="button">好友列表</button>
						<button id="show_friendApply" type="button">提出的申請</button>
						<button id="show_friendInvite" type="button">收到的邀請</button>
						<input id="nickname" type="text" placeholder="輸入暱稱">
						<button id="show_findPlayer" type="button">搜尋玩家</button>
					</div>
					<div class="about">
						<div class="content" >
							<ul >
								<li>
									<h3>好友列表(50)</h3>
									<div class="friendListBox">
										<ul>
											<li class="friendListLine_nodata">
												<label style="font-size: 20px;">目前尚未擁有好友</label>
											</li>
											<?PHP for($i = 1; $i <= 50; $i++){  ?>
											<li class="friendListLine">
												<img src="/images/head/head_1.jpg">
												<label>
													<span style="color: #6495ED	; font-size: 20px; font-weight: bold;">殺很大殺很大殺很殺很</span>
													<br>
													勝場：100
													<br>
													敗場：50</br>
													目前在線：是
												</label>
												<div>
													<input type="button" value="邀請對戰">
													<input type="button" value="刪除好友">
												</div>
											</li>
											<li style="text-align: right; margin: 0px 0px 15px 0px !important;">
												<span>No. <?= $i ?></span>
												<input style="width: 60px; height: 25px; font-size: 10px; padding: 0px;" type="button" value="TOP" onclick="$( 'div .friendListBox' ).animate({scrollTop:0}, 'slow');">
											</li>
											<?PHP } ?>
										</ul>
									</div>
								</li>
							</ul>
						</div>
						<div class="aside">
							<ul>
								<li>
									<h3>近期對戰玩家</h3>
									<div class="friendListBox">
										<ul>
											<li class="historyListLine">
												<label>
													<span style="color: #6495ED	; font-size: 20px; font-weight: bold;">殺很大殺很大殺很殺很</span>
													<br>
													<span style="font-size: 20px; font-weight: bold;">井字遊戲</span>
													<span style="color: green; font-size: 20px; font-weight: bold;">勝利</span>
													<input style="width: 100px; font-size: 10px" type="button" value="加入好友">
												</label>
												
											</li>
											<li class="historyListLine">
												<label>
													<span style="color: #6495ED	; font-size: 20px; font-weight: bold;">殺很大</span>
													<br>
													<span style="font-size: 20px; font-weight: bold;">井字遊戲</span>
													<span style="color: red; font-size: 20px; font-weight: bold;">戰敗</span>
													<input style="width: 100px; font-size: 10px" type="button" value="加入好友">
												</label>
												
											</li>
										</ul>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php include_once("footer.php"); ?>
	</div>
</body>
</html>