<?PHP

class Controller {
    public function model($model) {
        require_once "models/$model.php";
        return new $model ();
    }
    
    public function view($view, $data = Array()) {
        require_once "views/$view.php";
    }
    
    public function css($name){
        echo '<link rel="stylesheet" href="/css/'.$name.'.css"/>';
    }
    
    public function script($name){
        echo '<script src="/js/'.$name.'.js"></script>';
    }
    
    public function icon($name){
        echo '<link rel="icon" href="/images/'.$name.'.ico" type="image/x-icon" />';
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