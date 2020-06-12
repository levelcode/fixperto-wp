<?php /* Template Name: template_querys */ ?>
<?php
if( isset($_POST["select_category"]) ){
    echo func_select_sub_categorias($_POST["categoria"], "");
}
if( isset($_POST["select_zona"]) ){
    echo func_select_sub_zonas($_POST["categoria"], "");
}


function ajax_select_sub_categorias($categoria){
	$ret = "";
	$Qstr = "SELECT * FROM categories WHERE service ='".$categoria."' ORDER BY denomination ASC";
	$Query = plugDB($Qstr, "result");
	foreach ( $Query as $lista ){
		$ret .= "<option value='".$lista->id."'>".$lista->denomination."</option>";
	}
	return $ret;
}

?>