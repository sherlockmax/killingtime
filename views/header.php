<div class="loaderBox"><div class="loader"></div></div>

<?PHP if(!empty($isLogin)){ ?>
<div id="header">
	<div>
		<div>
			<a href="<?= $config->root ?>home" class="logo"><img src="<?= $config->imgRoot ?>logo2.png" alt="KT 殺時間"></a>
			<ul>
				<li <?PHP if($view=='index'){echo 'class="selected"';} ?>>
					<a href="<?= $config->root ?>home" id="menu1">首頁</a>
				</li>
				<li <?PHP if($view=='player'){echo 'class="selected"';} ?>>
					<a href="<?= $config->root ?>player" id="menu2">基本資料</a>
				</li>
				<li <?PHP if($view=='game'){echo 'class="selected"';} ?>>
					<a href="<?= $config->root ?>game" id="menu3">遊戲大廳</a>
				</li>
				<li <?PHP if($view=='friend'){echo 'class="selected"';} ?>>
					<a href="<?= $config->root ?>friend" id="menu4">好友管理</a>
				</li>
				<li>
					<a href="<?= $config->root ?>player/logout" id="menu5">登出</a>
				</li>
			</ul>
		</div>
	</div>
</div>
<?PHP } else {?>
<div id="header">
	<div>
		<div>
			<a href="<?= $config->root ?>home" class="logo"><img src="<?= $config->imgRoot ?>logo2.png" alt="KT 殺時間"></a>
			<ul>
				<li>
					<a href="<?= $config->root ?>home" id="menu1">歡迎來到</a>
				</li>
				<li>
					<a href="<?= $config->root ?>home" id="menu2">殺時間</a>
				</li>
				<li>
					<a href="<?= $config->root ?>home" id="menu3">登入|入登</a>
				</li>
				<li>
					<a href="<?= $config->root ?>home" id="menu4">間時殺</a>
				</li>
				<li>
					<a href="<?= $config->root ?>home" id="menu5">到來迎歡</a>
				</li>
			</ul>
		</div>
	</div>
</div>
<?PHP }?>