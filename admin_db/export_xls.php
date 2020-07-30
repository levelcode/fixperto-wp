<?php
require_once __DIR__.'/../configuracion.php';
$mysqli = new mysqli(DB_FIX_HOST, DB_FIX_USER, DB_FIX_PASS, DB_FIX_DB);
$mysqli->set_charset("utf8");
$select = base64_decode($_POST["xhr"]);
//
//mysql_query("SET SQL_BIG_SELECTS=1");
//
$export = $mysqli->query($select) or die("Error SQL : " . $mysqli->error);
$fields = mysqli_num_rows($export);
//////////
$file = 'reporte_' . $_POST["type"];
$header = "";
$data = "";
//
for ($i = 0; $i < $fields; $i++) {
	$header .= mysqli_field_name($export, $i) . "\t";
}
while ($row = mysqli_fetch_row($export)) {
	$line = '';
	foreach ($row as $i=>$value) {
		if ((!isset($value)) || ($value == "")) {
			$value = "\t";
		} else {
			/*if ($i === 9   ) {//columna "lugares_visitados"
				$value=lugares_visitados($value);
			}*/

			$value = str_replace('"', '""', $value);
			$value = '"' . $value . '"' . "\t";
		}
		$line .= $value;
	}
	$data .= trim($line) . "\n";
}
$data = str_replace("\r", "", $data);
if ($data == "") {
	$data = "\n(0) Registros Encontrados!\n";
}
//////////
$filename = $file . "_" . date("m-d-Y");
//
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8");
header("Content-disposition: filename=" . $filename . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
print "$header\n$data";

function mysqli_field_name($result, $field_offset)
{
    $properties = @mysqli_fetch_field_direct($result, $field_offset);
    return is_object($properties) ? $properties->name : null;
}

function lugares_visitados($data){
	if($data=="")return "";
	$cats = explode(",", $data);
	$ret = "";

	sort($cats);
	
	foreach($cats as $cat) {
		$interior = explode("-", $cat)[0];
		
		if($ret!="")$ret.=", ";
		switch ($interior) {
			case 0:
				$ret.="Admisiones y Programación Académica";
				break;
			case 1:
				$ret.="Biblioteca";
				break;
			case 2:
				$ret.="Bienestar Institucional";
				break;
			case 3:
				$ret.="Cooperación y Relaciones Internacionales";
				break;
			case 4:
				$ret.="Zona Deportiva";
				break;
			case 5:
				$ret.="Aula Pedagógica";
				break;
			case 6:
				$ret.="Laboratorios";
				break;
			case 7:
				$ret.="Extensión Académica";
				break;
			case 8:
				$ret.="Parque i";
				break;
			case 9:
				$ret.="Museo de Ciencias Naturales de la Salle y Observatorio";
				break;
			case 10:
				$ret.="Centro de Idiomas";
				break;
											
		}
	}
	return $ret;
	
}
?>