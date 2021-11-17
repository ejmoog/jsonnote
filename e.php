<?php 
require_once('./db.php');
$conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql_execution = "SELECT json FROM ejson_content WHERE id = 2";
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
		<title>Ejsoon Private JSONEditor</title>
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
		.hide {display: none;}
		.ib {display: inline-block;}
		.h100 {height: 100%;}
		</style>
	</head>
	<body>
		<div class="loadandsave">
			<input type="password" class="loginpw">
			<button class="las_btn logbtn" type="button">LOGIN</button>
			<button class="las_btn savebtn" type="button">SAVE</button>
			<div class="cryptdiv hide ib h100">
				<input id="cryptinput" type="password">
				<button class="las_btn cryptbtn" type="button">CRYPT</button>
			</div>
		</div>
		<div id="jsoneditor"></div>
<textarea class="hide" id="jsondata" name="" cols="30" rows="10"><?php print_r($result_array[0]['json']);?></textarea>
<script src="crypt/aesjs.js"></script>
<script src="crypt/pbkdf2.js"></script>
<script src="crypt/sha1.js"></script>
<script>
// login or not
var logbtn = document.querySelector(".logbtn");
var pw = document.querySelector(".loginpw");
if ("YES" == "<?php echo $_SESSION['login']?>") {
	toggle_button_display("login");
} else {
	toggle_button_display("logout");
}

// toggle button display
function toggle_button_display(value) {
	if ("login" == value) {
		pw.classList.add("hide");
		logbtn.innerHTML = "LOGOUT";
		document.querySelector(".cryptdiv").classList.remove("hide");
		document.querySelector(".cryptdiv").classList.add("ib");
	} else {
		pw.classList.remove("hide");
		logbtn.innerHTML = "LOGIN";
		document.querySelector(".cryptdiv").classList.add("hide");
		document.querySelector(".cryptdiv").classList.remove("ib");
	}
}

const container = document.getElementById('jsoneditor')
	const options = {
	mode: 'tree',
		modes: ['tree', 'text', 'view'], // allowed modes
		onEvent: function(node, event) {
			if (node.value === undefined && event.type === "focus") {
				var crtjson = editor.get();
				for (var key in node.path) {
					crtjson = crtjson[node.path[key]];
				}
				editor.options.templates = [
				{
					text: 'Copy',
						title: 'Insert a Copy Node',
						field: node.field,
						value: crtjson
				}
			];	
			}
		}
}
// const json = JSON.parse(document.querySelector("#jsondata").value);
const json = {};
const editor = new JSONEditor(container, options, json);
	// log out
	logbtn.onclick = function () {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				alert(xmlhttp.responseText);
				if ("Login" == xmlhttp.responseText) {
					toggle_button_display("login");
				} else {
					toggle_button_display("logout");
				}
			}
		}
		xmlhttp.open("POST","login.php",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");              //or multipart/form-data
		xmlhttp.send("log_action=" + logbtn.innerHTML + "&pw=" + pw.value);
	}
// save json data
document.querySelector(".savebtn").onclick = function () {
	// encrypt json value
	document.querySelector("#jsondata").value = editor.getText();
	acrypto("encrypto", function() {
		encryptJson = document.querySelector("#jsondata").value;
		// xmlhttp post
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				alert(xmlhttp.responseText);
			}
		}
		xmlhttp.open("POST","save.php",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");              //or multipart/form-data
		xmlhttp.send("id=2" + "&json=" + encryptJson);
	});
}
// crypt and update jsonEditor
document.querySelector(".cryptbtn").onclick = function () {
	originData = document.querySelector("#jsondata").value;
	acrypto("decrypto", function () {
		if (testJSON(document.querySelector("#jsondata").value)) {
			editor.updateText(document.querySelector("#jsondata").value);
		} else {
			alert("Password is wrong!");
			document.querySelector("#jsondata").value = originData;
		}
		// test json is valid
		function testJSON(text) {
			if (typeof text !== "string") {
				return false;
			}
			try {
				JSON.parse(text);
				return true;
			} catch (error) {
				return false;
			}
		}
	});
}
</script>
	<script>
		// encrypto or decrypto
		function acrypto(thus, restfun = undefined) {
			var ni = function(akey) {
				// case encrypto or decrypto
				var texthd = document.getElementById('jsondata');
				if ('encrypto' == thus) {
					texthd.value = ctrengo(akey, texthd.value);
				} else if ('decrypto' == thus) {
					texthd.value = ctrdego(akey, texthd.value);
				}
				if (typeof restfun == "function") {
					restfun();
				}
			}
			var apw = document.getElementById('cryptinput');
			pbkdf2go(apw.value, ni);
		}

// pbkdf2 create key by password
function pbkdf2go(psw, then_cb) {
	var mypbkdf2 = new PBKDF2(psw, "ejsoonsalt", 173, 16);
	var status_callback = function(percent_done) {};
	var result_callback = function(key) {
		//conver to array[int]
		var akey = [];
		for (var ki = 0; ki < 32; ki += 2) {
			akey.push(parseInt(key.substr(ki, 2), 16));
		}
		then_cb(akey);
	};
	mypbkdf2.deriveKey(status_callback, result_callback);
}
// encrypto
function ctrengo(key, text) {
	// Convert text to bytes
	var textBytes = aesjs.utils.utf8.toBytes(text);
	var aesCtr = new aesjs.ModeOfOperation.ctr(key, new aesjs.Counter(3));
	var encryptedBytes = aesCtr.encrypt(textBytes);
	var encryptedHex = aesjs.utils.hex.fromBytes(encryptedBytes);
	return encryptedHex;
}

// decrypto
function ctrdego(key, encryptedHex) {
	// When ready to decrypt the hex string, convert it back to bytes
	var encryptedBytes = aesjs.utils.hex.toBytes(encryptedHex);
	var aesCtr = new aesjs.ModeOfOperation.ctr(key, new aesjs.Counter(3));
	var decryptedBytes = aesCtr.decrypt(encryptedBytes);
	var decryptedText = aesjs.utils.utf8.fromBytes(decryptedBytes);
	return decryptedText;
}

	</script>
	</body>
</html>
