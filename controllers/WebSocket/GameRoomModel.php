<?php
namespace WebSocket;

class GameRoomModel {
	public $roomID;
	public $player1;
	public $player2;
	public $gameName;
	public $gameData;
	public $whosturn;
	
	function __construct($roomID, $player1, $player2, $gameName, $whosturn){
		$this->roomID = $roomID;
		$this->player1 = $player1;
		$this->player2 = $player2;
		$this->gameName = $gameName;
		$this->whosturn = $whosturn;
		
		$this->initGameData();
		
		var_dump($this->gameData);
	}
	
	function initGameData(){
		$gameData;
		for($r=1; $r<=3; $r++){
			for($c=1; $c<=3; $c++){
				$gameData[$c.'-'.$r] = null;
			}
		}
		
		$this->gameData = $gameData;
	}
}
?>