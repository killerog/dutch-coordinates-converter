<?php
	set_time_limit ( 3600 );
	$data = "";
	if (!empty($_POST["snelwegnr"]) && !empty($_POST["hectometer"])) {
		$hectometer = 0;
		if (is_numeric($_POST["hectometer"])) {
			$hectometer = $_POST["hectometer"] * 10;
		} else {
			$hectometer = str_replace(",", ".", $_POST["hectometer"]);
			if (is_numeric($hectometer)) {
				$hectometer = $hectometer * 10;
			} else {
				die ("Vul a.u.b. een nummer in bij de hectometerpaal");
			}
		}
		$data[0] = snelwegToRijkscoord($_POST["snelwegnr"], $hectometer);
	} elseif (!empty($_POST["snelweghecto"]) || ($_FILES['snelweghectoupload']['size'] > 0 && $_FILES['snelweghectoupload']['error'] == UPLOAD_ERR_OK)) {
		$separator = $rows = $tmp = "";
		if ($_FILES['snelweghectoupload']['name']) {
			$handle = @fopen($_FILES['snelweghectoupload']['tmp_name'], "r");
			if ($handle) {
				while (($buffer = fgets($handle, 4096)) !== false) {
					$tmp .= $buffer;
				}
				if (!feof($handle)) {
					echo "Sorry, geen data beschikbaar";
				}
				fclose($handle);
			}
			$rows = preg_split("/((\r\n)|(\n\r)|(\n)|(\r))/", $tmp);
			$separator = $_POST["shupload"];
		} elseif (!empty($_POST["snelweghecto"])) {
			$rows = preg_split("/((\r\n)|(\n\r)|(\n)|(\r))/", $_POST["snelweghecto"]);
			$separator = $_POST["shseparator"];
		}
		switch ($separator) {
			case "TAB":
				$separator = "\t";
				break;
			case "SPACE":
				$separator = " ";
				break;
			case ".":
			case ",":
			case ";":
				$separator = $separator;
				break;
			default:
				die("Ongeldige separator gebruikt");
		}
		foreach ($rows as $key => $row) {
			if (!empty($row)) {
				$tmp = explode($separator, $row);
				$snelweg = $hectometer = 0;
				if (strtoupper(substr($tmp[0], 0, 1) == "A")) {
					//Item 0 is the highway, so the other one is the hectometerpaal
					$snelweg = $tmp[0];
					$hectometer = $tmp[1];
				} elseif (strtoupper(substr($tmp[1], 0, 1) == "A")) {
					//Item 1 is the highway, so the other one is the hectometerpaal
					$snelweg = $tmp[1];
					$hectometer = $tmp[0];
				} elseif (strpos($tmp[0], ",") != false || strpos($tmp[0], ".") != false) {
					//If the item contains a dot or a comma, it should be the hectometerpaal
					$snelweg = "A".$tmp[1];
					$hectometer = $tmp[0];
				} elseif (strpos($tmp[1], ",") != false || strpos($tmp[1], ".") != false) {
					//If the item contains a dot or a comma, it should be the hectometerpaal
					$snelweg = "A".$tmp[0];
					$hectometer = $tmp[1];
				} else {
					//Else just take a guess and say that the first one is the highway
					$snelweg = "A".$tmp[0];
					$hectometer = $tmp[1];
				}
				if (is_numeric($hectometer)) {
					$hectometer = $hectometer * 10;
				} else {
					$hectometer = str_replace(",", ".", $hectometer);
					if (is_numeric($hectometer)) {
						$hectometer = $hectometer * 10;
					} else {
						die ("Vul a.u.b. een nummer in bij de hectometerpaal".$hectometer.";".$snelweg);
					}
				}
				$data[$key] = snelwegToRijkscoord($snelweg, $hectometer);
			}
		}
	} elseif (!empty($_POST["snelwegnrgeo"]) && !empty($_POST["hectometergeo"])) {
		$hectometer = 0;
		if (is_numeric($_POST["hectometergeo"])) {
			$hectometer = $_POST["hectometergeo"] * 10;
		} else {
			$hectometer = str_replace(",", ".", $_POST["hectometergeo"]);
			if (is_numeric($hectometer)) {
				$hectometer = $hectometer * 10;
			} else {
				die ("Vul a.u.b. een nummer in bij de hectometerpaal");
			}
		}
		$data[0] = snelwegToRijkscoord($_POST["snelwegnrgeo"], $hectometer);
		$data[0] = rijkscoordToGeo($data[0][0], $data[0][1]);
	} elseif (!empty($_POST["snelweghectogeo"]) || ($_FILES['snelweghectogeoupload']['size'] > 0 && $_FILES['snelweghectogeoupload']['error'] == UPLOAD_ERR_OK)) {
		$separator = $rows = $tmp = "";
		if ($_FILES['snelweghectogeoupload']['name']) {
			$handle = @fopen($_FILES['snelweghectogeoupload']['tmp_name'], "r");
			if ($handle) {
				while (($buffer = fgets($handle, 4096)) !== false) {
					$tmp .= $buffer;
				}
				if (!feof($handle)) {
					echo "Sorry, geen data beschikbaar";
				}
				fclose($handle);
			}
			$rows = preg_split("/((\r\n)|(\n\r)|(\n)|(\r))/", $tmp);
			$separator = $_POST["shgupload"];
		} elseif (!empty($_POST["snelweghectogeo"])) {
			$rows = preg_split("/((\r\n)|(\n\r)|(\n)|(\r))/", $_POST["snelweghectogeo"]);
			$separator = $_POST["shgseparator"];
		}
		switch ($separator) {
			case "TAB":
				$separator = "\t";
				break;
			case "SPACE":
				$separator = " ";
				break;
			case ".":
			case ",":
			case ";":
				$separator = $separator;
				break;
			default:
				die("Ongeldige separator gebruikt".$separator);
		}
		foreach ($rows as $key => $row) {
			if (!empty($row)) {
				$tmp = explode($separator, $row);
				$snelweg = $hectometer = 0;
				if (strtoupper(substr($tmp[0], 0, 1) == "A") || strtoupper(substr($tmp[0], 0, 1) == "N")) {
					//Item 0 is the highway, so the other one is the hectometerpaal
					$snelweg = $tmp[0];
					$hectometer = $tmp[1];
				} elseif (strtoupper(substr($tmp[1], 0, 1) == "A") || strtoupper(substr($tmp[1], 0, 1) == "N")) {
					//Item 1 is the highway, so the other one is the hectometerpaal
					$snelweg = $tmp[1];
					$hectometer = $tmp[0];
				} elseif (strpos($tmp[0], ",") != false || strpos($tmp[0], ".") != false) {
					//If the item contains a dot or a comma, it should be the hectometerpaal
					$snelweg = "A".$tmp[1];
					$hectometer = $tmp[0];
				} elseif (strpos($tmp[1], ",") != false || strpos($tmp[1], ".") != false) {
					//If the item contains a dot or a comma, it should be the hectometerpaal
					$snelweg = "A".$tmp[0];
					$hectometer = $tmp[1];
				} else {
					//Else just take a guess and say that the first one is the highway
					$snelweg = "A".$tmp[0];
					$hectometer = $tmp[1];
				}
				if (is_numeric($hectometer)) {
					$hectometer = $hectometer * 10;
				} else {
					$hectometer = str_replace(",", ".", $hectometer);
					if (is_numeric($hectometer)) {
						$hectometer = $hectometer * 10;
					} else {
						$data[$key][0] = 0;
						$data[$key][1] = 0;
						continue;
						//die ("Vul a.u.b. een nummer in bij de hectometerpaal".$hectometer.";".$snelweg);
					}
				}
				$dta[0] = snelwegToRijkscoord($snelweg, $hectometer);
				if (empty($dta[0][0]) || empty($dta[0][1])) {
						$data[$key][0] = 0;
						$data[$key][1] = 0;
						continue;
				}
				$data[$key] = rijkscoordToGeo($dta[0][0], $dta[0][1]);
			}
		}
	}elseif (!empty($_POST["rijksx"]) && !empty($_POST["rijksy"])) {
		$rijksy = $rijksx = 0;
		if (!is_numeric($_POST["rijksy"])) {
			$rijksy = str_replace(",", ".", $_POST["rijksy"]);
			if (!is_numeric($rijksy)) {
				die ("Vul a.u.b. een nummer in bij het y-coördinaat");
			}
		} else {
			$rijksy = $_POST["rijksy"];
		}
		if (!is_numeric($_POST["rijksx"])) {
			$rijksx = str_replace(",", ".", $_POST["rijksx"]);
			if (!is_numeric($rijksx)) {
				die ("Vul a.u.b. een nummer in bij het x-coördinaat");
			}
		} else {
			$rijksx = $_POST["rijksx"];
		}
		$data[0] = rijkscoordToGeo($rijksx, $rijksy);
	} elseif (!empty($_POST["rijksxygeo"]) || ($_FILES['rijksxyupload']['size'] > 0 && $_FILES['rijksxyupload']['error'] == UPLOAD_ERR_OK)) {
		$separator = $rows = $tmp = "";
		if ($_FILES['rijksxyupload']['name']) {
			$handle = @fopen($_FILES['rijksxyupload']['tmp_name'], "r");
			if ($handle) {
				while (($buffer = fgets($handle, 4096)) !== false) {
					$tmp .= $buffer;
				}
				if (!feof($handle)) {
					echo "Sorry, geen data beschikbaar";
				}
				fclose($handle);
			}
			$rows = preg_split("/((\r\n)|(\n\r)|(\n)|(\r))/", $tmp);
			$separator = $_POST["rijksxyuploadseparator"];
		} elseif (!empty($_POST["rijksxygeo"])) {
			$rows = preg_split("/((\r\n)|(\n\r)|(\n)|(\r))/", $_POST["rijksxygeo"]);
			$separator = $_POST["rijksxyseparator"];
		}
		switch ($separator) {
			case "TAB":
				$separator = "\t";
				break;
			case "SPACE":
				$separator = " ";
				break;
			case ".":
			case ",":
			case ";":
				$separator = $separator;
				break;
			default:
				die("Ongeldige separator gebruikt");
		}
		foreach ($rows as $key => $row) {
			if (!empty($row)) {
				$tmp = explode($separator, $row);
				$data[$key] = rijkscoordToGeo($tmp[0], $tmp[1]);
			}
		}
	}
	function snelwegToRijkscoord($weg, $hecto) {
		$rijkswaterstaat = "";
		$handle = @fopen('http://www.rijkswaterstaat.nl/apps/geoservices/rwsnl/searchdata.php?hectosnelweg='.$weg.'&hectonummer='.$hecto, "r");
		if ($handle) {
			while (($buffer = fgets($handle, 4096)) !== false) {
				$rijkswaterstaat .= $buffer;
			}
			if (!feof($handle)) {
				echo "Sorry, geen data beschikbaar";
			}
			fclose($handle);
		}
		$tmp = json_decode($rijkswaterstaat);
		$data = explode(" ",substr($tmp->result[0]->LOC, 7, -1 ));
		return $data;
	}
	function rijkscoordToGeo($x, $y) {
		if((13677<$x)&&($x<277977)&&(306628<$y)&&($y<619290)) {
			//155000 by 463000 are the central coords of Amersfoort, the Church tower of Onze-Lieve-Vrouwetoren (Lange Jan)
			$rijksX = ($x-155000)/100000;
			$rijksY = ($y-463000)/100000;
			//border coordinates?
			$k01=3235.65389;
			$k20=-32.58297;
			$k02=-0.24750;
			$k21=-0.84978;
			$k03=-0.06550;
			$k22=-0.01709;
			$k10=-0.00738;
			$k40=0.00530;
			$k23=-0.00039;
			$k41=0.00033;
			$k11=-0.00012;
			$l10=5260.52916;
			$l11=105.94684;
			$l12=2.45656;
			$l30=-0.81885;
			$l13=0.05594;
			$l31=-0.05607;
			$l01=0.01199;
			$l32=-0.00256;
			$l14=0.00128;
			$l02=0.00022;
			$l20=-0.00022;
			$l50=0.00026;
			$coord[0]=52.15517440+((1/3600)*(($k01*$rijksY)+($k20*pow($rijksX,2))+($k02*pow($rijksY,2))+($k21*pow($rijksX,2)*$rijksY)+($k03*pow($rijksY,3))+($k22*pow($rijksX,2)*pow($rijksY,2))+($k10*$rijksX)+($k40*pow($rijksX,4))+($k23*pow($rijksX,2)*pow($rijksY,3))+($k41*pow($rijksX,4)*$rijksY)+($k11*$rijksX*$rijksY)));
			$coord[1]=5.38720621+((1/3600)*(($l10*$rijksX)+($l11*$rijksX*$rijksY)+($l12*$rijksX*pow($rijksY,2))+($l30*pow($rijksX,3))+($l13*$rijksX*pow($rijksY,3))+($l31*pow($rijksX,3)*$rijksY)+($l01*$rijksY)+($l32*pow($rijksX,3)*pow($rijksY,2))+($l14*$rijksX*pow($rijksY,4))+($l02*pow($rijksY,2))+($l20*pow($rijksX,2))+($l50*pow($rijksX,5))));
			return $coord;
		} else {
			echo "Rijksdriehoekscoördinaten zijn alleen beschikbaar in NL (zegt men). $x, $y";
		}
		return false;
	}
	if ($_POST["savewrite"] == "save") {
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="coordinates-'.date('d-m-Y-H-i-s').'.csv"');
		foreach ($data as $record) {
			echo($record[0] . $separator . $record[1] . "\n"); 
		}
		die();
	}
