<?php
namespace WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use WebSocket\GameServerController;
use WebSocket\GamePlayerModel;
use WebSocket\GameRoomModel;

class GameHandler implements MessageComponentInterface {
    protected $clients;
	protected $gameRoomList = [];
	protected $playerList;
	protected $controller;
	protected $dataBox = array("action" => "", "data" => "");

    public function __construct() {
        $this->clients = new \SplObjectStorage;
		$this->controller = new GameServerController();
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
		
		echo "<--- " . $this->getConnectionID($from)." --->\nGET message\n[action]:".$this->dataBox['action']."\n---------------------------------------\n";
				
		if($this->dataBox['action'] == 'setPlayer'){
			$player = new GamePlayerModel($this->dataBox['data']['account'], $this->dataBox['data']['nickname'], $from, NULL);
			$this->playerList[$this->getConnectionID($from)] = $player;
		}else if($this->dataBox['action'] == 'createRoom'){
			$newRoomID = $this->getBlankRoomID();
			$player = $this->playerList[$this->getConnectionID($from)];
			$player->roomID = $newRoomID;
			$gameRoom = new GameRoomModel($newRoomID, $player, NULL, $this->dataBox['data']['gameName'], NULL);
			$this->gameRoomList[$newRoomID] = $gameRoom;
			
			$this->dataBox['data'] = json_encode($gameRoom);
			
			$this->sendDataTo($from->resourceId);
			
			$this->refreshGameRoom('all');
		}
		//$this->controller->$action();
    }

    public function onClose(ConnectionInterface $conn) {
		echo "<--- " . $conn->resourceId. " --->\nDisconnection\n---------------------------------------\n";
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
				echo "<--- 9999 --->\nSEND message to all \n[action]:".$this->dataBox['action']."\n---------------------------------------\n";
			}else{
				if($clientID == $client->resourceId){
					$client->send($data_JSON);
					echo "<--- " . $this->getConnectionID($client) . " --->\nSEND message\n[action]:".$this->dataBox['action']."\n---------------------------------------\n";
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
				$this->dataBox['action'] = 'anotherOneLeaveGameRoom';
				$this->dataBox['data'] = "";
				if($gameRoom->player1->account == $player->account){
					$player2 = $this->playerList[$this->getConnectionID($gameRoom->player2->client)];
					$player2->roomID = NULL;
					$this->sendDataTo($gameRoom->player2);
				}else{
					$player1 = $this->playerList[$this->getConnectionID($gameRoom->player1->client)];
					$player1->roomID = NULL;
					$this->sendDataTo($gameRoom->player1);
				}
			}
		}
		unset($this->gameRoomList[$player->roomID]);
		unset($this->playerList[$this->getConnectionID($client)]);
		
		$this->refreshGameRoom('all');
	}
}