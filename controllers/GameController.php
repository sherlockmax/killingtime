<?php
require_once "core/Tools.php";

class GameController extends Controller {
    
    function index() {
        $this->view("game");
    }
	
	function saveGameRecord(){
		$player1 = $_POST['player1'];
		$player2 = $_POST['player2'];
		$gamename = $_POST['gamename'];
		$winner = $_POST['winner'];
		$gamedata = $_POST['gamedata'];
		$memo = $_POST['memo'];
		$updatetime = Tools::getCurrentDateTime();
		
		$game = $this->model('game');
		
		$data = $game->saveGameRecord($player1, $player2, $gamename, $winner, $gamedata, $memo, $updatetime);
	}
}

?>