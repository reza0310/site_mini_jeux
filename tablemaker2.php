 <?php
set_include_path($_SERVER['DOCUMENT_ROOT']."projet_site");
include 'mdp.php';

$mysqli=mysqli_connect($servername,$username,$password,$dbname);
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}
$colonnes = "";
for ($x = 0; $x < 16; $x++) {
	for ($y = 0; $y < 16; $y++) {
		$colonnes .= "`{$x} {$y}` char(2), ";
	}
}
substr_replace($colonnes ,"", -2);
$sql = "CREATE TABLE `clients` (
  `mail` varchar(100) NOT NULL,
  `mdp` varchar(100) NOT NULL,
  constraint pk_clients primary key (mail)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
if ($mysqli->query($sql) === TRUE) {
  echo "Table created successfully";
} else {
  echo "Error creating table: " . $mysqli->error;
}

$mysqli->close();
?> 