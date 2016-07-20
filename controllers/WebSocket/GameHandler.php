<?php
namespace WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use WebSocket\GamePlayerModel;
use WebSocket\GameRoomModel;

class GameHandler implements MessageComponentInterface {
    protected $clients;
	protected $gameRoomList = [];
	protected $playerList;
	protected $controller;
	protected $dataBox = array("action" => "", "data" => "");
	protected $messageTime;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
		$this->dataBox['action'] = "";
		$this->dataBox['data'] = "";
		$this->messageTime = time();
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
			$gameRoom = new GameRoomModel($newRoomID, $player, NULL, $this->dataBox['data']['gameName'], NULL);
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
				
				$this->dataBox['action'] = 'anotherOneleft';
				$this->dataBox['data'] = null;
				if($gameRoom->player1->account == $this->dataBox['data']['player']['account']){
					$this->sendDataTo($player2->client->resourceId);
				}else{
					$this->sendDataTo($player1->client->resourceId);
				}
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
		}else if($this->dataBox['action'] == 'sendMessage'){
			$this->dataBox['action'] = 'sendMessage';
			if((time() - $this->messageTime) > 3 ){
				$gameRoom = $this->gameRoomList[$this->dataBox['data']['roomID']];
				if(!empty($gameRoom->player1) && !empty($gameRoom->player2)){
					$player = $player = $this->playerList[$this->getConnectionID($from)];
					if($gameRoom->player1->account == $player->account){
						$msgArray = array("nickname" => $gameRoom->player1->nickname.' ('.date('H:i:s').')' , "msg" => $this->dataBox['data']['msg']);
						$this->dataBox['data'] = json_encode($msgArray);
						$this->sendDataTo($gameRoom->player2->client->resourceId);
					}else{
						$msgArray = array("nickname" => $gameRoom->player2->nickname.' ('.date('H:i:s').')' , "msg" => $this->dataBox['data']['msg']);
						$this->dataBox['data'] = json_encode($msgArray);
						$this->sendDataTo($gameRoom->player1->client->resourceId);
					}
				}else{
					$msgArray = array("nickname" => '尚無對手! ('.date('H:i:s').')' , "msg" => "無法發送訊息。");
					$this->dataBox['data'] = json_encode($msgArray);
					$this->sendDataTo($this->getConnectionID($from));
				}
			}else{
				$msgArray = array("nickname" => '警告! ('.date('H:i:s').')' , "msg" => "發送訊息過於頻繁。");
				$this->dataBox['data'] = json_encode($msgArray);
				$this->sendDataTo($this->getConnectionID($from));
			}
			
			$this->messageTime = time();
		}
    }

    public function onClose(ConnectionInterface $conn) {
		echo "<--- " . $this->getConnectionID($conn) . " --->\nDisconnection\n---------------------------------------\n";
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
				$this->dataBox['action'] = 'anotherOneleft';
				$this->dataBox['data'] = null;
				if($gameRoom->player1->account == $player->account){
					$player2 = $this->playerList[$this->getConnectionID($gameRoom->player2->client)];
					$player2->roomID = NULL;
					$this->playerList[$this->getConnectionID($gameRoom->player2->client)] = $player2;
					$this->sendDataTo($gameRoom->player2->client->resourceId);
				}else{
					$player1 = $this->playerList[$this->getConnectionID($gameRoom->player1->client)];
					$player1->roomID = NULL;
					$this->playerList[$this->getConnectionID($gameRoom->player1->client)] = $player1;
					$this->sendDataTo($gameRoom->player1->client->resourceId);
				}
			}
		}
		unset($this->gameRoomList[$player->roomID]);
		unset($this->playerList[$this->getConnectionID($client)]);
		
		$this->refreshGameRoom('all');
	}
}