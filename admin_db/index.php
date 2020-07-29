<?php
//
require_once __DIR__.'/../configuracion.php';
//
add_action('admin_menu' , 'function_enable_pages');
function function_enable_pages(){
	//add_menu_page('Fixperto', 'Fixperto', 'read', 'admin_db', 'page_admin', '', 1);
	//add_submenu_page( 'admin_db', 'Clientes', 'Clientes', 'administrator', 'clientes', 'page_clientes');//	
	//add_submenu_page( 'admin_db', 'Reporte_Clientes', 'Reporte_Clientes', 'administrator', 'reporte_clientes', 'page_reporte_clientes');//	
	//add_submenu_page( 'admin_db', 'Profesionales', 'Profesionales', 'administrator', 'profesionales', 'page_profesionales');//	
	//add_submenu_page( 'admin_db', 'Servicios', 'Servicios', 'administrator', 'servicios', 'page_servicios');//	
	//add_submenu_page( 'admin_db', 'Transacciones_Planes', 'Transacciones_Planes', 'administrator', 'transacciones_planes', 'page_transacciones_planes');//	
	//add_submenu_page( 'admin_db', 'Transacciones_Fixcoins', 'Transacciones_Fixcoins', 'administrator', 'transacciones_fixcoins', 'page_transacciones_fixcoins');//	
	//add_submenu_page( 'admin_db', 'Atencion_Cliente', 'Atencion_Cliente', 'administrator', 'atencion_cliente', 'page_atencion_cliente');//	
	//add_submenu_page( 'admin_db', 'Cupones_Referidos', 'Cupones_Referidos', 'administrator', 'cupones_referidos', 'page_referidos');//	
	//add_submenu_page( 'admin_db', 'Categorias', 'Categorias', 'administrator', 'lista_servicios', 'page_lista_servicios');//	
	//add_submenu_page( 'admin_db', 'Contenidos', 'Contenidos', 'administrator', 'contenidos', 'page_contenidos');//	
	//add_submenu_page( 'admin_db', 'Graficos', 'Graficos', 'administrator', 'graficos', 'page_graficos');//	
	//remove_submenu_page( 'admin_db', 'admin_db' );

	add_menu_page('Transacciones', 'Transacciones', 'read', 'transaccion_db', 'page_admin', '', 1);
	add_submenu_page( 'transaccion_db', 'Transacciones_Planes', 'Transacciones_Planes', 'administrator', 'transacciones_planes', 'page_transacciones_planes');//	
	add_submenu_page( 'transaccion_db', 'Transacciones_Fixcoins', 'Transacciones_Fixcoins', 'administrator', 'transacciones_fixcoins', 'page_transacciones_fixcoins');//
	add_submenu_page( 'transaccion_db', 'Cupones_Referidos', 'Cupones_Referidos', 'administrator', 'cupones_referidos', 'page_referidos');//	
	remove_submenu_page( 'transaccion_db', 'transaccion_db' );

	add_menu_page('General', 'General', 'read', 'general_db', 'page_admin', '', 0);
	add_submenu_page( 'general_db', 'Reporte_Grafico', 'Reporte_Grafico', 'administrator', 'graficos', 'page_graficos');//
	add_submenu_page( 'general_db', 'Clientes', 'Clientes', 'administrator', 'clientes', 'page_clientes');
	add_submenu_page( 'general_db', 'Reporte_Clientes', 'Reporte_Clientes', 'administrator', 'reporte_clientes', 'page_reporte_clientes');//	
	add_submenu_page( 'general_db', 'Profesionales', 'Profesionales', 'administrator', 'profesionales', 'page_profesionales');//	
	add_submenu_page( 'general_db', 'Servicios', 'Servicios', 'administrator', 'servicios', 'page_servicios');//
	remove_submenu_page( 'general_db', 'general_db' );

	add_menu_page('Atencion_Cliente', 'Atencion_Cliente', 'read', 'atencion_cliente', 'page_atencion_cliente', '', 1);

	add_menu_page('Configuracion', 'Configuracion', 'read', 'configuracion_db', 'page_admin', '', 0);
	add_submenu_page( 'configuracion_db', 'Contenidos', 'Contenidos', 'administrator', 'contenidos', 'page_contenidos');//
	add_submenu_page( 'configuracion_db', 'Categorias', 'Categorias', 'administrator', 'lista_servicios', 'page_lista_servicios');//	
	remove_submenu_page( 'configuracion_db', 'configuracion_db' );
	
}

