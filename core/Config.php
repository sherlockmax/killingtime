<?PHP
class Config {
    
    public $projectName;
    public $root;
    public $imgRoot;
    public $cssRoot;
    public $jsRoot;
    
    public $db;
    
    public $whiteList;
    
    function __construct(){
         /* 專案名稱 - <title> */
        $this->projectName = 'KillingTime 殺時間';
        
        /* 專案目錄結構設定 */
        $this->root = '/killingtime/';
        $this->imgRoot = $this->root . 'views/images/';
        $this->cssRoot = $this->root . 'views/css/';
        $this->jsRoot = $this->root . 'views/js/';

        /* 資料庫連線設定 */
        $this->db['host']       = 'localhost';
        $this->db['port']       = '3306';
        $this->db['username']   = 'max';
        $this->db['password']   = '123456';
        $this->db['dbname']     = 'killingtime';
        
        
        /* 不需要經過 是否登入狀態 的 request */
        $this->whiteList = array(  "home",
                                    "player/isAccountExsist", 
                                    "player/isNicknameExsist",
                                    "player/forgetPassword",
                                    "player/login",
                                    "player/registe" 
                                );
    }
}
?>