<?php
set_include_path($_SERVER['DOCUMENT_ROOT']."/jeux");
include 'mdp.php';
$mysqli=mysqli_connect($servername,$username,$password,$dbname);
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}
session_start();
$output = "";
//$test = isset($_SESSION['partie']);
//output .= "<script>console.log('Status: {$test}')</script>";
if ($_POST["reinit"] == 1) {
	unset($_SESSION["partie"]);
}
if (isset($_POST["outil"])) {
	$outil = $_POST["outil"];
} else {
	$outil = 0;
}
if (isset($_SESSION["partie"]) != 1) {
	// Initialiser la partie
	$cases = [['00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00'],
			  ['00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00'],
			  ['00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00'],
			  ['00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00'],
			  ['00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00'],
			  ['00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00'],
			  ['00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00'],
			  ['00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00'],
			  ['00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00'],
			  ['00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00'],
			  ['00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00'],
			  ['00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00'],
			  ['00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00'],
			  ['00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00'],
			  ['00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00'],
			  ['00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00', '00']];
	
	// 1) Obtenir un code de partie
	// a) Obtention
	$trouve = false;
	while (!$trouve) {
		$code_potentiel = rand(100000, 999999);
		$query = "SELECT * FROM demineur WHERE id='$code_potentiel'";
		$result = $mysqli->query($query);
		$row = $result->fetch_array(MYSQLI_NUM);
		if ($row == null) {
			$trouve = true;
		}
	}
	// b) Mise en place de la session
	$_SESSION["partie"]=$code_potentiel;
	
	// 2) Placer les mines et calculer les numéros
	// a) Placer les mines
	$mines_restantes = 40;
	while ($mines_restantes > 0) {
		$x_potentiel = rand(0, 15);
		$y_potentiel = rand(0, 15);
		if ($cases[$x_potentiel][$y_potentiel] == '00') {
			$cases[$x_potentiel][$y_potentiel] = '09';
			$mines_restantes -= 1;
		}
	}
	// b) Calculer les numéros
	for ($x = 0; $x < 16; $x++) {
		for ($y = 0; $y < 16; $y++) {
			if ($cases[$x][$y] == '00') {
				$cpt = 0;
				if ($cases[min($x+1, 15)][$y] == '09') {
					$cpt += 1;
				}
				if ($cases[$x][max($y-1, 0)] == '09') {
					$cpt += 1;
				}
				if ($cases[$x][min($y+1, 15)] == '09') {
					$cpt += 1;
				}
				if ($cases[max($x-1, 0)][$y] == '09') {
					$cpt += 1;
				}
				if ($cases[max($x-1, 0)][max($y-1, 0)] == '09') {
					$cpt += 1;
				}
				if ($cases[min($x+1, 15)][min($y+1, 15)] == '09') {
					$cpt += 1;
				}
				if ($cases[min($x+1, 15)][max($y-1, 0)] == '09') {
					$cpt += 1;
				}
				if ($cases[max($x-1, 0)][min($y+1, 15)] == '09') {
					$cpt += 1;
				}
				$cases[$x][$y] = '0'.strval($cpt);
			}
		}
	}
	// c) Enregistrer le tout
	$colonnes = "";
	$valeurs = "";
	for ($x = 0; $x < 16; $x++) {
		for ($y = 0; $y < 16; $y++) {
			$colonnes .= "`{$x} {$y}`, ";
			$valeurs .= "'".$cases[$x][$y]."', ";
		}
	}
	$colonnes = substr($colonnes, 0, -2);
	$valeurs = substr($valeurs, 0, -2);
	$query = "INSERT INTO demineur (id, {$colonnes}) VALUES ({$code_potentiel}, {$valeurs})";
	if ($mysqli->query($query) !== TRUE) {
		$output .= "<script>console.log(\"Erreur SQL: {$mysqli->error}\")</script>";
	}
	
	// 3) Afficher un truc tout neuf
	$output .= "<table>";
	$i = 0;
	while ($i < 16) {
		$output .= "<tr>";
		$k = 0;
		while ($k < 16) {
			$output .= "<td class='cache' id='{$i}_{$k}'>0<form action='/jeux/demineur/index.php' method='post'><input type='hidden' name='outil' value='{$outil}'><input type='hidden' name='reinit' value='0'><input type='hidden' name='case' value='`{$i} {$k}`'></form></td>";
			$k += 1;
		}
		$output .= "</tr>";
		$i += 1;
	}
	$output .= "</table><script src='script.js'></script>";

} else {
	
	/* Partie en cours */
	// Gérer le click
	if (!isset($_POST["changement"])) {
		$click = $_POST["case"];
		$query = "SELECT {$click} FROM demineur WHERE id={$_SESSION["partie"]}";
		$output .= "<script>console.log('Status: {$click}')</script>";
		$result = $mysqli->query($query);
		$resultat = mysqli_fetch_array($result);
		$dessous = $resultat[0][1];
		$output .= "<script>console.log('Status: {$dessous}')</script>";
		if ($outil == 1) {
			$query = "UPDATE demineur SET {$click} = '2{$dessous}' WHERE id={$_SESSION["partie"]}";
		} else {
			$query = "UPDATE demineur SET {$click} = '1{$dessous}' WHERE id={$_SESSION["partie"]}";
		}
		if ($mysqli->query($query) !== TRUE) {
			echo "Request error: " . $mysqli->error;
		}
	}
	
	// Gérer l'affichage
	$query = "SELECT * FROM demineur WHERE id={$_SESSION["partie"]}";
	$result = $mysqli->query($query);
	$resultat = mysqli_fetch_assoc($result);
	$output .= "<table>";
	$i = 0;
	$perdu = False;
	while ($i < 16) {
		$output .= "<tr>";
		$k = 0;
		while ($k < 16) {
			$etat = $resultat["$i $k"];
			if ($etat[0] == '0') {
				$output .= "<td class='cache' id='{$i}_{$k}'>0<form action='/jeux/demineur/index.php' method='post'><input type='hidden' name='outil' value='{$outil}'><input type='hidden' name='reinit' value='0'><input type='hidden' name='case' value='`{$i} {$k}`'></form></td>";
			} elseif ($etat[0] == '2') {
				$output .= "<td class='drapeau' id='{$i}_{$k}'>0<form action='/jeux/demineur/index.php' method='post'><input type='hidden' name='outil' value='{$outil}'><input type='hidden' name='reinit' value='0'><input type='hidden' name='case' value='`{$i} {$k}`'></form></td>";
			} else {
				if ($etat[1] == '0') {
					$output .= "<td class='revele' id='{$i}_{$k}'></td>";
				} elseif ($etat[1] == '1') {
					$output .= "<td class='revele' id='{$i}_{$k}'>1</td>";
				} elseif ($etat[1] == '2') {
					$output .= "<td class='revele' id='{$i}_{$k}'>2</td>";
				} elseif ($etat[1] == '3') {
					$output .= "<td class='revele' id='{$i}_{$k}'>3</td>";
				} elseif ($etat[1] == '4') {
					$output .= "<td class='revele' id='{$i}_{$k}'>4</td>";
				} elseif ($etat[1] == '5') {
					$output .= "<td class='revele' id='{$i}_{$k}'>5</td>";
				} elseif ($etat[1] == '6') {
					$output .= "<td class='revele' id='{$i}_{$k}'>6</td>";
				} elseif ($etat[1] == '7') {
					$output .= "<td class='revele' id='{$i}_{$k}'>7</td>";
				} elseif ($etat[1] == '8') {
					$output .= "<td class='revele' id='{$i}_{$k}'>8</td>";
				} elseif ($etat[1] == '9') {
					$output .= "<td class='revele' id='{$i}_{$k}'>M</td>";
					$perdu = True;
				}
			}
			$k += 1;
		}
		$output .= "</tr>";
		$i += 1;
	}
	if ($perdu) {
		$output .= "</table><script>window.alert('Vous avez perdu!!!');</script>";
	}
	$output .= "</table><script src='script.js'></script>";
}
if ($outil == 1) {
	$output .= "<form action='/jeux/demineur/index.php' method='post'><input type='hidden' name='changement' value='1'><input type='hidden' name='reinit' value='0'><input type='hidden' name='outil' value='0'><input type='submit' id='b1' class='buton' value='Outil: Drapeau'></form>";
} else {
	$output .= "<form action='/jeux/demineur/index.php' method='post'><input type='hidden' name='changement' value='1'><input type='hidden' name='reinit' value='0'><input type='hidden' name='outil' value='1'><input type='submit' id='b1' class='buton' value='Outil: Pelle'></form>";
}
$output .= "<form action='/jeux/demineur/index.php' method='post'><input type='hidden' name='reinit' value='1'><input type='submit' id='b2' class='buton' value='Redémarrer'></form>";
$mysqli->close();
echo(str_replace("uno", "active", str_replace("%php%", $output, file_get_contents("header.html", true))));
?>