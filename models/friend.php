<?PHP
class friend{
     
     public $id;
     public $invite;
     public $player;
     public $status;
     public $datetime;

     function getFriendList($loginAccount){
          $PDO = new myPDO();
          $conn = $PDO->getConnection();
          $sql = "  SELECT `p`.`account`, `p`.`nickname`, `p`.`isOnline` FROM (
                         SELECT `player` as `friendAccount` FROM `friend` WHERE `invite` = :loginAccount AND `status` = 'F'
                         UNION
                         SELECT `invite` as `friendAccount` FROM `friend` WHERE `player` = :loginAccount AND `status` = 'F'
                    ) AS `f` LEFT JOIN `player` AS p ON `f`.`friendAccount` = `p`.`account`
                    WHERE `p`.`account` IS NOT NULL";
          $stmt = $conn->prepare($sql);
          $stmt->bindValue(':loginAccount', $loginAccount, PDO::PARAM_STR);
          
          $stmt->execute();
          $data = $stmt->fetchAll();
          $PDO->closeConnection();

          return $data;
     }
     
     function getFriendStatus($inviteAccount, $playerAccount){
          $PDO = new myPDO();
          $conn = $PDO->getConnection();
          $sql = "SELECT * FROM `friend` WHERE ( ( `invite` = :inviteAccount AND `player` = :playerAccount) OR (`invite` = :playerAccount AND `player` = :inviteAccount ) )";
          $stmt = $conn->prepare($sql);
          $stmt->bindValue(':inviteAccount', $inviteAccount, PDO::PARAM_STR);
          $stmt->bindValue(':playerAccount', $playerAccount, PDO::PARAM_STR);

          
          $stmt->execute();
          $data = $stmt->fetch();
          $PDO->closeConnection();
          
          return $data;
     }
     
     function getFriendInvite($inviteAccount){
          $PDO = new myPDO();
          $conn = $PDO->getConnection();
          $sql = "SELECT * FROM `friend` AS `f` LEFT JOIN `player` AS `p` ON `f`.`player` = `p`.`account` WHERE `invite` = :inviteAccount AND `status` = 'W'";
          $stmt = $conn->prepare($sql);
          $stmt->bindValue(':inviteAccount', $inviteAccount, PDO::PARAM_STR);

          $stmt->execute();
          $data = $stmt->fetchAll();
          $PDO->closeConnection();

          return $data;
     }
     
     function getWhoInviteMe($playerAccount){
          $PDO = new myPDO();
          $conn = $PDO->getConnection();
          $sql = "SELECT * FROM `friend` AS `f` LEFT JOIN `player` AS `p` ON `f`.`invite` = `p`.`account` WHERE `player` = :playerAccount AND `status` = 'W'";
          $stmt = $conn->prepare($sql);
          $stmt->bindValue(':playerAccount', $playerAccount, PDO::PARAM_STR);

          $stmt->execute();
          $data = $stmt->fetchAll();
          $PDO->closeConnection();

          return $data;
     }
     
     function addFriend($inviteAccount, $playerAccount, $updatetime){
          $PDO = new myPDO();
          $conn = $PDO->getConnection();
          $sql = "INSERT INTO `friend` (`invite`, `player`, `status`, `updatetime`) VALUES (:invite, :player, 'W', :updatetime)";
          $stmt = $conn->prepare($sql);
          $stmt->bindValue(':invite', $inviteAccount, PDO::PARAM_STR);
          $stmt->bindValue(':player', $playerAccount, PDO::PARAM_STR);
          $stmt->bindValue(':updatetime', $updatetime);

          $result = $stmt->execute();
          $PDO->closeConnection();
          
          return $result;
     }
     
     function acceptInvite($inviteAccount, $playerAccount, $updatetime){
          $PDO = new myPDO();
          $conn = $PDO->getConnection();
          $sql = "UPDATE `friend` SET `status` = 'F', `updatetime` = :updatetime WHERE `invite` = :invite AND `player` = :player ";
          $stmt = $conn->prepare($sql);
          $stmt->bindValue(':updatetime', $updatetime);
          $stmt->bindValue(':invite', $inviteAccount, PDO::PARAM_STR);
          $stmt->bindValue(':player', $playerAccount, PDO::PARAM_STR);
          
          $result = $stmt->execute();
          $PDO->closeConnection();
          
          return $result;
     }
     
     function deleteFriend($inviteAccount, $playerAccount){
          $PDO = new myPDO();
          $conn = $PDO->getConnection();
          $sql = "DELETE FROM `friend` WHERE (`invite` = :invite AND `player` = :player ) OR (`invite` = :player AND `player` = :invite)";
          $stmt = $conn->prepare($sql);
          $stmt->bindValue(':invite', $inviteAccount);
          $stmt->bindValue(':player', $playerAccount, PDO::PARAM_STR);
          
          $result = $stmt->execute();
          $PDO->closeConnection();
          
          return $result;
     }
}

?>