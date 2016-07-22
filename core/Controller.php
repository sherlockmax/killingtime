<?PHP

class Controller {
    public function model($model) {
        require_once "core/myPDO.php";
        require_once "models/$model.php";
        return new $model ();
    }
    
    public function view($view, $data = Array()) {
        $config = new config();
        require_once "views/$view.php";
    }

    public function getSession($sessionKey, $isRemove = false){
        if(isset($_SESSION[$sessionKey])){
            echo $_SESSION[$sessionKey];
            if($isRemove){
                unset($_SESSION[$sessionKey]);
            }
        }
    }
}
?>