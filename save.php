<?php 
	require_once('./db.php');
if (empty($_SESSION['login'])) {
	echo "Login is needed!";
} else {
	$conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql_execution = " UPDATE ejson_content SET json='".$_POST["json"]."' WHERE id = 1";
	$stmt = $conn->prepare($sql_execution);
	$stmt->execute();
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$conn = null;
	echo "Saved";
}
?>
