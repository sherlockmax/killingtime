<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class HomeController extends Controller {
    
    function index() {
		$gameScore = array("win" => 0, "lose" => 0, "tie" => 0);
		
		if(isset($_SESSION['player'])){
			$game = $this->model("game");
			$gameScore = $game->getScore($_SESSION['player']['account']);
		}
		$pageData['score'] = $gameScore;
		
        $this->view("index", $pageData);
    }
}

?>