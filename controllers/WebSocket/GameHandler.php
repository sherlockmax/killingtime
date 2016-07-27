<?php
namespace WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use WebSocket\GamePlayerModel;
use WebSocket\GameRoomModel;
use WebSocket\TicTacToe;
use \DateTime;

class GameHandler implements MessageComponentInterface {
    protected $clients;
	protected $gameRoomList = [];
	protected $playerList;
	protected $controller;
	protected $dataBox = array("action" => "", "data" => "");
	
    public function __construct() {
        $this->clients = new \SplObjectStorage;
		$this->dataBox['action'] = "";
		$this->dataBox['data'] = "";
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        echo "<--- " . $this->getConnectionID($conn) . " --->\nNew connection\n---------------------------------------\n";
		
		$this->refreshGameRoom($conn->resourceId);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
		$dataJson = json_decode($msg, true);	
		$this->dataBox['action'] = $dataJson['action'];
		$this->dataBox['data'] = $dataJson['data'];
		
		echo "<--- " . $this->getConnectionID($from)." ---> GET message\n[action]:".$this->dataBox['action']."\n---------------------------------------\n";
				
		if($this->dataBox['action'] == 'setPlayer'){
			$player = new GamePlayerModel($this->dataBox['data']['account'], $this->dataBox['data']['nickname'], $from, NULL);
			$this->playerList[$this->getConnectionID($from)] = $player;
			
		}else if($this->dataBox['action'] == 'createRoom'){
			$newRoomID = $this->getBlankRoomID();
			$player = $this->playerList[$this->getConnectionID($from)];
			$player->roomID = $newRoomID;
			$gameRoom = new GameRoomModel($newRoomID, $player, NULL, $this->dataBox['data']['gameName'], $player->account);
			$this->gameRoomList[$newRoomID] = $gameRoom;
			
			$this->dataBox['data'] = json_encode($gameRoom);
			
			$this->sendDataTo($from->resourceId);
			
			$this->refreshGameRoom('all');
			
		}else if($this->dataBox['action'] == 'leaveRoom'){
			$roomID = $this->dataBox['data']['roomID'];
			$gameRoom = $this->gameRoomList[$roomID];
			
			if(!empty($gameRoom->player1) && !empty($gameRoom->player2)){
				$player1 = $gameRoom->player1;
				$player2 = $gameRoom->player2;
				$player1->roomID = NULL;
				$player2->roomID = NULL;
				$this->playerList[$this->getConnectionID($from)] = $player1;
				$this->playerList[$this->getConnectionID($player2->client)] = $player2;
				
				$player = $this->playerList[$this->getConnectionID($from)];
				$msgArray = array("leaveAccount" => $player->account);
				
				$this->dataBox['action'] = 'anotherOneleft';
				$this->dataBox['data'] = json_encode($msgArray);
				$this->sendDataTo($player2->client->resourceId);
				$this->dataBox['action'] = 'anotherOneleft';
				$this->dataBox['data'] = json_encode($msgArray);
				$this->sendDataTo($player1->client->resourceId);
			}else{
				$player;
				if($gameRoom->player1->account == $this->dataBox['data']['player']['account']){
					$player = $gameRoom->player1;
				}else{
					$player = $gameRoom->player2;
				}
				$player->roomID = NULL;
				$this->playerList[$this->getConnectionID($from)] = $player;
			}
			
			unset($this->gameRoomList[$roomID]);
			$this->refreshGameRoom('all');
		}else if($this->dataBox['action'] == 'joinGameRoom'){
			$gameRoom = $this->gameRoomList[$this->dataBox['data']['roomID']];
			if( !empty($gameRoom->palyer1) &&  !empty($gameRoom->palyer2)){
				$this->dataBox['action'] = 'roomIsFull';
				$this->dataBox['data'] = null;
				$this->sendDataTo($from->resourceId);
			}else{
				$player = $this->playerList[$this->getConnectionID($from)];
				$player->roomID = $this->dataBox['data']['roomID'];
				$player1 = $this->playerList[$this->getConnectionID($gameRoom->player1->client)];
				
				$gameRoom->player2 = $player;
				
				$this->gameRoomList[$this->dataBox['data']['roomID']] = $gameRoom;
				$this->playerList[$this->getConnectionID($from)]= $player;
				
				$this->dataBox['action'] = 'joinGameRoom';
				$this->dataBox['data'] = json_encode($gameRoom);
				$this->sendDataTo($from->resourceId);
				
				$this->dataBox['action'] = 'joinGameRoom';
				$this->dataBox['data'] = json_encode($gameRoom);
				$this->sendDataTo($player1->client->resourceId);
				
				$msgArray = array("account" => $gameRoom->whosturn);
				
				$this->dataBox['action'] = 'whosturn';
				$this->dataBox['data'] = json_encode($msgArray);
				$this->sendDataTo($from->resourceId);
				$this->dataBox['action'] = 'whosturn';
				$this->dataBox['data'] = json_encode($msgArray);
				$this->sendDataTo($player1->client->resourceId);
				
				$this->refreshGameRoom('all');
			}
		}else if($this->dataBox['action'] == 'sendMessage'){
			$this->dataBox['action'] = 'sendMessage';
			$gameRoom = $this->gameRoomList[$this->dataBox['data']['roomID']];
			if(!empty($gameRoom->player1) && !empty($gameRoom->player2)){
				$player = $player = $this->playerList[$this->getConnectionID($from)];
				if($gameRoom->player1->account == $player->account){
					$msgArray = array("nickname" => $gameRoom->player1->nickname.' ('.$this->getTime().')' , "msg" => $this->dataBox['data']['msg']);
					$this->dataBox['data'] = json_encode($msgArray);
					$this->sendDataTo($gameRoom->player2->client->resourceId);
				}else{
					$msgArray = array("nickname" => $gameRoom->player2->nickname.' ('.$this->getTime().')' , "msg" => $this->dataBox['data']['msg']);
					$this->dataBox['data'] = json_encode($msgArray);
					$this->sendDataTo($gameRoom->player1->client->resourceId);
				}
			}else{
				$msgArray = array("nickname" => '尚無對手! ('.$this->getTime().')' , "msg" => "無法發送訊息。");
				$this->dataBox['data'] = json_encode($msgArray);
				$this->sendDataTo($from->resourceId);
			}
		}else if($this->dataBox['action'] == 'gameStep'){
			$player = $this->playerList[$this->getConnectionID($from)];
			$gameRoom = $this->gameRoomList[$this->dataBox['data']['roomID']];
			
			if($player->account == $gameRoom->whosturn){
				$tableID = $this->dataBox['data']['tableID'];
				$mark;
				$whosturn;
				if($player->account == $gameRoom->player1->account){
					$mark = "O";
					$whosturn = $gameRoom->whosturn;
					
					$gameRoom->whosturn = $gameRoom->player2->account;
				}else{
					$mark = "X";
					$whosturn = $gameRoom->whosturn;
					
					$gameRoom->whosturn = $gameRoom->player1->account;
				}
				
				$gameData = $gameRoom->gameData;
				$gameData[$tableID] = $mark;
				$gameRoom->gameData = $gameData;
				
				$msgArray = array("mark" => $mark, "tableID" => $tableID, "whosturn" => $whosturn);
				$this->dataBox['action'] = 'gameStep';
				$this->dataBox['data'] = json_encode($msgArray);
				$this->sendDataTo($this->getConnectionID($gameRoom->player1->client));
				$this->dataBox['action'] = 'gameStep';
				$this->dataBox['data'] = json_encode($msgArray);
				$this->sendDataTo($this->getConnectionID($gameRoom->player2->client));
				
				$winner = TicTacToe::checkWinner($gameRoom->player1->account, $gameRoom->player2->account, $gameRoom->gameData);
				if(!empty( $winner )){
					$msgArray = Array("account" => $winner );
					$this->dataBox['action'] = 'winner';
					$this->dataBox['data'] = json_encode($msgArray);
					$this->sendDataTo($this->getConnectionID($gameRoom->player2->client));
					$this->dataBox['action'] = 'winner';
					$this->dataBox['data'] = json_encode($msgArray);
					$this->sendDataTo($this->getConnectionID($gameRoom->player1->client));
					
					//$player1, $player2, $gameName, $winner, $gameData, $memo
					$gameRecord = Array( "player1" 	=>	$gameRoom->player1->account,
										 "player2" 	=> 	$gameRoom->player2->account,
										 "gamename" => 	'井字遊戲',
										 "winner" 	=> 	$winner,
										 "gamedata" => 	$this->arrayToString($gameRoom->gameData),
										 "memo" 	=> 	null);
										 
					$this->dataBox['action'] = 'saveGameRecord';
					$this->dataBox['data'] = json_encode($gameRecord);
					$this->sendDataTo($this->getConnectionID($from));
				} 
			}
		}else if($this->dataBox['action'] == 'oneMoreTime'){
			$gameRoom = $this->gameRoomList[$this->dataBox['data']['roomID']];

			$gameRoom->whosturn = $gameRoom->player1->account;
			$gameRoom->initGameData();
			
			$this->gameRoomList[$this->dataBox['data']['roomID']] = $gameRoom;
			
			$msgArray = array("whosturn" => $gameRoom->whosturn);
			$this->dataBox['action'] = 'oneMoreTime';
			$this->dataBox['data'] = json_encode($msgArray);
			$this->sendDataTo($this->getConnectionID($gameRoom->player1->client));
			$this->dataBox['action'] = 'oneMoreTime';
			$this->dataBox['data'] = json_encode($msgArray);
			$this->sendDataTo($this->getConnectionID($gameRoom->player2->client));
		}
    }

