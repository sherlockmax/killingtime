<?php
require_once "core/Tools.php";

class PlayerController extends Controller {
    
    
    function setDefaultValue($player){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $player->account = isset( $_POST["account"] ) ? $_POST["account"] : $_SESSION["player"]["account"] ;
        $player->password = isset( $_POST["password"] ) ? $_POST["password"] : $_SESSION["player"]["password"] ;
        $player->email = isset( $_POST["email"] ) ? $_POST["email"] : $_SESSION["player"]["email"] ;
        $player->nickname = isset( $_POST["nickname"] ) ? $_POST["nickname"] : $_SESSION["player"]["nickname"] ;
    }
    
    function setDataToClass($player, $data){
        $player->account = isset( $data["account"] ) ? $data["account"] : "" ;
        $player->password = isset( $data["password"] ) ? $data["password"] : "" ;
        $player->email = isset( $data["email"] ) ? $data["email"] : "" ;
        $player->nickname = isset( $data["nickname"] ) ? $data["nickname"] : "" ;
        $player->registtime = isset( $data["registtime"] ) ? $data["registtime"] : "" ;
        $player->updatetime = isset( $data["updatetime"] ) ? $data["updatetime"] : "" ;
    }
    
    function index() {
        
        $this->view("player");
    }
    
    function login(){
        $player = $this->model("player");
        $this->setDefaultValue($player);
        $player->registtime = Tools::getCurrentDateTime();
        $player->updatetime = Tools::getCurrentDateTime();
        $player->password_hash = Tools::getPasswordHash($player->password);
        
        $data = $player->getPlayer();
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if(empty($data)){
            $_SESSION['errMsg'] = '登入失敗，請確認帳號或密碼是否正確。';
        }else{
            $_SESSION['isLogin'] = true;
            $_SESSION['player'] = $data;
            
            if(file_exists("images/head/".$data['account'].".jpg")){
                $_SESSION['photo'] = $data['account'];
            }else{
                $_SESSION['photo'] = "head_".rand(1, 10);
            }
            
        }

        $this->view("index");
    }
    
    function logout(){
        session_start();
        session_destroy();
        
        header("Location: ../home");
    }
    
    function registe(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $isPass = true;
        $player = $this->model("player");
        $this->setDefaultValue($player);
        $player->registtime = Tools::getCurrentDateTime();
        $player->updatetime = Tools::getCurrentDateTime();
        $player->password_hash = Tools::getPasswordHash($player->password);
        
        if($isPass){
            $data = $player->getPlayerByAccount();
            if(!empty($data)){
                
                $_SESSION['show_form'] = 'show_registe';
                $_SESSION['err_registe'] = '申請失敗，帳號已被使用。';
                $isPass = false;
            }
        }
        
        if($isPass){
            $data = $player->getPlayerByNickname();
            if(!empty($data)){
                $_SESSION['show_form'] = 'show_registe';
                $_SESSION['err_registe'] = '申請失敗，暱稱已被使用。';
                $isPass = false;
            }
        }
        
        if($isPass){
            $data = $player->addPlayer();
        }

        $this->view("index");
    }
    
    function isAccountExsist(){
        $player = $this->model("player");
        $this->setDefaultValue($player);
        $data = $player->getPlayerByAccount();
        if(!empty($data)){
            echo true;
        }else{
            echo false;
        }
    }
    
    function isNicknameExsist(){
        $player = $this->model("player");
        $this->setDefaultValue($player);
        $data = $player->getPlayerByNickname();
        if(!empty($data)){
            echo true;
        }else{
            echo false;
        }
    }
    
    function updateData(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        } 
        
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
            $_SESSION['err_update'] = '修改成功';
            $data = $player->getPlayerByAccount();
            $_SESSION['player'] = $data;
        }else{
            $_SESSION['err_update'] = '修改失敗';
        }
        
        $this->view("player");
    }
    
        
    function forgetPassword(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $player = $this->model("player");
        $this->setDefaultValue($player);
        $data = $player->getPlayerByAccount();
        $this->setDataToClass($player, $data);
        $player->password = Tools::getRandPassword();
        $player->updatetime = Tools::getCurrentDateTime();
        $player->password_hash = Tools::getPasswordHash($player->password);
        
        $player->updateData();
        
        $data = $player->getPlayerByAccount();
        $data['password'] = $player->password;
        
        $_SESSION['show_form'] = 'show_forgetPassword';
        if(!empty($data) && !empty($data['email'])){
            if(Tools::sendResetPasswordMail($data)){
                $_SESSION['err_forgetPassword'] = '成功將密碼重置信件寄至'.$data['email'];
            }else{
                $_SESSION['err_forgetPassword'] = '密碼重置信件寄送失敗。';
            }
        }else{
            $_SESSION['err_forgetPassword'] = '查無帳號或未設定信箱。';
        }
        
        $this->view("index");
    }
    
    
    function uploadPhoto(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // location to save cropped image
    	$url = 'images/head/'. $_SESSION['player']['account'].'.jpg';
    	
    	if(file_exists($url)){
    	    unlink($url);
    	}
    	
    	// remove the base64 part
    	$base64 = base64_decode(preg_replace('#^data:image/[^;]+;base64,#', '', $_POST['string']));
     
    	// create image
    	$source = imagecreatefromstring($base64);
     
    	// save image
    	imagejpeg($source, $url, 100);
    }
}
?>