<?PHP
require_once "Config.php";

class myPDO{
     private static $connection = NULL;
     
     function __construct() {
          $config = new Config();
          $dns = sprintf("mysql:host=%s:%s;dbname=%s", $config->db['host'], $config->db['port'], $config->db['dbname']);
          self::$connection = new PDO($dns, $config->db['username'], $config->db['password']);
          self::$connection->exec("SET CHARACTER SET utf8");
     }
     
     function getConnection(){
          return self::$connection;
     }
     
     
     function closeConnection(){
          self::$connection = NULL;
     }
}
?>
