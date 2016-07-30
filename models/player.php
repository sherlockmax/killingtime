<?php
class Player {
    public $account;
    public $password;
    public $password_hash;
    public $email;
    public $nickname;
    public $registtime;
    public $isOnline;
    public $updatetime;
    
    function getPlayer(){
        $PDO = new myPDO();
        $conn = $PDO->getConnection();
        $stmt = $conn->prepare("SELECT * FROM `player` WHERE `account` = :account AND `password` = :password LIMIT 1");
        $stmt->bindValue(':account', $this->account, PDO::PARAM_STR);
        $stmt->bindValue(':password', $this->password_hash, PDO::PARAM_STR);

        $stmt->execute();
        $data = $stmt->fetch();
        $PDO->closeConnection();

        return $data;
    }
    
    function getPlayerByAccount(){
        $PDO = new myPDO();
        $conn = $PDO->getConnection();
        $stmt = $conn->prepare("SELECT * FROM `player` WHERE `account` = :account LIMIT 1");
        $stmt->bindValue(':account', $this->account, PDO::PARAM_STR);

        $stmt->execute();
        $data = $stmt->fetch();
        
        $PDO->closeConnection();

        return $data;
    }
    
    function getPlayerByNickname(){
        $PDO = new myPDO();
        $conn = $PDO->getConnection();
        $stmt = $conn->prepare("SELECT * FROM `player` WHERE `nickname` = :nickname LIMIT 1");
        $stmt->bindValue(':nickname', $this->nickname, PDO::PARAM_STR);

        $stmt->execute();
        $data = $stmt->fetch();
        
        $PDO->closeConnection();

        return $data;
    }
    
    function addPlayer(){
        $PDO = new myPDO();
        $conn = $PDO->getConnection();
        $sql = "INSERT INTO `player` (`account`, `password`, `email`, `nickname`, `registtime`, `updatetime`) VALUES (:account, :password, :email, :nickname, :registtime, :updatetime)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':account', $this->account, PDO::PARAM_STR);
        $stmt->bindValue(':password', $this->password_hash, PDO::PARAM_STR);
        $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
        $stmt->bindValue(':nickname', $this->nickname, PDO::PARAM_STR);
        $stmt->bindValue(':registtime', $this->registtime);
        $stmt->bindValue(':updatetime', $this->updatetime);

        $data = $stmt->execute();
        $PDO->closeConnection();
        
        return $data;
    }
    
    function updateData(){
        $PDO = new myPDO();
        $conn = $PDO->getConnection();
        $sql = "UPDATE `player` SET `password` = :password, `email` = :email, `nickname` = :nickname, `updatetime` = :updatetime WHERE `account` = :account";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':password', $this->password_hash, PDO::PARAM_STR);
        $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
        $stmt->bindValue(':nickname', $this->nickname, PDO::PARAM_STR);
        $stmt->bindValue(':updatetime', $this->updatetime, PDO::PARAM_STR);
        $stmt->bindValue(':account', $this->account, PDO::PARAM_STR);

        $data = $stmt->execute();
        $PDO->closeConnection();
        
        return $data;
    }
    
    function queryByNickname($nickName){
        $PDO = new myPDO();
        $conn = $PDO->getConnection();
        $sql = "SELECT `nickname` FROM `player` WHERE `nickname` like :nickname";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':nickname', $nickName, PDO::PARAM_STR);

        $stmt->execute();
        $data = $stmt->fetchAll();
        
        $PDO->closeConnection();
        
        return $data;
    }
    
    function setLoginState( $account ){
        $PDO = new myPDO();
        $conn = $PDO->getConnection();
        $sql = "UPDATE `player` SET `isOnline` = '是' WHERE `account` = :account";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':account', $account, PDO::PARAM_STR);

        $stmt->execute();
        $PDO->closeConnection();
    }
    
    function setLogoutState( $account ){
        $PDO = new myPDO();
        $conn = $PDO->getConnection();
        $sql = "UPDATE `player` SET `isOnline` = '否' WHERE `account` = :account";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':account', $account, PDO::PARAM_STR);

        $stmt->execute();
        $PDO->closeConnection();
    }
}

?>