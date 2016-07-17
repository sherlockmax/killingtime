<?php

class FriendController extends Controller {
    
    function index() {
        $this->view("friend");
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
}

?>