?>
<!doctype html>
<html>
	<head>
		<title>Coördinaten converter - Snelwegnummers en Rijksdriehoekscoördinaten/Amersfoortcoördinaten naar geocoördinaten</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link href="style.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<div id="header">
			<h1>Deze tool converteert Snelwegnummers en Rijksdriehoekscoördinaten/Amersfoortcoördinaten naar geocoördinaten</h1>
			<p>Voor meer gedetailleerdere informatie zie elk los onderdeel van het formulier. Veel plezier!</p>
		</div>
		<div id="content">
			<div class="error">
			
			</div>
			<div class="results">
				<?php
					if (!empty($data)) {
				?>
				<h2>Resultaten:</h2>
					<?php foreach ($data as $record) { ?>
					<p><?php echo($record[0]); ?>, <?php echo($record[1]); ?></p>
					<?php
						}
					}
				?>
			</div>
			<form method="post" enctype="multipart/form-data" action="index.php" name="snelwegconv">
				<fieldset>
					<legend>Snelwegnummers naar Geocoördinaten</legend>
					<label for="snelwegnrgeo">Snelwegnummer (bv. A1):<input type="text" id="snelwegnrgeo" name="snelwegnrgeo" placeholder="A1" value="<?php echo($_POST["snelwegnrgeo"]); ?>" /></label>
					<label for="hectometergeo">hectometerpaal (bv. 14,7):<input type="text" id="hectometergeo" name="hectometergeo" placeholder="14,7" value="<?php echo($_POST["hectometergeo"]); ?>" /></label>
					<p>Plak de snelwegnummers en de hectometerpaal in onderstaand veld. Geef in het tweede veld aan hoe het snelwegnummer en de hectometerpaal gescheiden zijn. Elke set moet op een nieuwe regel staan.</p>
					<textarea cols="30" rows="2" name="snelweghectogeo" id="snelweghectogeo"><?php echo($_POST["snelweghectogeo"]); ?></textarea>
					<select id="shgseparator" name="shgseparator">
						<option value="."<?php echo($_POST["shgseparator"] == "." ? ' selected="selected"' : ''); ?>>Punt</option>
						<option value=","<?php echo($_POST["shgseparator"] == "," ? ' selected="selected"' : ''); ?>>Komma</option>
						<option value=";"<?php echo($_POST["shgseparator"] == ";" ? ' selected="selected"' : ''); ?>>Puntkomma</option>
						<option value="SPACE"<?php echo($_POST["shgseparator"] == "SPACE" ? ' selected="selected"' : ''); ?>>Spatie</option>
						<option value="TAB"<?php echo($_POST["shgseparator"] == "TAB" ? ' selected="selected"' : ''); ?>>Tab</option>
					</select><br />
					<p>Upload hier een csv bestand of een tekstbestand. Selecteer hiernaast het scheidingsteken en zorg dat elke set op een nieuwe regel begint.</p>
					<input name="snelweghectogeoupload" type="file" />
					<select id="shgupload" name="shgupload">
						<option value="."<?php echo($_POST["shgupload"] == "." ? ' selected="selected"' : ''); ?>>Punt</option>
						<option value=","<?php echo($_POST["shgupload"] == "," ? ' selected="selected"' : ''); ?>>Komma</option>
						<option value=";"<?php echo($_POST["shgupload"] == ";" ? ' selected="selected"' : ''); ?>>Puntkomma</option>
						<option value="SPACE"<?php echo($_POST["shgupload"] == "SPACE" ? ' selected="selected"' : ''); ?>>Spatie</option>
						<option value="TAB"<?php echo($_POST["shgupload"] == "TAB" ? ' selected="selected"' : ''); ?>>Tab</option>
					</select><br />
				</fieldset>
				<fieldset>
					<legend>Snelwegnummers naar Rijksdriehoekscoördinaten</legend>
					<label for="snelwegnr">Snelwegnummer (bv. A1):<input type="text" id="snelwegnr" name="snelwegnr" placeholder="A1" value="<?php echo($_POST["snelwegnr"]); ?>" /></label>
					<label for="hectometer">Hectometerpaal (bv. 14,7):<input type="text" id="hectometer" name="hectometer" placeholder="14,7" value="<?php echo($_POST["hectometer"]); ?>" /></label>
					<p>Plak de snelwegnummers en de hectometerpaal in onderstaand veld. Geef in het tweede veld aan hoe het snelwegnummer en de hectometerpaal gescheiden zijn. Elke set moet op een nieuwe regel staan.</p>
					<textarea cols="30" rows="2" name="snelweghecto" id="snelweghecto"><?php echo($_POST["snelweghecto"]); ?></textarea>
					<select id="shseparator" name="shseparator">
						<option value="."<?php echo($_POST["shseparator"] == "." ? ' selected="selected"' : ''); ?>>Punt</option>
						<option value=","<?php echo($_POST["shseparator"] == "," ? ' selected="selected"' : ''); ?>>Komma</option>
						<option value=";"<?php echo($_POST["shseparator"] == ";" ? ' selected="selected"' : ''); ?>>Puntkomma</option>
						<option value="SPACE"<?php echo($_POST["shseparator"] == "SPACE" ? ' selected="selected"' : ''); ?>>Spatie</option>
						<option value="TAB"<?php echo($_POST["shseparator"] == "TAB" ? ' selected="selected"' : ''); ?>>Tab</option>
					</select><br />
					<p>Upload hier een csv bestand of een tekstbestand. Selecteer hiernaast het scheidingsteken en zorg dat elke set op een nieuwe regel begint.</p>
					<input name="snelweghectoupload" type="file" />
					<select id="shupload" name="shupload">
						<option value="."<?php echo($_POST["shupload"] == "." ? ' selected="selected"' : ''); ?>>Punt</option>
						<option value=","<?php echo($_POST["shupload"] == "," ? ' selected="selected"' : ''); ?>>Komma</option>
						<option value=";"<?php echo($_POST["shupload"] == ";" ? ' selected="selected"' : ''); ?>>Puntkomma</option>
						<option value="SPACE"<?php echo($_POST["shupload"] == "SPACE" ? ' selected="selected"' : ''); ?>>Spatie</option>
						<option value="TAB"<?php echo($_POST["shupload"] == "TAB" ? ' selected="selected"' : ''); ?>>Tab</option>
					</select><br />
				</fieldset>
				<fieldset>
					<legend>Rijksdriehoekscoördinaten naar Geocoördinaten</legend>
					<label for="rijksx">x-coördinaat (bv. 19851):<input type="text" id="rijksx" name="rijksx" placeholder="19851" value="<?php echo($_POST["rijksx"]); ?>" /></label>
					<label for="rijksy">y-coördinaat (bv. 395168):<input type="text" id="rijksy" name="rijksy" placeholder="395168" value="<?php echo($_POST["rijksy"]); ?>" /></label>
					<p>Plak de Rijksdriehoekscoördinaten in onderstaand veld. Geef in het tweede veld aan hoe de Rijksdriehoekscoördinaten gescheiden zijn. Elke set moet op een nieuwe regel staan.</p>
					<textarea cols="30" rows="2" name="rijksxygeo" id="rijksxygeo"><?php echo($_POST["rijksxygeo"]); ?></textarea>
					<select id="rijksxyseparator" name="rijksxyseparator">
						<option value="."<?php echo($_POST["rijksxyseparator"] == "." ? ' selected="selected"' : ''); ?>>Punt</option>
						<option value=","<?php echo($_POST["rijksxyseparator"] == "," ? ' selected="selected"' : ''); ?>>Komma</option>
						<option value=";"<?php echo($_POST["rijksxyseparator"] == ";" ? ' selected="selected"' : ''); ?>>Puntkomma</option>
						<option value="SPACE"<?php echo($_POST["rijksxyseparator"] == "SPACE" ? ' selected="selected"' : ''); ?>>Spatie</option>
						<option value="TAB"<?php echo($_POST["rijksxyseparator"] == "TAB" ? ' selected="selected"' : ''); ?>>Tab</option>
					</select><br />
					<p>Upload hier een csv bestand of een tekstbestand. Selecteer hiernaast het scheidingsteken en zorg dat elke set op een nieuwe regel begint.</p>
					<input name="rijksxyupload" type="file" />
					<select id="rijksxyuploadseparator" name="rijksxyuploadseparator">
						<option value="."<?php echo($_POST["rijksxyuploadseparator"] == "." ? ' selected="selected"' : ''); ?>>Punt</option>
						<option value=","<?php echo($_POST["rijksxyuploadseparator"] == "," ? ' selected="selected"' : ''); ?>>Komma</option>
						<option value=";"<?php echo($_POST["rijksxyuploadseparator"] == ";" ? ' selected="selected"' : ''); ?>>Puntkomma</option>
						<option value="SPACE"<?php echo($_POST["rijksxyuploadseparator"] == "SPACE" ? ' selected="selected"' : ''); ?>>Spatie</option>
						<option value="TAB"<?php echo($_POST["rijksxyuploadseparator"] == "TAB" ? ' selected="selected"' : ''); ?>>Tab</option>
					</select><br />
				</fieldset>
				<fieldset>
					<legend>Opties</legend>
					<select id="savewrite" name="savewrite">
						<option value="write">Toon resultaten op het scherm</option>
						<option value="save">Sla op als csv bestand</option>
					</select><br />
					<input type="submit" />
				</fieldset>
			</form>
		</div>
		<div id="footer">
			<p>Met dank aan <a href="http://www.rijkswaterstaat.nl/apps/geoservices/rwsnl/?baselayer=KAART&projecttype=wegbeheerders&center=155000%2C463000&mapscale=3072000&height=530&width=670&cookieload=true">Rijkswaterstaat</a> en <a href="http://www.nev.nl/hymenoptera/maps.html">de NEV</a> voor het voorbeeld. Vinegar font by <a href="http://jelloween.deviantart.com/art/Font-VINEGAR-free-83101514">Tjarda Koster / Jelloween Font Foundry</a>.</p>
		</div>
	</body>
</html>