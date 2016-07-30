<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once "core/Tools.php";

class FriendController extends Controller {
    
    private $pageData = Array();
    
    function index() {
		$game = $this->model("game");
		
        $friend = $this->model("friend");
        $data = $friend->getFriendList($_SESSION['player']['account']);
        
        //$gameRecord = $this->model("gameRecord");
		$friendList = array();
		foreach($data as $player){
			$gameScore = array("win" => 0, "lose" => 0, "tie" => 0);
			$gameScore = $game->getScore($player['account']);
			$player['score'] = $gameScore;
			
			$friendList[] = $player;
		}
		
		$gameRecord = $game->getHistory($_SESSION['player']['account']);
        
        $this->pageData['action'] = 'friendList';
        $this->pageData['friendList'] = $friendList;
        $this->pageData['gameRecord'] = $gameRecord;

        $this->view("friend", $this->pageData);
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
        $this->view("ajaxMessage", substr( $result, 1));
    }
    
    function findPlayer(){
        $player = $this->model("player");
        $friend = $this->model("friend");
        $player->nickname = $_POST['nickname'];
        $data = $player->getPlayerByNickname();
        
        if(empty($data)){
            $result['player'] = NULL;
        }else{
			if($_SESSION['player']['account'] == $data['account']){
				$data['friendStatus'] = "self";
			}else{
				$friendStatus = $friend->getFriendStatus($_SESSION['player']['account'], $data['account']);
				$data['friendStatus'] = Tools::checkFriendStatus($friendStatus, $_SESSION['player']['account']);
			}
            $result['player'] = $data;
			
			$gameScore = array("win" => 0, "lose" => 0, "tie" => 0);
			$game = $this->model("game");
			$gameScore = $game->getScore($data['account']);
			
			$result['player']['score'] = $gameScore;
        }
		
		$game = $this->model("game");
		$gameRecord = $game->getHistory($_SESSION['player']['account']);
        $result['action'] = 'findPlayer';
        $result['gameRecord'] = $gameRecord;
        
        $this->view("friend", $result);
    }
    
    function friendInvite(){
        $friend = $this->model("friend");
        $data = $friend->getFriendInvite($_SESSION['player']['account']);
		
		$friendList = array();
		foreach($data as $player){
			$gameScore = array("win" => 0, "lose" => 0, "tie" => 0);
			$game = $this->model("game");
			$gameScore = $game->getScore($player['account']);
			$player['score'] = $gameScore;
			
			$friendList[] = $player;
		}
		
		$game = $this->model("game");
		$gameRecord = $game->getHistory($_SESSION['player']['account']);
        $this->pageData['action'] = 'friendInvite';
        $this->pageData['applyList'] = $friendList;
        $this->pageData['gameRecord'] = $gameRecord;

        $this->view("friend", $this->pageData);
    }
    
    function whoInviteMe(){
        $friend = $this->model("friend");
        $data = $friend->getWhoInviteMe($_SESSION['player']['account']);
		
		$friendList = array();
		foreach($data as $player){
			$gameScore = array("win" => 0, "lose" => 0, "tie" => 0);
			$game = $this->model("game");
			$gameScore = $game->getScore($player['account']);
			$player['score'] = $gameScore;
			
			$friendList[] = $player;
		}
        
		$game = $this->model("game");
		$gameRecord = $game->getHistory($_SESSION['player']['account']);
        $result['action'] = 'whoInviteMe';
        $result['applyList'] = $friendList;
        $result['gameRecord'] = $gameRecord;
        
        $this->view("friend", $result);
    }
    
    function addFriend(){
        $friend = $this->model("friend");
        $updatetime = Tools::getCurrentDateTime();
        $result = $friend->addFriend($_SESSION['player']['account'], $_POST['account'], $updatetime);
        
        if($result){
            $this->pageData['alert_message'] = "好友邀請成功";
        }else{
            $this->pageData['alert_message'] = "好友邀請失敗";
        }

        $this->friendInvite();
    }
    
    
    function acceptInvite(){
        $friend = $this->model("friend");
        $updatetime = Tools::getCurrentDateTime();
        $result = $friend->acceptInvite($_POST['account'], $_SESSION['player']['account'], $updatetime);
        
        if($result){
             $this->pageData['alert_message'] = "接受好友邀請成功";
        }else{
             $this->pageData['alert_message'] = "接受好友邀請失敗";
        }
        
        $this->index();
    }
    
    
    function rejectInvite(){
        $friend = $this->model("friend");
        $updatetime = Tools::getCurrentDateTime();
        $result = $friend->deleteFriend($_POST['account'], $_SESSION['player']['account']);
        
        if($result){
             $this->pageData['alert_message'] = "拒絕好友邀請成功";
        }else{
             $this->pageData['alert_message'] = "拒絕好友邀請失敗";
        }
        
        $this->friendInvite();
    }
    
    function removeFriend(){
        $friend = $this->model("friend");
        $updatetime = Tools::getCurrentDateTime();
        $result = $friend->deleteFriend($_POST['account'], $_SESSION['player']['account']);
        
        if($result){
             $this->pageData['alert_message'] = "刪除好友成功";
        }else{
             $this->pageData['alert_message'] = "刪除好友失敗";
        }
        
        $this->index();
    }
    
    function deleteInvite(){
        $friend = $this->model("friend");
        $updatetime = Tools::getCurrentDateTime();
        $result = $friend->deleteFriend($_POST['account'], $_SESSION['player']['account']);
        
        if($result){
             $this->pageData['alert_message'] = "取消邀請成功";
        }else{
             $this->pageData['alert_message'] = "取消邀請失敗";
        }
        
        $this->friendInvite();
    }
}

?>