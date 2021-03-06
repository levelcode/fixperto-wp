<?php
require_once __DIR__.'/configuracion.php';
define( 'WP_DEBUG', true );
require_once (get_template_directory() . '/admin_db/index.php');

function plugDB($consulta, $modo) {
	$newdb = new wpdb(DB_FIX_USER, DB_FIX_PASS, DB_FIX_DB, DB_FIX_HOST);
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