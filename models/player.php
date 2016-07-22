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
    
    function __construct(){
        
    }
    
    function getPlayer(){
        $PDO = new myPDO();
        $conn = $PDO->getConnection();
        $stmt = $conn->prepare("SELECT * FROM player WHERE account = ? and password = ? LIMIT 1");
        $stmt->bindValue(1, $this->account, PDO::PARAM_STR);
        $stmt->bindValue(2, $this->password_hash, PDO::PARAM_STR);

        $stmt->execute();
        $data = $stmt->fetch();
        $PDO->closeConnection();

        return $data;
    }
    
    function getPlayerByAccount(){
        $PDO = new myPDO();
        $conn = $PDO->getConnection();
        $stmt = $conn->prepare("SELECT * FROM player WHERE account = ? LIMIT 1");
        $stmt->bindValue(1, $this->account, PDO::PARAM_STR);

        $stmt->execute();
        $data = $stmt->fetch();
        
        $PDO->closeConnection();

        return $data;
    }
    
    function getPlayerByNickname(){
        $PDO = new myPDO();
        $conn = $PDO->getConnection();
        $stmt = $conn->prepare("SELECT * FROM player WHERE nickname = ? LIMIT 1");
        $stmt->bindValue(1, $this->nickname, PDO::PARAM_STR);

        $stmt->execute();
        $data = $stmt->fetch();
        
        $PDO->closeConnection();

        return $data;
    }
    
    function addPlayer(){
        $PDO = new myPDO();
        $conn = $PDO->getConnection();
        $sql = "INSERT INTO player (account, password, email, nickname, registtime, updatetime) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $this->account, PDO::PARAM_STR);
        $stmt->bindValue(2, $this->password_hash, PDO::PARAM_STR);
        $stmt->bindValue(3, $this->email, PDO::PARAM_STR);
        $stmt->bindValue(4, $this->nickname, PDO::PARAM_STR);
        $stmt->bindValue(5, $this->registtime);
        $stmt->bindValue(6, $this->updatetime);

        $data = $stmt->execute();
        
        $PDO->closeConnection();
        
        return $data;
    }
    
    function updateData(){
        $PDO = new myPDO();
        $conn = $PDO->getConnection();
        $sql = "UPDATE player SET password = ?, email = ?, nickname = ?, updatetime = ? WHERE account = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $this->password_hash, PDO::PARAM_STR);
        $stmt->bindValue(2, $this->email, PDO::PARAM_STR);
        $stmt->bindValue(3, $this->nickname, PDO::PARAM_STR);
        $stmt->bindValue(4, $this->updatetime, PDO::PARAM_STR);
        $stmt->bindValue(5, $this->account, PDO::PARAM_STR);

        $data = $stmt->execute();
        
        $PDO->closeConnection();
        
        return $data;
    }
    
    function queryByNickname($nickName){
        $PDO = new myPDO();
        $conn = $PDO->getConnection();
        $sql = "SELECT nickname FROM player WHERE nickname like ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $nickName, PDO::PARAM_STR);

        $stmt->execute();
        $data = $stmt->fetchAll();
        
        $PDO->closeConnection();
        
        return $data;
    }
    
    function setLoginState( $account ){
        $PDO = new myPDO();
        $conn = $PDO->getConnection();
        $sql = "UPDATE player SET isOnline = '是' WHERE account = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $account, PDO::PARAM_STR);

        $stmt->execute();
        $PDO->closeConnection();
    }
    
    function setLogoutState( $account ){
        $PDO = new myPDO();
        $conn = $PDO->getConnection();
        $sql = "UPDATE player SET isOnline = '否' WHERE account = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $account, PDO::PARAM_STR);

        $stmt->execute();
        $PDO->closeConnection();
    }
}

?>