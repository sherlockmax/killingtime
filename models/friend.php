<?PHP
class friend{
     
     public $id;
     public $invite;
     public $player;
     public $status;
     public $datetime;
     
     function __construct(){   }
     
     function getFriendList($loginAccount){
          $PDO = new myPDO();
          $conn = $PDO->getConnection();
          $sql = "  SELECT p.account, p.nickname, p.isOnline FROM (
                         SELECT player as friendAccount FROM friend WHERE invite = ? and status = 'F'
                         union
                         SELECT invite as friendAccount FROM friend WHERE player = ? and status = 'F'
                    ) as f LEFT JOIN player as p ON f.friendAccount = p.account
                    WHERE p.account IS NOT NULL";
          $stmt = $conn->prepare($sql);
          $stmt->bindValue(1, $loginAccount, PDO::PARAM_STR);
          $stmt->bindValue(2, $loginAccount, PDO::PARAM_STR);
          
          $stmt->execute();
          $data = $stmt->fetchAll();
          $PDO->closeConnection();

          return $data;
     }
     
     function getFriendStatus($inviteAccount, $playerAccount){
          $PDO = new myPDO();
          $conn = $PDO->getConnection();
          $sql = "SELECT * FROM friend WHERE ( ( invite = ? AND player = ?) OR (invite = ? AND player = ? ) )";
          $stmt = $conn->prepare($sql);
          $stmt->bindValue(1, $inviteAccount, PDO::PARAM_STR);
          $stmt->bindValue(2, $playerAccount, PDO::PARAM_STR);
          $stmt->bindValue(3, $playerAccount, PDO::PARAM_STR);
          $stmt->bindValue(4, $inviteAccount, PDO::PARAM_STR);
          
          $stmt->execute();
          $data = $stmt->fetch();
          $PDO->closeConnection();
          
          return $data;
     }
     
     function getFriendInvite($inviteAccount){
          $PDO = new myPDO();
          $conn = $PDO->getConnection();
          $sql = "SELECT * FROM friend f LEFT JOIN player p ON f.player = p.account WHERE invite = ? AND status = 'W'";
          $stmt = $conn->prepare($sql);
          $stmt->bindValue(1, $inviteAccount, PDO::PARAM_STR);

          $stmt->execute();
          $data = $stmt->fetchAll();
          $PDO->closeConnection();

          return $data;
     }
     
     function getWhoInviteMe($playerAccount){
          $PDO = new myPDO();
          $conn = $PDO->getConnection();
          $sql = "SELECT * FROM friend f LEFT JOIN player p ON f.invite = p.account WHERE player = ? AND status = 'W'";
          $stmt = $conn->prepare($sql);
          $stmt->bindValue(1, $playerAccount, PDO::PARAM_STR);

          $stmt->execute();
          $data = $stmt->fetchAll();
          $PDO->closeConnection();

          return $data;
     }
     
     function addFriend($inviteAccount, $playerAccount, $updatetime){
          $PDO = new myPDO();
          $conn = $PDO->getConnection();
          $sql = "INSERT INTO friend (invite, player, status, updatetime) VALUES (?, ?, ?, ?)";
          $stmt = $conn->prepare($sql);
          $stmt->bindValue(1, $inviteAccount, PDO::PARAM_STR);
          $stmt->bindValue(2, $playerAccount, PDO::PARAM_STR);
          $stmt->bindValue(3, 'W', PDO::PARAM_STR);
          $stmt->bindValue(4, $updatetime);

          $result = $stmt->execute();
          $PDO->closeConnection();
          
          return $result;
     }
     
     function acceptInvite($inviteAccount, $playerAccount, $updatetime){
          $PDO = new myPDO();
          $conn = $PDO->getConnection();
          $sql = "UPDATE friend SET status = 'F', updatetime = ? WHERE invite = ? AND player = ? ";
          $stmt = $conn->prepare($sql);
          $stmt->bindValue(1, $updatetime);
          $stmt->bindValue(2, $inviteAccount, PDO::PARAM_STR);
          $stmt->bindValue(3, $playerAccount, PDO::PARAM_STR);
          
          $result = $stmt->execute();
          $PDO->closeConnection();
          
          return $result;
     }
     
     function deleteFriend($inviteAccount, $playerAccount){
          $PDO = new myPDO();
          $conn = $PDO->getConnection();
          $sql = "DELETE FROM friend WHERE (invite = ? AND player = ?) OR (invite = ? AND player = ?)";
          $stmt = $conn->prepare($sql);
          $stmt->bindValue(1, $inviteAccount);
          $stmt->bindValue(2, $playerAccount, PDO::PARAM_STR);
          $stmt->bindValue(3, $playerAccount, PDO::PARAM_STR);
          $stmt->bindValue(4, $inviteAccount, PDO::PARAM_STR);
          
          $result = $stmt->execute();
          $PDO->closeConnection();
          
          return $result;
     }
}

?>