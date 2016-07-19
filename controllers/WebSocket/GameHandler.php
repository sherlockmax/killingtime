<?php
namespace WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use WebSocket\GameServerController;

class GameHandler implements MessageComponentInterface {
    protected $clients;
	protected $gameRoomList;
	protected $controller;
	protected $data = array();

    public function __construct() {
        $this->clients = new \SplObjectStorage;
		$this->controller = new GameServerController();
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo $this->getConnectionID($conn) . "---> New connection\n";
		
		$this->data['action'] = 'gameRoomList';
		$this->data['gameRoomList'] = json_encode($this->gameRoomList);
		$this->sendDataTo($conn->resourceId);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
		$dataJson = json_decode($msg, true);
		$action = $dataJson['action'];
		$data = $dataJson['data'];
		
		$this->data['action'] = 'createRoom';
		$this->data['data'] = '{"roomID":0001, "gameName" : '.$data['gameName'].'}';
		
		//$this->controller->$action();
		
		$this->sendDataTo($from->resourceId);
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "--->Disconnection ID: {$conn->resourceId}\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
	
	public function sendDataTo($clientID){
		$data_JSON = json_encode($this->data);
		foreach ($this->clients as $client) {
			if($clientID == 'all'){
				$client->send($data_JSON);
				echo "99999 ---> send message to all [action]:".$this->data['action']."\n [data]:".$this->data['data']."\n";
			}else{
				if($clientID == $client->resourceId){
					$client->send($data_JSON);
					echo $this->getConnectionID($client) . " ---> send message to all [action]:".$this->data['action']."\n [data]:".$this->data['data']."\n";
				}
			}
		}
	}
	
	public function getConnectionID($client){
		return str_pad($client->resourceId, 5, "0", STR_PAD_LEFT);
	}
}