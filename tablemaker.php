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
$sql = "create table demineur (
id int(6) not null,
{$colonnes}
constraint pk_demineur primary key (id)
);";
if ($mysqli->query($sql) === TRUE) {
  echo "Table created successfully";
} else {
  echo "Error creating table: " . $mysqli->error;
}

$mysqli->close();
?> 