    public function onClose(ConnectionInterface $conn) {
		echo "<--- " . $this->getConnectionID($conn) . " ---> Disconnection\n---------------------------------------\n";
		$this->playerDisconnect($conn);
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
	
	public function sendDataTo($clientID){
		$data_JSON = json_encode($this->dataBox);
		foreach ($this->clients as $client) {
			if($clientID == 'all'){
				$client->send($data_JSON);
				echo "<--- 9999 --->\nSEND message to all\n[action]:".$this->dataBox['action']."\n---------------------------------------\n";
			}else{
				if($clientID == $client->resourceId){
					$client->send($data_JSON);
					echo "<--- " . $this->getConnectionID($client) . " ---> SEND message\n[action]:".$this->dataBox['action']."\n---------------------------------------\n";
				}
			}
		}
		$this->dataBox = array("action" => "", "data" => "");
	}
	
	public function getConnectionID($client){
		return $this->fillZero($client->resourceId);
	}
	
	public function fillZero($data){
		return str_pad($data, 4, "0", STR_PAD_LEFT);
	}
	
	public function getBlankRoomID(){
		if(sizeOf($this->gameRoomList) > 0){
			ksort($this->gameRoomList);
			foreach($this->gameRoomList as $roomID => $gameRoom){
				if(empty($gameRoom)){
					return $roomID;
				}
			}
			return $this->fillZero(sizeOf($this->gameRoomList)+1);
		}else{
			return $this->fillZero('1');
		}
	}
	
	public function refreshGameRoom($clientID){
		if(!empty($this->gameRoomList)){
			ksort($this->gameRoomList);
		}
		$this->dataBox['action'] = 'refreshGameRoom';
		$this->dataBox['data'] = json_encode($this->gameRoomList);
		$this->sendDataTo($clientID);
	}
	
	public function playerDisconnect($client){
		$player = $this->playerList[$this->getConnectionID($client)];
		if(!empty($player->roomID)){
			$gameRoom = $this->gameRoomList[$player->roomID];
			if(!empty($gameRoom->player1) && !empty($gameRoom->player2)){
				
				$this->dataBox['action'] = 'anotherOneleft';
				$this->dataBox['data'] = null;
				$this->sendDataTo($gameRoom->player2->client->resourceId);
				$this->dataBox['action'] = 'anotherOneleft';
				$this->dataBox['data'] = null;
				$this->sendDataTo($gameRoom->player1->client->resourceId);
				
				if($gameRoom->player1->account == $player->account){
					$player2 = $this->playerList[$this->getConnectionID($gameRoom->player2->client)];
					$player2->roomID = NULL;
					$this->playerList[$this->getConnectionID($gameRoom->player2->client)] = $player2;
				}else{
					$player1 = $this->playerList[$this->getConnectionID($gameRoom->player1->client)];
					$player1->roomID = NULL;
					$this->playerList[$this->getConnectionID($gameRoom->player1->client)] = $player1;
				}
			}
		}
		unset($this->gameRoomList[$player->roomID]);
		unset($this->playerList[$this->getConnectionID($client)]);
		
		$this->refreshGameRoom('all');
	}
	
	public function getCurrentDateTime(){
        $date = new DateTime();
        return $date->format('Y-m-d H:i:s');
    }
	
	public function getTime(){
		$date = new DateTime();
        return $date->format('H:i:s');
	}
	
	public function arrayToString($array, $split = '#'){
		$result = null;
		foreach($array as $value){
			if(empty($value)){
				$result .= $split . '_';
			}else{
				$result .= $split . $value;
			}
		}
		
		if(empty($result)){
			return null;
		}else{
			return substr($result, 1);
		}
	}
}