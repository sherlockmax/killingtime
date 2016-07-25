<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once "core/Tools.php";

class PlayerController extends Controller {
    
    function setDefaultValue($player){
        $player->account = isset( $_POST["account"] ) ? $_POST["account"] : "" ;
        $player->password = isset( $_POST["password"] ) ? $_POST["password"] : "" ;
        $player->email = isset( $_POST["email"] ) ? $_POST["email"] : "" ;
        $player->nickname = isset( $_POST["nickname"] ) ? $_POST["nickname"] : "" ;
        $player->isOnline = isset( $_SESSION["isLogin"] ) ? "是" : "否";
    }
    
    function setDataToModel($player, $data){
        $player->account = isset( $data["account"] ) ? $data["account"] : "" ;
        $player->password = isset( $data["password"] ) ? $data["password"] : "" ;
        $player->email = isset( $data["email"] ) ? $data["email"] : "" ;
        $player->nickname = isset( $data["nickname"] ) ? $data["nickname"] : "" ;
        $player->registtime = isset( $data["registtime"] ) ? $data["registtime"] : "" ;
        $player->updatetime = isset( $data["updatetime"] ) ? $data["updatetime"] : "" ;
        $player->isOnline = $data["isOnline"]=="是" ? "是" : "否";
    }
    
    function index() {
        $this->view("player");
    }
    
    function login(){
        $player = $this->model("player");
        $this->setDefaultValue($player);
        $player->password_hash = Tools::getPasswordHash($player->password);
        
        $data = $player->getPlayer();

        if(empty($data)){
            $pageData['errMsg'] = '登入失敗，請確認帳號或密碼是否正確。';
        }else{
            
            $_SESSION['isLogin'] = true;
            $_SESSION['player'] = $data;
            $player->setLoginState($data['account']);
		}
		
		$game = $this->model("game");
		$gameScore = $game->getScore($data['account']);
		
		$pageData['score'] = $gameScore;
        $this->view("index", $pageData);
    }
    
    function logout(){
        $player = $this->model("player");
        $player->setLogoutState($_SESSION['player']['account']);

        session_destroy();
        
        header("Location: ".$this->root."home");
    }
    
    function registe(){
        $isPass = true;
        $player = $this->model("player");
        $this->setDefaultValue($player);
        $player->registtime = Tools::getCurrentDateTime();
        $player->updatetime = Tools::getCurrentDateTime();
        $player->password_hash = Tools::getPasswordHash($player->password);
        
        if($isPass){
            $data = $player->getPlayerByAccount();
            if(!empty($data)){
                $pageData['show_form'] = 'show_registe';
                $pageData['err_registe'] = '申請失敗，帳號已被使用。';
                $isPass = false;
            }
        }
        
        if($isPass){
            $data = $player->getPlayerByNickname();
            if(!empty($data)){
                $pageData['show_form'] = 'show_registe';
                $pageData['err_registe'] = '申請失敗，暱稱已被使用。';
                $isPass = false;
            }
        }
        
        if($isPass){
            $data = $player->addPlayer();
            
            copy('.'.$this->config()->imgRoot . 'head/head_'.rand(1, 10).'.jpg', '.'.$this->config()->imgRoot . 'head/'.$player->account.'.jpg');
            $pageData['account'] = $player->account;
            $pageData['show_form'] = 'show_login';
            $pageData['alert_message'] = '會員申請成功。';
        }
		
        $this->view("index", $pageData);
    }
    
    function isAccountExsist(){
        $player = $this->model("player");
        $this->setDefaultValue($player);
        $data = $player->getPlayerByAccount();
        if(!empty($data)){
            echo "true";
        }else{
            echo "false";
        }
    }
    
    function isNicknameExsist(){
        $player = $this->model("player");
        $this->setDefaultValue($player);
        $data = $player->getPlayerByNickname();
        if(!empty($data)){
            echo "true";
        }else{
            echo "false";
        }
    }
    
    function updateData(){
        $player = $this->model("player");
        $this->setDefaultValue($player);
        $player->account = $_SESSION['player']['account'];
        $player->updatetime = Tools::getCurrentDateTime();
        if(isset( $_POST["password"] ) &&  strlen($_POST["password"]) > 0){
            $player->password_hash = Tools::getPasswordHash($player->password);
        }else{
            $player->password_hash = $_SESSION["player"]["password"];
        }
        
        if($player->updateData()){
            $pageData['err_update'] = '修改成功';
            $data = $player->getPlayerByAccount();
            $_SESSION['player'] = $data;
        }else{
            $pageData['err_update'] = '修改失敗';
        }
        
        $this->view("player", $pageData);
    }
    
        
    function forgetPassword(){
        $player = $this->model("player");
        $this->setDefaultValue($player);
        $data = $player->getPlayerByAccount();
        $this->setDataToModel($player, $data);
        $player->password = Tools::getRandPassword();
        $player->updatetime = Tools::getCurrentDateTime();
        $player->password_hash = Tools::getPasswordHash($player->password);
        
        $player->updateData();
        
        $data = $player->getPlayerByAccount();
        $data['password'] = $player->password;
        
        $pageData['show_form'] = 'show_forgetPassword';
        if(!empty($data) && !empty($data['email'])){
            if(Tools::sendResetPasswordMail($data)){
                $pageData['err_forgetPassword'] = '密碼重置申請信成功寄至'.$data['email'];
            }else{
                $pageData['err_forgetPassword'] = '密碼重置申請信寄送失敗。';
            }
        }else{
            $pageData['err_forgetPassword'] = '查無帳號或未設定信箱。';
        }
        
        $this->view("index", $pageData);
    }
    
    
    function uploadPhoto(){
		try{
			$url = '.'.$this->config()->imgRoot . 'head/'. $_SESSION['player']['account'].'.jpg';
			if(file_exists($url)){
				unlink($url);
			}
			// remove the base64 part
			$base64 = base64_decode(preg_replace('#^data:image/[^;]+;base64,#', '', $_POST['string']));
			// create image
			$source = imagecreatefromstring($base64);
			// save image
			imagejpeg($source, $url, 100);
			
			if(file_exists($url)){
				echo '照片上傳成功。';
			}else{
			    echo '照片上傳失敗。';
			}
		}catch( Exception $e ){
			echo '照片上傳失敗。';
		}
    }
}
?>