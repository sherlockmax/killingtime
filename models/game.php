<?PHP
class game{
     public $id;
     public $player1;
     public $player2;
     public $gamename;
     public $winner;
     public $gamedata;
     public $memo;
     public $updatetime;
     
     function getHistory($playerAccount){
          $PDO = new myPDO();
          $conn = $PDO->getConnection();
          $sql = "  SELECT `f`.*, `p`.`nickname` FROM ( 
						(SELECT `player1` as `playerAccount`, `winner`, `updatetime` FROM `gamerecord` WHERE `player2` = :playerAccount) 
						UNION ALL
						(SELECT `player2` as `playerAccount`, `winner`, `updatetime` FROM `gamerecord` WHERE `player1` = :playerAccount) 
					) AS `f` LEFT JOIN `player` AS `p` ON `f`.`playerAccount` = `p`.`account` ORDER BY `f`.`updatetime` DESC LIMIT 10";
          $stmt = $conn->prepare($sql);
          $stmt->bindValue(':playerAccount', $playerAccount, PDO::PARAM_STR);
          
          $stmt->execute();
          $data = $stmt->fetchAll();
          $PDO->closeConnection();

          return $data;
     }
	 	 
	 function saveGameRecord($player1, $player2, $gameName, $winner, $gameData, $memo, $updatetime){	
          $PDO = new myPDO();
          $conn = $PDO->getConnection();
          $sql = "INSERT INTO `gamerecord` (`player1`, `player2`, `gamename`, `winner`, `gamedata`, `updatetime`, `memo`) VALUES (:player1, :player2, :gamename, :winner, :gamedata, :updatetime, :memo)";		
          
          $stmt = $conn->prepare($sql);		
          
          $stmt->bindValue(':player1', $player1, PDO::PARAM_STR);
          $stmt->bindValue(':player2', $player2, PDO::PARAM_STR);
          $stmt->bindValue(':gamename', $gameName, PDO::PARAM_STR);
          $stmt->bindValue(':winner', $winner, PDO::PARAM_STR);
          $stmt->bindValue(':gamedate', $gameData, PDO::PARAM_STR);
          $stmt->bindValue(':updatetime', $updatetime);
          $stmt->bindValue(':memo', $memo, PDO::PARAM_STR);
          
          $data = $stmt->execute();
          
          $PDO->closeConnection();
          
          return $data;
	}
	
	function getScore($playerAccount){
          $PDO = new myPDO();
          $conn = $PDO->getConnection();
          $sql = 	"SELECT SUM(win) AS win, SUM(lose) AS lose, SUM(tie) AS tie FROM (
          			(SELECT COUNT(*) AS win, 0 AS lose, 0 AS tie FROM `gamerecord` WHERE ( `player1` = :playerAccount OR `player2` = :playerAccount ) AND `winner` = :playerAccount)
          			UNION ALL
          			(SELECT 0 AS win, COUNT(*) AS lose, 0 AS tie FROM `gamerecord` WHERE ( `player1` = :playerAccount OR `player2` = :playerAccount ) AND `winner` != :playerAccount AND `winner` != 'tie')
          			UNION ALL
          			(SELECT 0 AS win, 0 AS lose, COUNT(*) AS tie FROM `gamerecord` WHERE ( `player1` = :playerAccount OR `player2` = :playerAccount ) AND `winner` = 'tie')
          		) AS t";
          $stmt = $conn->prepare($sql);		
          
          $stmt->bindParam(':playerAccount', $playerAccount, PDO::PARAM_STR);
          
          $stmt->execute();
          $data = $stmt->fetch();
          
          $PDO->closeConnection();
          
          return $data;
	}
}

?>