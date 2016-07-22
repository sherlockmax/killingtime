<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$pageName = 'memberEdit';
?>
<!DOCTYPE HTML>
<html>
<head>
	<?PHP include_once('init.php'); ?>
	
	<script src="<?= $config->jsRoot ?>cropImg.js"></script>
	<link rel="stylesheet" href="<?= $config->cssRoot ?>cropImg.css"/>
	<style>
		.crop-overlay{
			border: 0px !important;
			background: none !important;
		}
		.default, .cropMain, .crop-container, .crop-overlay{
			margin: 0 auto !important;
			padding: 0px !important;
		}

		.div_hide{
			display: none !important;
		}
	</style>
	<script>
		$(document).ready(function(){
			var isPass_nickname = true;
			
			var foo = new CROP();
			foo.init({
				container: '.default',
				image: "<?= $config->imgRoot ?>head/<?= $_SESSION['player']['account'] ?>.jpg",
				width: 150,
				height: 150,
				mask: true,
				zoom: {
					steps: 0.01,
					min: 1,
					max: 5
				}
			});
			
			$( "#dialog_photo_successed, #dialog_photo_failed" ).dialog({
				autoOpen: false,
				resizable: false,
				show: {
					effect: "blind",
					duration: 400
				},
				hide: {
					effect: "blind",
					duration: 400
				},
				buttons: {
					"確認": function() {
						$( this ).dialog( "close" );
					}
				}
			});
			
			
			
			$('div[data-imgcrop] input[type="range"]').addClass("div_hide");
			$('.crop-overlay').addClass("div_hide");
			
			$('#btn_file_select').click(function(){
				foo.import();
				$('div[data-imgcrop] input[type="range"]').removeClass("div_hide");
				$('.crop-overlay').removeClass("div_hide");
			});
			
			$('#btn_file_upload').click(function(){
				//look at http://danielhellier.com/crop/index.html
				$('div .default').find('img').remove();
				$('div .loaderBox').show();
				
				$.ajax({
					type: "post",
					url: "<?= $config->root ?>player/uploadPhoto",
					data: foo.crop(150, 150, 'jpg')
				}).fail(function(){
					location.reload();
				})
				.done(function(data) {
					location.reload();
				});
			});
			
			$('#btn_reset').click(function(){
				$('#form_editProfile').find('form').trigger('reset');
			});
			
			$('#btn_update').click(function(){
				var thisForm = $('#form_updateProfile').find('form');
				var data = thisForm.toObject();
				var isPass = true;
				
				if(data.password.length > 0 || data.passwordCheck.length > 0 ){
					if(data.passwordCheck != data.password){
						setErrMsg('update', '兩次密碼輸入不相符。');
						isPass = false;
					}
					if(!validatePassword(data.password)){
						setErrMsg('update', '密碼請以英文字母開頭，長度為6~15個英數字。');
						isPass = false;
					}
				}
				if(!validateEmail(data.email)){
					setErrMsg('update', '請確認信箱格式是否正確。');
					isPass = false;
				}
				if(!validateNickname(data.nickname)){
					setErrMsg('update', '暱稱僅接受中文、英文及數字，長度為3~11個字。');
					isPass = false;
				}
				
				
				if(isPass && isPass_nickname){
					$('#form_updateProfile').find('form').attr("action","<?= $config->root ?>player/updateData").submit();
				}
			});
			
			$('#form_updateProfile').find("#nickname").on("blur change",function(){
				if($(this).val() != '<?PHP echo $_SESSION['player']['nickname'] ?>'){
					$.ajax({
						method: "POST",
						url: "<?= $config->root ?>player/isNicknameExsist",
						data: { nickname: $(this).val() }
					}).done(function( msg ) {
						if(msg){
							setErrMsg('update', '該暱稱已被使用。');
							isPass_nickname = false;
						}else{
							clearErrMsg('update');
							isPass_nickname = true;
						}
					});
				}
			});
		});
		
	</script>
	<?PHP
		if(isset($_SESSION['err_uploadPhoto'])){
			echo "<script>$(window).on('load', function() {";
			echo "alertMsg('提示訊息', '".$_SESSION['err_uploadPhoto']."');";
			echo "});</script>";

			unset($_SESSION['err_uploadPhoto']);
		}
	?>
</head>
<body>
	<div id="background">
		<?php include_once("header.php") ?>
		<div id="body">
			<div>
				<div>
					<div class="section">
						<div id="form_changePhoto">
							<form>
							<h3>變更頭像</h3>
							<div>
								<div style="text-align: center;">
									<div class="default"></div>
									
									
									<img id="preA" />
								</div>
								<span id="err_Tip">(選擇上傳照片後)</span>
								<span id="err_Tip">1.可直接拖曳照片，選擇欲裁切的區域。</span>
								<span id="err_Tip">2.照片右方範圍鈕，可上下移動設定圖片大小。</span>
								<span><a id="btn_file_select" href="javascript:void(0);">選擇照片</a></span>
								<span><a id="btn_file_upload" href="javascript:void(0);">更新照片</a></span>
								<br />
							</div>
							</form>
						</div>
						
						
						<div id="form_updateProfile">
							<form method="post" action="<?= $config->root ?>player/updateData">
							<h3>編輯基本資料</h3>
							<div>
								<div>
									<label for="account">帳號(不可更改)</label>
									<input type="text" class="form-control" id="account" name="account" disabled value="<?= $_SESSION['player']['account'] ?>">
									<label for="nickName">暱稱</label>
									<input type="text" class="form-control" id="nickname" name="nickname" value="<?= $_SESSION['player']['nickname'] ?>">
									<label for="account">信箱</label>
									<input type="email" class="form-control" id="email" name="email" value="<?= $_SESSION['player']['email'] ?>">
									<label for="password">新密碼</label>
									<input type="password" class="form-control" id="password" name="password">
									<label for="passwordCheck">新密碼確認</label>
									<input type="password" class="form-control" id="passwordCheck" name="passwordCheck">
								</div>
								<span id='err_update'><?PHP $this->getSession('err_update', true);  ?></span>
								<span><a id="btn_update" href="javascript:void(0);">確認修改</a></span>
								<span><a id="btn_reset" href="javascript:void(0);">重新填寫</a></span>
							</div>
							</form>
						</div>
						
					</div>
				</div>
			</div>
		</div>
		<?php include_once("footer.php"); ?>
	</div>
</body>
</html>