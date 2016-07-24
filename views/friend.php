<!DOCTYPE HTML>
<html>
<head>
	<?PHP include_once('init.php'); ?>
	<script>
	$(document).ready(function(){
		var availableTags = [];
		
		$("#findPlayer").button({
			icons: { primary: "ui-icon-search" }
		});
		$('#friendInvite').button({
			icons: { primary: "ui-icon-plus"}
		});
		$('#whoInviteMe').button({
			icons: { primary: "ui-icon-mail-closed"}
		});
		$('#friendList').button({
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
				url: "<?= $config->root ?>friend/queryNicname",
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
		
		$('#findPlayer').click(function(){
			if($('#nickname').val().length <= 0){
				alertMsg("提示訊息", "請輸入暱稱!!");
			}else{
				var thisForm = $("div .toolBar").find("form");
				$(thisForm).attr("action", "<?= $config->root ?>friend/findPlayer");
				$(thisForm).submit();
			}
		});
		
		$('#friendInvite').click(function(){
			window.location.href = "<?= $config->root ?>friend/friendInvite";
		});
		
		$('#whoInviteMe').click(function(){
			window.location.href = "<?= $config->root ?>friend/whoInviteMe";
		});
		
		$('#friendList').click(function(){
			window.location.href = "<?= $config->root ?>friend";
		});
		
		$('#btn_addFriend').click(function(){
			var thisForm = $(this).parent("form");
			$(thisForm).attr("action", "<?= $config->root ?>friend/addFriend");
			$(thisForm).submit();
		});
		
		$('#btn_acceptInvite').click(function(){
			var thisForm = $(this).parent("form");
			$(thisForm).attr("action", "<?= $config->root ?>friend/acceptInvite");
			$(thisForm).submit();
		});
		
		$('#btn_rejectInvite').click(function(){
			var thisForm = $(this).parent("form");
			$(thisForm).attr("action", "<?= $config->root ?>friend/rejectInvite");
			$(thisForm).submit();
		});
		
		$('#btn_removeFriend').click(function(){
			var thisForm = $(this).parent("form");
			var friendNickname = $(this).closest('form').find('input[name=nickname]').val();
			$(thisForm).attr("action", "<?= $config->root ?>friend/removeFriend");
			
			$('div #alertMsg').attr("title", "提示訊息");
			$('div #alertMsg').html("確定刪除好友 - "+friendNickname+" - 嗎?");
			$("div #alertMsg").dialog({
				modal: true,
				autoOpen: true,
				resizable: false,
				draggable: false,
				buttons: {
					"確認": function() {
						$(thisForm).submit();
						$( this ).dialog( "close" );
					},
					"取消": function() {
						$( this ).dialog( "close" );
					}
				}
			});
			
			
		});
		
		$("#btn_deleteInvite").click(function(){
			var thisForm = $(this).parent("form");
			$(thisForm).attr("action", "<?= $config->root ?>friend/deleteInvite");
			$(thisForm).submit();
		});
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
						<form method="post" action="<?= $config->root ?>friend/findPlayer">
						<button id="friendList" type="button">好友列表</button>
						<button id="friendInvite" type="button">提出的邀請</button>
						<button id="whoInviteMe" type="button">收到的邀請</button>
						<input id="nickname" name="nickname" type="text" placeholder="輸入暱稱">
						<button id="findPlayer" type="button">搜尋玩家</button>
						</form>
					</div>
					<div class="about">
						<div class="content" >
							<ul >
								<li>
									<?PHP if($data['action'] == 'friendList')  { ?>
									<h3>好友列表(<?= count($data['friendList']) ?>)</h3>
									<div class="friendListBox">
										<ul>
											<?PHP if( empty($data['friendList']) ) {  ?>
											<li class="friendListLine_nodata">
												<label style="font-size: 20px;">目前尚未擁有好友</label>
											</li>
											<?PHP } else {
												$i = 1;
												foreach($data['friendList'] as $player) {  ?>
											<li class="friendListLine">
												<img src="<?= $config->imgRoot ?>head/<?= $player['account'] ?>.jpg">
												<label>
													<span style="color: #6495ED	; font-size: 20px; font-weight: bold;"><?= $player['nickname'] ?></span>
													<br>
													勝場：100
													<br>
													敗場：50</br>
													目前在線：<?= $player['isOnline'] ?>
												</label>
												<div>
													<form method="POST">
														<input type="hidden" id="account" name="account" value="<?= $player['account'] ?>">
														<input type="hidden" id="nickname" name="nickname" value="<?= $player['nickname'] ?>">
														<!--<input type="button" value="邀請對戰"  <?PHP if( $player['isOnline'] == "否" ) { echo "disabled"; } ?>> -->
														<input id="btn_removeFriend" type="button" value="刪除好友">
													</form>
												</div>
											</li>
											<li style="text-align: right; margin: 0px 0px 15px 0px !important;">
												<span>No. <?= $i ?></span>
												<input style="width: 60px; height: 25px; font-size: 10px; padding: 0px;" type="button" value="TOP" onclick="$( 'div .friendListBox' ).animate({scrollTop:0}, 'slow');">
											</li>
											<?PHP 
												$i++;
												} //end for $data
											} //end if
											?>
										</ul>
									</div>
									
									<?PHP } //end if (action) ?>
									
									<?PHP if($data['action'] == 'findPlayer')  { ?>
									<h3>玩家搜尋結果</h3>
									<div class="friendListBox">
										<ul>
											<?PHP if( empty($data['player']) ) {  ?>
											<li class="friendListLine_nodata">
												<label style="font-size: 20px;">查無符合搜尋條件的玩家</label>
											</li>
											<?PHP } else { ?>
											<li class="friendListLine">
												<img src="<?= $config->imgRoot ?>head/<?= $data['player']['account'] ?>.jpg">
												<label>
													<span style="color: #6495ED	; font-size: 20px; font-weight: bold;"><?= $data['player']['nickname'] ?></span>
													<br>
													勝場：100
													<br>
													敗場：50</br>
													目前在線：<?= $data['player']['isOnline'] ?>
												</label>
												<div>
													<form method="post">
													<input type="hidden" id="account" name="account" value="<?= $data['player']['account'] ?>">
													<?PHP if( $data['player']['friendStatus'] == "self" ) { ?>
													<input id="btn_removeFriend" type="button" value="自己" disabled>
													<?PHP }else if( $data['player']['friendStatus'] == "friend" ) { ?>
													<input id="btn_removeFriend" type="button" value="刪除好友">
													<?PHP }else if( $data['player']['friendStatus'] == "no" ) { ?>
													<input id="btn_addFriend" type="button" value="加入好友">
													<?PHP }else if( $data['player']['friendStatus'] == "invite" ) { ?>
													<input id="btn_deleteInvite" type="button" value="取消邀請">
													<?PHP }else if( $data['player']['friendStatus'] == "notInvite" ) { ?>
													<input id="btn_acceptInvite" type="button" value="接受邀請">
													<input id="btn_rejectInvite" type="button" value="拒絕邀請">
													<?PHP }?>
													</form>
												</div>
											</li>
											<?PHP 
											} //end if
											?>
										</ul>
									</div>
									
									<?PHP } //end if (action) ?>
									
									<?PHP if($data['action'] == 'friendInvite')  { ?>
									<h3>提出的好友邀請(<?= count($data['applyList']) ?>)</h3>
									<div class="friendListBox">
										<ul>
											<?PHP if( empty($data['applyList']) ) {  ?>
											<li class="friendListLine_nodata">
												<label style="font-size: 20px;">尚無提出的好友申請</label>
											</li>
											<?PHP } else {
												$i = 1;
												foreach($data['applyList'] as $player) {  ?>
											<li class="friendListLine">
												<img src="<?= $config->imgRoot ?>head/<?= $player['account'] ?>.jpg">
												<label>
													<span style="color: #6495ED	; font-size: 20px; font-weight: bold;"><?= $player['nickname'] ?></span>
													<br>
													勝場：100
													<br>
													敗場：50</br>
													目前在線：<?= $player['isOnline'] ?>
												</label>
												<div>
													<form method="post">
														<input type="hidden" id="account" name="account" value="<?= $player['account'] ?>">
														<input id="btn_deleteInvite" type="button" value="取消邀請">
													</form>
												</div>
											</li>
											<li style="text-align: right; margin: 0px 0px 15px 0px !important;">
												<span>No. <?= $i ?></span>
												<input style="width: 60px; height: 25px; font-size: 10px; padding: 0px;" type="button" value="TOP" onclick="$( 'div .friendListBox' ).animate({scrollTop:0}, 'slow');">
											</li>
											<?PHP 
												$i++;
												} //end for $data
											} //end if
											?>
										</ul>
									</div>
									
									<?PHP } //end if (action) ?>
									
									<?PHP if($data['action'] == 'whoInviteMe')  { ?>
									<h3>收到的好友邀請(<?= count($data['applyList']) ?>)</h3>
									<div class="friendListBox">
										<ul>
											<?PHP if( empty($data['applyList']) ) {  ?>
											<li class="friendListLine_nodata">
												<label style="font-size: 20px;">尚無收到的好友邀請</label>
											</li>
											<?PHP } else {
												$i = 1;
												foreach($data['applyList'] as $player) {  ?>
											<li class="friendListLine">
												<img src="<?= $config->imgRoot ?>head/<?= $player['account'] ?>.jpg">
												<label>
													<span style="color: #6495ED	; font-size: 20px; font-weight: bold;"><?= $player['nickname'] ?></span>
													<br>
													勝場：100
													<br>
													敗場：50</br>
													目前在線：<?= $player['isOnline'] ?>
												</label>
												<div>
													<form method="post">
														<input type="hidden" id="account" name="account" value="<?= $player['account'] ?>">
														<input id="btn_acceptInvite" type="button" value="接受邀請">
														<input id="btn_rejectInvite" type="button" value="拒絕邀請">
													</form>
												</div>
											</li>
											<li style="text-align: right; margin: 0px 0px 15px 0px !important;">
												<span>No. <?= $i ?></span>
												<input style="width: 60px; height: 25px; font-size: 10px; padding: 0px;" type="button" value="TOP" onclick="$( 'div .friendListBox' ).animate({scrollTop:0}, 'slow');">
											</li>
											<?PHP 
												$i++;
												} //end for $data
											} //end if
											?>
										</ul>
									</div>
									
									<?PHP } //end if (action) ?>
									
								</li>
							</ul>
						</div>
						<div class="aside">
							<ul>
								<li>
									<h3>近期對戰玩家</h3>
									<div class="friendListBox">
										<ul>
											<?PHP if( empty($data['gameRecord']) ) {  ?>
											<li class="friendListLine_nodata">
												<label style="font-size: 20px;">目前尚無對戰紀錄</label>
											</li>
											<?PHP } else {
												$i = 1;
												foreach($data['gameRecord'] as $player) {  ?>
											<li class="historyListLine">
												<label>
													<form method="post">
													<span style="color: #6495ED	; font-size: 20px; font-weight: bold;">殺很大殺很大殺很殺很</span>
													<br>
													<span style="font-size: 20px; font-weight: bold;">井字遊戲</span>
													<span style="color: green; font-size: 20px; font-weight: bold;">勝利</span>
													<input type="hidden" id="account" name="account" value="">
													<input id="btn_addFriend" type="button" value="加入好友">
													</form>
												</label>
											</li>
											<?PHP 
												$i++;
												} //end for $data
											} //end if
											?>
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