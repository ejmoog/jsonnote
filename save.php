<?php 
require_once('./db.php');
if (empty($_SESSION['login'])) {
	echo "Login is needed!";
} else {
	try {
		$conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("UPDATE ejson_content SET json=:postjson WHERE id = :id");
		$stmt->bindParam(':postjson', json_decode(json_encode($_POST["json"])));
		$stmt->bindParam(':id', $_POST["id"]);
		// $stmt->bindParam(':postjson', $_POST["json"]);
		$stmt->execute();
		// $stmt->setFetchMode(PDO::FETCH_ASSOC);
		echo "Saved";
	}
	catch(PDOException $e)
	{
		echo "Error: " . $e->getMessage();
	}

	$conn = null;
}
?>
