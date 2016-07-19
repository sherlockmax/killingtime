<?php
namespace WebSocket;

class GamePlayerModel {
	public $account;
	public $nickname;
	public $client;
	public $roomID;
	
	function __construct($account, $nickname, $client, $roomID){
		$this->account = $account;
		$this->nickname = $nickname;
		$this->client = $client;
		$this->roomID = $roomID;
	}
}
?>