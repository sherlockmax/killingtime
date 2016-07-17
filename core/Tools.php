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
			<html>
			<head>
			    <style>
			    	body{
			    		color: white;
			            background: black;
			    	}
			        .box{
			            width: 300px;
			            margin: 0 auto;
			            padding: 0px;
			            color: white;
			            background: black;
			        }
			       .box div{
			           margin-top: 5px;
			           margin-bottom: 10px;
			        }
			        
			        .title{
			            font-size: 20px;
			            color: white;
			            font-weight: bold;
			        }
			        
			        .content{
			            font-size: 16px;
			            color: #DCDCDC;
			        }
			        
			        .password{
			            font-size: 30px;
			            color: #90EE90;
			            font-weight: bold;
			            text-align: center;
			        }

			        .copyright{
			            font-size: 14px;
			            color: #87CEEB;
			        }
			        
			    </style>
			</head>
			<body>
			<div class="box">
			    <div class="title">'.$data['nickname'].' 您好：</div>
			    <div class="content">以下是您於'.$data['updatetime'].'申請重置的新密碼，請妥善保管並請儘速登入修改密碼，以確保帳戶安全。</div>
			    <br />
			    <div class="password"><strong>'.$data['password'].'</strong></div>
			    <br />
			    <div class="copyright">此信件由 Killing Time 系統自動發送，請勿直接回覆。</div>
			</div>
			</body>
			</html>';
    	
    	
    	
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
		$mail->Subject = "密碼重置信件 - Killing Time";    //設定郵件標題
		$mail->Body = $mailHTML;  //設定郵件內容
		$mail->IsHTML(true);                     //設定郵件內容為HTML
		$mail->AddAddress("uutony29@gmail.com", "uutony29"); //設定收件者郵件及名稱        

		if(!$mail->Send()) {
			echo "<div style='display:none'>Mailer Error: " . $mail->ErrorInfo . "</div>";
			return false;
		}else {
			return true;
		}
    }
}

?>