<?php
require_once "mysqlDao.php";

class Player {
    public $account;
    public $password;
    public $password_hash;
    public $email;
    public $nickname;
    public $registtime;
    public $isOnline;
    public $updatetime;
    
    function __construct(){   }
    
    function getPlayer(){
        $conn = new mysqlDao();
        $stmt = $conn->getConnection()->prepare("SELECT * FROM player WHERE account = ? and password = ? LIMIT 1");
        $stmt->bindValue(1, $this->account, PDO::PARAM_STR);
        $stmt->bindValue(2, $this->password_hash, PDO::PARAM_STR);

        $stmt->execute();
        $data = $stmt->fetch();
        $conn->closeConnection();

        return $data;
    }
    
    function getPlayerByAccount(){
        $conn = new mysqlDao();
        $stmt = $conn->getConnection()->prepare("SELECT * FROM player WHERE account = ? LIMIT 1");
        $stmt->bindValue(1, $this->account, PDO::PARAM_STR);

        $stmt->execute();
        $data = $stmt->fetch();
        
        $conn->closeConnection();

        return $data;
    }
    
    function getPlayerByNickname(){
        $conn = new mysqlDao();
        $stmt = $conn->getConnection()->prepare("SELECT * FROM player WHERE nickname = ? LIMIT 1");
        $stmt->bindValue(1, $this->nickname, PDO::PARAM_STR);

        $stmt->execute();
        $data = $stmt->fetch();
        
        $conn->closeConnection();

        return $data;
    }
    
    function addPlayer(){
        $conn = new mysqlDao();
        $sql = "INSERT INTO player (account, password, email, nickname, registtime, updatetime) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->getConnection()->prepare($sql);
        $stmt->bindValue(1, $this->account, PDO::PARAM_STR);
        $stmt->bindValue(2, $this->password_hash, PDO::PARAM_STR);
        $stmt->bindValue(3, $this->email, PDO::PARAM_STR);
        $stmt->bindValue(4, $this->nickname, PDO::PARAM_STR);
        $stmt->bindValue(5, $this->registtime);
        $stmt->bindValue(6, $this->updatetime);

        $data = $stmt->execute();
        
        $conn->closeConnection();
        
        return $data;
    }
    
    function updateData(){
        $conn = new mysqlDao();
        $sql = "UPDATE player SET password = ?, email = ?, nickname = ?, updatetime = ? WHERE account = ?";
        $stmt = $conn->getConnection()->prepare($sql);
        $stmt->bindValue(1, $this->password_hash, PDO::PARAM_STR);
        $stmt->bindValue(2, $this->email, PDO::PARAM_STR);
        $stmt->bindValue(3, $this->nickname, PDO::PARAM_STR);
        $stmt->bindValue(4, $this->updatetime, PDO::PARAM_STR);
        $stmt->bindValue(5, $this->account, PDO::PARAM_STR);

        $data = $stmt->execute();
        
        $conn->closeConnection();
        
        return $data;
    }
    
    function queryByNickname($nickName){
        $conn = new mysqlDao();
        $sql = "SELECT nickname FROM player WHERE nickname like ?";
        $stmt = $conn->getConnection()->prepare($sql);
        $stmt->bindValue(1, $nickName, PDO::PARAM_STR);

        $stmt->execute();
        $data = $stmt->fetchAll();
        
        $conn->closeConnection();
        
        return $data;
    }
    
    function setLoginState( $account ){
        $conn = new mysqlDao();
        $sql = "UPDATE player SET isOnline = '是' WHERE account = ?";
        $stmt = $conn->getConnection()->prepare($sql);
        $stmt->bindValue(1, $account, PDO::PARAM_STR);

        $stmt->execute();
        $conn->closeConnection();
    }
    
    function setLogoutState( $account ){
        $conn = new mysqlDao();
        $sql = "UPDATE player SET isOnline = '否' WHERE account = ?";
        $stmt = $conn->getConnection()->prepare($sql);
        $stmt->bindValue(1, $account, PDO::PARAM_STR);

        $stmt->execute();
        $conn->closeConnection();
    }
}

?>