<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body>
<input id="msg" type="text" >
<input type="button" value="send" onclick="sendMsg()">
<div id="msgBox"></div>

	<script>
		var conn = new WebSocket('ws://localhost:8080/');
		conn.onopen = function(e) {
			console.log("Connection established!");
		};

		conn.onmessage = function(e) {
			console.log(e.data);
			document.getElementById("msgBox").innerHTML +=  e.data + "<BR>";
			
		};
		
		
		function sendMsg(){
			conn.send( document.getElementById("msg").value );
		}
		
		
	</script>
</body>
</html>
