<div class="loaderBox"><div class="loader"></div></div>

<?PHP if(isset($_SESSION['isLogin'])){ ?>
<div id="header">
	<div>
		<div>
			<a href="/home" class="logo"><img src="/images/logo2.png" alt="KT 殺時間"></a>
			<ul>
				<li <?PHP if($pageName=='index'){echo 'class="selected"';} ?>>
					<a href="/home" id="menu1">首頁</a>
				</li>
				<li <?PHP if($pageName=='memberEdit'){echo 'class="selected"';} ?>>
					<a href="/player" id="menu2">基本資料</a>
				</li>
				<li <?PHP if($pageName=='gameLobby'){echo 'class="selected"';} ?>>
					<a href="/game" id="menu3">遊戲大廳</a>
				</li>
				<li <?PHP if($pageName=='friendManage'){echo 'class="selected"';} ?>>
					<a href="/friend" id="menu4">好友管理</a>
				</li>
				<li <?PHP if($pageName=='index'){echo 'class="selected"';} ?>>
					<a href="/player/logout" id="menu5">登出</a>
				</li>
			</ul>
		</div>
	</div>
</div>
<?PHP } else {?>
<div id="header">
	<div>
		<div>
			<a href="/home" class="logo"><img src="/images/logo2.png" alt="KT 殺時間"></a>
			<ul>
				<li <?PHP if($pageName=='index'){echo 'class="selected"';} ?>>
					<a href="javascript:void(0);" id="menu1">歡迎來到</a>
				</li>
				<li <?PHP if($pageName=='memberEdit'){echo 'class="selected"';} ?>>
					<a href="javascript:void(0);" id="menu2">殺時間</a>
				</li>
				<li <?PHP if($pageName=='gameLobby'){echo 'class="selected"';} ?>>
					<a href="javascript:void(0);" id="menu3">登入|入登</a>
				</li>
				<li <?PHP if($pageName=='friendManage'){echo 'class="selected"';} ?>>
					<a href="javascript:void(0);" id="menu4">間時殺</a>
				</li>
				<li <?PHP if($pageName=='index'){echo 'class="selected"';} ?>>
					<a href="javascript:void(0);" id="menu5">到來迎歡</a>
				</li>
			</ul>
		</div>
	</div>
</div>
<?PHP }?>