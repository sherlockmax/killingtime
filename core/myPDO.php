<?PHP
require_once "Config.php";

class myPDO{
     private static $connection = NULL;
     
     function __construct() {
          $config = new Config();
          $pdo = new PDO("mysql:host=".$config->db['host'].":".$config->db['port'].";dbname=".$config->db['dbname'], $config->db['username'], $config->db['password']);
          $pdo->exec("SET CHARACTER SET utf8");
          self::$connection = $pdo;
          $config = null;
          $pdo = null;
     }
     
     function getConnection(){
          return self::$connection;
     }
     
     
     function closeConnection(){
          self::$connection = NULL;
     }
}
?>
