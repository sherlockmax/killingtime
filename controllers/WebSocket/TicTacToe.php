<?PHP
namespace WebSocket;

class TicTacToe {
	
	static function compareArray($array){
		if(!empty($array[0]) && !empty($array[1]) && !empty($array[2])){
			if($array[0] == $array[1] && $array[0] == $array[2]){
				return true;
			}
		}
		return false;
	}

	static function checkWinner($player1, $player2, $gameData){
		/*
			win
			1-1 1-2 1-3
			2-1 2-2 2-3
			3-1 3-2 3-3
		*/
		$winnerMark = null;
		
		for($r=1; $r<=3; $r++){
			$row = null;
			$col = null;
			for($c=1; $c<=3; $c++){
				$row[] = $gameData[$c.'-'.$r]; //1-1 1-2 1-3
				$col[] = $gameData[$r.'-'.$c]; //2-1 2-2 2-3
			}
			if(self::compareArray($row)){
				$winnerMark = $row[0];
				break;
			}
			if(self::compareArray($col)){
				$winnerMark = $col[0];
				break;
			}
		}
		
		$mark = Array($gameData["1-1"], $gameData["2-2"], $gameData["3-3"]);
		if(self::compareArray($mark)){
			$winnerMark = $mark[0];
		}
		
		$mark = Array($gameData["1-3"], $gameData["2-2"], $gameData["3-1"]);
		if(self::compareArray($mark)){
			$winnerMark = $mark[0];
		}

		if($winnerMark == "O"){
			return $player1;
		}else if($winnerMark == "X"){
			return $player2;
		}else if($winnerMark == "tie"){
			return "tie";
		}else{
			return null;
		}
	}
}
?>