<?php
require_once __DIR__.'/../configuracion.php';

$mysqli = new mysqli(DB_FIX_HOST, DB_FIX_USER, DB_FIX_PASS, DB_FIX_DB);
$mysqli->set_charset("utf8");
//$select = base64_decode($_POST["xhr"]);
$select = "
SELECT 
IF(e.type = 1,'Empresa','Independiente') as tipo_registro,
u.name as nombre,
u.email as correo,
u.birth_date as fecha_nacimiento,
e.phone as telefono,
e.date_arl as fecha_arl,
e.date_salud_pension as fecha_salud_pension,
e.fitcoints as fixcoins,
e.profile_description as descripcion,
g.denomination as genero,
e.id as plan_activo,
edu_lev.denomination as nivel_educativo,
e.title as profesion,
e.id as categorias,
e.id as _id_expert,
u.id as _id_user,
e.id as region,
e.id as proyectos,
IF(e.certification_sena=1,'Si','No') as Sena,
CONCAT(u.code, u.id) AS coupon,
CONCAT(u.code, u.id) AS coupon_redimidos,
e.avatar as foto_personal,
e.fotocopy as foto_documento
FROM 
experts e INNER JOIN users u ON e.user = u.id
LEFT JOIN gender g ON u.gender = g.id
LEFT JOIN educational_level edu_lev ON edu_lev.id = e.educational_level
";
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
		if ( (!isset($value)) ) {
			$value = "\t";
		} else {
			if ($i === 10) {//plan_activo
				$value = plan_activo($value);
			}else if($i === 13){//categorias
				$value = categorias($value);
			}else if($i === 16){//region
				$value = regiones($value);
			}else if($i === 17){//trabajos.numero_referidos
				$value = trabajos($value);
			}else if($i === 20){//numero_referidos
				$value = numero_referidos($value);
			}else if($i === 21){//avatar
				$value = imagen_foto($value);
			}else if($i === 22){//documento
				$value = imagen_foto($value);
			}else{
				$value = str_replace('"', '""', $value);
			}
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
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-disposition: filename=" . $filename . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
print "$header\n$data";
//
function mysqli_field_name($result, $field_offset){
    $properties = @mysqli_fetch_field_direct($result, $field_offset);
    return is_object($properties) ? $properties->name : null;
}
//
function plan_activo($id_expert){
	$ret = "";
	
	$mysqli = new mysqli('localhost', 'root', 'wololo', 'fixperto');
	$mysqli->set_charset("utf8");
	
	$select = "SELECT b.denomination as titulo FROM expert_plan a LEFT JOIN plans b ON a.plan = b.id WHERE a.expert = '".$id_expert."'";
	$query = $mysqli->query($select) or die("Error SQL : " . $mysqli->error);
	while ( $row = $query->fetch_assoc() ) {
		$ret .= $row["titulo"];
	}
	return $ret;
}
function categorias($id_expert){
	$ret = "";
	
	$mysqli = new mysqli('localhost', 'root', 'wololo', 'fixperto');
	$mysqli->set_charset("utf8");
	
	$sel_expert_cats = "SELECT * FROM experts_categories a WHERE a.expert = '".$id_expert."'";
	$query_expert_cats = $mysqli->query($sel_expert_cats) or die("Error SQL : " . $mysqli->error);

	while ( $row1 = $query_expert_cats->fetch_assoc() ) {
		//$ret .= $row1["category"].",";
		$sel_b = "SELECT a.denomination as sub_cat, b.denomination as cat FROM categories a LEFT JOIN services b ON a.service=b.id WHERE a.id = '".$row1["category"]."'";
		$query_b = $mysqli->query($sel_b) or die("Error SQL : " . $mysqli->error);
		while ( $row2 = $query_b->fetch_assoc() ) {
			if($ret!="")$ret.=", ";
			$ret .= $row2["cat"]." : ".$row2["sub_cat"];
		}
	}
	return $ret;
}
function regiones($id_expert){
	$ret = "";
	
	$mysqli = new mysqli('localhost', 'root', 'wololo', 'fixperto');
	$mysqli->set_charset("utf8");
	
	$sel_expert_cats = "SELECT * FROM experts_regions a WHERE a.expert = '".$id_expert."'";
	$query_expert_cats = $mysqli->query($sel_expert_cats) or die("Error SQL : " . $mysqli->error);

	while ( $row1 = $query_expert_cats->fetch_assoc() ) {
		//$ret .= $row1["category"].",";
		$sel_b = "SELECT a.name as sub_cat, b.denomination as cat FROM regions a LEFT JOIN cities b ON a.city=b.id WHERE a.id = '".$row1["region"]."'";
		$query_b = $mysqli->query($sel_b) or die("Error SQL : " . $mysqli->error);
		while ( $row2 = $query_b->fetch_assoc() ) {
			if($ret!="")$ret.=", ";
			$ret .= $row2["cat"]." : ".$row2["sub_cat"];
		}
	}
	return $ret;
}
function trabajos($id_expert){
	$ret = "";
	
	$mysqli = new mysqli('localhost', 'root', 'wololo', 'fixperto');
	$mysqli->set_charset("utf8");
	
	$select = "SELECT description FROM expert_jobs WHERE expert = '".$id_expert."'";
	$query = $mysqli->query($select) or die("Error SQL : " . $mysqli->error);
	while ( $row = $query->fetch_assoc() ) {
		if($ret != "")$ret.=", ";
		$ret .= "".$row["description"];
	}
	return $ret;
}
function numero_referidos($coupon){
	$ret = "";
	
	$mysqli = new mysqli('localhost', 'root', 'wololo', 'fixperto');
	$mysqli->set_charset("utf8");
	
	$select = "SELECT COUNT(*) as numero FROM experts WHERE coupon = '".$coupon."'";
	$query = $mysqli->query($select) or die("Error SQL : " . $mysqli->error);
	while ( $row = $query->fetch_assoc() ) {
		if($ret != "")$ret.=", ";
		$ret .= $row["numero"];
	}
	return $ret;
}
function imagen_foto($campo){
	if($campo=="")return "NO";
	if($campo=="avatar.png")return "NO";
	return "SI";
}
?>