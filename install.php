<?php
	require_once "./db.php";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		echo 'start sql';
    // sql to create table
		$sql = " CREATE TABLE `ejson_content` ( `id` int(11) NOT NULL COMMENT 'id', `name` varchar(77) NOT NULL COMMENT 'the name of note', `json` text NOT NULL COMMENT 'json value') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; INSERT INTO `ejson_content` (`id`, `name`, `json`) VALUES (1, '', '{}'); ALTER TABLE `ejson_content` ADD PRIMARY KEY (`id`); ALTER TABLE `ejson_content` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id', AUTO_INCREMENT=2; COMMIT; ";

    $conn->exec($sql);
    echo "Mysql data install successfully!";
    echo "<br>";
    echo "<br>";
    echo "Rename install.php or delete it.";
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;
?>
