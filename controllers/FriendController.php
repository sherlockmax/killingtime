<?php
require_once "core/Tools.php";

class FriendController extends Controller {
    
    function index() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $friend = $this->model("friend");
        $data = $friend->getFriendList($_SESSION['player']['account']);
        
        //$gameRecord = $this->model("gameRecord");
        
        $result['action'] = 'friendList';
        $result['friendList'] = $data;
        $result['gameRecord'] = NULL;

        $this->view("friend", $result);
    }
    
    function queryNicname(){
        $keyname = $_POST["keyname"];  
        if (!$keyname){ 
            return;
        }
        
        $player = $this->model("player");
        $data = $player->queryByNickname($keyname."%");
        
        $result = "";
        foreach ($data as $value) {
            if (strpos($value['nickname'], $keyname) !== false) {
                $result .= ",". $value['nickname'];
            }  
        }
        echo substr( $result, 1);
    }
    
    function findPlayer(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $player = $this->model("player");
        $friend = $this->model("friend");
        $player->nickname = $_POST['nickname'];
        $data = $player->getPlayerByNickname();
        
        var_dump($data);
        if(empty($data)){
            $result['player'] = NULL;
        }else{
            $friendStatus = $friend->getFriendStatus($_SESSION['player']['account'], $data['account']);
            $data['friendStatus'] = Tools::checkFriendStatus($friendStatus, $_SESSION['player']['account']);
            $result['player'] = $data;
        }
        $result['action'] = 'findPlayer';
        $result['gameRecord'] = NULL;
        
        $this->view("friend", $result);
    }
    
    function friendInvite(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $friend = $this->model("friend");
        $data = $friend->getFriendInvite($_SESSION['player']['account']);
        
        $result['action'] = 'friendInvite';
        $result['applyList'] = $data;
        $result['gameRecord'] = NULL;
        
        $this->view("friend", $result);
    }
    
    function whoInviteMe(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $friend = $this->model("friend");
        $data = $friend->getWhoInviteMe($_SESSION['player']['account']);
        
        $result['action'] = 'whoInviteMe';
        $result['applyList'] = $data;
        $result['gameRecord'] = NULL;
        
        $this->view("friend", $result);
    }
    
    function addFriend(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $friend = $this->model("friend");
        $updatetime = Tools::getCurrentDateTime();
        $result = $friend->addFriend($_SESSION['player']['account'], $_POST['account'], $updatetime);
        
        if($result){
            $_SESSION['alert_message'] = "好友邀請成功";
        }else{
            $_SESSION['alert_message'] = "好友邀請失敗";
        }
        
        $this->friendInvite();
    }
    
    
    function acceptInvite(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $friend = $this->model("friend");
        $updatetime = Tools::getCurrentDateTime();
        $result = $friend->acceptInvite($_POST['account'], $_SESSION['player']['account'], $updatetime);
        
        if($result){
            $_SESSION['alert_message'] = "接受好友邀請成功";
        }else{
            $_SESSION['alert_message'] = "接受好友邀請失敗";
        }
        
        header("Location: /friend");
    }
    
    
    function rejectInvite(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $friend = $this->model("friend");
        $updatetime = Tools::getCurrentDateTime();
        $result = $friend->deleteFriend($_POST['account'], $_SESSION['player']['account']);
        
        if($result){
            $_SESSION['alert_message'] = "拒絕好友邀請成功";
        }else{
            $_SESSION['alert_message'] = "拒絕好友邀請失敗";
        }
        
        $this->friendInvite();
    }
    
    function removeFriend(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $friend = $this->model("friend");
        $updatetime = Tools::getCurrentDateTime();
        $result = $friend->deleteFriend($_POST['account'], $_SESSION['player']['account']);
        
        if($result){
            $_SESSION['alert_message'] = "刪除好友成功";
        }else{
            $_SESSION['alert_message'] = "刪除好友失敗";
        }
        
        header("Location: /friend");
    }
    
    function deleteInvite(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $friend = $this->model("friend");
        $updatetime = Tools::getCurrentDateTime();
        $result = $friend->deleteFriend($_POST['account'], $_SESSION['player']['account']);
        
        if($result){
            $_SESSION['alert_message'] = "取消邀請成功";
        }else{
            $_SESSION['alert_message'] = "取消邀請失敗";
        }
        
        $this->friendInvite();
    }
}

?>