<?php 
require_once('./db.php');
$conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql_execution = "SELECT json FROM ejson_content WHERE id = 1";
$stmt = $conn->prepare($sql_execution);
$stmt->execute();
$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
$result_array = $stmt->fetchAll();
$conn = null;
?>
<!DOCTYPE HTML>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>ejsoon JSONEditor</title>
		<link href="./jsoneditor.min.css" rel="stylesheet" type="text/css">
		<script src="./jsoneditor.min.js"></script>
		<meta name="viewport" content="width=device-width,minimum-scale=1,maximum-scale=1,initial-scale=1,user-scalable=no" />
		<style type="text/css">
html, body {width: 100%; height: 100%; margin: 0; padding: 0;}
body {
	font: 10.5pt arial;
	color: #4d4d4d;
	line-height: 150%;
}
		code {
			background-color: #f5f5f5;
		}
		#jsoneditor {
			width: 100%;
			height: 95%;
		}
		.loadandsave {height: 5%;}
		.las_btn {height: 100%; font-size: 16px;}
		.savebtn {}
		</style>
	</head>
	<body>
		<div class="loadandsave">
			<input type="password" class="loginpw">
			<button class="las_btn logbtn" type="button">LOGIN</button>
			<button class="las_btn savebtn" type="button">SAVE</button>
		</div>
		<div id="jsoneditor"></div>
		<script>
			// login or not
			var logbtn = document.querySelector(".logbtn");
var pw = document.querySelector(".loginpw");
if ("YES" == "<?php echo $_SESSION['login']?>") {
	pw.style.display = "none";
	logbtn.innerHTML = "LOGOUT";
} else {
	pw.style.display = "inline";
	logbtn.innerHTML = "LOGIN";
}
const container = document.getElementById('jsoneditor')
const options = {
	mode: 'tree',
	modes: ['tree', 'text', 'code', 'view'], // allowed modes
}
const json = <?php echo $result_array[0]['json']?>;
const editor = new JSONEditor(container, options, json)
// log out
logbtn.onclick = function () {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			alert(xmlhttp.responseText);
			if ("Login" == xmlhttp.responseText) {
				pw.style.display = "none";
				logbtn.innerHTML = "LOGOUT";
			} else {
				pw.style.display = "inline";
				logbtn.innerHTML = "LOGIN";
			}
		}
	}
	xmlhttp.open("POST","login.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");              //or multipart/form-data
	xmlhttp.send("log_action=" + logbtn.innerHTML + "&pw=" + pw.value);
}
// save json data
document.querySelector(".savebtn").onclick = function () {
	var pwvalue = pw.value;
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			alert(xmlhttp.responseText);
		}
	}
	xmlhttp.open("POST","save.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");              //or multipart/form-data
	xmlhttp.send("pw=" + pwvalue + "&json=" + JSON.stringify(editor.get()));
}
		</script>
	</body>
</html>
