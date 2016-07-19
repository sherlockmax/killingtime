<?php
namespace WebSocket;

class GameRoomModel {
	public $roomID;
	public $player1;
	public $player2;
	public $gameName;
	public $gameData;
	
	function __construct($roomID, $player1, $player2, $gameName, $gameData){
		$this->roomID = $roomID;
		$this->player1 = $player1;
		$this->player2 = $player2;
		$this->gameName = $gameName;
		$this->gameData = $gameData;
	}
}
?>