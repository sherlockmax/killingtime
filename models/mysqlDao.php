<?PHP


class mysqlDao{
     
     private $host = 'localhost';
     private $port = '3306';
     private $username = 'max';
     private $password = '123456';
     private $dbname = 'KillingTime';
     
     private static $connection = NULL;
     
     function __construct() {
          $pdo = new PDO("mysql:host=$this->host:$this->port;dbname=$this->dbname", $this->username, $this->password);
          $pdo->exec("SET CHARACTER SET utf8");
          self::$connection = $pdo;
     }
     
     public function getConnection(){
          return self::$connection;
     }
     
     
     function closeConnection(){
          self::$connection = NULL;
     }
     
     
}


?>