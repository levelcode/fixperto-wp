<?php
define( 'WP_DEBUG', true );
require_once (get_template_directory() . '/admin_db/index.php');

function plugDB($consulta, $modo) {
	$newdb = new wpdb("root", "wololo", "fixperto", "localhost");
	$newdb -> show_errors();	
	if ($modo == "var") {
		$resultado = $newdb -> get_var($consulta);
	} else if ($modo == "row") {
		$resultado = $newdb -> get_row($consulta);
	} else if ($modo == "result") {
		$resultado = $newdb -> get_results($consulta);
	} else if ($modo == "q") {
		$resultado = $newdb -> query($consulta);
	}
	return $resultado;
}

?>