function page_admin(){
	?>
	<div class="wrap">
		<h2>Fixperto</h2>
	</div>
	<?php
}
//
function page_graficos(){
	?>
	<div class="wrap">
		<iframe width="800" height="1350" src="https://datastudio.google.com/embed/reporting/f1fe94c8-6a6c-4e6b-8aec-af1627906138/page/YuNWB" frameborder="0" style="border:0" allowfullscreen></iframe>
	</div>
	<?php
}
//
function page_clientes(){
	//print_r($_POST);
	if( isset($_POST["editar_cliente"]) ){
		page_clientes_editar_cliente();
		return;
	}
	//
	$WHERE = "";
	if(isset($_POST["genero"]) &&  $_POST["genero"]!="-1"){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "g.id = '".$_POST["genero"]."'";
	}
	if(isset($_POST["edad"]) &&  $_POST["edad"]!="-1"){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "TIMESTAMPDIFF(YEAR,u.birth_date,CURDATE()) = '".$_POST["edad"]."'";
	}
	if(isset($_POST["buscador"])){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "u.name LIKE '%".$_POST["buscador"]."%' OR u.email LIKE '%".$_POST["buscador"]."%'";
	}
	//
	$Qstr = "
	SELECT u.id as id_user, u.name, u.email, u.date_registry, u.birth_date, g.denomination as Genero, TIMESTAMPDIFF(YEAR,u.birth_date,CURDATE()) AS Edad, serv.cant as Cant_Servicios
	FROM 
	users u INNER JOIN customers c ON u.id = c.user
	LEFT JOIN gender g ON u.gender = g.id
	LEFT JOIN (SELECT COUNT(*) as cant, s.customer FROM requests s GROUP BY s.customer)serv ON serv.customer = c.id
	$WHERE
	ORDER BY id_user DESC
	";
	$Query = plugDB($Qstr, "result");
	$Colum_No_Mostrar = array("birth_date");
	?>
	<script type="text/javascript" >
		jQuery(document).on("mobileinit", function(){
			jQuery.mobile.ajaxEnabled = false;
		});
	</script>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.css"/>
	<script src="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.jqueryui.min.css">
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.jqueryui.min.js"></script>
	<script type="text/javascript" class="init">
		jQuery(document).ready(function() {
			jQuery('#la_data_tabla').DataTable();
			jQuery('#wpfooter').remove();
		} );
	</script>
	<style>
		.controlgroup-textinput{
		    padding-top:.22em;
		    padding-bottom:.22em;
		}
		.ui-title{
			color:white;
		}
		div.DataTables_sort_wrapper span {
			right: -10px !important;
		}

		.fix_table .ui-btn-inner{
			padding: .55em 11px;
		}
		.ui-select .ui-icon{
			background-image: url(https://code.jquery.com/mobile/1.2.1/images/icons-18-white.png) !important;
			background-position: -217px 50% !important;
		}
		[id="formulario"] *{
			font:inherit !important;
			font-size:16px !important;
		}

		.btn_per{
			border: none;
			width: 80px;
			padding: 10px;
			box-shadow: 0 1px 4px rgba(0,0,0,.3);
			outline: none;
			margin-top: 10px;
			font-weight: bold;
			border-radius: 6px;
			font-size : 0.8rem;
		}
	</style>

	<div data-role="page">
		<div data-role="header" data-theme="b">
			<h1>Clientes (<?php echo Count($Query);?> Registros)</h1>
		</div>

		<div data-role="header" data-theme="c">
			<div data-role="controlgroup" data-type="horizontal">
				<form action='<?php bloginfo('template_url'); ?>/admin_db/export_xls.php' target='_blank' method='post'>
					<input type='hidden' name='type' value='usuarios'/>
					<input type='hidden' name='xhr' value='<?php echo base64_encode($Qstr); ?>'/>
					<input type="submit" id="exportar" name="exportar" value="Exportar a excel" class="ui-btn" data-theme="b" />
				</form>
			</div>
			<div data-role="controlgroup" data-type="horizontal" style="width:10px">
			</div>
			<div data-role="controlgroup" data-type="horizontal">
				<form method='post'>
					<select name="genero" onchange="this.form.submit()">
						<option value='-1'>Genero</option>
						<?php echo func_select_tabla_id_denomination("gender", $_POST["genero"]);?>
					</select>
					<select name="edad" onchange="this.form.submit()">
						<option value='-1' selected>Edad</option>
						<?php echo func_select_edad($_POST["edad"]);?>
					</select>

					<div data-role="controlgroup" data-type="horizontal">
						<form method="post" onSubmit="return Validar_Nombre(this);">
							<div class="ui-input-text ui-body-inherit controlgroup-textinput ui-btn ui-shadow-inset" style="height:100%">
								<input type="text" name="buscador" value="<?php echo $_POST['buscador'];?>" placeholder="Buscar" required class="ui-btn " style="height:100%; margin-top: -6px">
							</div>
							<input type="submit" data-role="none" name="crear_categoria" value="Buscar" class="ui-btn" data-theme="b" style="margin-top: -5px; margin-left: 15px; padding: 8px; background: #5a91c0; border: none; color: white;">
						</form>
					</div>


				</form>
			</div>
			<div data-role="controlgroup" data-type="horizontal">
				<form method='post'>
					<input type="submit" data-role="none" name="todos" value="Limpiar Filtros" style="mmargin-top: -5px; margin-left: 15px; padding: 8px; background: #F5F5F5; border: none; color: #23282d;">
				</form>
			</div>
		</div>

		<div role="content" class="ui-content">
	
			<!-- <div data-role="controlgroup" data-type="horizontal">
				<form method='post'>
					<select name="genero" onchange="this.form.submit()">
						<option value='-1'>Genero</option>
						<?php echo func_select_tabla_id_denomination("gender", $_POST["genero"]);?>
					</select>
					<select name="edad" onchange="this.form.submit()">
						<option value='-1' selected>Edad</option>
						<?php echo func_select_edad($_POST["edad"]);?>
					</select>
					<div class="ui-input-text ui-body-inherit ui-corner-all controlgroup-textinput ui-btn ui-shadow-inset">
						<input type="text" name="buscador" value="<?php echo $_POST['buscador'];?>" placeholder="Buscar" class="ui-btn ui-corner-all">
					</div>
					
					<input type="submit" value="Buscar">
				</form>
			</div>
			<div data-role="controlgroup" data-type="horizontal">
				<form method='post'>
					<input type="submit" name="todos" value="Limpiar Filtros" class="ui-btn">
				</form>
			</div> -->
		
			<?php if($Query[0] != null):?>
			<!-- <div data-role="controlgroup" data-type="horizontal">
				<form action='<?php bloginfo('template_url'); ?>/admin_db/export_xls.php' target='_blank' method='post'>
					<input type='hidden' name='type' value='usuarios'/>
					<input type='hidden' name='xhr' value='<?php echo base64_encode($Qstr); ?>'/>
					<input type="submit" id="exportar" name="exportar" value="Exportar a excel" class="ui-btn" data-theme="b" />
				</form>
			</div> -->

			<table id="la_data_tabla" class="fix_table" cellspacing="0">
				<thead>

					<tr valign="top">
						<?php  foreach($Query[0] as $key => $value):?>
							<?php if(!in_array($key, $Colum_No_Mostrar)): ?>
								<th><?php echo Traductor_Nombre_Columnas($key); ?></th>
							<?php endif;?>
						<?php endforeach; ?>
						<th>Editar</th>
					</tr>

				</thead>
				<tbody id="the-list">
					<?php 
					$alter = "";
					foreach ( $Query as $lista ):
						if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
					?>
					<tr valign="top" <?php echo $alter; ?>>
						<?php foreach($lista as $it => $va):?>
							<td><?php echo $va;?></td>
						<?php endforeach; ?>
						<td>
							<form method="post" data-role="controlgroup">
								<input type="hidden" name="id" value="<?php echo $lista->id_user;?>">
								<input type="hidden" name="editar_cliente" value="ok">
								<input type="submit" value="Editar" class="btn_per" data-theme="b" data-role="none" style="background: #3f72a5; color: white;">
							</form>
						</td>
					</tr>
				<?php endforeach;?>
			</table>
			<?php endif;?>
		</div>
	</div>
	
	<?php
}
function page_clientes_editar_cliente(){
	//print_r($_POST);
	if( isset($_POST["es_usuario"]) ){
		//echo "<br><br>".Gen_String_Update($_POST, array("editar_cliente", "es_usuario"));
		$qs_update = "UPDATE users SET ".Gen_String_Update($_POST, array("editar_cliente", "es_usuario", "phone"))." WHERE id='".$_POST["id"]."'";
		$q_update = plugDB($qs_update, "result");

		$qs_update_phone = "UPDATE customers SET phone='".$_POST["phone"]."' WHERE user='".$_POST["id"]."'";
		$q_update_phone = plugDB($qs_update_phone, "result");
	}
	if( isset($_POST["es_customer"]) ){
		//echo "<br><br>".Gen_String_Update($_POST, array("editar_cliente", "es_usuario"));
		$qs_update_phone = "UPDATE customers SET phone='".$_POST["phone"]."' WHERE user='".$_POST["id"]."'";
		//$qs_update = "UPDATE customers SET ".Gen_String_Update($_POST, array("editar_cliente", "es_customer"))." WHERE user='".$_POST["id"]."'";
		$q_update_phone = plugDB($qs_update_phone, "result");
		//echo "<h3>".$qs_update."</h3>";
	}
	if( isset($_POST["update_active"]) ){
		$qs_update = "UPDATE customers SET active='".$_POST["active"]."' WHERE user='".$_POST["id"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	//
	$Qstr = "SELECT * FROM users WHERE id = '". $_POST["id"] ."'";
	$Query = plugDB($Qstr, "row");
	//
	$Qstr_Customer = "SELECT * FROM customers WHERE user = '".$_POST["id"]."'";
	$Query_Customer = plugDB($Qstr_Customer, "row");
	//
	$Qsrt_Request = "SELECT r.*, cat.b_name as Categoria, cat.a_name as Sub_Categoria, reg.b_name as Ciudad, reg.a_name as Zona
	FROM requests r
	LEFT JOIN
	(
		SELECT a.id as id_a, a.denomination as a_name, b.denomination as b_name FROM categories a LEFT JOIN services b ON a.service = b.id
	)cat ON cat.id_a = r.category
	LEFT JOIN
	(
		SELECT a.id as id_a, a.name as a_name, b.denomination as b_name FROM regions a LEFT JOIN cities b ON a.city = b.id
	)reg ON reg.id_a = r.region
	WHERE r.customer = '".$Query_Customer->id."'";
	$Query_Request = plugDB($Qsrt_Request, "result");
	//
	$Qsrt_Calificaciones = "SELECT * FROM evaluations_customer WHERE customer='".$_POST["id"]."'";
	$Query_Calificaciones = plugDB($Qsrt_Calificaciones, "result");
	//
	$exclude_tabla_servicios = array("id", "customer", "category", "region", "emergency", "cancellation_type");
	$exclude_tabla_calificaciones = array();
	//
	?>
	<script type="text/javascript" >
		jQuery(document).on("mobileinit", function(){
			jQuery.mobile.ajaxEnabled = false;
		});
	</script>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.css"/>
	<script src="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.jqueryui.min.css">
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.jqueryui.min.js"></script>
	<script type="text/javascript" class="init">
		jQuery(document).ready(function() {
			<?php if( Count($Query_Request) > 0 ):?>
				jQuery('#tabla_servicios').DataTable();
			<?php endif;?>
			
			<?php if( Count($Query_Calificaciones) > 0 ):?>
				jQuery('#tabla_calificaciones').DataTable();
			<?php endif;?>

			jQuery('#wpfooter').remove();
			jQuery.mobile.page.prototype.options.keepNative = "select, input, textarea";
		} );
	</script>
	<style>
		.controlgroup-textinput{
		    padding-top:.22em;
		    padding-bottom:.22em;
		}
		.ui-title{
			color:white;
		}
		div.DataTables_sort_wrapper span {
			right: -10px !important;
		}

		.fix_table .ui-btn-inner{
			padding: .55em 11px;
		}
		.ui-select .ui-icon{
			background-image: url(https://code.jquery.com/mobile/1.2.1/images/icons-18-white.png) !important;
			background-position: -217px 50% !important;
		}
		.formulario{
			font:inherit !important;
			font-size:16px !important;
		}

		.ui-collapsible-inset .ui-collapsible-content{
			overflow-y: scroll !important;
		}

		
		.dataTables_wrapper{
			overflow-x : scroll;
		}

		@media(min-width : 80em){
			.li_form{
				display : flex !important;
			}

			.li_form .label_form{
				width : 20%
			}

			.li_form .inp_form{
				width : 80%
			}

			.btn_form{
				width : 15%;
				margin: auto
			}
		}
	</style>
	
	<div data-role="page">
		<div data-role="header" data-theme="b">
			<h1><?php echo $Query->name;?></h1>
		</div>
		<div data-role="header" data-theme="c">
			<div data-role="controlgroup" data-type="horizontal">
				<?php if($Query_Customer->active == 1):?>
					<form method="post">
						<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
						<input type="hidden" name="editar_cliente" value="ok">
						<input type="hidden" name="update_active" value="ok">
						<input type="hidden" name="active" value="0">
						<input type="submit" value="Desactivar" data-theme="b">
					</form>
				<?php else:?>
					<form method="post">
						<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
						<input type="hidden" name="editar_cliente" value="ok">
						<input type="hidden" name="update_active" value="ok">
						<input type="hidden" name="active" value="1">
						<input type="submit" value="Activar" data-theme="b">
					</form>
				<?php endif;?>
			</div>
			<div data-role="controlgroup" data-type="horizontal">
				<form method="post" onSubmit="return Do_Reestablecer_Clave(this);">
					<input type="hidden" name="correo" value="<?php echo $Query->email;?>">
					<input type="hidden" name="id_user" value="<?php echo $_POST["id"];?>">
					<input type="submit" value="Restablecer Contraseña" data-theme="b">
				</form>
			</div>	
		</div>

		<div role="content" class="ui-content">
			<a href="<?php echo URL_BASE;?>/uploads/registros/cliente/<?php echo $Query_Customer->avatar;?>" target="_blank">
				<img src="<?php echo URL_BASE;?>/uploads/registros/cliente/<?php echo $Query_Customer->avatar;?>" height="200">
			</a>

			<div data-role="collapsible" >
			    <h4>Información personal</h4>
			    <p>
					<form method="post" name="la_data" class="formulario">
						<ul data-role="listview" data-inset="true">
							<?php 
								echo func_tabla_form_fieldcontainer(
									$Query, 
									array("photo", "notification", "notification_chat", "token", "code", "password", "code_number", "validate_number"),
									array("id", "date_registry", "authentication_date")
								);
							?>
							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label for="phone">Telefono:</label>
								</div>
								<div class="inp_form">
									<input type="text" name="phone" value="<?php echo $Query_Customer->phone;?>" data-clear-btn="true">
								</div>
							</li>
							<li class="ui-body ui-body-b">
								<fieldset class="">
										<div class="btn_form">
											<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
											<input type="hidden" name="editar_cliente" value="ok">
											<input type="submit" name="es_usuario" value="Guardar" data-role="button" data-theme="b">
										</div>
								</fieldset>
							</li>
						</ul>
					</form>
				</p>
			</div>

			<div data-role="collapsible">
			    <h4>Servicios</h4>
			    <p>
					<?php if( Count($Query_Request) > 0 ):?>
					<table id="tabla_servicios" class="fix_table" cellspacing="0">
						<thead>
							<tr valign="top">
								<?php foreach($Query_Request[0] as $key => $value):?>
								<?php if(!in_array($key, $exclude_tabla_servicios)):?><th><?php echo Traductor_Nombre_Columnas($key); ?></th><?php endif;?>
								<?php endforeach; ?>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody id="the-list">
							<?php 
							$alter = "";
							foreach ( $Query_Request as $lista ):
								if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
							?>
							<tr valign="top" <?php echo $alter; ?>>
								<?php foreach($lista as $key => $va):?>
									<?php if(!in_array($key, $exclude_tabla_servicios)):?><td><?php echo $va;?></td><?php endif;?>
								<?php endforeach; ?>
								<td>
									<form method="post" action="admin.php?page=servicios" data-role="controlgroup">
										<input type="hidden" name="id" value="<?php echo $lista->id;?>">
										<input type="submit" name="editar_servicio" value="Editar" class="ui-btn" data-theme="b">
									</form>
								</td>
							</tr>
							<?php endforeach;?>
						</tbody>
					</table>
					<?php else:?>
						Servicios (0)
					<?php endif;?>
				</p>
			</div>

			<div data-role="collapsible">
			    <h4>Calificaciones</h4>
			    <p>
					<?php if( Count($Query_Calificaciones) > 0 ):?>
					<table id="tabla_calificaciones" class="wp-list-table widefat" cellspacing="0">
						<thead>
							<tr valign="top">
								<?php foreach($Query_Calificaciones[0] as $key => $value):?>
								<?php if(!in_array($key, $exclude_tabla_calificaciones)):?><th><?php echo $key; ?></th><?php endif;?>
								<?php endforeach; ?>
							</tr>
						</thead>
						<tbody id="the-list">
							<?php 
							$alter = "";
							foreach ( $Query_Calificaciones as $lista ):
								if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
							?>
							<tr valign="top" <?php echo $alter; ?>>
								<?php foreach($lista as $key => $va):?>
									<?php if(!in_array($key, $exclude_tabla_calificaciones)):?><td><?php echo $va;?></td><?php endif;?>
								<?php endforeach; ?>
							</tr>
							<?php endforeach;?>
						</tbody>
					</table>
					<?php else:?>
						Calificaciones (0)
					<?php endif;?>
				</p>
			</div>
		</div>
	</div>
	
	<script type="text/javascript">
	function Do_Reestablecer_Clave(form){
		if( confirm("Esta accion enviara un correo de confirmacion, ¿desea continuar?") ){
			jQuery.ajax({
				url:"<?php echo URL_BASE;?>/backOffice/changePassword", 
				method: "POST",
				data: { id : form.id_user.value, email : form.correo.value }
			}).done(function(html){
				//alert(html);
				//$("#select_zona").html(html);
				alert( "Correo enviado con exito!" );
			}).fail(function() {
				alert( "error enviando el correo" );
			});
		}
		return false;
	}
	</script>
	<?php
}
//
function page_profesionales(){
	if( isset($_POST["editar_cliente"]) ){
		page_profesionales_editar_experto();
		return;
	}
	//
	$WHERE = "";
	if(isset($_POST["tipo"]) &&  $_POST["tipo"]!="-1"){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "e.type = '".$_POST["tipo"]."'";
	}
	if(isset($_POST["ciudad"]) &&  $_POST["ciudad"]!="-1"){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "reg.city_ids LIKE '%".$_POST["ciudad"]."%'";
	}
	if(isset($_POST["zona"]) &&  $_POST["zona"]!="-1"){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "reg.reg_ids LIKE '%%".$_POST["zona"]."%'";
	}
	if(isset($_POST["categoria"]) &&  $_POST["categoria"]!="-1"){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "cat.lista_ser LIKE '%".$_POST["categoria"]."%'";
	}
	if(isset($_POST["sub_categoria"]) &&  $_POST["sub_categoria"]!="-1"){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "cat.lista_cat LIKE '%".$_POST["sub_categoria"]."%'";
	}
	if(isset($_POST["plan"]) &&  $_POST["plan"]!="-1"){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "plan.id_plan = '".$_POST["plan"]."'";
	}
	if(isset($_POST["estado_plan"]) &&  $_POST["estado_plan"]!="-1"){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "plan.status = '".$_POST["estado_plan"]."'";
	}
	if(isset($_POST["certification_sena"]) &&  $_POST["certification_sena"]!="-1"){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "e.certification_sena = '".$_POST["certification_sena"]."'";
	}
	if(isset($_POST["buscador"])){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "u.name LIKE '%".$_POST["buscador"]."%' OR u.email LIKE '%".$_POST["buscador"]."%'";
	}
	//u.birth_date, e.profile_description,
	$Qstr = "
	SELECT 
	u.id as id_user,
	e.id as id_expert,
	IF(e.type,'Empresa','Independiente') as tipo_registro,
	u.name, 
	u.email, 
	u.date_registry, 
	u.birth_date,
	if(u.gender = 1, 'Masculino', 'Femenino') as genero,
	e.phone, 
	e.title as profesion, 
	IF(e.certification_sena,'Si','No') as Sena,
	e.date_arl,
	e.date_salud_pension,
	e.profile_description,
	e.fitcoints as fixcoins,
	edu_lev.denomination as educational_level,
	reg.names_city as Ciudad, 
	reg.ret as Region, 
	cat.names_ser as Categorias, 
	cat.ret as Sub_Categorias,
	plan.nombre_plan as Plan,
	plan.status as Estado_Plan,
	offers.suma as Ofertas
	FROM
	users u INNER JOIN experts e ON u.id = e.user
	LEFT JOIN 
	(
		SELECT *, GROUP_CONCAT(DISTINCT b.name,' ') as ret, GROUP_CONCAT(DISTINCT a.region,' ') as reg_ids, GROUP_CONCAT(DISTINCT b.city,' ') as city_ids, GROUP_CONCAT(DISTINCT b.name_city,' ') as names_city
		FROM 
		experts_regions a 
		LEFT JOIN (SELECT a1.*, a2.denomination as name_city FROM regions a1 LEFT JOIN cities a2 ON a1.city = a2.id) b ON b.id = a.region 
		GROUP BY a.expert
	)reg ON reg.expert = e.id
	LEFT JOIN 
	(
		SELECT *, GROUP_CONCAT(b.denomination,' ') as ret, GROUP_CONCAT(a.category,' ') as lista_cat, GROUP_CONCAT(DISTINCT b.service,' ') as lista_ser, GROUP_CONCAT(DISTINCT b.name_serv,' ') as names_ser 
		FROM 
		experts_categories a 
		LEFT JOIN (SELECT r1.*, r2.denomination as name_serv FROM categories r1 LEFT JOIN services r2 ON r1.service=r2.id) b ON a.category = b.id 
		GROUP BY a.expert
	)cat ON cat.expert = e.id
	LEFT JOIN
	(
		SELECT a.expert, b.denomination as nombre_plan, a.status, b.id as id_plan
		FROM
		expert_plan a LEFT JOIN plans b ON a.plan=b.id 
	)plan ON plan.expert = e.id
	LEFT JOIN
	(
		SELECT COUNT(id) as suma, expert FROM offers GROUP BY expert
	)offers ON offers.expert = e.id
	LEFT JOIN educational_level edu_lev ON edu_lev.id = e.educational_level
	$WHERE
	ORDER BY id_expert DESC
	";
	$Query = plugDB($Qstr, "result");
	$Colum_No_Mostrar = array("id_user", "birth_date", "date_arl", "date_salud_pension", "profile_description", "genero", "phone", "profesion", "Sena", "education_level", "Region", "Sub_Categorias" );
	?>

	<script type="text/javascript">
		jQuery(document).on("mobileinit", function(){
			jQuery.mobile.ajaxEnabled = false;
		});
	</script>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.css"/>
	<script src="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.jqueryui.min.css">
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.jqueryui.min.js"></script>
	<script type="text/javascript" class="init">
		jQuery(document).ready(function() {
			jQuery('#la_data_tabla').DataTable();
			jQuery('#wpfooter').remove();
		} );
	</script>

	<style>
		.controlgroup-textinput{
		    padding-top:.22em;
		    padding-bottom:.22em;
		}
		.ui-title{
			color:white;
		}
		div.DataTables_sort_wrapper span {
			right: -10px !important;
		}

		.fix_table .ui-btn-inner{
			padding: .55em 11px;
		}
		.ui-select .ui-icon{
			background-image: url(https://code.jquery.com/mobile/1.2.1/images/icons-18-white.png) !important;
			background-position: -217px 50% !important;
		}
		.formulario{
			font:inherit !important;
			font-size:16px !important;
		}

		.btn_per{
    		border: none;
			width: 80px;
			padding: 10px;
			box-shadow: 0 1px 4px rgba(0,0,0,.3);
			outline: none;
			margin-top: 10px;
			font-weight: bold;
			border-radius: 6px;
			font-size : 0.8rem;
		}
	</style>

	<div data-role="page">
		<div data-role="header" data-theme="b">
			<h1>Profesionales (<?php echo Count($Query);?> Registros)</h1>
		</div>

		<div data-role="header" data-theme="c">
			<div data-role="controlgroup" data-type="horizontal">
				<form action='<?php bloginfo('template_url'); ?>/admin_db/export_xls_experts.php' target='_blank' method='post'>
					<input type='hidden' name='type' value='expertos'/>
					<input type='hidden' name='xhr' value='<?php echo base64_encode($Qstr); ?>'/>
					<input type="submit" id="exportar" name="exportar" value="Exportar a excel" class="ui-btn" data-theme="b" />
				</form>
			</div>
			<div data-role="controlgroup" data-type="horizontal" style="width:10px">
			</div>
			<div data-role="controlgroup" data-type="horizontal">
				<form method='post'>
					<select name="tipo" onchange="this.form.submit()">
						<option value="-1">Tipo</option>
						<?php echo func_select_estado_servicio(array("Independiente"=>"0", "Empresa"=>"1"), $_POST["tipo"]);?>
					</select>
					<select name="ciudad" onchange="this.form.submit()">
						<option value='-1'>Ciudad</option>
						<?php echo func_select_tabla_id_denomination("cities", $_POST["ciudad"]);?>
					</select>
					<select name="zona" onchange="this.form.submit()"><?php echo func_select_sub_zonas($_POST["ciudad"], $_POST["zona"]);?></select>
					<select name="categoria" onchange="this.form.submit()">
						<option value='-1'>Categoria</option>
						<?php echo func_select_tabla_id_denomination("services", $_POST["categoria"]);?>
					</select>
					<select name="sub_categoria" onchange="this.form.submit()"><?php echo func_select_sub_categorias($_POST["categoria"], $_POST["sub_categoria"]);?></select>
					<select name="plan" onchange="this.form.submit()">
						<option value='-1'>Plan</option>
						<?php echo func_select_tabla_id_denomination("plans", $_POST["plan"]);?>
					</select>
					<select name="estado_plan" onchange="this.form.submit()">
						<option value='-1'>Estado Plan</option>
						<?php echo func_select_estado_servicio(array("Activo"=>"active", "Cancelado"=>"cancelled"), $_POST["estado_plan"]);?>
					</select>
					<select name="certification_sena" onchange="this.form.submit()">
						<option value='-1'>Certification Sena</option>
						<?php echo func_select_estado_servicio(array("Si"=>"1", "No"=>"0"), $_POST["certification_sena"]);?>
					</select>

					<div class="ui-input-text ui-body-inherit controlgroup-textinput ui-btn ui-shadow-inset" style="height:100%">
						<input type="text" name="buscador" value="<?php echo $_POST['buscador'];?>" placeholder="Buscar" required class="ui-btn " style="height:100%; margin-top: 0px">
					</div>
					<input type="submit" data-role="none" value="Buscar" class="ui-btn" data-theme="b" style="margin-top: 2px; margin-left: 5px; padding: 8px; background: #5a91c0; border: none; color: white;">

				</form>
			</div>

			<div data-role="controlgroup" data-type="horizontal">
				<form method='post'>

					<input type="submit" data-role="none" name="todos" value="Limpiar Filtros" style="mmargin-top: -5px; margin-left: 15px; padding: 8px; background: #F5F5F5; border: none; color: #23282d;">

				</form>
			</div>
		</div>
		
		<!-- <div role="content" class="ui-content"> -->
			<?php if($Query[0] != null):?>
				<table class="wp-list-table widefat" id="la_data_tabla" cellspacing="0">
					<thead>
						<tr valign="top">
							<?php foreach($Query[0] as $key => $value):?>
								<?php if( !in_array($key, $Colum_No_Mostrar) ):?>
									<th><?php echo Traductor_Nombre_Columnas($key); ?></th>
								<?php endif;?>
							<?php endforeach; ?>
							<th>Editar</th>
						</tr>
					</thead>
					<tbody id="the-list">
						<?php 
						$alter = "";
						foreach ( $Query as $lista ):
							if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
						?>
						<tr valign="top" <?php echo $alter; ?>>
							<?php foreach($lista as $it => $va):?>
								<?php if( !in_array($it, $Colum_No_Mostrar) ):?><td><?php echo $va;?></td><?php endif;?>
							<?php endforeach; ?>
							<td>
								<form method="post" data-role="controlgroup">
									<input type="hidden" name="id" value="<?php echo $lista->id_user;?>">
									<input type="hidden" name="editar_cliente" value="ok">
									<input type="submit" value="Editar" class="btn_per" data-theme="b" data-role="none" style="background: #3f72a5; color: white;">
								</form>
							</td>
						</tr>
					<?php endforeach;?>
				</table>
			<?php endif;?>
		<!-- </div> -->
	</div>

	<?php
}
function page_profesionales_editar_experto(){
	//print_r($_POST);
	if( isset($_POST["es_usuario"]) ){
		//echo "<br><br>".Gen_String_Update($_POST, array("editar_cliente", "es_usuario"));
		$qs_update = "UPDATE users SET ".Gen_String_Update($_POST, array("editar_cliente", "es_usuario"))." WHERE id='".$_POST["id"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["es_experto"]) ){
		////echo "<br><br>".Gen_String_Update($_POST, array("editar_cliente", "es_experto"));
		$qs_update = "UPDATE experts SET ".Gen_String_Update($_POST, array("editar_cliente", "es_experto", "id"))." WHERE user='".$_POST["id"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["es_plan"]) ){
		////echo "<br><br>".Gen_String_Update($_POST, array("editar_cliente", "es_experto"));
		$qs_update = "UPDATE expert_plan SET ".Gen_String_Update($_POST, array("editar_cliente", "es_plan", "id", "id_plan"))." WHERE id='".$_POST["id_plan"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["add_categoria"]) ){
		$qs_update = "INSERT INTO experts_categories (expert, category) VALUES ('".$_POST["id_experto"]."', '".$_POST["category"]."')";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["del_categoria"]) ){
		$qs_update = "DELETE FROM experts_categories WHERE expert = '".$_POST["id_experto"]."' AND category = '".$_POST["category"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["add_region"]) ){
		$qs_update = "INSERT INTO experts_regions (expert, region) VALUES ('".$_POST["id_experto"]."', '".$_POST["region"]."')";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["del_region"]) ){
		$qs_update = "DELETE FROM experts_regions WHERE expert = '".$_POST["id_experto"]."' AND region = '".$_POST["category"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["update_active"]) ){
		$qs_update = "UPDATE experts SET active='".$_POST["active"]."' WHERE user='".$_POST["id"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["update_colaborador"]) ){
		$qs_update = "UPDATE collaborators SET ".Gen_String_Update($_POST, array("editar_cliente", "update_colaborador", "id", "id_col", "categorias"))." WHERE id='".$_POST["id_col"]."'";
		$q_update = plugDB($qs_update, "result");

		$q_delcats = plugDB("DELETE FROM collaborators_categories WHERE collaborator ='".$_POST["id_col"]."'", "result");
		foreach( $_POST['categorias'] as $cat_temp ){
			//echo $cat_temp."<br>";
			plugDB("INSERT INTO collaborators_categories (collaborator, category) VALUES ('".$_POST["id_col"]."', '".$cat_temp."')", "result");
		}
	}
	if( isset($_POST["add_colaborador"]) ){
		$qs_update = "INSERT INTO collaborators (name, email, identification_type, number, phone, expert) VALUES ('".$_POST["name"]."', '".$_POST["email"]."', '".$_POST["identification_type"]."', '".$_POST["number"]."', '".$_POST["phone"]."', '".$_POST["expert"]."')";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["borrar_colaborador"]) ){
		$qs_update = "DELETE FROM collaborators WHERE id = '".$_POST["id_col"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	//
	if( isset($_POST["add_certificado"]) ){
		$qs_update = "INSERT INTO expert_certifications (expert, certification_type) VALUES ('".$_POST["expert"]."', '".$_POST["certification_type"]."')";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["del_certificado"]) ){
		$qs_update = "DELETE FROM expert_certifications WHERE id = '".$_POST["id_certi"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["update_certificado"]) ){
		$qs_update = "UPDATE expert_certifications SET ".Gen_String_Update($_POST, array("editar_cliente", "update_certificado", "id", "id_certi"))." WHERE id='".$_POST["id_certi"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	//
	if( isset($_POST["add_proyecto"]) ){
		$qs_update = "INSERT INTO expert_jobs (expert, description) VALUES ('".$_POST["expert"]."', '".$_POST["description"]."')";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["del_proyecto"]) ){
		$qs_update = "DELETE FROM expert_jobs WHERE id = '".$_POST["id_proy"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["update_proyecto"]) ){
		$qs_update = "UPDATE expert_jobs SET ".Gen_String_Update($_POST, array("editar_cliente", "update_proyecto", "id", "id_proy"))." WHERE id='".$_POST["id_proy"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	//
	$Query = plugDB("SELECT * FROM users WHERE id = '". $_POST["id"] ."'", "row");
	$Query_expert = plugDB("SELECT * FROM experts WHERE user = '". $_POST["id"] ."'", "row");
	$Query_Expert_Plan = plugDB("SELECT * FROM expert_plan WHERE expert = '". $Query_expert->id ."'", "row");
	$Query_Colaboradores = plugDB("SELECT * FROM collaborators WHERE expert='". $Query_expert->id ."'", "result");
	//
	$Qstr_Ofers = "SELECT r.id as id_request, r.description, r.registry_date, r.status, r.address, o.observations
	FROM 
	offers o 
	LEFT JOIN requests r ON o.request = r.id
	WHERE o.expert = '".$Query_expert->id."'";
	$Query_Ofers = plugDB($Qstr_Ofers, "result");
	//
	$Qstr_Categorias = "SELECT * 
	FROM experts_categories ec 
	LEFT JOIN 
	(
		SELECT c.id as id_cat, s.denomination as Categoria, c.denomination as Sub_Categoria
		FROM categories c LEFT JOIN services s ON c.service = s.id
	)cat ON cat.id_cat = ec.category
	WHERE ec.expert = '".$Query_expert->id."'";
	$Query_Categorias = plugDB($Qstr_Categorias, "result");
	//
	$Qstr_Region = "SELECT * 
	FROM experts_regions ec 
	LEFT JOIN 
	(
		SELECT c.id as id_cat, s.denomination as Categoria, c.name as Sub_Categoria
		FROM regions c LEFT JOIN cities s ON c.city = s.id
	)cat ON cat.id_cat = ec.region
	WHERE ec.expert = '".$Query_expert->id."'";
	$Query_Region = plugDB($Qstr_Region, "result");
	//
	$Qsrt_Calificaciones = "SELECT * FROM evaluations WHERE expert='".$Query_expert->id."'";
	$Query_Calificaciones = plugDB($Qsrt_Calificaciones, "result");
	$exclude_tabla_calificaciones = array();
	//
	$Query_Proyectos = plugDB("SELECT * FROM expert_jobs WHERE expert='".$Query_expert->id."'", "result");
	$exclude_tabla_proyectos = array();
	//
	$Query_Certificados = plugDB("SELECT * FROM expert_certifications WHERE expert='".$Query_expert->id."'", "result");
	$Query_Referidos = plugDB("SELECT e.id as id, u.date_registry as fecha FROM experts e LEFT JOIN users u ON e.user=u.id WHERE coupon='".$Query->code.$Query->id."'", "result");
	//
	?>

	<script type="text/javascript" >
		jQuery(document).on("mobileinit", function(){
			jQuery.mobile.ajaxEnabled = false;
		});
	</script>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.css"/>
	<script src="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.jqueryui.min.css">
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.jqueryui.min.js"></script>
	<script type="text/javascript" class="init">
		jQuery(document).ready(function() {
			<?php if( Count($Query_Ofers) > 0 ):?>
				jQuery('#tabla_servicios').DataTable();
			<?php endif;?>
			
			<?php if( Count($Query_Calificaciones) > 0 ):?>
				jQuery('#tabla_calificaciones').DataTable();
			<?php endif;?>

			jQuery('#wpfooter').remove();
			//jQuery.mobile.page.prototype.options.keepNative = "select, input, textarea";
		} );
	</script>
	<style>
		.controlgroup-textinput{
		    padding-top:.22em;
		    padding-bottom:.22em;
		}
		.ui-title{
			color:white;
		}
		div.DataTables_sort_wrapper span {
			right: -10px !important;
		}

		.fix_table .ui-btn-inner{
			padding: .55em 11px;
		}
		.ui-select .ui-icon{
			background-image: url(https://code.jquery.com/mobile/1.2.1/images/icons-18-white.png) !important;
			background-position: -217px 50% !important;
		}
		.formulario{
			font:inherit !important;
			font-size:16px !important;
		}

		.btn_form{
			width : 15%;
			margin:auto;
		}

		.ui-collapsible-inset .ui-collapsible-content{
			overflow-y: scroll !important;
		}

		.btn_per{
			border: none;
			width: 150px;
			padding: 10px;
			box-shadow: 0 1px 4px rgba(0,0,0,.3);
			outline: none;
			margin-top: 10px;
			font-weight: bold;
			border-radius: 6px;
			font-size : 0.8rem;
		}

		@media(min-width : 80em){
			.li_form{
				display : flex !important;
				align-items : center;
			}

			.li_form .label_form{
				width : 20%
			}

			.li_form .label_form label{
				font-size : 1rem;
			}


			.li_form .inp_form{
				width : 80%
			}

			.li_form .img_form{
				width: 40%;
				position: relative;
			}

			.li_form .img_form img{
				width: 70%;
				object-fit: cover;
				margin: auto;
				position: relative;
			}

			.li_form .act_form{
				width: 20%;
			}

			.li_form .act_form .btn_form{
				width : 142px;
				margin:auto;
			}

			.table_form th{
				font-size: 1rem;
    			font-weight: bold;
			}

			.li_cat .ui-select{
				width : 90%;
			}

			.dataTables_wrapper{
				overflow-x : scroll;
			}

			.btn_per{
				border: none;
				width: 150px;
				padding: 10px;
				box-shadow: 0 1px 4px rgba(0,0,0,.3);
				outline: none;
				margin-top: 10px;
				font-weight: bold;
				border-radius: 6px;
				font-size : 0.8rem;
			}



		}
	</style>

	<link rel="stylesheet" href="https://harvesthq.github.io/chosen/chosen.css">

	<div data-role="page">
		<div data-role="header" data-theme="b">
			<h1><?php echo $Query->name;?></h1>
		</div>
		<div data-role="header" data-theme="c">
			<div data-role="controlgroup" data-type="horizontal">
				<?php if($Query_expert->active == 1):?>
					<form method="post" onSubmit="return Do_Activar_Experto(this);">
						<input type="hidden" name="nombre" value="<?php echo $Query->name;?>">
						<input type="hidden" name="correo" value="<?php echo $Query->email;?>">
						<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
						<input type="hidden" name="editar_cliente" value="ok">
						<input type="hidden" name="update_active" value="ok">
						<input type="hidden" name="active" value="0">
						<input type="submit" value="Desactivar" data-theme="b">
					</form>
				<?php else:?>
					<form method="post" onSubmit="return Do_Activar_Experto(this);">
						<input type="hidden" name="nombre" value="<?php echo $Query->name;?>">
						<input type="hidden" name="correo" value="<?php echo $Query->email;?>">
						<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
						<input type="hidden" name="editar_cliente" value="ok">
						<input type="hidden" name="update_active" value="ok">
						<input type="hidden" name="active" value="1">
						<input type="submit" value="Activar" data-theme="b">
					</form>
				<?php endif;?>
			</div>

			<div data-role="controlgroup" data-type="horizontal">
				<form method="post" onSubmit="return Do_Reestablecer_Clave(this);">
					<input type="hidden" name="correo" value="<?php echo $Query->email;?>">
					<input type="hidden" name="id_user" value="<?php echo $_POST["id"];?>">
					<input type="submit" value="Restablecer Contraseña" data-theme="b">
				</form>
			</div>
		</div>

		<div role="content" class="ui-content">
			<a href="<?php echo URL_BASE;?>/uploads/registros/profesional/<?php echo $Query_expert->avatar;?>" target="_blank">
				<img src="<?php echo URL_BASE;?>/uploads/registros/profesional/<?php echo $Query_expert->avatar;?>" height="200">
			</a>

			<!-- <div data-role="collapsible">
			    <h4>template</h4>
			    <p>
					<form method="post" name="la_data" class="formulario">
						<ul data-role="listview" data-inset="true">
							<li class="ui-field-contain">
								<label for="name2">Text Input:</label>
								<input type="text" name="name2" id="name2" value="" data-clear-btn="true">
							</li>
							
							<li class="ui-body ui-body-b">
								<fieldset class="ui-grid-a">
									<div class="ui-block-a"></div>
									<div class="ui-block-b">
										<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
										<input type="hidden" name="editar_cliente" value="ok">
										<input type="submit" name="es_usuario" value="Guardar" data-role="button" data-theme="b">
									</div>
								</fieldset>
							</li>
						</ul>
					</form>
				</p>
			</div> -->

			<div data-role="collapsible">
			    <h4>Fixcoins</h4>

				<p>
					<form method="post" name="la_data" class="formulario">
						<ul data-role="listview" data-inset="true">
							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label for="phone">Actual:</label>
								</div>
								<div class="inp_form">
									<?php echo $Query_expert->fitcoints;?>
								</div>
							</li>
							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label for="phone">Dar:</label>
								</div>
								<div class="inp_form">
									<input type="number" value="0" min="0" id="cant_dar_fixcoins">
								</div>
							</li>
							<li class="ui-body ui-body-b">
								<fieldset class="">
										<div class="btn_form">
											<input type="button" value="Enviar" class="button action" onClick="Do_Dar_Fixcoins();">
										</div>
								</fieldset>
							</li>
						</ul>
					</form>
				</p>
			</div>

			<div data-role="collapsible">
			    <h4>Información personal</h4>
			    <p>
					<form method="post" name="la_data" class="formulario">
						<ul data-role="listview" data-inset="true">
							<?php 
								echo func_tabla_form_fieldcontainer(
									$Query, 
									array("photo", "notification", "notification_chat", "token", "code", "password", "code_number", "validate_number"),
									array("id", "date_registry", "authentication_date")
								);
							?>

							<li class="ui-body ui-body-b">
								<fieldset class="">
									<div class="btn_form">
										<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
										<input type="hidden" name="editar_cliente" value="ok">
										<input type="submit" name="es_usuario" value="Guardar" class="button action">
									</div>
								</fieldset>
							</li>

						</ul>
					</form>
				</p>
			</div>

			<div data-role="collapsible">
			    <h4>Información profesional</h4>
			    <p>
					<form method="post" name="la_data" class="formulario">
						<ul data-role="listview" data-inset="true">
							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label>Tipo:</label>
								</div>

								<div class="inp_form">
									<?php echo ($Query_expert->type == 0)?"Independiente":"Empresa";?>
								</div>
							</li>

							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label>identification_type:</label>
								</div>
								
								<div class="inp_form">
									<select name="identification_type">
										<?php echo func_select_tabla_id_denomination("identification_type", $Query_expert->identification_type);?>
									</select>
								</div>
							</li>

							<?php
							echo func_tabla_form_fieldcontainer(
								$Query_expert, 
								array("id","user","type","profile_description","active","identification_type", "educational_level", "date_arl", "arl" , "date_salud_pension", "salud_pension", "avatar", "fotocopy", "fitcoints"),
								array("id", "user")
							);
							?>
							<li class="ui-field-contain li_form">

								<div class="label_form">
									<label>educational_level:</label>
								</div>

								<div class="inp_form">
									<select name="educational_level">
										<?php echo func_select_tabla_id_denomination("educational_level", $Query_expert->educational_level);?>
									</select>
								</div>
							</li>
							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label>Descripción:</label>
								</div>

								<div class="inp_form">
									<textarea name="profile_description"><?php echo $Query_expert->profile_description;?></textarea>
								</div>
								
							</li>

							<li class="ui-body ui-body-b">
								<fieldset class="">
									<div class="btn_form">
										<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
										<input type="hidden" name="editar_cliente" value="ok">
										<input type="submit" name="es_experto" value="Guardar" class="button action">
									</div>
								</fieldset>
							</li>
						</ul>
					</form>
				</p>
			</div>

			<?php if($Query_expert->type == 0):?>
			<div data-role="collapsible">
			    <h4>Seguridad social</h4>

				<p>

					<table class="wp-list-table widefat table_form" cellspacing="0">
						<thead>
							<tr valign="top">
								<th>Campo</th>
								<th>Valor</th>
								<th>Accion</th>
							</tr>
						</thead>
					</table>

					<form method="post" name="la_data">
						<ul data-role="listview" data-inset="true">

							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label>Fotocopia documento</label>
								</div>

								<div class="img_form">
									<a href="<?php echo URL_BASE;?>/uploads/registros/profesional/<?php echo $Query_expert->fotocopy;?>" target="_blank" id="link_fotocopy">
									<img src="<?php echo URL_BASE;?>/uploads/registros/profesional/<?php echo $Query_expert->fotocopy;?>" height="200" id="img_fotocopy">
									</a>
								</div>

								<div class="act_form">

									<fieldset class="">
										<div class="btn_form">
											<input type="file" class="form-control-file" id="imagen_fotocopy"><br>
											<input type="button" class="btn_per upload" value="Subir" data-role="none" onClick="SubirImagen_Fotocopy();">
										</div>
									</fieldset>

								</div>
							</li>
							
							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label>Fotocopia documento</label>
								</div>

								<div class="img_form">
									<a href="<?php echo URL_BASE;?>/uploads/registros/profesional/<?php echo $Query_expert->fotocopy;?>" target="_blank" id="link_fotocopy">
									<img src="<?php echo URL_BASE;?>/uploads/registros/profesional/<?php echo $Query_expert->fotocopy;?>" height="200" id="img_fotocopy">
									</a>
								</div>

								<div class="act_form">

									<fieldset class="">
										<div class="btn_form">
											<input type="file" class="form-control-file" id="imagen_fotocopy"><br>

											<input type="button" class="btn_per upload" value="Subir" data-role="none" onClick="SubirImagen_Fotocopy();">
										</div>
									</fieldset>

								</div>
							</li>

							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label>Fecha arl</label>
								</div>

								<div class="inp_form">
									<input type="datetime-local" name="date_arl" value="<?php echo (new DateTime($Query_expert->date_arl))->format("Y-m-d\\TH:i:s");?>">
								</div>

							</li>

							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label>Arl</label>
								</div>

								<div class="img_form">
									<a href="<?php echo URL_BASE;?>/uploads/registros/profesional/arl/<?php echo $Query_expert->arl;?>" target="_blank" id="link_arl">
									<img src="<?php echo URL_BASE;?>/uploads/registros/profesional/arl/<?php echo $Query_expert->arl;?>" height="200" id="img_arl">
									</a>
								</div>

								<div class="act_form">

									<fieldset class="">
										<div class="btn_form">
											<input type="file" class="form-control-file" id="imagen_arl"><br>
											<input type="button" class="btn_per upload" value="Subir" data-role="none" onClick="SubirImagen_Arl();">
										</div>
									</fieldset>

								</div>
							</li>

							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label>Fecha salud y pension</label>
								</div>

								<div class="inp_form">
									<input type="datetime-local" name="date_salud_pension" value="<?php echo (new DateTime($Query_expert->date_salud_pension))->format("Y-m-d\\TH:i:s");?>">
								</div>

							</li>

							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label>Salud y pension</label>
								</div>

								<div class="img_form">
									<a href="<?php echo URL_BASE;?>/uploads/registros/profesional/salud_pension/<?php echo $Query_expert->salud_pension;?>" target="_blank" id="link_salud">
									<img src="<?php echo URL_BASE;?>/uploads/registros/profesional/salud_pension/<?php echo $Query_expert->salud_pension;?>" height="200" id="img_salud">
									</a>
								</div>

								<div class="act_form">

									<fieldset class="">
										<div class="btn_form">
											<input type="file" class="form-control-file" id="imagen_salud"><br>

											<input type="button" class="btn_per upload" value="Subir" data-role="none" onClick="SubirImagen_Salud();">
										</div>
									</fieldset>

								</div>
							</li>

							<li class="ui-body ui-body-b">
								<fieldset class="">
									<div class="btn_form">
										<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
										<input type="hidden" name="editar_cliente" value="ok">
										<input type="submit" name="es_experto" value="Guardar" class="button action">
									</div>
								</fieldset>
							</li>

						</ul>
					</form>
				</p>

			</div>

			<div data-role="collapsible">
			    <h4>Certificados</h4>

			    <p>

					<table class="wp-list-table widefat table_form" cellspacing="0">
						<thead>
							<tr valign="top">
								<th>Imagen</th>
								<th>Editar Imagen</th>
								<th>Tipo Certificado</th>
								<th>Acción</th>
							</tr>
						</thead>
					</table>

					<ul data-role="listview" data-inset="true" id="the-list">
						<?php 
						$alter = "";
						$suma_certificados = 0;
						foreach($Query_Certificados as $item_certificado):
							if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
						?>
						<form method="post">
							<li class="ui-field-contain li_form" <?php echo $alter; ?>>
								<div style="width : 20%">
									<a href="<?php echo URL_BASE;?>/uploads/registros/profesional/certifications/<?php echo $item_certificado->certification;?>" target="_blank" id="link_certificado_<?php echo $suma_certificados;?>">
									<img src="<?php echo URL_BASE;?>/uploads/registros/profesional/certifications/<?php echo $item_certificado->certification;?>" height="150" id="img_certificado_<?php echo $suma_certificados;?>" style="width : 90%; position: relative; left : 5%; margin-top : 10px">
									</a>
								</div>
								<div style="width : 25%">
									<input type="file" class="form-control-file" id="imagen_certificado_<?php echo $suma_certificados;?>"><br>

									<input type="button" class="btn_per upload" value="Subir" data-role="none" onClick="SubirImagen_Certificado('<?php echo $item_certificado->id;?>', '<?php echo $suma_certificados;?>');">


								</div>
								<div style="width : 25%; position: relative; top: 17px; margin-left: 40px;">
									<select name="certification_type">
										<?php echo func_select_tabla_id_denomination("certifications_type", $item_certificado->certification_type);?>
									</select>
								</div>
								<div style="width : 25%">
									<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
									<input type="hidden" name="editar_cliente" value="ok">
									<input type="hidden" name="id_certi" value="<?php echo $item_certificado->id;?>">
									<input type="submit" name="update_certificado" value="Actualizar" class="btn_per" data-role="none">
									<input type="submit" name="del_certificado" value="Borrar" class="btn_per" data-role="none">
								</div>
							</li>
						</form>
						<?php 
						$suma_certificados++;
						endforeach;
						?>

						<form method="post">
							<li class="ui-field-contain li_form">
								<div style="width : 20%"></div>
								<div style="width : 28%"></div>
								<div style="width : 25%">
									<select name="certification_type">
										<?php echo func_select_tabla_id_denomination("certifications_type", "");?>
									</select>
								</div>
								<div style="width : 25%"> 
									<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
									<input type="hidden" name="editar_cliente" value="ok">
									<input type="hidden" name="expert" value="<?php echo $Query_expert->id;?>">
									<input type="submit" name="add_certificado" value="Crear" class="btn_per" data-role="none" style="background: #3f72a5; color: white;">
								</div>
							</li>
						</form>

					</ul>
				</p>
			</div>
			<?php endif;?>

			<div data-role="collapsible">
			    <h4>Proyectos</h4>
			    <p>
					<table class="wp-list-table widefat table_form" cellspacing="0">
						<thead>
							<tr valign="top" valign="top">
								<th>Imagen</th>
								<th>Editar Imagen</th>
								<th>Titulo Proyecto</th>
								<th>Accion</th>
							</tr>
						</thead>
					</table>
					<ul data-role="listview" data-inset="true" id="the-list">
						<?php 
						$alter = "";
						$suma_proyectos = 0;
						foreach($Query_Proyectos as $item_proyecto):
							if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
						?>
						<form method="post">
						<li class="ui-field-contain li_form" <?php echo $alter; ?>>
							<div style="width : 20%">
								<a href="<?php echo URL_BASE;?>/uploads/registros/profesional/jobs/<?php echo $item_proyecto->job;?>" target="_blank" id="link_proyecto_<?php echo $suma_proyectos;?>">
								<img src="<?php echo URL_BASE;?>/uploads/registros/profesional/jobs/<?php echo $item_proyecto->job;?>" height="150" id="img_proyecto_<?php echo $suma_proyectos;?>" style="width : 90%; position: relative; left : 5%; margin-top : 10px">
								</a>
							</div>
							<div style="width : 25%">
								<input type="file" class="form-control-file" id="imagen_proyecto_<?php echo $suma_proyectos;?>"><br> 
								<input type="button" class="btn_per upload" value="Subir" data-role="none" onClick="SubirImagen_Proyecto('<?php echo $item_proyecto->id;?>', '<?php echo $suma_proyectos;?>');">
							</div>
							<div style="width : 25%; position: relative; top: 17px; margin-left: 40px;">
								<input type="text" name="description" value="<?php echo $item_proyecto->description;?>" required>
							</div>

							<div style="width : 25%">
								<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
								<input type="hidden" name="editar_cliente" value="ok">
								<input type="hidden" name="id_proy" value="<?php echo $item_proyecto->id;?>">
								<input type="submit" name="update_proyecto" value="Actualizar" class="btn_per" data-role="none">
								<input type="submit" name="del_proyecto" value="Borrar" class="btn_per" data-role="none">
							</div>

						</li>
						</form>
						<?php 
						$suma_proyectos++;
						endforeach;
						?>

						<form method="post">
							<li class="ui-field-contain li_form">
								<div style="width : 20%"></div>
								<div style="width : 28%"></div>
								<div style="width : 25%">
									<input type="text" name="description" placeholder="Titulo Proyecto" required>
								</div>
								<div style="width : 25%"> 
									<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
									<input type="hidden" name="editar_cliente" value="ok">
									<input type="hidden" name="expert" value="<?php echo $Query_expert->id;?>">
									<input type="submit" name="add_proyecto" value="Crear" class="btn_per" data-role="none"  style="background: #3f72a5; color: white;">
								</div>
							</li>
						</form>
					</ul>
				</p>
			</div>

			<div data-role="collapsible">
			    <h4>Referidos</h4>
			    <p>

					<ul data-role="listview" data-inset="true">
						<li class="ui-field-contain li_form">
							<div class="label_form">
								<label>Mi Cupon </label>
							</div>

							<div class="inp_form">
								<?php echo $Query->code."".$Query->id;?>
							</div>
						</li>

						<li class="ui-field-contain li_form">
							<div class="label_form">
								<label>Referido </label>
							</div>

							<div class="inp_form">
								Fecha registro
							</div>
						</li>

						<li id="the-list">
							<?php 
							$alter = "";
							foreach($Query_Referidos as $item_referido):
								if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
							?>
							<div class="label_form">
								<label><?php Gen_Btn_Experto($item_referido->id);?> </label>
							</div>

							<div class="inp_form">
								<?php echo $item_referido->fecha;?>
							</div>
							<?php endforeach;?>
						</li>

					</ul>

						
				</p>
			</div>

			<div data-role="collapsible">
			    <h4>Plan</h4>
			    <p>

					<form method="post" name="la_data" class="formulario">
						<ul data-role="listview" data-inset="true">
							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label for="phone">Plan</label>
								</div>
								<div class="inp_form">
									<select name="plan">
										<?php echo func_select_tabla_id_denomination("plans", $Query_Expert_Plan->plan);?>
									</select>
								</div>
							</li>

							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label for="phone">Fecha inicio</label>
								</div>
								<div class="inp_form">
									<input type="datetime-local" name="start_date" value="<?php echo (new DateTime($Query_Expert_Plan->start_date))->format("Y-m-d\\TH:i:s");?>">
								</div>
							</li>

							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label for="phone">Fecha final</label>
								</div>
								<div class="inp_form">
									<input type="datetime-local" name="end_date" value="<?php echo (new DateTime($Query_Expert_Plan->end_date))->format("Y-m-d\\TH:i:s");?>">
								</div>
							</li>

							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label for="phone">Estado</label>
								</div>
								<div class="inp_form">
									<?php echo $Query_Expert_Plan->status;?>
								</div>
							</li>

							<li class="ui-body ui-body-b">
								<fieldset class="">
									<div class="btn_form">
										<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
										<input type="hidden" name="id_plan" value="<?php echo $Query_Expert_Plan->id;?>">
										<input type="hidden" name="editar_cliente" value="ok">
										<input type="submit" name="es_plan" value="Guardar" class="button action">
									</div>
								</fieldset>
							</li>
						</ul>
					</form>
				</p>
			</div>

			<div data-role="collapsible">
			    <h4>Categorias</h4>
			    <p>
					<table class="wp-list-table widefat" cellspacing="0">
						<thead>
							<tr valign="top">
								<?php foreach($Query_Categorias[0] as $key => $value):?>
									<?php if($key == "Categoria" || $key == "Sub_Categoria"):?>
										<th>
											<?php echo $key; ?>
										</th>
									<?php endif;?>
								<?php endforeach; ?>
								<th>Acciones</th>
							</tr>
						</thead>
					</table>
						<ul id="the-list" data-role="listview" data-inset="true">
							<?php 
							$alter = "";
							foreach ( $Query_Categorias as $lista ):
								if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
							?>
							<li valign="top"  class="ui-body ui-body-b li_form" <?php echo $alter; ?>>
								<div class="label_form">
									<?php foreach($lista as $it => $va):?>
										<?php if($it == "Categoria" || $it == "Sub_Categoria"):?>
										<div>
											<?php echo $va;?>
										</div>
										<?php endif;?>
									<?php endforeach; ?>
								</div>
								
								<div class="inp_form">
									<form method="post">
										<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
										<input type="hidden" name="editar_cliente" value="ok">
										<input type="hidden" name="id_experto" value="<?php echo $Query_expert->id;?>">
										<input type="hidden" name="category" value="<?php echo $lista->id_cat;?>">
										<input type="submit" name="del_categoria" value="Borrar" data-role="none" class="btn_per" >
									</form>
								</div>
							</li>
							<?php endforeach;?>
							
							<form method="post">
							<li valign="top"  class=" li_form li_cat">
								<div style="width : 33%">
									<select id="select_service" onchange="Do_Select_Category();" >
										<?php echo func_select_tabla_id_denomination("services", "");?>
									</select>
								</div>

								<div style="width : 33%">
									<select name="category" id="select_category" >
										<?php echo func_select_sub_categorias("", "");?>
									</select>
								</div>
								
								<div style="width : 33%">
									<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
									<input type="hidden" name="editar_cliente" value="ok">
									<input type="hidden" name="id_experto" value="<?php echo $Query_expert->id;?>">
									<input type="submit" name="add_categoria" value="Crear" class="btn_per" data-role="none" style="background: #3f72a5; color: white;">
								</div>
							</li>
							</form>

						</ul>
				</p>
			</div>

			<div data-role="collapsible">
			    <h4>Regiones</h4>
			    <p>
					<table class="wp-list-table widefat" cellspacing="0">
						<thead>
							<tr valign="top">
								<?php foreach($Query_Region[0] as $key => $value):?>
								<?php if($key == "Categoria" || $key == "Sub_Categoria"):?><th><?php echo $key; ?></th><?php endif;?>
								<?php endforeach; ?>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody id="the-list">
							<?php 
							$alter = "";
							foreach ( $Query_Region as $lista ):
								if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
							?>
							<tr valign="top" <?php echo $alter; ?>>
								<?php foreach($lista as $it => $va):?>
									<?php if($it == "Categoria" || $it == "Sub_Categoria"):?><td><?php echo $va;?></td><?php endif;?>
								<?php endforeach; ?>
								<td>
									<form method="post">
										<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
										<input type="hidden" name="editar_cliente" value="ok">
										<input type="hidden" name="id_experto" value="<?php echo $Query_expert->id;?>">
										<input type="hidden" name="category" value="<?php echo $lista->id_cat;?>">
										<input type="submit" name="del_region" value="Borrar" class="btn_per" data-role="none">
									</form>
								</td>
							</tr>
							<?php endforeach;?>
							
							<form method="post">
							<tr valign="top">
								<td>
									<select id="select_ciudad" onchange="Do_Select_Zona();">
										<?php echo func_select_tabla_id_denomination("cities", "");?>	
									</select>
								</td>

								<td>
									<select id="select_zona" name="region">
										<?php echo func_select_sub_zonas("", "");?>
									</select>
								</td>
								
								<td>
									<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
									<input type="hidden" name="editar_cliente" value="ok">
									<input type="hidden" name="id_experto" value="<?php echo $Query_expert->id;?>">
									<input type="submit" name="add_region" value="Crear"  style="background: #3f72a5; color: white;" data-role="none" class="btn_per">
								</td>
							</tr>
							</form>
						</tbody>
					</table>
				</p>
			</div>

			<?php if($Query_expert->type == 1):?>
			<div data-role="collapsible">
			    <h4>Colaboradores</h4>
			    <p>
					<table class="wp-list-table widefat" cellspacing="0">
						<thead>
							<tr valign="top">
								<th>Nombre</th>
								<th>Email</th>
								<th>Tipo identificacion</th>
								<th>Numero identificacion</th>
								<th>Telefono</th>
								<th>Foto</th>
								<th>Categorias</th>
								<th>Guardar</th>
								<th>Borrar</th>
							</tr>
						</thead>
						<tbody id="the-list">
							<?php 
							$alter = "";
							foreach($Query_Colaboradores as $item_colaborador):
								if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
							?>
							<form method="post">
								<tr valign="top" <?php echo $alter; ?>>
									<td><?php //echo $item_colaborador->id;?><input type="text" name="name" value="<?php echo $item_colaborador->name;?>"></td>
									<td><input type="email" name="email" value="<?php echo $item_colaborador->email;?>"></td>
									<td>
										<select name="identification_type">
											<?php echo func_select_tabla_id_denomination("identification_type", $item_colaborador->identification_type);?>
										</select>
									</td>
									<td><input type="text" name="number" value="<?php echo $item_colaborador->number;?>"></td>
									<td><input type="text" name="phone" value="<?php echo $item_colaborador->phone;?>"></td>

									<td>
									<a href="<?php echo URL_BASE;?>/uploads/registros/empresa/collaborators/<?php echo $item_colaborador->photo;?>" target="_blank" id="link_col_<?php echo $item_colaborador->id;?>">
										<img src="<?php echo URL_BASE;?>/uploads/registros/empresa/collaborators/<?php echo $item_colaborador->photo;?>" id="img_col_<?php echo $item_colaborador->id;?>" height="50">
									</a>
									
									<br>
									<input type="file" class="form-control-file" id="imagen_col_<?php echo $item_colaborador->id;?>"><br>
									<input type="button" class="btn btn-primary upload" value="Subir" onClick="SubirImagen_Colaborador('<?php echo $item_colaborador->id;?>', '<?php echo $item_colaborador->id;?>');">
									</td>
								
									<td>
									<?php echo Select_Categorias_Multi_Colaborador($item_colaborador->id);?>
									</td>

									<td>
										<input type="hidden" name="id_col" value="<?php echo $item_colaborador->id;?>">
										<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
										<input type="hidden" name="editar_cliente" value="ok">
										<input type="submit" name="update_colaborador" value="Guardar" class="button action">
									</td>

									<td>
										<input type="submit" name="borrar_colaborador" value="Borrar" class="button action">
									</td>
								</tr>
							</form>
							<?php endforeach;
							if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
							?>
							<form method="post">
								<tr valign="top" <?php echo $alter; ?>>
									<td><input type="text" name="name" placeholder="Nombre" required></td>
									<td><input type="email" name="email" placeholder="Correo" required></td>
									<td>
										<select name="identification_type">
											<?php echo func_select_tabla_id_denomination("identification_type", "");?>
										</select>
									</td>
									<td><input type="text" name="number" placeholder="Numero Documento"></td>
									<td><input type="text" name="phone" placeholder="Telefono"></td>
									<td></td>
									<td></td>
									<td>
										<input type="hidden" name="expert" value="<?php echo $Query_expert->id;?>">
										<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
										<input type="hidden" name="editar_cliente" value="ok">
										<input type="submit" name="add_colaborador" value="Crear" class="button action">
									</td>
									<td></td>
								</tr>
							</form>
						</tbody>
					</table>
				</p>
			</div>
			<?php endif;?>

			<div data-role="collapsible">
			    <h4>Servicios</h4>
			    <p>
					<?php if( Count($Query_Ofers) > 0 ):?>
					<table class="wp-list-table widefat" cellspacing="0" id="tabla_servicios">
						<thead>
							<tr valign="top">
								<?php foreach($Query_Ofers[0] as $key => $value):?>
									<th><?php echo $key; ?></th>
								<?php endforeach; ?>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody id="the-list">
							<?php 
							$alter = "";
							foreach ( $Query_Ofers as $lista ):
								if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
							?>
							<tr valign="top" <?php echo $alter; ?>>
								<?php foreach($lista as $it => $va):?>
									<td><?php echo $va;?></td>
								<?php endforeach; ?>
								<td>
									<form method="post" action="admin.php?page=servicios">
										<input type="hidden" name="id" value="<?php echo $lista->id_request;?>">
										<input type="submit" name="editar_servicio" value="Editar" class="button action">
									</form>
								</td>
							</tr>
							<?php endforeach;?>
						</tbody>
					</table>
					<?php endif;?>
				</p>
			</div>

			<div data-role="collapsible">
			    <h4>Calificaciones</h4>
			    <p>
					<?php if( Count($Query_Calificaciones) > 0 ):?>
					<table class="wp-list-table widefat" cellspacing="0" id="tabla_calificaciones">
						<thead>
							<tr valign="top">
								<?php foreach($Query_Calificaciones[0] as $key => $value):?>
								<?php if(!in_array($key, $exclude_tabla_calificaciones)):?><th><?php echo $key; ?></th><?php endif;?>
								<?php endforeach; ?>
							</tr>
						</thead>
						<tbody id="the-list">
							<?php 
							$alter = "";
							foreach ( $Query_Calificaciones as $lista ):
								if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
							?>
							<tr valign="top" <?php echo $alter; ?>>
								<?php foreach($lista as $key => $va):?>
									<?php if(!in_array($key, $exclude_tabla_calificaciones)):?><td><?php echo $va;?></td><?php endif;?>
								<?php endforeach; ?>
							</tr>
							<?php endforeach;?>
						</tbody>
					</table>
					<?php endif;?>
				</p>
			</div>

		</div>
		
	</div>

	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>  -->
	<script src="https://harvesthq.github.io/chosen/chosen.jquery.js" type="text/javascript"></script>
	<script src="https://harvesthq.github.io/chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
	<script src="https://harvesthq.github.io/chosen/docsupport/init.js" type="text/javascript" charset="utf-8"></script>
	
	<script type="text/javascript">
		function Do_Select_Category(){
			//alert( jQuery("#select_service").val() );
			jQuery.ajax({
				url:"<?php echo home_url('pagina_querys');?>", 
				method: "POST",
				data: { select_category : "ok", categoria : jQuery("#select_service").val() }
			}).done(function(html){
				//alert(html);
				jQuery("#select_category").html(html);
			}).fail(function() {
				alert( "error" );
			});
		}
		function Do_Select_Zona(){
			//alert( jQuery("#select_service").val() );
			jQuery.ajax({
				url:"<?php echo home_url('pagina_querys');?>", 
				method: "POST",
				data: { select_zona : "ok", categoria : jQuery("#select_ciudad").val() }
			}).done(function(html){
				//alert(html);
				jQuery("#select_zona").html(html);
			}).fail(function() {
				alert( "error" );
			});
		}
		function Do_Activar_Experto(form){
			//alert(form.active.value);
			if( confirm("Esta accion enviara un correo de confirmacion, ¿desea continuar?") ){
				jQuery.ajax({
					url:"<?php echo URL_BASE;?>/fixperto/sendEmailActive", 
					method: "POST",
					data: { name : form.nombre.value, email : form.correo.value, active:form.active.value }
				}).done(function(html){
					//alert(html);
					//$("#select_zona").html(html);
				}).fail(function() {
					alert( "error enviando el correo" );
				});
				return true;
			}
			return false;
		}
		function Do_Reestablecer_Clave(form){
			if( confirm("Esta accion enviara un correo de confirmacion, ¿desea continuar?") ){
				jQuery.ajax({
					url:"<?php echo URL_BASE;?>/backOffice/changePassword", 
					method: "POST",
					data: { id : form.id_user.value, email : form.correo.value }
				}).done(function(html){
					//alert(html);
					//$("#select_zona").html(html);
					alert( "Correo enviado con exito!" );
				}).fail(function() {
					alert( "error enviando el correo" );
				});
			}
			return false;
		}
		function Do_Dar_Fixcoins(){
			if( $("#cant_dar_fixcoins").val() == 0 )return false;
			if( confirm("Esta accion enviara un correo de confirmacion, ¿desea continuar?") ){
				jQuery.ajax({
					url:"<?php echo URL_BASE;?>/fixperto/fixcoinGift", 
					method: "POST",
					data: { user : "<?php echo $_POST["id"];?>", cant : $("#cant_dar_fixcoins").val() }
				}).done(function(html){
					console.log(html);
					$("#label_actual_fixcoins").html(html);
					alert( "Correo enviado con exito!" );
				}).fail(function() {
					//alert( "error enviando el correo" );
				});
			}
			return false;
		}
		//
		function SubirImagen_Arl(){
			var formData = new FormData();
			var files = $('#imagen_arl')[0].files[0];
			formData.append('archivo', files);
			formData.append('id', "<?php echo $Query_expert->id;?>");
			jQuery.ajax({
				url: '<?php echo URL_BASE;?>/fixperto/modArlProfesional',
				type: 'post',
				data: formData,
				contentType: false,
				processData: false,
				success: function(response) {
					console.log(response);
					if (response != 0) {
						$("#img_arl").attr("src", response);
						$("#link_arl").attr("href", response);
					} else {
						alert('Formato de imagen incorrecto.');
					}
				}
			});
		}
		function SubirImagen_Salud(){
			var formData = new FormData();
			var files = $('#imagen_salud')[0].files[0];
			formData.append('archivo', files);
			formData.append('id', "<?php echo $Query_expert->id;?>");
			jQuery.ajax({
				url: '<?php echo URL_BASE;?>/fixperto/modSaludPensionProfesional',
				type: 'post',
				data: formData,
				contentType: false,
				processData: false,
				success: function(response) {
					console.log(response);
					if (response != 0) {
						$("#img_salud").attr("src", response);
						$("#link_salud").attr("href", response);
					} else {
						alert('Formato de imagen incorrecto.');
					}
				}
			});
		}
		function SubirImagen_Fotocopy(){
			var formData = new FormData();
			var files = $('#imagen_fotocopy')[0].files[0];
			formData.append('archivo', files);
			formData.append('id', "<?php echo $Query_expert->id;?>");
			jQuery.ajax({
				url: '<?php echo URL_BASE;?>/fixperto/modFotocopy',
				type: 'post',
				data: formData,
				contentType: false,
				processData: false,
				success: function(response) {
					console.log(response);
					if (response != 0) {
						$("#img_fotocopy").attr("src", response);
						$("#link_fotocopy").attr("href", response);
					} else {
						alert('Formato de imagen incorrecto.');
					}
				}
			});
		}
		//
		function SubirImagen_Certificado(_id, _suma){
			var formData = new FormData();
			var files = $('#imagen_certificado_' + _suma)[0].files[0];
			formData.append('archivo', files);
			formData.append('id', _id);
			jQuery.ajax({
				url: '<?php echo URL_BASE;?>/fixperto/modCertificationsProfesional',
				type: 'post',
				data: formData,
				contentType: false,
				processData: false,
				success: function(response) {
					console.log(response);
					if (response != 0) {
						$("#img_certificado_" + _suma).attr("src", response);
						$("#link_certificado_"+ _suma).attr("href", response);
					} else {
						alert('Formato de imagen incorrecto.');
					}
				}
			});
		}
		function SubirImagen_Proyecto(_id, _suma){
			var formData = new FormData();
			var files = $('#imagen_proyecto_' + _suma)[0].files[0];
			formData.append('archivo', files);
			formData.append('id', _id);
			jQuery.ajax({
				url: '<?php echo URL_BASE;?>/fixperto/modJobsProfesional',
				type: 'post',
				data: formData,
				contentType: false,
				processData: false,
				success: function(response) {
					console.log(response);
					if (response != 0) {
						$("#img_proyecto_" + _suma).attr("src", response);
						$("#link_proyecto_"+ _suma).attr("href", response);
					} else {
						alert('Formato de imagen incorrecto.');
					}
				}
			});
		}
		function SubirImagen_Colaborador(_id, _suma){
			var formData = new FormData();
			var files = $('#imagen_col_' + _suma)[0].files[0];
			formData.append('archivo', files);
			formData.append('id', _id);
			jQuery.ajax({
				url: '<?php echo URL_BASE;?>/fixperto/modAvatarCollaborator',
				type: 'post',
				data: formData,
				contentType: false,
				processData: false,
				success: function(response) {
					console.log(response);
					if (response != 0) {
						$("#img_col_" + _suma).attr("src", response);
						$("#link_col_"+ _suma).attr("href", response);
					} else {
						alert('Formato de imagen incorrecto.');
					}
				}
			});
		}
	</script>
	<?php
}
//
function page_lista_servicios(){
	//print_r($_POST);
	if(isset($_POST["crear_categoria"])){
		$qs_insert = "INSERT INTO services (denomination) VALUES ('".$_POST["nombre"]."')";
		$q_insert = plugDB($qs_insert, "result");
	}
	if(isset($_POST["borrar_cat"])){
		$qs_insert = "DELETE FROM services WHERE id='".$_POST["id_item"]."'";
		$q_insert = plugDB($qs_insert, "result");
	}
	if(isset($_POST["editar_nombre_cat"])){
		$qs_insert = "UPDATE services SET denomination='".$_POST["nombre"]."' WHERE id='".$_POST["id_item"]."'";
		$q_insert = plugDB($qs_insert, "result");
	}
	if(isset($_POST["crear_subcat"])){
		$qs_insert = "INSERT INTO categories (service, denomination) VALUES ('".$_POST["id_parent"]."', '".$_POST["nombre"]."')";
		$q_insert = plugDB($qs_insert, "result");
	}
	if(isset($_POST["borrar_subcat"])){
		$qs_insert = "DELETE FROM categories WHERE id='".$_POST["id_item"]."'";
		$q_insert = plugDB($qs_insert, "result");
	}
	if(isset($_POST["editar_fixcoin_subcat"])){
		$qs_insert = "UPDATE categories SET cost='".$_POST["costo_item"]."' WHERE id='".$_POST["id_item"]."'";
		$q_insert = plugDB($qs_insert, "result");
	}
	if(isset($_POST["editar_nombre_subcat"])){
		$qs_insert = "UPDATE categories SET denomination='".$_POST["nombre"]."' WHERE id='".$_POST["id_item"]."'";
		$q_insert = plugDB($qs_insert, "result");
	}
	if(isset($_POST["suspender_cat"])){
		$qs_insert = "UPDATE services SET hidden='".$_POST["estado"]."' WHERE id='".$_POST["id_item"]."'";
		$q_insert = plugDB($qs_insert, "result");
	}
	//
	$WHERE = "";
	$Qstr = "
	SELECT s.id, s.denomination as Servicio, subs.suma_req as cant_req, if(exps.suma,exps.suma,0) as cant_exp, s.hidden, s.imagen
	FROM
	services s
	LEFT JOIN
	(
		SELECT c.service, COUNT(r.id) as suma_req
		FROM categories c LEFT JOIN requests r 
		ON r.category = c.id
		GROUP BY c.service
	)subs ON s.id = subs.service
	LEFT JOIN
	(
		SELECT r.suma, c.service
		FROM
		categories c 
		LEFT JOIN (SELECT a.category, COUNT(*) as suma FROM experts_categories a GROUP BY a.category)r 
		ON r.category = c.id
		GROUP BY c.service
	)exps ON s.id = exps.service
	$WHERE
	ORDER BY denomination ASC
	";
	//
	$Query = plugDB($Qstr, "result");
	?>

	<script type="text/javascript" >
		jQuery(document).on("mobileinit", function(){
			jQuery.mobile.ajaxEnabled = false;
		});
	</script>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.css"/>
	<script src="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.jqueryui.min.css">
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.jqueryui.min.js"></script>
	<script type="text/javascript" class="init">
		jQuery(document).ready(function() {
			//jQuery('#la_data_tabla').DataTable();
			jQuery('#wpfooter').remove();
		} );
	</script>
	
	<style>
		.controlgroup-textinput{
		    padding-top:.22em;
		    padding-bottom:.22em;
		}
		.ui-title{
			color:white;
		}
		div.DataTables_sort_wrapper span {
			right: -10px !important;
		}

		.fix_table .ui-btn-inner{
			padding: .55em 11px;
		}
		.ui-select .ui-icon{
			background-image: url(https://code.jquery.com/mobile/1.2.1/images/icons-18-white.png) !important;
			background-position: -217px 50% !important;
		}
		[id="formulario"] *{
			font:inherit !important;
			font-size:16px !important;
		}

		.ui-shadow-inset{
			box-shadow: none !important;
			margin-left : 10px;
		}

		.flex{
			display : flex !important;
		}

		.btn_per{
			border: none;
			width: 150px;
			padding: 10px;
			box-shadow: 0 1px 4px rgba(0,0,0,.3);
			outline: none;
			margin-top: 10px;
			font-weight: bold;
			border-radius: 6px;
			font-size : 0.8rem;
		}

		.btn_form{
			width : 40%;
			margin: auto
		}


	</style>

	<div data-role="page">
		<div data-role="header" data-theme="b">
			<h1>Categorias (<?php echo Count($Query);?> Registros)</h1>
		</div>

		<div data-role="header" data-theme="c">
			<div data-role="controlgroup" data-type="horizontal">
				<form method="post" onSubmit="return Validar_Nombre(this);">
					<div class="ui-input-text ui-body-inherit controlgroup-textinput ui-btn ui-shadow-inset" style="height:100% ; margin-left : 15px">
						<input type="text" name="nombre" placeholder="Ingrese Nombre" value="">
					</div>
					<input type="submit" data-role="none" name="crear_categoria" value="Crear Categoria" class="ui-btn" data-theme="b" style="margin-top: 10px; margin-left: 15px; padding: 8px; background: #5a91c0; border: none; color: white;">
				</form>
			</div>
		</div>

		

		<div role="content" class="ui-content">
			<?php if($Query[0] != null):?>
				<?php foreach ( $Query as $lista ):?>
					<div data-role="collapsible" >
					    <h4><?php echo $lista->Servicio;?><!-- , Servicios: <?php echo $lista->cant_req;?>, Expertos: <?php echo $lista->cant_exp;?> --></h4>
					    <p>
							<div style="display:flex">

								<div style="width:20%">
									<a href="<?php echo URL_BASE;?>/uploads/categories/<?php echo $lista->imagen;?>" target="_blank" id="link_cat_<?php echo $lista->id;?>">
										<img src="<?php echo URL_BASE;?>/uploads/categories/<?php echo $lista->imagen;?>" height="100" id="img_cat_<?php echo $lista->id;?>">
									</a><br>

									<input type="file" class="form-control-file" id="imagen_cat_<?php echo $lista->id;?>" style="width : 80%"><br>
									<input type="button" data-role="none" class=" upload btn_per" value="Subir" onClick="SubirImagen_Categoria('<?php echo $lista->id;?>', '<?php echo $lista->id;?>');">
								</div>

								<div style="width:20%">
									<form method="post" onSubmit="return Validar_Nombre(this);">
										<input type="hidden" name="id_item" value="<?php echo $lista->id;?>">
										<input type="text" name="nombre" value="<?php echo $lista->Servicio;?>" style="width : 80%">
										<input type="submit" data-role="none" class="btn_per" name="editar_nombre_cat" value="Editar">
									</form>
									<form method="post" onSubmit="return confirm('Quiere borrar: <?php echo $lista->Servicio;?>?');">
									<input type="hidden" name="id_item" value="<?php echo $lista->id;?>">	
									<input type="submit" data-role="none" name="borrar_cat" value="Borrar Categoria" class="btn_per">
									</form>
									<?php if($lista->hidden == "0"):?>
										<form method="post" onSubmit="return confirm('Quiere Suspender: <?php echo $lista->Servicio;?>?');">
											<input type="hidden" name="id_item" value="<?php echo $lista->id;?>">
											<input type="hidden" name="estado" value="1">	
											<input type="submit" data-role="none" name="suspender_cat" class="btn_per" value="Suspender Categoria">
										</form>
									<?php else:?>
										<form method="post" onSubmit="return confirm('Quiere Activar: <?php echo $lista->Servicio;?>?');">
											<input type="hidden" name="id_item" value="<?php echo $lista->id;?>">	
											<input type="hidden" name="estado" value="0">
											<input type="submit" data-role="none" name="suspender_cat" class="btn_per" value="Activar Categoria">
										</form>
									<?php endif;?>
								</div>

								<div style="width:55%">
									<?php Mis_Sub_Cates($lista->id);?>
								</div>
							</div>
						</p>
					</div>
				<?php endforeach;?>


				
			<?php endif;?>
		</div>

	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
	<script type="text/javascript">
		function HolaMundo(){
			alert("Holas");
			jQuery.ajax( {url:"https://google.com"} );
		}
		function Validar_Nombre(form){
			if(form.nombre.value == ""){
				alert("Ingrese un nombre!!");
				return false;
			}
			return true;
		}
		//
		function SubirImagen_Categoria(_id, _suma){
			var formData = new FormData();
			var files = $('#imagen_cat_' + _suma)[0].files[0];
			formData.append('archivo', files);
			formData.append('id', _id);
			jQuery.ajax({
				url: '<?php echo URL_BASE;?>/fixperto/modCategoryIcon',
				type: 'post',
				data: formData,
				contentType: false,
				processData: false,
				success: function(response) {
					console.log(response);
					if (response != 0) {
						jQuery("#img_cat_" + _suma).attr("src", response);
						jQuery("#link_cat_"+ _suma).attr("href", response);
					} else {
						alert('Formato de imagen incorrecto.');
					}
				}
			});
		}
	</script>
	<?php
}

function Mis_Sub_Cates($id){
	$q_sub = "
	SELECT c.*, if(req.suma,req.suma,0)as suma
	FROM categories c 
	LEFT JOIN (SELECT COUNT(*) as suma, category FROM requests GROUP BY category)req ON c.id = req.category
	WHERE service = '".$id."'
	ORDER BY c.denomination ASC
	";
	$Query2 = plugDB($q_sub, "result");
	foreach ( $Query2 as $lista_sub ):
	?>
	<div style="margin-bottom: 10px;">
		<div class="flex">
			<div style="width : 150px">
				<form method="post" onSubmit="return Validar_Nombre(this);">
				<input type="hidden" name="id_item" value="<?php echo $lista_sub->id;?>">
				<input type="text" name="nombre" value="<?php echo $lista_sub->denomination;?>">
				<input type="submit" data-role="none"  class="btn_per" name="editar_nombre_subcat" value="Editar">
				</form>
			</div>
			<div style="width : 150px;  margin-left : 15px">
				<p> <b> Suma : </b> <?php echo $lista_sub->suma;?></p>
			</div>
			<div style="width : 150px">
				<form method="post">
					<input type="hidden" name="id_item" value="<?php echo $lista_sub->id;?>">
					<input type="number" name="costo_item" value="<?php echo $lista_sub->cost;?>">
					<input type="submit" data-role="none" class="btn_per" name="editar_fixcoin_subcat" value="Editar">
				</form>
			</div>
			<div style="width : 150px;  margin-left : 15px">
				<form method="post" onSubmit="return confirm('Quiere borrar: <?php echo $lista_sub->denomination;?>?');">
					<input type="hidden" name="id_item" value="<?php echo $lista_sub->id;?>">	
					<input type="submit" data-role="none" class="btn_per" name="borrar_subcat" value="Borrar Subcategoria">
				</form>
			</div>
		</div>
		<hr>
	</div>
	
	<?php
	endforeach;
	?>
	<div>
		<div>
			<form method="post" onSubmit="return Validar_Nombre(this);">
				<ul data-role="listview" data-inset="true">
					<li class="ui-body ui-body-b">
						<fieldset class="">
							<input type="hidden" name="id_parent" value="<?php echo $id;?>">
							<input type="text" name="nombre" placeholder="Ingrese nombre">
							<div class="btn_form">
								<input type="submit" name="crear_subcat" value="Crear Subcategoria">
							</div>
						</fieldset>
					</li>
				</ul>
				
			</form>
		</div>
	</div>
	<?php
}
//
function page_servicios(){
	//print_r($_POST);
	if( isset($_POST["editar_servicio"]) ){
		page_servicios_editar_servicio();
		return;
	}
	//
	$WHERE = "";
	if(isset($_POST["estado"]) &&  $_POST["estado"]!="-1"){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "r.status = '".$_POST["estado"]."'";
	}
	if(isset($_POST["categoria"]) &&  $_POST["categoria"]!="-1"){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "cat.cat2id = '".$_POST["categoria"]."'";
	}
	if(isset($_POST["sub_categoria"]) &&  $_POST["sub_categoria"]!="-1"){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "cat.cat1id = '".$_POST["sub_categoria"]."'";
	}
	if(isset($_POST["ciudad"]) &&  $_POST["ciudad"]!="-1"){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "reg.reg2id = '".$_POST["ciudad"]."'";
	}
	if(isset($_POST["zona"]) &&  $_POST["zona"]!="-1"){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "reg.reg1id = '".$_POST["zona"]."'";
	}
	if(isset($_POST["buscador"])){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "c.name LIKE '%".$_POST["buscador"]."%' OR r.description LIKE '%".$_POST["buscador"]."%'";
	}
	//
	$Qstr = "
	SELECT r.id as id_req, c.name as Cliente, cat.cat as Categoria, cat.sub_cat as Sub_Categoria, reg.ciudad as Ciudad, reg.zona as Zona, r.description as Descripcion, r.registry_date as Fecha, r.status as Status, r.address as Direccion, IF(sum_pos.suma,sum_pos.suma,0) as Postulados
	FROM 
	requests r
	LEFT JOIN (SELECT c1.id as c1_id, c2.name FROM customers c1 LEFT JOIN users c2 ON c1.user=c2.id)c ON r.customer = c.c1_id
	LEFT JOIN (SELECT cat1.id as cat1id, cat2.id as cat2id, cat1.denomination as sub_cat, cat2.denomination as cat FROM categories cat1 LEFT JOIN services cat2 ON cat1.service = cat2.id)cat ON r.category = cat.cat1id
	LEFT JOIN (SELECT regions.id as reg1id, cities.id as reg2id, regions.name as zona, cities.denomination as ciudad FROM regions LEFT JOIN cities ON cities.id = regions.city)reg ON r.region = reg.reg1id
	LEFT JOIN (SELECT COUNT(id) as suma, request FROM offers GROUP BY request )sum_pos ON r.id = sum_pos.request
	$WHERE
	ORDER BY id_req DESC
	";
	$Query = plugDB($Qstr, "result");
	$Colum_No_Mostrar = array("Descripcion", "Sub_Categoria", "Zona");
	?>

	<script type="text/javascript" >
		jQuery(document).on("mobileinit", function(){
			jQuery.mobile.ajaxEnabled = false;
		});
	</script>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.css"/>
	<script src="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.jqueryui.min.css">
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.jqueryui.min.js"></script>
	<script type="text/javascript" class="init">
		jQuery(document).ready(function() {
			jQuery('#la_data_tabla').DataTable();
			jQuery('#wpfooter').remove();
		} );
	</script>
	<style>
		.controlgroup-textinput{
		    padding-top:.22em;
		    padding-bottom:.22em;
		}
		.ui-title{
			color:white;
		}
		div.DataTables_sort_wrapper span {
			right: -10px !important;
		}

		.fix_table .ui-btn-inner{
			padding: .55em 11px;
		}
		.ui-select .ui-icon{
			background-image: url(https://code.jquery.com/mobile/1.2.1/images/icons-18-white.png) !important;
			background-position: -217px 50% !important;
		}
		[id="formulario"] *{
			font:inherit !important;
			font-size:16px !important;
		}

		.btn_per{
			border: none;
			width: 80px;
			padding: 10px;
			box-shadow: 0 1px 4px rgba(0,0,0,.3);
			outline: none;
			margin-top: 10px;
			font-weight: bold;
			border-radius: 6px;
			font-size : 0.8rem;
		}
	</style>

	<div data-role="page">
		<div data-role="header" data-theme="b">
			<h1>Servicios (<?php echo Count($Query);?> Registros)</h1>
		</div>
		<div data-role="header" data-theme="c">
			<div data-role="controlgroup" data-type="horizontal">
				<form action='<?php bloginfo('template_url'); ?>/admin_db/export_xls.php' target='_blank' method='post'>
					<input type='hidden' name='type' value='servicios'/>
					<input type='hidden' name='xhr' value='<?php echo base64_encode($Qstr); ?>'/>
					<input type="submit" id="exportar" name="exportar" value="Exportar a excel" class="ui-btn" data-theme="b" />
				</form>
			</div>
			<div data-role="controlgroup" data-type="horizontal" style="width:10px">
			</div>
			<div data-role="controlgroup" data-type="horizontal">
				<form method='post'>
					<select name="estado" onchange="this.form.submit()">
						<option value='-1'>Estado</option>
						<?php echo func_select_estado_servicio(array("progress"=>"progress", "acepted"=>"acepted", "scheduled"=>"scheduled", "completed"=>"completed", "rejected"=>"rejected"), $_POST["estado"]);?>
					</select>
					<select name="categoria" onchange="this.form.submit()">
						<option value='-1'>Categoria</option>
						<?php echo func_select_tabla_id_denomination("services", $_POST["categoria"]);?>
					</select>
					<select name="sub_categoria" onchange="this.form.submit()"><?php echo func_select_sub_categorias($_POST["categoria"], $_POST["sub_categoria"]);?></select>
					<select name="ciudad" onchange="this.form.submit()">
						<option value='-1'>Ciudad</option>
						<?php echo func_select_tabla_id_denomination("cities", $_POST["ciudad"]);?>
					</select>
					<select name="zona" onchange="this.form.submit()"><?php echo func_select_sub_zonas($_POST["ciudad"], $_POST["zona"]);?></select>
					<div class="ui-input-text ui-body-inherit controlgroup-textinput ui-btn ui-shadow-inset" style="height:100%">
						<input type="text" name="buscador" value="<?php echo $_POST['buscador'];?>" placeholder="Buscar" required class="ui-btn " style="height:100%">
					</div>
					<input type="submit" value="Buscar" data-theme="b">
				</form>
			</div>
			<div data-role="controlgroup" data-type="horizontal">
				<form method='post'>
					<input type="submit" name="todos" value="Limpiar Filtros" class="ui-btn">
				</form>
			</div>
		</div>
		<div role="content" class="ui-content">
			<?php if($Query[0] != null):?>
				<table id="la_data_tabla" cellspacing="0">
					<thead>
						<tr valign="top">
							<?php  foreach($Query[0] as $key => $value):?>
								<?php if(!in_array($key, $Colum_No_Mostrar)): ?>
									<th><?php echo Traductor_Nombre_Columnas($key); ?></th>
								<?php endif;?>
							<?php endforeach; ?>
							<th>Editar</th>
						</tr>
					</thead>
					<tbody id="the-list">
						<?php 
						$alter = "";
						foreach ( $Query as $lista ):
							if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
						?>
						<tr valign="top" <?php echo $alter; ?>>
							<?php foreach($lista as $it => $va):?>
								<?php if(!in_array($it, $Colum_No_Mostrar)):?><td><?php echo $va;?></td><?php endif;?>
							<?php endforeach; ?>
							<td>
								<form method="post" data-role="controlgroup">
									<input type="hidden" name="id" value="<?php echo $lista->id_req;?>">
									<input type="submit" name="editar_servicio" value="Editar" class="btn_per" data-theme="b" data-role="none" style="background: #3f72a5; color: white;">
								</form>
							</td>
						</tr>
					<?php endforeach;?>
				</table>
			<?php endif;?>
		</div>
	</div>
	<?php
}
function page_servicios_editar_servicio(){
	//print_r($_POST);
	if( isset($_POST["es_usuario"]) ){
		$qs_update = "UPDATE requests SET ".Gen_String_Update($_POST, array("editar_servicio", "es_usuario"))." WHERE id='".$_POST["id"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["update_offer"]) ){
		$qs_update = "UPDATE offers SET ".Gen_String_Update($_POST, array("editar_servicio", "id", "id_offer", "update_offer"))." WHERE id='".$_POST["id_offer"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	//
	$Query = plugDB("SELECT * FROM requests WHERE id = '". $_POST["id"] ."'", "row");
	//
	$Str_q_cat = "
	SELECT s.id 
	FROM
	categories c
	LEFT JOIN services s ON c.service = s.id
	WHERE c.id = '".$Query->category."'";
	$Query_Cat = plugDB($Str_q_cat, "row");
	//
	$Str_q_reg = "
	SELECT s.id 
	FROM
	regions c
	LEFT JOIN cities s ON c.city = s.id
	WHERE c.id = '".$Query->region."'";
	$Query_Reg = plugDB($Str_q_reg, "row");
	//
	$Str_q_cliente = "SELECT b.* 
	FROM 
	customers a 
	LEFT JOIN users b ON a.user = b.id
	WHERE a.id = '".$Query->customer."'";
	$Query_Cus = plugDB($Str_q_cliente, "row");
	//
	$Str_Query_Offers = "SELECT * 
	FROM
	offers a
	WHERE a.request = '".$Query->id."'
	";
	$Str_Query_Offers2 = "SELECT * 
	FROM
	offers a
	LEFT JOIN
	(
		SELECT a.id as id_expert, b.id as id_user, b.name, b.email
		FROM 
		experts a LEFT JOIN users b ON a.user = b.id
	)b ON a.expert = b.id_expert
	WHERE a.request = '".$Query->id."'
	";
	$Query_Offers = plugDB($Str_Query_Offers, "result");
	$exclude_tabla_offers = array("expert", "request", "cost");
	//
	$Str_Query_imagenes = "SELECT * FROM request_images WHERE request = '".$Query->id."'";
	$Query_Imagenes = plugDB($Str_Query_imagenes, "result");
	//
	$Query_Chats = plugDB("SELECT * FROM messages WHERE request = '".$Query->id."'", "result");
	$exclude_tabla_chats = array();
	//
	$Query_Problemas = plugDB("SELECT p.id, p.user, p.request, pt.denomination as problem_type, p.problem, p.date FROM problems p LEFT JOIN problem_type pt ON p.problem_type=pt.id WHERE request = '".$Query->id."'", "result");
	$exclude_tabla_problemas = array();
	//
	$Query_cancel_cliente = plugDB("SELECT c.id, c.requests_id, ct.denomination as type, c.texto, c.date FROM cancel_request c LEFT JOIN cancellation_type ct ON c.type=ct.id WHERE c.requests_id = '".$Query->id."'", "result");
	//
	$alter_tabla_1 = "";
	?>
	<script type="text/javascript" >
		jQuery(document).on("mobileinit", function(){
			jQuery.mobile.ajaxEnabled = false;
		});
	</script>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.css"/>
	<script src="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.jqueryui.min.css">
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.jqueryui.min.js"></script>
	<script type="text/javascript" class="init">
		jQuery(document).ready(function() {
			<?php if( Count($Query_Request) > 0 ):?>
				//jQuery('#tabla_servicios').DataTable();
			<?php endif;?>
			
			<?php if( Count($Query_Calificaciones) > 0 ):?>
				//jQuery('#tabla_calificaciones').DataTable();
			<?php endif;?>

			jQuery('#wpfooter').remove();
			jQuery.mobile.page.prototype.options.keepNative = "select, input, textarea";
		} );
	</script>
	<style>
		.controlgroup-textinput{
		    padding-top:.22em;
		    padding-bottom:.22em;
		}
		.ui-title{
			color:white;
		}
		div.DataTables_sort_wrapper span {
			right: -10px !important;
		}

		.fix_table .ui-btn-inner{
			padding: .55em 11px;
		}
		.ui-select .ui-icon{
			background-image: url(https://code.jquery.com/mobile/1.2.1/images/icons-18-white.png) !important;
			background-position: -217px 50% !important;
		}
		.formulario{
			font:inherit !important;
			font-size:16px !important;
		}

		.ui-content{
			overflow-x : scroll !important;
		}

		.btn_form{
			width : 15%;
			margin: auto
		}

		@media(min-width : 80em){
			.li_form{
				display : flex !important;
			}

			.li_form .label_form{
				width : 20%
			}

			.li_form .inp_form{
				width : 80%
			}


		}

		.btn_per{
			border: none;
			width: 150px;
			padding: 10px;
			box-shadow: 0 1px 4px rgba(0,0,0,.3);
			outline: none;
			margin-top: 10px;
			font-weight: bold;
			border-radius: 6px;
			font-size : 0.8rem;
		}


		
		.dataTables_wrapper{
			overflow-x : scroll;
		}

		ul.flex li{
			display: flex !important;
			justify-content: space-around;
			width: 150%;
			align-items: center;
		} 

		form.flex{
			display: flex !important;
			justify-content: space-around;
			align-items: center;
			width : 140% !important;
		}


	</style>

	<div data-role="page">
		<div data-role="header" data-theme="b">
			<h1>Servicio</h1>
		</div>

		<?php if( Count($Query_cancel_cliente) > 0 ):?>
			<h2>Cancelado por el usuario</h2>
			<table class="wp-list-table widefat" cellspacing="0">
				<thead>
					<tr valign="top">
						<?php foreach($Query_cancel_cliente[0] as $key => $value):?>
						<?php if(!in_array($key, $exclude_tabla_problemas)):?><th><?php echo $key; ?></th><?php endif;?>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody id="the-list">
					<?php 
					$alter = "";
					foreach ( $Query_cancel_cliente as $lista ):
						if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
					?>
					<tr valign="top" <?php echo $alter; ?>>
						<?php foreach($lista as $key => $va):?>
							<?php if(!in_array($key, $exclude_tabla_problemas)):?>
								<td><?php echo $va;?></td>
							<?php endif;?>
						<?php endforeach; ?>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
		<?php endif;?>

		<div role="content" class="ui-content">
			<!-- <div data-role="collapsible">
			    <h4>template</h4>
			    <p>
					<form method="post" name="la_data" class="formulario">
						<ul data-role="listview" data-inset="true">
							<li class="ui-field-contain">
								<label for="name2">Text Input:</label>
								<input type="text" name="name2" id="name2" value="" data-clear-btn="true">
							</li>
							
							<li class="ui-body ui-body-b">
								<fieldset class="ui-grid-a">
									<div class="ui-block-a"></div>
									<div class="ui-block-b">
										<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
										<input type="hidden" name="editar_cliente" value="ok">
										<input type="submit" name="es_usuario" value="Guardar" data-role="button" data-theme="b">
									</div>
								</fieldset>
							</li>
						</ul>
					</form>
				</p>
			</div> -->

			<div data-role="collapsible">
			    <h4>Información</h4>
			    <p>
					<form method="post" action="admin.php?page=clientes" target="_blank">
						<ul data-role="listview" data-inset="true">
							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label for="">Cliente:</label>
								</div>

								<div class="inp_form">
									<input type="hidden" name="id" value="<?php echo $Query_Cus->id;?>">
									<input type="hidden" name="editar_cliente" value="ok">
									<input type="submit" value="<?php echo $Query_Cus->name;?>" class="button action">
								</div>
							</li>
						</ul>			
					</form>
					<form method="post" name="la_data">
						<ul data-role="listview" data-inset="true">
							<?php 
							echo func_tabla_form_fieldcontainer(
								$Query, 
								array("category", "region", "customer", "cancellation_type", "status", "emergency", "registry_date", "start_date", "hour", "completed_date"),
								array("id")
							);
							?>
							<li class="ui-field-contain">
								<label for="">Cliente:</label>
							</li>
							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label for="">completed_date:</label>
								</div>

								<div class="inp_form">
									<input type="datetime-local" name="completed_date" value="<?php echo (new DateTime($Query->completed_date))->format("Y-m-d\\TH:i:s");?>">
								</div>
							</li>
							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label for="">Categoria:</label>
								</div>

								<div class="inp_form">
									<select id="select_service" onchange="Do_Select_Category();">
										<?php echo func_select_tabla_id_denomination("services", $Query_Cat->id);?>
									</select>
								</div>
								
							</li>

							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label for="">Sub Categoria:</label>
								</div>

								<div class="inp_form">
									<select name="category" id="select_category">
										<?php echo func_select_sub_categorias($Query_Cat->id, $Query->category);?>
									</select>
								</div>
							</li>

							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label for="">Ciudad:</label>
								</div>

								<div class="inp_form">
									<select id="select_ciudad" onchange="Do_Select_Zona();">
										<?php echo func_select_tabla_id_denomination("cities", $Query_Reg->id);?>	
									</select>
								</div>
							</li>

							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label for="">Zona:</label>
								</div>

								<div class="inp_form">
									<select id="select_zona" name="region">
										<?php echo func_select_sub_zonas($Query_Reg->id, $Query->region);?>
									</select>
								</div>
							</li>

							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label for="">Estado:</label>
								</div>

								<div class="inp_form">
									<select name="status">
										<?php echo func_select_estado_servicio(
											array("progress"=>"progress", "acepted"=>"acepted", "scheduled"=>"scheduled", "completed"=>"completed", "rejected"=>"rejected"), 
											$Query->status
										);?>
									</select>
								</div>
							</li>

							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label for="">Fecha Registro: </label>
								</div>

								<div class="inp_form">
									<?php echo $Query->registry_date;?>
								</div>
							</li>

							<li class="ui-field-contain li_form">
								<div class="label_form">
									<label for="">Costo del servicio (Fixcoins):</label>
								</div>

								<div class="inp_form">
									<?php $q_costo = plugDB("SELECT cost FROM categories WHERE id = '".$Query->category."'", "row"); echo $q_costo->cost;?>
								</div>
								
							</li>

							<li class="ui-body ui-body-b">
								<fieldset class="">
										<div class="btn_form">
											<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
											<input type="hidden" name="editar_servicio" value="ok">
											<input type="submit" name="es_usuario" value="Guardar" class="button action">
										</div>
								</fieldset>
							</li>
							
						</ul>		
					</form>
				</p>
			</div>

			<div data-role="collapsible">
			    <h4>Imagenes</h4>
			    <p>
					<?php if( Count($Query_Imagenes) > 0 ):?>
						<?php foreach($Query_Imagenes as $item_imagen):?>
							<a href="<?php echo URL_BASE;?>/uploads/requests/<?php echo $item_imagen->image;?>" target="_blank"><img src="<?php echo URL_BASE;?>/uploads/requests/<?php echo $item_imagen->image;?>" height="200"></a>
						<?php endforeach;?>
					<?php endif;?>
				</p>
			</div>

			<div data-role="collapsible">
			    <h4>Ofertas</h4>
			    <p>
					<?php if( Count($Query_Offers) > 0 ):?>

					
					<ul data-role="listview" data-inset="true" class="flex">
						<li valign="top" class="ui-body ui-body-b">
						<div>Experto</div>
							<?php foreach($Query_Offers[0] as $key => $value):?>
								<?php if(!in_array($key, $exclude_tabla_offers)):?>
									<div><?php echo Traductor_Nombre_Columnas($key); ?></div>
								<?php endif;?>
							<?php endforeach; ?>
							<div>Acciones</div>
						</li>
					</ul>



					<ul id="the-list" data-role="listview" data-inset="true" class="flex">
						<?php 
						$alter = "";
						foreach ( $Query_Offers as $lista ):
							if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
							$Query_Cancel_Offer = plugDB("SELECT c.texto, c.date, ct.denomination as type FROM cancel_offert c LEFT JOIN cancellation_type ct ON c.type=ct.id WHERE offert = '".$lista->id."'", "result");
						?>
						<li valign="top" <?php echo $alter; ?> class="ui-body ui-body-b flex" >
							<div>
								<?php Gen_Btn_Experto($lista->expert);?>
							</div>
							<form method="post" class="flex">
							<?php foreach($lista as $it => $va):?>
								<?php if(!in_array($it, $exclude_tabla_offers)):?>
									<?php if($it == "start_date"):?>
										<div>
											<input type="datetime-local" name="start_date" value="<?php echo (new DateTime($va))->format("Y-m-d\\TH:i:s");?>">
										</div>
									<?php elseif($it == "completed_date"):?>
										<div>
											<input type="datetime-local" name="completed_date" value="<?php echo (new DateTime($va))->format("Y-m-d\\TH:i:s");?>">
										</div>
									<?php elseif($it == "status"):?>
										<div>
											<select name="status">
												<?php echo func_select_estado_servicio(
													array("progress"=>"progress", "acepted"=>"acepted", "scheduled"=>"scheduled", "completed"=>"completed", "rejected"=>"rejected"), 
													$va
												);?>
											</select>
										</div>
									<?php elseif($it == "collaborator"):?>
										<div>
											<select name="collaborator">
												<?php echo Select_ColByExpert($lista->expert, $lista->collaborator);?>
											</select>
										</div>
									<?php elseif($it == "hour"):?>
										<div>
											<input type="time" name="hour" value="<?php echo $va;?>">
										</div>
									<?php else:?>
										<div>
											<?php echo $va;?>
										</div>
									<?php endif;?>
								<?php endif;?>
							<?php endforeach; ?>
							<div>
								<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
								<input type="hidden" name="editar_servicio" value="ok">
								<input type="hidden" name="id_offer" value="<?php echo $lista->id;?>">
								<input type="submit" name="update_offer" value="Guardar" class="btn_per" data-role="none"  style="background: #3f72a5; color: white;">
							</div>
							</form>
						</li>
						<?php if( Count($Query_Cancel_Offer) > 0 ):?>
							<?php 
							foreach ( $Query_Cancel_Offer as $lista_cancel ):
							?>
						<li class="ui-body ui-body-b flex">
							<div><h3>Cancelado por el Experto</h3></div>
							<div><?php echo $lista_cancel->texto;?></div>
							<div><?php echo $lista_cancel->date;?></div>
							<div><?php echo $lista_cancel->type;?></div>
						</li>
						<?php endforeach; ?>
						<?php endif;?>
						<?php endforeach;?>
					</ul>
					<?php endif;?>
				</p>
			</div>

			<div data-role="collapsible">
			    <h4>Chats Activos</h4>
			    <p>
					<?php if( Count($Query_Chats) > 0 ):?>
					<table class="wp-list-table widefat" cellspacing="0">
						<thead>
							<tr valign="top">
								<?php foreach($Query_Chats[0] as $key => $value):?>
								<?php if(!in_array($key, $exclude_tabla_chats)):?><th><?php echo $key; ?></th><?php endif;?>
								<?php endforeach; ?>
							</tr>
						</thead>
						<tbody id="the-list">
							<?php 
							$alter = "";
							foreach ( $Query_Chats as $lista ):
								if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
							?>
							<tr valign="top" <?php echo $alter; ?>>
								<?php foreach($lista as $key => $va):?>
									<?php if(!in_array($key, $exclude_tabla_chats)):?>

										<?php if($key == "de" || $key == "para"):?>
											<td><?php Gen_Btn_By_User($va, true);?></td>
										<?php else:?>
											<td><?php echo $va;?></td>
										<?php endif;?>
									
									
									<?php endif;?>
								<?php endforeach; ?>
							</tr>
							<?php endforeach;?>
						</tbody>
					</table>
					<?php endif;?>
				</p>
			</div>

			<div data-role="collapsible">
			    <h4>Problemas</h4>
			    <p>
					<?php if( Count($Query_Problemas) > 0 ):?>
					<table class="wp-list-table widefat" cellspacing="0">
						<thead>
							<tr valign="top">
								<?php foreach($Query_Problemas[0] as $key => $value):?>
								<?php if(!in_array($key, $exclude_tabla_problemas)):?><th><?php echo $key; ?></th><?php endif;?>
								<?php endforeach; ?>
							</tr>
						</thead>
						<tbody id="the-list">
							<?php 
							$alter = "";
							foreach ( $Query_Problemas as $lista ):
								if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
							?>
							<tr valign="top" <?php echo $alter; ?>>
								<?php foreach($lista as $key => $va):?>
									<?php if(!in_array($key, $exclude_tabla_problemas)):?>

										<?php if($key == "user"):?>
											<td><?php Gen_Btn_By_User($va, true);?></td>
										<?php else:?>
											<td><?php echo $va;?></td>
										<?php endif;?>
									
									<?php endif;?>
								<?php endforeach; ?>
							</tr>
							<?php endforeach;?>
						</tbody>
					</table>
					<?php endif;?>
				</p>
			</div>

		</div>
		
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
	<script type="text/javascript">
	function Do_Select_Category(){
		//alert( $("#select_service").val() );
		jQuery.ajax({
			url:"<?php echo home_url('pagina_querys');?>", 
			method: "POST",
			data: { select_category : "ok", categoria : $("#select_service").val() }
		}).done(function(html){
			//alert(html);
			$("#select_category").html(html);
		}).fail(function() {
			alert( "error" );
		});
	}
	function Do_Select_Zona(){
		//alert( $("#select_service").val() );
		jQuery.ajax({
			url:"<?php echo home_url('pagina_querys');?>", 
			method: "POST",
			data: { select_zona : "ok", categoria : $("#select_ciudad").val() }
		}).done(function(html){
			//alert(html);
			$("#select_zona").html(html);
		}).fail(function() {
			alert( "error" );
		});
	}
	</script>
	<?php
}
//
function page_transacciones_planes(){
	$WHERE = "";
	if(isset($_POST["buscador"])){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "entidad.nombre LIKE '%".$_POST["buscador"]."%' OR entidad.correo LIKE '%".$_POST["buscador"]."%'";
	}

	$tabla = "transaction_plan";
	$Qstr = "SELECT entidad.nombre, entidad.correo, entidad.nombre, entidad.documento, entidad.nit, p.denomination as plan, tran.date_registry, tran.date_response, tran.transaction_id, p.price as valor, sta.denomination as estado
	FROM 
	".$tabla." tran
	LEFT JOIN 
	(
		SELECT e.id as id_expert, u.name as nombre, u.email as correo, e.number as documento, e.nit
		FROM experts e LEFT JOIN users u ON e.user=u.id
	)entidad ON tran.expert = entidad.id_expert
	LEFT JOIN plans p ON tran.plan = p.id
	LEFT JOIN status_epayco sta ON sta.id = tran.status
	$WHERE
	";
	$Query = plugDB($Qstr, "result");
	$Colum_No_Mostrar = array("transaction_id", "valor");
	?>

	<script type="text/javascript" >
		jQuery(document).on("mobileinit", function(){
			jQuery.mobile.ajaxEnabled = false;
		});
	</script>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.css"/>
	<script src="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.jqueryui.min.css">
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.jqueryui.min.js"></script>
	<script type="text/javascript" class="init">
		jQuery(document).ready(function() {
			jQuery('#la_data_tabla').DataTable();
			jQuery('#wpfooter').remove();
		} );
	</script>
	<style>
		.controlgroup-textinput{
		    padding-top:.22em;
		    padding-bottom:.22em;
		}
		.ui-title{
			color:white;
		}
		div.DataTables_sort_wrapper span {
			right: -10px !important;
		}

		.fix_table .ui-btn-inner{
			padding: .55em 11px;
		}
		.ui-select .ui-icon{
			background-image: url(https://code.jquery.com/mobile/1.2.1/images/icons-18-white.png) !important;
			background-position: -217px 50% !important;
		}
		[id="formulario"] *{
			font:inherit !important;
			font-size:16px !important;
		}

		table thead th{
			text-transform : capitalize
		}
	</style>

	<div data-role="page">
		<div data-role="header" data-theme="b">
			<h1>Transacciones -> Planes (<?php echo Count($Query);?> Registros)</h1>
		</div>

		<div data-role="header" data-theme="c">
			<div data-role="controlgroup" data-type="horizontal">
				<form action='<?php bloginfo('template_url'); ?>/admin_db/export_xls.php' target='_blank' method='post'>
					<input type='hidden' name='type' value='transacciones_planes'/>
					<input type='hidden' name='xhr' value='<?php echo base64_encode($Qstr); ?>'/>
					<input type="submit" id="exportar" name="exportar" value="Exportar a excel" class="ui-btn" data-theme="b" />
				</form>
			</div>
			<div data-role="controlgroup" data-type="horizontal" style="width:10px">
			</div>
			<div data-role="controlgroup" data-type="horizontal">
				<form method='post'>

					<div class="ui-input-text ui-body-inherit controlgroup-textinput ui-btn ui-shadow-inset" style="height:100%">
						<input type="text" name="buscador" value="<?php echo $_POST['buscador'];?>" placeholder="Buscar" required class="ui-btn " style="height:100%; margin-top: 0px">
					</div>
					<input type="submit" data-role="none" value="Buscar" class="ui-btn" data-theme="b" style="margin-top: 0px; margin-left: 15px; padding: 8px; background: #5a91c0; border: none; color: white;">

				</form>
			</div>
			<div data-role="controlgroup" data-type="horizontal">
				<form method='post'>
					<input type="submit" data-role="none" name="todos" value="Limpiar Filtros" style="mmargin-top: -5px; margin-left: 15px; padding: 8px; background: #F5F5F5; border: none; color: #23282d;">
				</form>
			</div>
		</div>

		<div role="content" class="ui-content">
			<?php if($Query[0] != null):?>
				<table id="la_data_tabla" cellspacing="0">

					<thead>
						<tr valign="top">
							<?php  foreach($Query[0] as $key => $value):?>
								<?php if(!in_array($key, $Colum_No_Mostrar)): ?>
									<th><?php echo Traductor_Nombre_Columnas($key); ?></th>
								<?php endif;?>
							<?php endforeach; ?>
						</tr>
					</thead>


					<tbody id="the-list">
						<?php 
						$alter = "";
						foreach ( $Query as $lista ):
							if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
						?>

						<tr valign="top" <?php echo $alter; ?>>
							<?php foreach($lista as $it => $va):?>
								<?php if(!in_array($it, $Colum_No_Mostrar)):?>
									<td><?php echo $va;?></td>
								<?php endif;?>
							<?php endforeach; ?>
						</tr>

					<?php endforeach;?>
				</table>
			<?php endif;?>
		</div>

	</div>
	<?php
}
function page_transacciones_fixcoins(){
	$WHERE = "";
	if(isset($_POST["buscador"])){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "entidad.nombre LIKE '%".$_POST["buscador"]."%' OR entidad.correo LIKE '%".$_POST["buscador"]."%'";
	}

	$tabla = "transaction_fixcoin";
	$Qstr = "SELECT entidad.nombre, entidad.correo, entidad.nombre, entidad.documento, entidad.nit, fp.cant as fixcoins, tran.date_registry, tran.date_response, tran.transaction_id, fp.price as valor, sta.denomination as estado
	FROM 
	".$tabla." tran
	LEFT JOIN 
	(
		SELECT e.id as id_expert, u.name as nombre, u.email as correo, e.number as documento, e.nit
		FROM experts e LEFT JOIN users u ON e.user=u.id
	)entidad ON tran.expert = entidad.id_expert
	LEFT JOIN fixcoins_package fp ON tran.fixcoins_package = fp.id
	LEFT JOIN status_epayco sta ON sta.id = tran.status
	$WHERE
	";
	$Query = plugDB($Qstr, "result");
	?>

	<script type="text/javascript" >
		jQuery(document).on("mobileinit", function(){
			jQuery.mobile.ajaxEnabled = false;
		});
	</script>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.css"/>
	<script src="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.jqueryui.min.css">
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.jqueryui.min.js"></script>
	<script type="text/javascript" class="init">
		jQuery(document).ready(function() {
			jQuery('#la_data_tabla').DataTable();
			jQuery('#wpfooter').remove();
		} );
	</script>
	<style>
		.controlgroup-textinput{
		    padding-top:.22em;
		    padding-bottom:.22em;
		}
		.ui-title{
			color:white;
		}
		div.DataTables_sort_wrapper span {
			right: -10px !important;
		}

		.fix_table .ui-btn-inner{
			padding: .55em 11px;
		}
		.ui-select .ui-icon{
			background-image: url(https://code.jquery.com/mobile/1.2.1/images/icons-18-white.png) !important;
			background-position: -217px 50% !important;
		}
		[id="formulario"] *{
			font:inherit !important;
			font-size:16px !important;
		}

		table thead th{
			text-transform : capitalize
		}
	</style>

	<div data-role="page">
		<div data-role="header" data-theme="b">
			<h1>Transacciones -> Fixcoins (<?php echo Count($Query);?> Registros)</h1>
		</div>

		<div data-role="header" data-theme="c">
			<div data-role="controlgroup" data-type="horizontal">
				<form action='<?php bloginfo('template_url'); ?>/admin_db/export_xls.php' target='_blank' method='post'>
					<input type='hidden' name='type' value='transacciones_fixcoins'/>
					<input type='hidden' name='xhr' value='<?php echo base64_encode($Qstr); ?>'/>
					<input type="submit" id="exportar" name="exportar" value="Exportar a excel" class="ui-btn" data-theme="b" />
				</form>
			</div>
			<div data-role="controlgroup" data-type="horizontal" style="width:10px">
			</div>
			<div data-role="controlgroup" data-type="horizontal">
				<form method='post'>
					<div class="ui-input-text ui-body-inherit controlgroup-textinput ui-btn ui-shadow-inset" style="height:100%">
					<input type="text" name="buscador" value="<?php echo $_POST['buscador'];?>" placeholder="Buscar" required class="ui-btn " style="height:100%; margin-top: 0">
				</div>
				<input type="submit" data-role="none" name="crear_categoria" value="Buscar" class="ui-btn" data-theme="b" style="margin-top: 0px; margin-left: 15px; padding: 8px; background: #5a91c0; border: none; color: white;">
				</form>
			</div>
			<div data-role="controlgroup" data-type="horizontal">
				<form method='post'>
					<input type="submit" data-role="none" name="todos" value="Limpiar Filtros" style="mmargin-top: -5px; margin-left: 15px; padding: 8px; background: #F5F5F5; border: none; color: #23282d;">
				</form>
			</div>
		</div>

		<div role="content" class="ui-content">
			<?php if($Query[0] != null):?>
				<table id="la_data_tabla" cellspacing="0">
					<thead>
						<tr valign="top">
							<?php foreach($Query[0] as $key => $value):?>
								<th><?php echo Traductor_Nombre_Columnas($key); ?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody id="the-list">
						<?php 
						$alter = "";
						foreach ( $Query as $lista ):
							if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
						?>
						<tr valign="top" <?php echo $alter; ?>>
							<?php foreach($lista as $it => $va):?>
								<td><?php echo $va;?></td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach;?>
				</table>
			<?php endif;?>
		</div>

	</div>
	<?php
}
//
function page_atencion_cliente(){
	if( isset($_POST["editar_atencion"]) ){
		page_atencion_cliente_editar();
		return;
	}
	$WHERE = "";
	if(isset($_POST["buscador"])){
		if($WHERE == ""){
			$WHERE .= "WHERE ";
		}else{
			$WHERE .= " AND ";
		}
		$WHERE .= "u.name LIKE '%".$_POST["buscador"]."%' OR u.email LIKE '%".$_POST["buscador"]."%'";
	}
	//
	$Qstr = "SELECT cs.id, u.name, u.email, cs.date_registry, ty.denomination as tipo, cs.description, st.denomination as status
	FROM customer_support cs
	LEFT JOIN users u ON cs.user = u.id	
	LEFT JOIN type_customer_support ty ON cs.type_customer_support=ty.id
	LEFT JOIN status_customer_support st ON cs.status = st.id
	$WHERE
	ORDER BY cs.id DESC
	";
	$Query = plugDB($Qstr, "result");
	?>
	<script type="text/javascript" >
		jQuery(document).on("mobileinit", function(){
			jQuery.mobile.ajaxEnabled = false;
		});
	</script>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.css"/>
	<script src="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.jqueryui.min.css">
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.jqueryui.min.js"></script>
	<script type="text/javascript" class="init">
		jQuery(document).ready(function() {
			jQuery('#la_data_tabla').DataTable();
			jQuery('#wpfooter').remove();
		} );
	</script>
	<style>
		.controlgroup-textinput{
		    padding-top:.22em;
		    padding-bottom:.22em;
		}
		.ui-title{
			color:white;
		}
		div.DataTables_sort_wrapper span {
			right: -10px !important;
		}

		.fix_table .ui-btn-inner{
			padding: .55em 11px;
		}
		.ui-select .ui-icon{
			background-image: url(https://code.jquery.com/mobile/1.2.1/images/icons-18-white.png) !important;
			background-position: -217px 50% !important;
		}
		[id="formulario"] *{
			font:inherit !important;
			font-size:16px !important;
		}

		table thead th{
			text-transform : capitalize
		}

		.btn_per{
			border: none;
			width: 150px;
			padding: 10px;
			box-shadow: 0 1px 4px rgba(0,0,0,.3);
			outline: none;
			margin-top: 10px;
			font-weight: bold;
			border-radius: 6px;
			font-size : 0.8rem;
		}
	</style>

	<div data-role="page">
		<div data-role="header" data-theme="b">
			<h1>Atencion al Cliente (<?php echo Count($Query);?> Registros)</h1>
		</div>

		<div data-role="header" data-theme="c">
			<div data-role="controlgroup" data-type="horizontal">
				<form action='<?php bloginfo('template_url'); ?>/admin_db/export_xls.php' target='_blank' method='post'>
					<input type='hidden' name='type' value='atencion_al_cliente'/>
					<input type='hidden' name='xhr' value='<?php echo base64_encode($Qstr); ?>'/>
					<input type="submit" id="exportar" name="exportar" value="Exportar a excel" class="ui-btn" data-theme="b" />
				</form>
			</div>
			<div data-role="controlgroup" data-type="horizontal" style="width:10px">
			</div>
			<div data-role="controlgroup" data-type="horizontal">
				<form method='post'>
					<div class="ui-input-text ui-body-inherit controlgroup-textinput ui-btn ui-shadow-inset" style="height:100%">
						<input type="text" name="buscador" value="<?php echo $_POST['buscador'];?>" placeholder="Buscar" required class="ui-btn " style="height:100%; margin-top: 0px">
					</div>
					<input type="submit" data-role="none" name="crear_categoria" value="Buscar" class="ui-btn" data-theme="b" style="margin-top: 0px; margin-left: 15px; padding: 8px; background: #5a91c0; border: none; color: white;">
				</form>
			</div>
			<div data-role="controlgroup" data-type="horizontal">
				<form method='post'>
					<input type="submit" data-role="none" name="todos" value="Limpiar Filtros" style="mmargin-top: -5px; margin-left: 15px; padding: 8px; background: #F5F5F5; border: none; color: #23282d;">
				</form>
			</div>
		</div>

		<div role="content" class="ui-content">
			<?php if($Query[0] != null):?>
				<table id="la_data_tabla" cellspacing="0">
					<thead>
						<tr valign="top">
							<?php foreach($Query[0] as $key => $value):?>
								<th><?php echo Traductor_Nombre_Columnas($key); ?></th>
							<?php endforeach; ?>
							<th>Editar</th>
						</tr>
					</thead>
					<tbody id="the-list">
						<?php 
						$alter = "";
						foreach ( $Query as $lista ):
							if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
						?>
						<tr valign="top" <?php echo $alter; ?>>
							<?php foreach($lista as $it => $va):?>
								<td><?php echo $va;?></td>
							<?php endforeach; ?>
							<td>
								<form method="post">
									<input type="hidden" name="id" value="<?php echo $lista->id;?>">
									<input type="submit" name="editar_atencion" value="Editar" class="btn_per" data-role="none" style="background: #3f72a5; color: white; margin-left : 45px">
								</form>
							</td>
						</tr>
					<?php endforeach;?>
				</table>
			<?php endif;?>
		</div>
	</div>
	<?php
}
function page_atencion_cliente_editar(){
	//
	if( isset($_POST["update_atencion"]) ){
		$qs_update = "UPDATE customer_support SET ".Gen_String_Update($_POST, array("update_atencion", "editar_atencion", "id"))." WHERE id='".$_POST["id"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	//
	if( isset($_POST["add_descripcion"]) ){
		$qs_update = "INSERT INTO descriptions_customer_support (description, customer_support) VALUES ('".$_POST["description"]."', '".$_POST["id"]."')";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["update_descripcion"]) ){
		$qs_update = "UPDATE descriptions_customer_support SET ".Gen_String_Update($_POST, array("editar_atencion", "update_descripcion", "id", "id_item"))." WHERE id='".$_POST["id_item"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["del_descripcion"]) ){
		$qs_update = "DELETE FROM descriptions_customer_support WHERE id = '".$_POST["id_item"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["add_respuesta"]) ){
		$qs_update = "INSERT INTO response_customer_support (response, customer_support) VALUES ('".$_POST["response"]."', '".$_POST["id"]."')";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["update_respuesta"]) ){
		$qs_update = "UPDATE response_customer_support SET ".Gen_String_Update($_POST, array("editar_atencion", "update_respuesta", "id", "id_item"))." WHERE id='".$_POST["id_item"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["del_respuesta"]) ){
		$qs_update = "DELETE FROM response_customer_support WHERE id = '".$_POST["id_item"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	//
	$Query = plugDB("SELECT * FROM customer_support WHERE id = '".$_POST["id"]."'", "row");
	$Query_descripcion = plugDB("SELECT * FROM descriptions_customer_support WHERE customer_support = '".$_POST["id"]."'", "result");
	$Query_respuesta = plugDB("SELECT * FROM response_customer_support WHERE customer_support = '".$_POST["id"]."'", "result");
	?>
	<div class="wrap">

	<h2>Detalles</h2>
		<table class="wp-list-table widefat" cellspacing="0">
			<thead>
				<tr valign="top">
					<th>Campo</th>
					<th>Valor</th>
				</tr>
			</thead>
			<tbody id="the-list">
				<tr>
					<td>Cliente</td>
					<td><?php Gen_Btn_Cliente($Query->user);?></td>
				<tr>
				<form method="post">
					<?php 
					echo func_tabla_form(
						$Query, 
						array("type_customer_support", "status", "hide"),
						array("id","user","date_registry")
					);
					?>
					<tr class="alternate">
						<td>type_customer_support</td>
						<td>
							<select name="type_customer_support">
								<?php echo func_select_tabla_id_denomination("type_customer_support", $Query->type_customer_support);?>
							</select>
						</td>
					</tr>
					<tr>
						<td>status</td>
						<td>
							<select name="status">
								<?php echo func_select_tabla_id_denomination("status_customer_support", $Query->status);?>
							</select>
						</td>
					</tr>
					<tr class="alternate">
						<td>
							<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
							<input type="hidden" name="editar_atencion" value="ok">
							<input type="submit" name="update_atencion" value="Guardar" class="button action">
						</td>
						<td></td>
					</tr>
				</form>
			</tbody>
		</table>

		<h2>Descripcion</h2>
		<table class="wp-list-table widefat" cellspacing="0">
			<thead>
				<tr valign="top">
					<?php foreach($Query_descripcion[0] as $key => $value):?>
						<th><?php echo $key; ?></th>
					<?php endforeach; ?>
					<th>Acciones</th>
				</tr>
			</thead>
			<tbody id="the-list">
				<?php 
				$alter = "";
				foreach ( $Query_descripcion as $lista ):
					if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
				?>
				<form method="post">
					<tr valign="top" <?php echo $alter; ?>>
						<td><?php echo $lista->id;?></td>
						<td><input type="text" name="description" value="<?php echo $lista->description;?>" required></td>
						<td><?php echo $lista->customer_support;?></td>
						<td><?php echo $lista->date_registry;?></td>
						<td>
							<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
							<input type="hidden" name="editar_atencion" value="ok">
							<input type="hidden" name="id_item" value="<?php echo $lista->id;?>">
							<input type="submit" name="update_descripcion" value="Actualizar" class="button action">
							<input type="submit" name="del_descripcion" value="Borrar" class="button action">
						</td>
					</tr>
				</form>
				<?php endforeach;?>
				<form method="post">
					<tr>
						<td><input type="text" name="description" required></td>
						<td>
							<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
							<input type="hidden" name="editar_atencion" value="ok">
							<input type="submit" name="add_descripcion" value="Crear" class="button action">
						</td>
					</tr>
				</form>
			</tbody>
		</table>

		<h2>Respuesta</h2>
		<table class="wp-list-table widefat" cellspacing="0">
			<thead>
				<tr valign="top">
					<?php foreach($Query_respuesta[0] as $key => $value):?>
						<th><?php echo $key; ?></th>
					<?php endforeach; ?>
					<th>Acciones</th>
				</tr>
			</thead>
			<tbody id="the-list">
				<?php 
				$alter = "";
				foreach ( $Query_respuesta as $lista ):
					if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
				?>
				<form method="post">
					<tr valign="top" <?php echo $alter; ?>>
						<td><?php echo $lista->id;?></td>
						<td><input type="text" name="response" value="<?php echo $lista->response;?>" required></td>
						<td><?php echo $lista->customer_support;?></td>
						<td><?php echo $lista->date_registry;?></td>
						<td>
							<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
							<input type="hidden" name="editar_atencion" value="ok">
							<input type="hidden" name="id_item" value="<?php echo $lista->id;?>">
							<input type="submit" name="update_respuesta" value="Actualizar" class="button action">
							<input type="submit" name="del_respuesta" value="Borrar" class="button action">
						</td>
					</tr>
				</form>
				<?php endforeach;?>
				<form method="post">
					<tr>
						<td><input type="text" name="response" required></td>
						<td>
							<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
							<input type="hidden" name="editar_atencion" value="ok">
							<input type="submit" name="add_respuesta" value="Crear" class="button action">
						</td>
					</tr>
				</form>
			</tbody>
		</table>
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
	<script type="text/javascript">
	</script>
	<?php
}
//
function page_referidos(){
	if( isset($_POST["editar_atencion"]) ){
		page_atencion_cliente_editar();
		return;
	}
	//
	$Qstr = "SELECT enti.id_expert as experto, enti.email, e_b.coupon, e_b.id as experto_redimio
		FROM experts e_b
		INNER JOIN 
		(
			SELECT e.id as id_expert, u.email, u.id as id_user, u.code FROM users u INNER JOIN experts e ON u.id = e.user 
		)enti ON e_b.coupon = concat(enti.code, enti.id_user)
		WHERE e_b.coupon IS NOT NULL 
		ORDER BY e_b.coupon
	";
	$Query = plugDB($Qstr, "result");
	?>
	<script type="text/javascript" >
		jQuery(document).on("mobileinit", function(){
			jQuery.mobile.ajaxEnabled = false;
		});
	</script>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.css"/>
	<script src="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.jqueryui.min.css">
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.jqueryui.min.js"></script>
	<script type="text/javascript" class="init">
		jQuery(document).ready(function() {
			jQuery('#la_data_tabla').DataTable();
			jQuery('#wpfooter').remove();
		} );
	</script>
	<style>
		.controlgroup-textinput{
		    padding-top:.22em;
		    padding-bottom:.22em;
		}
		.ui-title{
			color:white;
		}
		div.DataTables_sort_wrapper span {
			right: -10px !important;
		}

		.fix_table .ui-btn-inner{
			padding: .55em 11px;
		}
		.ui-select .ui-icon{
			background-image: url(https://code.jquery.com/mobile/1.2.1/images/icons-18-white.png) !important;
			background-position: -217px 50% !important;
		}
		[id="formulario"] *{
			font:inherit !important;
			font-size:16px !important;
		}
	</style>

	<div data-role="page">
		<div data-role="header" data-theme="b">
			<h1>Cupones de Referidos (<?php echo Count($Query);?> Registros)</h1>
		</div>
		<div data-role="header" data-theme="c">
			<div data-role="controlgroup" data-type="horizontal">
				<form action='<?php bloginfo('template_url'); ?>/admin_db/export_xls.php' target='_blank' method='post'>
					<input type='hidden' name='type' value='cupones'/>
					<input type='hidden' name='xhr' value='<?php echo base64_encode($Qstr); ?>'/>
					<input type="submit" id="exportar" name="exportar" value="Exportar a excel" class="ui-btn" data-theme="b" />
				</form>
			</div>
		</div>
		<div role="content" class="ui-content">
			<?php if($Query[0] != null):?>
				<table id="la_data_tabla" cellspacing="0">
					<thead>
						<tr valign="top">
							<?php foreach($Query[0] as $key => $value):?>
								<th><?php echo $key; ?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody id="the-list">
						<?php 
						$alter = "";
						foreach ( $Query as $lista ):
							if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
						?>
						<tr valign="top" <?php echo $alter; ?>>
							<?php foreach($lista as $it => $va):?>
								<?php if($it == "experto_redimio" || $it == "experto"):?>
									<td><?php echo Gen_Btn_Experto($va);?></td>
								<?php else:?>
									<td><?php echo $va;?></td>
								<?php endif;?>
								
							<?php endforeach; ?>
							
						</tr>
					<?php endforeach;?>
				</table>
			<?php endif;?>
		</div>
	</div>

	<?php
}
//
function page_reporte_clientes(){
	if( isset($_POST["editar_atencion"]) ){
		page_atencion_cliente_editar();
		return;
	}
	//
	$Qstr = "select exp.id, us.name, us.email,
	(select count(ofr.id) from offers ofr where(ofr.expert= exp.id and ofr.status= 'completed') ) as trabajos_culminados,
	(select sum(cat.cost) from offers ofr inner join requests req on(ofr.request= req.id)
	  inner join categories cat on(req.category= cat.id) where(ofr.expert= exp.id )) as gasto_fixcoin,
	(select count(ofr.id) from offers ofr where (ofr.expert= exp.id and (ofr.status= 'completed' or ofr.status= 'accepted'))) as ofertas_aceptadas,
	(select count(ofr.id) from offers ofr where (ofr.expert= exp.id and (ofr.status= 'rejected'))) as ofertas_rechazadas,
	(select count(req.id) from requests req
   left join expert_cancel_requests can on(req.id = can.request)
   left join offers off on(req.id = off.request)
   where((can.id is null) and(off.expert is null) and req.status != 'progress'
   and req.category in (select exp_cat.category from experts_categories exp_cat where(exp_cat.expert= exp.id))
   and req.region in (select exp_reg.region from experts_regions exp_reg where(exp_reg.expert= exp.id)))) perdidas_oportunidades
	from experts exp
   inner join users us on( us.id= exp.user)";
	$Query = plugDB($Qstr, "result");
	?>

	<script type="text/javascript">
		jQuery(document).on("mobileinit", function(){
			jQuery.mobile.ajaxEnabled = false;
		});
	</script>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.css"/>
	<script src="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.jqueryui.min.css">
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.jqueryui.min.js"></script>
	<script type="text/javascript" class="init">
		jQuery(document).ready(function() {
			jQuery('#la_data_tabla').DataTable();
			jQuery('#wpfooter').remove();
		} );
	</script>
	<style>
		.controlgroup-textinput{
		    padding-top:.22em;
		    padding-bottom:.22em;
		}
		.ui-title{
			color:white;
		}
		div.DataTables_sort_wrapper span {
			right: -10px !important;
		}

		.ui-collapsible-inset .ui-collapsible-content{
			overflow-y: scroll !important;
		}

		
		.dataTables_wrapper{
			overflow-x : scroll;
		}


		@media(min-width : 80em){
			.li_form{
				display : flex
			}

			.li_form .label_form{
				width : 20%
			}

			.li_form .inp_form{
				width : 80%
			}

		}

		
	</style>

	<div data-role="page">
		<div data-role="header" data-theme="b">
			<h1>Reporte Clientes (<?php echo Count($Query);?> Registros)</h1>
		</div>

		<div data-role="header" data-theme="c">
			<div data-role="controlgroup" data-type="horizontal">
				<form action='<?php bloginfo('template_url'); ?>/admin_db/export_xls.php' target='_blank' method='post'>
					<input type='hidden' name='type' value='usuarios'/>
					<input type='hidden' name='xhr' value='<?php echo base64_encode($Qstr); ?>'/>
					<input type="submit" id="exportar" name="exportar" value="Exportar a excel" class="ui-btn" data-theme="b" />
				</form>
			</div>
		</div>

		<div role="main" class="ui-content">
			<?php if($Query[0] != null):?>
			<table class="wp-list-table widefat" id="la_data_tabla" cellspacing="0">
				<thead>
					<tr valign="top">
						<?php foreach($Query[0] as $key => $value):?>
							<th><?php echo Traductor_Nombre_Columnas($key);?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody id="the-list">
					<?php 
					$alter = "";
					foreach ( $Query as $lista ):
						if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
					?>
					<tr valign="top" <?php echo $alter; ?>>
						<?php foreach($lista as $it => $va):?>
							<td><?php echo $va;?></td>
						<?php endforeach; ?>
						
					</tr>
				<?php endforeach;?>
			</table>
			<?php endif;?>
		</div>

	</div>
	<?php
}
//
function page_contenidos(){
	//print_r($_POST);
	if( isset($_POST["save_valor_fixcoin"]) ){
		$qs_update = "UPDATE contenidos SET ".Gen_String_Update($_POST, array("save_valor_fixcoin"))." WHERE clave='valor_fixcoin_referido'";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["add_certificado"]) ){
		$qs_update = "INSERT INTO certifications_type (denomination, uri) VALUES ('".$_POST["denomination"]."', '".$_POST["uri"]."')";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["borrar_certificado"]) ){
		$qs_update = "DELETE FROM certifications_type WHERE id = '".$_POST["id_item"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["update_certificado"]) ){
		$qs_update = "UPDATE certifications_type SET ".Gen_String_Update($_POST, array("update_certificado", "id_item"))." WHERE id='".$_POST["id_item"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	//
	$Query_Valor_Fixcoin = plugDB("SELECT * FROM contenidos WHERE clave = 'valor_fixcoin_referido'", "row");
	$Query_Certificados = plugDB("SELECT * FROM certifications_type", "result");
	$exclude_tabla_certificados = array();
	//
	?>
	<script type="text/javascript" >
		jQuery(document).on("mobileinit", function(){
			jQuery.mobile.ajaxEnabled = false;
		});
	</script>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.css"/>
	<script src="https://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.jqueryui.min.css">
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.jqueryui.min.js"></script>
	<script type="text/javascript" class="init">
		jQuery(document).ready(function() {
			//jQuery('#la_data_tabla').DataTable();
			jQuery('#wpfooter').remove();
		} );
	</script>
	<style>
		.controlgroup-textinput{
		    padding-top:.22em;
		    padding-bottom:.22em;
		}
		.ui-title{
			color:white;
		}
		div.DataTables_sort_wrapper span {
			right: -10px !important;
		}

		.fix_table .ui-btn-inner{
			padding: .55em 11px;
		}
		.ui-select .ui-icon{
			background-image: url(https://code.jquery.com/mobile/1.2.1/images/icons-18-white.png) !important;
			background-position: -217px 50% !important;
		}
		[id="formulario"] *{
			font:inherit !important;
			font-size:16px !important;
		}

		.flex{
			display : flex !important;
			align-items : center;
		}

		.btn_per{
			border: none;
			width: 150px;
			padding: 10px;
			box-shadow: 0 1px 4px rgba(0,0,0,.3);
			outline: none;
			margin-top: 10px;
			font-weight: bold;
			border-radius: 6px;
			font-size : 0.8rem;
		}

		table th{
			font-weight : bold !important;
			text-transform: capitalize;
		}


	</style>

	<div data-role="page">
		<div data-role="header" data-theme="b">
			<h1>Contenidos y Ajustes</h1>
		</div>
		<div role="content" class="ui-content">
				<form method="post" >
					<div class="flex">
						<div style="width : 15%">
							 <b>Valor Fixcoin cuando <br> refieres a un amigo</b> 
						</div>
						<div  style="width : 25%">
							<input type="number" name="valor" value="<?php echo $Query_Valor_Fixcoin->valor;?>" required  style="width : 90%">
						</div>
						<div  style="width : 15%">
						 	<b><?php echo $Query_Valor_Fixcoin->fecha;?></b>	
						</div>
						<div style="width : 25%">
							<input data-role="none" type="submit" name="save_valor_fixcoin" value="Guardar" class=" btn_per"  style="background: #3f72a5; color: white;">
						</div>
					</div>
				</form>

			<h2>Certificados</h2>

			<table class="wp-list-table widefat" cellspacing="0">
				<thead>
					<tr valign="top">
						<?php foreach($Query_Certificados[0] as $key => $value):?>
							<?php if(!in_array($key, $exclude_tabla_certificados)):?>
								<th><?php echo Traductor_Nombre_Columnas($key); ?></th>
							<?php endif;?>
						<?php endforeach; ?>
						<th>Acciones</th>
					</tr>
				</thead>
			</table>

			<div id="the-list">
				<?php 
				$alter = "";
				foreach ( $Query_Certificados as $lista ):
					if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
				?>
				<form method="post">
					<ul class="flex" valign="top" data-role="listview" data-inset="true" <?php echo $alter; ?>>
						<?php foreach($lista as $key => $va):?>
							<?php if(!in_array($key, $exclude_tabla_certificados)):?>
								<?php if( $key == "denomination" || $key == "uri" ):?>
									<li style="height: 52px; width: 25%; border: none;">
										<input type="text" name="<?php echo $key;?>" value="<?php echo $va;?>" required>
									</li>
								<?php else:?>
									<li style="height: 52px; width: 10%; border: none;">
										<?php echo $va;?>
									</li>
								<?php endif;?>
							<?php endif;?>
						<?php endforeach; ?>
						<li style="height: 52px; width: 40%; border: none; display : flex">
							<input type="hidden" name="id_item" value="<?php echo $lista->id;?>">
							<input type="submit" data-role="none" name="update_certificado" value="Actualizar" class="btn_per">
							<input type="submit" data-role="none" name="borrar_certificado" value="Borrar" class=" btn_per" style="margin-left : 15px">
						</li>
					</ul>
				</form>

				<?php endforeach;?>

				<form method="post">
					<ul data-role="listview" data-inset="true" class="flex">
						<li style="height: 52px; width: 10%; border: none;">
							<b>Nuevo</b> 
						</li>
						<li style="height: 52px; width: 25%; border: none">
							<input type="text" name="denomination" placeholder="Nombre" required>
						</li>
						<li style="height: 52px; width: 25%; border: none">
							<input type="text" name="uri" placeholder="Uri" required>
						</li>
						<li style="height: 52px; width: 40%; border: none">
							<input data-role="none" type="submit"  name="add_certificado" value="Crear" class="btn_per">
						</li>
					</ul>
				</form>

		</div>
	</div>

	<?php
}
//
function func_select_estado_servicio($valores, $_actual){
	$ret = "";
	foreach($valores as $k => $v){
		if($_actual == $v){
			$ret .= "<option value='".$v."' selected>" . $k . "</option>";
		}else{
			$ret .= "<option value='".$v."'>" . $k . "</option>";
		}
	}
	//$ret .= "<option value='-1'>-Todos</option>";
	return $ret;
}
function func_select_tabla_id_denomination($tabla, $_actual){
	$ret = "";
	$Qstr = "SELECT * FROM ".$tabla." ORDER BY denomination ASC";
	$Query = plugDB($Qstr, "result");
	foreach ( $Query as $lista ){
		if($_actual == $lista->id){
			$ret .= "<option value='".$lista->id."' selected>".$lista->denomination."</option>";
		}else{
			$ret .= "<option value='".$lista->id."'>".$lista->denomination."</option>";
		}
	}
	//$ret .= "<option value='-1'>todos</option>";
	return $ret;
}
function func_select_sub_categorias($categoria, $_actual){
	$ret = "";
	$ret .= "<option value='-1'>Sub Categoria</option>";
	if(isset($categoria) && $categoria != "-1"){
		$Qstr = "SELECT * FROM categories WHERE service ='".$categoria."' ORDER BY denomination ASC";
		$Query = plugDB($Qstr, "result");
		foreach ( $Query as $lista ){
			if($_actual == $lista->id){
				$ret .= "<option value='".$lista->id."' selected>".$lista->denomination."</option>";
			}else{
				$ret .= "<option value='".$lista->id."'>".$lista->denomination."</option>";
			}
		}
	}
	return $ret;
}
function func_select_sub_zonas($categoria, $_actual){
	$ret = "";
	$ret .= "<option value='-1'>Zona</option>";
	if(isset($categoria) && $categoria != "-1"){
		$Qstr = "SELECT * FROM regions WHERE city ='".$categoria."' ORDER BY name ASC";
		$Query = plugDB($Qstr, "result");
		foreach ( $Query as $lista ){
			if($_actual == $lista->id){
				$ret .= "<option value='".$lista->id."' selected>".$lista->name."</option>";
			}else{
				$ret .= "<option value='".$lista->id."'>".$lista->name."</option>";
			}
		}
	}
	return $ret;
}
function func_select_edad($_actual){
	$ret = "";
	for($i=1;$i<99;$i++){
		if($i==$_actual){
			$ret.="<option selected>".$i."</option>";
		}else{
			$ret.="<option>".$i."</option>";
		}
	}
	return $ret;
}
function func_tabla_form($q, $excluir_rows, $only_read){
	$ret = "";
	$alter = "";
	foreach($q as $k => $v){
		if( !in_array($k, $excluir_rows) ){
			if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
			$ret .= "<tr valign='top' ".$alter.">";
			$ret .= "<td>".$k."</td>";

			if( in_array($k, $only_read) ){
				$ret .= "<td>".$v."</td>";
			}else{
				if($k == "gender"){
					$ret .= "<td><select name='gender'>".func_select_tabla_id_denomination("gender", $v)."</select></td>";
				}else if($k == "birth_date"){
					$ret .= "<td><input type='datetime-local' name='".$k."' value='".(new DateTime($v))->format("Y-m-d\\TH:i:s")."'></td>";
				}else{
					$ret .= "<td><input type='text' name='".$k."' value='".$v."'></td>";
				}
			}
			$ret .= "</tr>";
		}
	}
	return $ret;
}
function func_tabla_form_servicio($q, $excluir_rows, $only_read){
	$ret = "";
	$alter = "";
	foreach($q as $k => $v){
		if( !in_array($k, $excluir_rows) ){
			if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
			$ret .= "<tr valign='top' ".$alter.">";
			$ret .= "<td>".$k."</td>";

			if( in_array($k, $only_read) ){
				$ret .= "<td>".$v."</td>";
			}else{
				if($k == "description"){
					$ret .= "<td><textarea name='".$k."'>".$v."</textarea></td>";
				}else{
					$ret .= "<td><input type='text' name='".$k."' value='".$v."'></td>";
				}
			}
			$ret .= "</tr>";
		}
	}
	return $ret;
}
//
function func_tabla_form_fieldcontainer($q, $excluir_rows, $only_read){
	$ret = "";
	$alter = "";
	foreach($q as $k => $v){
		if( !in_array($k, $excluir_rows) ){
			$ret .= "<li class='ui-field-contain li_form'>";
			$ret .= "<div class='label_form'><label for='".$k."'>".Traductor_Nombre_Columnas($k). "</label></div>";

			if( in_array($k, $only_read) ){
				$ret .= "".$v."";
			}else{
				$ret .= "<div class='inp_form'>";
				if($k == "gender"){
					$ret .= "<select name='gender'>".func_select_tabla_id_denomination("gender", $v)."</select>";
				}else if($k == "birth_date"){
					$ret .= "<input type='datetime-local' name='".$k."' value='".(new DateTime($v))->format("Y-m-d\\TH:i:s")."' data-clear-btn='true'>";
				}else{
					$ret .= "<input type='text' name='".$k."' value='".$v."' data-clear-btn='true'>";
				}
				$ret .= "</div>";
			}

			$ret .= "</li>";
		}
	}
	return $ret;
}
//
function Gen_Btn_Experto($id_expert, $show_tipo = false){
	$Query__Expert = plugDB("SELECT u.name as nombre, u.id as id_user FROM experts e LEFT JOIN users u ON e.user = u.id WHERE e.id = '".$id_expert."'", "row");
	?>
	<style>
		.btn_per{
			border: none;
			width: 150px;
			padding: 10px;
			box-shadow: 0 1px 4px rgba(0,0,0,.3);
			outline: none;
			margin-top: 10px;
			font-weight: bold;
			border-radius: 6px;
			font-size : 0.8rem;
		}

	</style>
	<form method="post" action="admin.php?page=profesionales" target="_blank">
		<input type="hidden" name="id" value="<?php echo $Query__Expert->id_user;?>">
		<input type="hidden" name="editar_cliente" value="ok">
		<input type="submit" value="<?php echo $Query__Expert->nombre;?><?php if($show_tipo == true) echo " (experto)";?>" class="btn_per" data-role="none">
	</form>
	<?php
}
function Gen_Btn_Cliente($id_user, $show_tipo = false){
	$Query_Cus = plugDB("SELECT * FROM users WHERE id = '".$id_user."'", "row");
	?>
	<form method="post" action="admin.php?page=clientes" target="_blank">
		<input type="hidden" name="id" value="<?php echo $Query_Cus->id;?>">
		<input type="hidden" name="editar_cliente" value="ok">
		<input type="submit" value="<?php echo $Query_Cus->name;?><?php if($show_tipo == true) echo " (cliente)";?>" class="button action">
	</form>
	<?php
}
function Gen_Btn_By_User($id_user, $show_tipo = false){
	$Query_Cus = plugDB("SELECT * FROM users WHERE id = '".$id_user."'", "row");
	$Query__Expert = plugDB("SELECT * FROM experts e WHERE e.user = '".$id_user."'", "row");

	if($Query__Expert != null){
		Gen_Btn_Experto($Query__Expert->id, $show_tipo);
	}else{
		Gen_Btn_Cliente($id_user, $show_tipo);
	}
}
//
function Gen_String_Update($data, $excluir_rows){
	$ret = "";
	foreach($data as $k => $v){
		if( !in_array($k, $excluir_rows) ){
			if($ret != ""){
				$ret .= ",";
			}
			$ret.= $k . "='".$v."' ";
		}
	}
	return $ret;
}
//
function Select_Categorias_Multi_Colaborador($cola){
	$pepe = array();
	$Query_mis_cats = plugDB("SELECT category FROM collaborators_categories WHERE collaborator='".$cola."'", "result");
	foreach($Query_mis_cats as $pepito){
		array_push($pepe, $pepito->category);
	}
	//print_r($pepe);

	$Query_servicios = plugDB("SELECT * FROM services", "result");
	$ret = "<select name='categorias[]' data-placeholder='Categorias' class='chosen-select' multiple tabindex='6'>";
	foreach($Query_servicios as $item_servicio){
		$Query_cats = plugDB("SELECT * FROM categories WHERE service = '".$item_servicio->id."'", "result");
		$ret .= "<optgroup label='".$item_servicio->denomination."'>";
		foreach($Query_cats as $item_cat){
			if( in_array($item_cat->id, $pepe) ){
				$ret .= "<option value='".$item_cat->id."' selected>".$item_cat->denomination."</option>";
			}else{
				$ret .= "<option value='".$item_cat->id."'>".$item_cat->denomination."</option>";
			}
		}
		$ret .= "</optgroup>";
	}
	$ret .= "</select>";
	return $ret;
}
//
function Do_Alter_Tr($alt){
	if($alt == ""){
		$alt = "class='alternate'";
	}else{
		$alt = "";
	}
	return $alt;
}
//
function Select_ColByExpert($expert, $actual){
	$ret = "";
	$Query_cols = plugDB("SELECT * FROM collaborators WHERE expert='".$expert."'", "result");
	foreach($Query_cols as $item_col){
		if($actual == $item_col->id){
			$ret .= "<option value='".$item_col->id."' selected>".$item_col->name."</option>";
		}else{
			$ret .= "<option value='".$item_col->id."'>".$item_col->name."</option>";
		}
	}
	return $ret;
}
//
function Traductor_Nombre_Columnas($key){
	$datos = array(
		"name" 				=> "Nombre", 
		"date_registry" 	=> "Fecha Registro", 
		"birth_date" 		=> "Fecha Nacimiento", 
		"id_user" 			=> "Id Usuario",
		"email" 			=> "Correo", 
		"gender" 			=> "Genero", 
		"id_expert"			=> "ID Experto",
		"tipo_registro"		=> "Tipo Registro",
		"fixcoins"			=> "Fixcoin",
		"education_level" 	=> "Nivel educativo",
		"educational_level" 	=> "Nivel educativo",
		"Estado_Plan"       => "Estado del Plan",
		"Status" 			=> "Estado",
		"status"			=> "Estado",
		"description"		=> "Descripcion",
		"id"				=> "ID",
		"observations"		=> "Observaciones",
		"collaborator"		=> "Colaborador",
		"date"				=> "Fecha",
		"start_date"		=> "Fecha_inicio",
		"hour"				=> "Hora",
		"response_time"		=> "Hora Respuesta",
		"completed_date"	=> "Completar Fecha",
		"date_response"		=> "Fecha Respuesta",
		"denomination"		=> "Denominación",
		"authentication_date" => "Ultimo Login",
		"trabajos_culminados" => "Trabajos Culminados",
		"gasto_fixcoin"		  => "Gasto Fixcoin",
		"ofertas_aceptadas"    => "Ofertas Aceptadas",
		"ofertas_rechazadas"  => "Ofertas Rechazadas",
		"perdidas_oportunidades" => "Oportunudades Perdidas",
		
	);
	if( !$datos[$key] ){
		return $key;
	}else{
		return $datos[$key];
	}
}
?>