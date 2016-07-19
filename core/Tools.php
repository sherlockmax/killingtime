<?PHP
require_once "core/class.phpmailer.php";

class Tools{
	static function getRandPassword() {
	    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	    $pass = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    for ($i = 0; $i < 8; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
	    return implode($pass); //turn the array into a string
	}
	
	static function getPasswordHash($password){
		$salt = substr($password, 0, 2);
        return hash("MD5", $password . $salt);
	}

    static function getCurrentDateTime(){
        $date = new DateTime();
        return $date->format('Y-m-d H:i:s');
    }
    
    static function sendResetPasswordMail($data){
    	$mailHTML = '
			    <p>'.$data['nickname'].'('.$data['account'].') 您好：</p>
			    <p>以下是您於'.$data['updatetime'].'申請重置的新密碼，請妥善保管並請儘速登入修改密碼，以確保帳戶安全。</p>
			    <p style="text-align:center">--------------------------------------</p>
			    <p style="text-align:center"><strong>'.$data['password'].'</strong></p>
			    <p style="text-align:center"><a href="https://killingtime-sherlockmax.c9users.io/">前往並修改密碼</a></p>
			    <p style="text-align:center">--------------------------------------</p>
			    <p class="copyright">此信件由 Killing Time 系統自動發送，請勿直接回覆。</p>';
    	
    	
    	
        $mail= new PHPMailer();          //建立新物件
		$mail->IsSMTP();                 //設定使用SMTP方式寄信
		$mail->SMTPAuth = true;          //設定SMTP需要驗證
		$mail->SMTPSecure = "ssl";       // Gmail的SMTP主機需要使用SSL連線
		$mail->Host = "smtp.gmail.com";  //Gamil的SMTP主機
		$mail->Port = 465;               //Gamil的SMTP主機的SMTP埠位為465埠。
		$mail->CharSet = "utf-8";         //設定郵件編碼        

		$mail->Username = "killingtime.max";  //Gmail帳號
		$mail->Password = "mtjvxmaagoqlwrcv";  //Gmail密碼        

		$mail->From = "killingtime.max@gmail.com"; //設定寄件者信箱
		$mail->FromName = "KillingTime";           //設定寄件者姓名
		$mail->Subject = "密碼重置申請 - Killing Time";    //設定郵件標題
		$mail->Body = $mailHTML;  //設定郵件內容
		$mail->IsHTML(true);                     //設定郵件內容為HTML
		$mail->AddAddress($data['email'], $data['account']); //設定收件者郵件及名稱        

		if(!$mail->Send()) {
			//echo "<div style='display:none'>Mailer Error: " . $mail->ErrorInfo . "</div>";
			return false;
		}else {
			return true;
		}
    }
    
    static function checkFriendStatus($data, $loginAccount){
    	if(empty($data)){
    		return "no";
    	}else if($data['status'] == 'F'){
    		return "friend";
    	}else{
    		if($data['invite'] == $loginAccount){
    			return "invite";
    		}else{
    			return "notInvite";
    		}
    	}
    }
}

?>