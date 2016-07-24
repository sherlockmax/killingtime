<?PHP

class Controller {
    public function model($model) {
        require_once "core/myPDO.php";
        require_once "models/$model.php";
        return new $model ();
    }
    
    public function view($view, $data = Array()) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $config = $this->config();
        $player = Array();
        if(isset($_SESSION['player'])){
            $player = $_SESSION['player'];
        }
        $isLogin = null;
        if(isset($_SESSION['isLogin'])){ 
            $isLogin = $_SESSION['isLogin'];
        }
        require_once "views/$view.php";
    }
    
    public function config(){
        return new config();
    }
}
?>