<?PHP
require_once "mysqlDao.php";

class game{
     public $id;
     public $player1;
     public $player2;
     public $gamename;
     public $winner;
     public $gamedata;
     public $memo;
     public $updatetime;
     
     function getHistory($account){
          $conn = new mysqlDao();
          $sql = "  SELECT p.account, p.nickname, p.isOnline FROM (
                         SELECT player1 as playerAccount FROM gamerecord WHERE player2 = ?
                         union
                         SELECT player2 as friendAccount FROM gamerecord WHERE player1 = ?
                    ) as f LEFT JOIN player as p ON f.friendAccount = p.account
                    WHERE p.account IS NOT NULL";
          $stmt = $conn->getConnection()->prepare($sql);
          $stmt->bindValue(1, $loginAccount, PDO::PARAM_STR);
          $stmt->bindValue(2, $loginAccount, PDO::PARAM_STR);
          
          $stmt->execute();
          $data = $stmt->fetchAll();
          $conn->closeConnection();

          return $data;
     }
	 	 
	 function saveGameRecord($player1, $player2, $gameName, $winner, $gameData, $memo, $updatetime){	
		$conn = new mysqlDao();
        $sql = "INSERT INTO gamerecord (player1, player2, gamename, winner, gamedata, updatetime, memo) VALUES (?, ?, ?, ?, ?, ?, ?)";		
		
		$stmt = $conn->getConnection()->prepare($sql);		
        
		$stmt->bindValue(1, $player1, PDO::PARAM_STR);
        $stmt->bindValue(2, $player2, PDO::PARAM_STR);
        $stmt->bindValue(3, $gameName, PDO::PARAM_STR);
        $stmt->bindValue(4, $winner, PDO::PARAM_STR);
        $stmt->bindValue(5, $gameData, PDO::PARAM_STR);
        $stmt->bindValue(6, $updatetime);
		$stmt->bindValue(7, $memo, PDO::PARAM_STR);
        
		$data = $stmt->execute();
		
        $conn->closeConnection();
		
		return $data;
	}
}

?>