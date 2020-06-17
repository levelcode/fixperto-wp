<?php
//
require_once __DIR__.'/../configuracion.php';
//
add_action('admin_menu' , 'function_enable_pages');
function function_enable_pages(){
	add_menu_page('Fixperto', 'Fixperto', 'read', 'admin_db', 'page_admin', '', 3);
	add_submenu_page( 'admin_db', 'Clientes', 'Clientes', 'administrator', 'clientes', 'page_clientes');//	
	add_submenu_page( 'admin_db', 'Reporte_Clientes', 'Reporte_Clientes', 'administrator', 'reporte_clientes', 'page_reporte_clientes');//	
	add_submenu_page( 'admin_db', 'Profesionales', 'Profesionales', 'administrator', 'profesionales', 'page_profesionales');//	
	add_submenu_page( 'admin_db', 'Servicios', 'Servicios', 'administrator', 'servicios', 'page_servicios');//	
	add_submenu_page( 'admin_db', 'Transacciones_Planes', 'Transacciones_Planes', 'administrator', 'transacciones_planes', 'page_transacciones_planes');//	
	add_submenu_page( 'admin_db', 'Transacciones_Fixcoins', 'Transacciones_Fixcoins', 'administrator', 'transacciones_fixcoins', 'page_transacciones_fixcoins');//	
	add_submenu_page( 'admin_db', 'Atencion_Cliente', 'Atencion_Cliente', 'administrator', 'atencion_cliente', 'page_atencion_cliente');//	
	add_submenu_page( 'admin_db', 'Cupones_Referidos', 'Cupones_Referidos', 'administrator', 'cupones_referidos', 'page_referidos');//	
	add_submenu_page( 'admin_db', 'Categorias', 'Categorias', 'administrator', 'lista_servicios', 'page_lista_servicios');//	
	remove_submenu_page( 'admin_db', 'admin_db' );
}
//
function page_admin(){
	?>
	<div class="wrap">
		<h2>Fixperto</h2>
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
	?>
	<div class="wrap">
		<h2>Clientes</h2>
		<div div class="alignleft actions bulkactions">
			<form method='post'>
				<select name="genero" onchange="this.form.submit()">
					<option value='-1'>Genero</option>
					<?php echo func_select_tabla_id_denomination("gender", $_POST["genero"]);?>
				</select>
				<select name="edad" onchange="this.form.submit()">
					<option value='-1' selected>Edad</option>
					<?php echo func_select_edad($_POST["edad"]);?>
				</select>
			</form>
		</div><br><br>
		<div div class="alignleft actions bulkactions">
			<form method='post'>
			<input type="submit" class="button action" name="todos" value="Limpiar Filtros">
			</form>
		</div><br><br>
		<h4><?php echo Count($Query);?> Registros encontrados</h4>
		<?php if($Query[0] != null):?>
			<div div class="alignleft bulkactions">
				<form action='<?php bloginfo('template_url'); ?>/admin_db/export_xls.php' target='_blank' method='post'>
					<input type='hidden' name='type' value='usuarios'/>
					<input type='hidden' name='xhr' value='<?php echo base64_encode($Qstr); ?>'/>
					<input type="submit" class="button action" id="exportar" name="exportar" value="Exportar a excel" />
				</form>
			</div><br><br>
			<table class="wp-list-table widefat" cellspacing="0">
				<thead>
					<tr valign="top">
						<?php foreach($Query[0] as $key => $value):?>
							<th><?php echo $key; ?></th>
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
								<input type="hidden" name="id" value="<?php echo $lista->id_user;?>">
								<input type="hidden" name="editar_cliente" value="ok">
								<input type="submit" value="Editar" class="button action">
							</form>
						</td>
					</tr>
				<?php endforeach;?>
			</table>
		<?php endif;?>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
	<script type="text/javascript"></script>
	<?php
}
function page_clientes_editar_cliente(){
	//print_r($_POST);
	if( isset($_POST["es_usuario"]) ){
		//echo "<br><br>".Gen_String_Update($_POST, array("editar_cliente", "es_usuario"));
		$qs_update = "UPDATE users SET ".Gen_String_Update($_POST, array("editar_cliente", "es_usuario"))." WHERE id='".$_POST["id"]."'";
		$q_update = plugDB($qs_update, "result");
	}
	if( isset($_POST["es_customer"]) ){
		//echo "<br><br>".Gen_String_Update($_POST, array("editar_cliente", "es_usuario"));
		$qs_update = "UPDATE customers SET ".Gen_String_Update($_POST, array("editar_cliente", "es_customer"))." WHERE user='".$_POST["id"]."'";
		$q_update = plugDB($qs_update, "result");
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
	<div class="wrap">
		
		<img src="<?php echo URL_BASE;?>/uploads/registros/cliente/<?php echo $Query_Customer->avatar;?>" height="300">

		<h2>Estado</h2>
		<table class="wp-list-table widefat" cellspacing="0">
			<?php if($Query_Customer->active == 1):?>
				<tr>
					<td>ACTIVO</td>
					<td>
					<form method="post">
						<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
						<input type="hidden" name="editar_cliente" value="ok">
						<input type="hidden" name="update_active" value="ok">
						<input type="hidden" name="active" value="0">
						<input type="submit" value="Desactivar" class="button action">
					</form>
					</td>
				</tr>
				<?php else:?>
				<tr>
					<td>INACTIVO</td>
					<td>
					<form method="post">
						<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
						<input type="hidden" name="editar_cliente" value="ok">
						<input type="hidden" name="update_active" value="ok">
						<input type="hidden" name="active" value="1">
						<input type="submit" value="Activar" class="button action">
					</form>
					</td>
				</tr>
			<?php endif;?>
			<tr class="alternate">
					<td>Restablecer Contraseña</td>
					<td>
						<form method="post" onSubmit="return Do_Reestablecer_Clave(this);">
							<input type="hidden" name="correo" value="<?php echo $Query->email;?>">
							<input type="hidden" name="id_user" value="<?php echo $_POST["id"];?>">
							<input type="submit" value="Restablecer Contraseña" class="button action">
						</form>
					</td>
				</tr>
		</table>

		<h2>Información personal</h2>
		<table class="wp-list-table widefat" cellspacing="0">
			<thead>
				<tr valign="top">
					<th>Campo</th>
					<th>Valor</th>
				</tr>
			</thead>

			<form method="post" name="la_data">
				<tbody id="the-list">
					<?php 
					echo func_tabla_form(
						$Query, 
						array("photo", "notification", "notification_chat", "token", "code", "password", "code_number", "validate_number"),
						array("id", "date_registry", "authentication_date")
					);
					?>
					<tr>
						<td>
							<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
							<input type="hidden" name="editar_cliente" value="ok">
							<input type="submit" name="es_usuario" value="Guardar" class="button action">
						</td>
						<td></td>
					</tr>
				</tbody>
			</form>

			<form method="post" name="la_data">
				<tbody id="the-list">
					<tr>
						<td>Telefono:</td>
						<td>
							<input type="text" name="phone" value="<?php echo $Query_Customer->phone;?>">
						</td>
					</tr>
					<tr>
						<td>
							<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
							<input type="hidden" name="editar_cliente" value="ok">
							<input type="submit" name="es_customer" value="Guardar" class="button action">
						</td>
						<td></td>
					</tr>
				</tbody>
			</form>

		</table>

		<h2>Servicios</h2>
		<table class="wp-list-table widefat" cellspacing="0">
			<thead>
				<tr valign="top">
					<?php foreach($Query_Request[0] as $key => $value):?>
					<?php if(!in_array($key, $exclude_tabla_servicios)):?><th><?php echo $key; ?></th><?php endif;?>
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
						<form method="post" action="admin.php?page=servicios">
							<input type="hidden" name="id" value="<?php echo $lista->id;?>">
							<input type="submit" name="editar_servicio" value="Editar" class="button action">
						</form>
					</td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>
		
		<h2>Calificaciones</h2>
		<table class="wp-list-table widefat" cellspacing="0">
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

	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 

	<script type="text/javascript">
	function Do_Reestablecer_Clave(form){
		if( confirm("Esta accion enviara un correo de confirmacion, ¿desea continuar?") ){
			$.ajax({
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
	$Colum_No_Mostrar = array("id_user", "birth_date", "date_arl", "date_salud_pension", "profile_description");
	?>

	<div class="wrap">
		<h2>Profesionales</h2>
		<div div class="alignleft actions bulkactions">
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
			</form>
		</div><br><br>
		<div div class="alignleft actions bulkactions">
			<form method='post'>
			<input type="submit" class="button action" name="todos" value="Limpiar Filtros">
			</form>
		</div><br><br>
		<h4><?php echo Count($Query);?> Registros encontrados</h4>
		<?php if($Query[0] != null):?>
			<div div class="alignleft bulkactions">
				<form action='<?php bloginfo('template_url'); ?>/admin_db/export_xls_experts.php' target='_blank' method='post'>
					<input type='hidden' name='type' value='expertos'/>
					<input type='hidden' name='xhr' value='<?php echo base64_encode($Qstr); ?>'/>
					<input type="submit" class="button action" id="exportar" name="exportar" value="Exportar a excel" />
				</form>
			</div><br><br>
			<table class="wp-list-table widefat" cellspacing="0">
				<thead>
					<tr valign="top">
						<?php foreach($Query[0] as $key => $value):?>
						<?php if( !in_array($key, $Colum_No_Mostrar) ):?><th><?php echo $key; ?></th><?php endif;?>
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
							<form method="post">
								<input type="hidden" name="id" value="<?php echo $lista->id_user;?>">
								<input type="hidden" name="editar_cliente" value="ok">
								<input type="submit" value="Editar" class="button action">
							</form>
						</td>
					</tr>
				<?php endforeach;?>
			</table>
		<?php endif;?>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
	<script type="text/javascript"></script>
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
	<link rel="stylesheet" href="https://harvesthq.github.io/chosen/chosen.css">

	<div class="wrap">
		<img src="<?php echo URL_BASE;?>/uploads/registros/profesional/<?php echo $Query_expert->avatar;?>" height="300">

		<h2>Estado</h2>
		<table class="wp-list-table widefat" cellspacing="0">
			<tbody id="the-list">
				<?php if($Query_expert->active == 1):?>
				<tr>
					<td>ACTIVO</td>
					<td>
					<form method="post" onSubmit="return Do_Activar_Experto(this);">
						<input type="hidden" name="nombre" value="<?php echo $Query->name;?>">
						<input type="hidden" name="correo" value="<?php echo $Query->email;?>">
						<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
						<input type="hidden" name="editar_cliente" value="ok">
						<input type="hidden" name="update_active" value="ok">
						<input type="hidden" name="active" value="0">
						<input type="submit" value="Desactivar" class="button action">
					</form>
					</td>
				</tr>
				<?php else:?>
				<tr>
					<td>INACTIVO</td>
					<td>
					<form method="post" onSubmit="return Do_Activar_Experto(this);">
						<input type="hidden" name="nombre" value="<?php echo $Query->name;?>">
						<input type="hidden" name="correo" value="<?php echo $Query->email;?>">
						<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
						<input type="hidden" name="editar_cliente" value="ok">
						<input type="hidden" name="update_active" value="ok">
						<input type="hidden" name="active" value="1">
						<input type="submit" value="Activar" class="button action">
					</form>
					</td>
				</tr>
				<?php endif;?>
				<tr class="alternate">
					<td>Restablecer Contraseña</td>
					<td>
						<form method="post" onSubmit="return Do_Reestablecer_Clave(this);">
							<input type="hidden" name="correo" value="<?php echo $Query->email;?>">
							<input type="hidden" name="id_user" value="<?php echo $_POST["id"];?>">
							<input type="submit" value="Restablecer Contraseña" class="button action">
						</form>
					</td>
				</tr>
			</tbody>
		</table>

		<h2>Fixcoins</h2>
		<table class="wp-list-table widefat" cellspacing="0">
			<tbody id="the-list">
				<tr>
					<td>Actual</td>
					<td id="label_actual_fixcoins"><?php echo $Query_expert->fitcoints;?></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>

				<tr class="alternate">
					<td>Dar</td>
					<td><input type="number" value="0" min="0" id="cant_dar_fixcoins"></td>
					<td>
						<input type="button" value="Enviar" class="button action" onClick="Do_Dar_Fixcoins();">
					</td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
		</table>

		<h2>Información personal</h2>
		<table class="wp-list-table widefat" cellspacing="0">
			<thead>
				<tr valign="top">
					<th>Campo</th>
					<th>Valor</th>
				</tr>
			</thead>
			<tbody id="the-list">
				<form method="post" name="la_data">
				<?php 
				echo func_tabla_form(
					$Query, 
					array("photo", "notification", "notification_chat", "token", "code", "password", "code_number", "validate_number"),
					array("id", "date_registry", "authentication_date")
				);
				?>
				
				<tr>
					<td>
						<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
						<input type="hidden" name="editar_cliente" value="ok">
						<input type="submit" name="es_usuario" value="Guardar" class="button action">
					</td>
					<td></td>
				</tr>
				</form>

				
			</tbody>
		</table>

		<h2>Información profesional</h2>
		<table class="wp-list-table widefat" cellspacing="0">
			<thead>
				<tr valign="top">
					<th>Campo</th>
					<th>Valor</th>
				</tr>
			</thead>
			<tbody id="the-list">
				<form method="post" name="la_data">

				<tr class="alternate">
					<td>Tipo</td>
					<td><?php echo ($Query_expert->type == 0)?"Independiente":"Empresa";?></td>
				</tr>

				<tr>
					<td>identification_type</td>
					<td>
					<select name="identification_type">
						<?php echo func_select_tabla_id_denomination("identification_type", $Query_expert->identification_type);?>
					</select>
					</td>
				</tr>

				<?php
				echo func_tabla_form(
					$Query_expert, 
					array("id","user","type","profile_description","active","identification_type", "educational_level", "date_arl", "arl" , "date_salud_pension", "salud_pension", "avatar", "fotocopy", "fitcoints"),
					array("id", "user")
				);
				?>

				<tr>
					<td>educational_level</td>
					<td>
					<select name="educational_level">
						<?php echo func_select_tabla_id_denomination("educational_level", $Query_expert->educational_level);?>
					</select>
					</td>
				</tr>
				
				<tr class="alternate">
					<td>Descripción</td>
					<td>
						<textarea name="profile_description"><?php echo $Query_expert->profile_description;?></textarea>
					</td>
				</tr>

				<tr>
					<td>
						<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
						<input type="hidden" name="editar_cliente" value="ok">
						<input type="submit" name="es_experto" value="Guardar" class="button action">
					</td>
					<td></td>
				</tr>

				</form>
			</tbody>
		</table>

		<?php if($Query_expert->type == 0):?>
		<h2>Seguridad social</h2>
		<table class="wp-list-table widefat" cellspacing="0">
			<thead>
				<tr valign="top">
					<th>Campo</th>
					<th>Valor</th>
					<th>Accion</th>
				</tr>
			</thead>
			<tbody id="the-list">
				<form method="post" name="la_data">
					<tr>
						<td>Fotocopia documento</td>
						<td>
							<a href="<?php echo URL_BASE;?>/uploads/registros/profesional/<?php echo $Query_expert->fotocopy;?>" target="_blank" id="link_fotocopy">
								<img src="<?php echo URL_BASE;?>/uploads/registros/profesional/<?php echo $Query_expert->fotocopy;?>" height="200" id="img_fotocopy">
							</a>
						</td>
						<td>
							<input type="file" class="form-control-file" id="imagen_fotocopy"><br>
							<input type="button" class="btn btn-primary upload" value="Subir" onClick="SubirImagen_Fotocopy();">
						</td>
					</tr>

					<tr valign="top" class="alternate">
						<td>date_arl</td>
						<td><input type="datetime-local" name="date_arl" value="<?php echo (new DateTime($Query_expert->date_arl))->format("Y-m-d\\TH:i:s");?>"></td>
						<td></td>
					</tr>
					<tr valign="top">
						<td>arl</td>
						<td>
							<a href="<?php echo URL_BASE;?>/uploads/registros/profesional/arl/<?php echo $Query_expert->arl;?>" target="_blank" id="link_arl">
								<img src="<?php echo URL_BASE;?>/uploads/registros/profesional/arl/<?php echo $Query_expert->arl;?>" height="200" id="img_arl">
							</a>
						</td>
						<td>
							<input type="file" class="form-control-file" id="imagen_arl"><br>
							<input type="button" class="btn btn-primary upload" value="Subir" onClick="SubirImagen_Arl();">
						</td>
					</tr>

					<tr valign="top" class="alternate">
						<td>date_salud_pension</td>
						<td><input type="datetime-local" name="date_salud_pension" value="<?php echo (new DateTime($Query_expert->date_salud_pension))->format("Y-m-d\\TH:i:s");?>"></td>
						<td></td>
					</tr>
					<tr valign="top">
						<td>salud_pension</td>
						<td>
							<a href="<?php echo URL_BASE;?>/uploads/registros/profesional/salud_pension/<?php echo $Query_expert->salud_pension;?>" target="_blank" id="link_salud">
								<img src="<?php echo URL_BASE;?>/uploads/registros/profesional/salud_pension/<?php echo $Query_expert->salud_pension;?>" height="200" id="img_salud">
							</a>
						</td>
						<td>
							<input type="file" class="form-control-file" id="imagen_salud"><br>
							<input type="button" class="btn btn-primary upload" value="Subir" onClick="SubirImagen_Salud();">
						</td>
					</tr>
					<tr valign="top">
						<td>
							<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
							<input type="hidden" name="editar_cliente" value="ok">
							<input type="submit" name="es_experto" value="Guardar" class="button action">
						</td>
						<td></td>
					</tr>
				</form>
			</tbody>
		</table>
		<h2>Certificados</h2>
		<table class="wp-list-table widefat" cellspacing="0">
			<thead>
				<tr valign="top" valign="top">
					<th>Imagen</th>
					<th>Editar Imagen</th>
					<th>Tipo certificado</th>
					<th>Accion</th>
				</tr>
			</thead>
			<tbody id="the-list">
				<?php 
				$alter = "";
				$suma_certificados = 0;
				foreach($Query_Certificados as $item_certificado):
					if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
				?>
				<form method="post">
				<tr <?php echo $alter; ?>>
					<td>
						<a href="<?php echo URL_BASE;?>/uploads/registros/profesional/certifications/<?php echo $item_certificado->certification;?>" target="_blank" id="link_certificado_<?php echo $suma_certificados;?>">
							<img src="<?php echo URL_BASE;?>/uploads/registros/profesional/certifications/<?php echo $item_certificado->certification;?>" height="150" id="img_certificado_<?php echo $suma_certificados;?>">
						</a>
					</td>
					<td>
						<input type="file" class="form-control-file" id="imagen_certificado_<?php echo $suma_certificados;?>"><br>
						<input type="button" class="btn btn-primary upload" value="Subir" onClick="SubirImagen_Certificado('<?php echo $item_certificado->id;?>', '<?php echo $suma_certificados;?>');">
					</td>
					<td>
						<select name="certification_type">
							<?php echo func_select_tabla_id_denomination("certifications_type", $item_certificado->certification_type);?>
						</select>
					</td>
					<td>
						<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
						<input type="hidden" name="editar_cliente" value="ok">
						<input type="hidden" name="id_certi" value="<?php echo $item_certificado->id;?>">
						<input type="submit" name="update_certificado" value="Actualizar" class="button action">
						<input type="submit" name="del_certificado" value="Borrar" class="button action">
					</td>
				</tr>
				</form>
				<?php 
				$suma_certificados++;
				endforeach;
				?>
				<form method="post">
					<tr>
						<td></td>
						<td></td>
						<td>
							<select name="certification_type">
								<?php echo func_select_tabla_id_denomination("certifications_type", "");?>
							</select>
						</td>
						<td>
							<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
							<input type="hidden" name="editar_cliente" value="ok">
							<input type="hidden" name="expert" value="<?php echo $Query_expert->id;?>">
							<input type="submit" name="add_certificado" value="Crear" class="button action">
						</td>
					</tr>
				</form>
			</tbody>
		</table>
		<?php endif;?>

		<h2>Proyectos</h2>
		<table class="wp-list-table widefat" cellspacing="0">
			<thead>
				<tr valign="top" valign="top">
					<th>Imagen</th>
					<th>Editar Imagen</th>
					<th>Titulo Proyecto</th>
					<th>Accion</th>
				</tr>
			</thead>
			<tbody id="the-list">
				<?php 
				$alter = "";
				$suma_proyectos = 0;
				foreach($Query_Proyectos as $item_proyecto):
					if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
				?>
				<form method="post">
				<tr <?php echo $alter; ?>>
					<td>
						<a href="<?php echo URL_BASE;?>/uploads/registros/profesional/jobs/<?php echo $item_proyecto->job;?>" target="_blank" id="link_proyecto_<?php echo $suma_proyectos;?>">
							<img src="<?php echo URL_BASE;?>/uploads/registros/profesional/jobs/<?php echo $item_proyecto->job;?>" height="150" id="img_proyecto_<?php echo $suma_proyectos;?>">
						</a>
					</td>
					<td>
						<input type="file" class="form-control-file" id="imagen_proyecto_<?php echo $suma_proyectos;?>"><br>
						<input type="button" class="btn btn-primary upload" value="Subir" onClick="SubirImagen_Proyecto('<?php echo $item_proyecto->id;?>', '<?php echo $suma_proyectos;?>');">
					</td>
					<td>
						<input type="text" name="description" value="<?php echo $item_proyecto->description;?>" required>
					</td>
					<td>
						<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
						<input type="hidden" name="editar_cliente" value="ok">
						<input type="hidden" name="id_proy" value="<?php echo $item_proyecto->id;?>">
						<input type="submit" name="update_proyecto" value="Actualizar" class="button action">
						<input type="submit" name="del_proyecto" value="Borrar" class="button action">
					</td>
				</tr>
				</form>
				<?php 
				$suma_proyectos++;
				endforeach;
				?>
				<form method="post">
					<tr>
						<td></td>
						<td></td>
						<td>
							<input type="text" name="description" placeholder="Titulo Proyecto" required>
						</td>
						<td>
							<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
							<input type="hidden" name="editar_cliente" value="ok">
							<input type="hidden" name="expert" value="<?php echo $Query_expert->id;?>">
							<input type="submit" name="add_proyecto" value="Crear" class="button action">
						</td>
					</tr>
				</form>
			</tbody>
		</table>

		<h2>Referidos</h2>
		<table class="wp-list-table widefat" cellspacing="0">
			<tr>
				<td>Mi Cupon</td>
				<td><?php echo $Query->code."".$Query->id;?></td>
			</tr>
		</table>
		<table class="wp-list-table widefat" cellspacing="0">
			<thead>
				<tr valign="top" valign="top">
					<th>Referido</th>
					<th>Fecha registro</th>
				</tr>
			</thead>
			<tbody id="the-list">
				<?php 
				$alter = "";
				foreach($Query_Referidos as $item_referido):
					if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
				?>
				<tr valign="top" valign="top">
					<td><?php Gen_Btn_Experto($item_referido->id);?></td>
					<td><?php echo $item_referido->fecha;?></td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>

		<h2>Plan</h2>
		<table class="wp-list-table widefat" cellspacing="0">
			<thead>
				<tr valign="top">
					<th>Campo</th>
					<th>Valor</th>
				</tr>
			</thead>
			<tbody id="the-list">
				<form method="post" name="la_data">
					<tr valign="top" class="alternate">
						<td>Plan</td>
						<td>
							<select name="plan">
							<?php echo func_select_tabla_id_denomination("plans", $Query_Expert_Plan->plan);?>
							</select>
						</td>
					</tr>
					<tr valign="top" >
						<td>start_date</td>
						<td><input type="datetime-local" name="start_date" value="<?php echo (new DateTime($Query_Expert_Plan->start_date))->format("Y-m-d\\TH:i:s");?>"></td>
					</tr>
					<tr valign="top" class="alternate">
						<td>end_date</td>
						<td><input type="datetime-local" name="end_date" value="<?php echo (new DateTime($Query_Expert_Plan->end_date))->format("Y-m-d\\TH:i:s");?>"></td>
					</tr>
					<tr valign="top" >
						<td>status</td>
						<td><?php echo $Query_Expert_Plan->status;?></td>
					</tr>
					<tr valign="top" class="alternate">
						<td>
							<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
							<input type="hidden" name="id_plan" value="<?php echo $Query_Expert_Plan->id;?>">
							<input type="hidden" name="editar_cliente" value="ok">
							<input type="submit" name="es_plan" value="Guardar" class="button action">
						</td>
						<td></td>
					</tr>
				</form>
			</tbody>
		</table>

		<h2>Categorias</h2>
		<table class="wp-list-table widefat" cellspacing="0">
			<thead>
				<tr valign="top">
					<?php foreach($Query_Categorias[0] as $key => $value):?>
					<?php if($key == "Categoria" || $key == "Sub_Categoria"):?><th><?php echo $key; ?></th><?php endif;?>
					<?php endforeach; ?>
					<th>Acciones</th>
				</tr>
			</thead>
			<tbody id="the-list">
				<?php 
				$alter = "";
				foreach ( $Query_Categorias as $lista ):
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
							<input type="submit" name="del_categoria" value="Borrar" class="button action">
						</form>
					</td>
				</tr>
				<?php endforeach;?>
				
				<form method="post">
				<tr valign="top">
					<td>
						<select id="select_service" onchange="Do_Select_Category();">
							<?php echo func_select_tabla_id_denomination("services", "");?>
						</select>
					</td>

					<td>
						<select name="category" id="select_category">
							<?php echo func_select_sub_categorias("", "");?>
						</select>
					</td>
					
					<td>
						<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
						<input type="hidden" name="editar_cliente" value="ok">
						<input type="hidden" name="id_experto" value="<?php echo $Query_expert->id;?>">
						<input type="submit" name="add_categoria" value="Crear" class="button action">
					</td>
				</tr>
				</form>

			</tbody>
		</table>

		<h2>Regiones</h2>
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
							<input type="submit" name="del_region" value="Borrar" class="button action">
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
						<input type="submit" name="add_region" value="Crear" class="button action">
					</td>
				</tr>
				</form>
			</tbody>
		</table>

		<?php if($Query_expert->type == 1):?>
		<h2>Colaboradores</h2>
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
		<?php endif;?>

		<h2>Servicios</h2>
		<table class="wp-list-table widefat" cellspacing="0">
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

		<h2>Calificaciones</h2>
		<table class="wp-list-table widefat" cellspacing="0">
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

	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
	<script src="https://harvesthq.github.io/chosen/chosen.jquery.js" type="text/javascript"></script>
	<script src="https://harvesthq.github.io/chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
	<script src="https://harvesthq.github.io/chosen/docsupport/init.js" type="text/javascript" charset="utf-8"></script>

	<script type="text/javascript">
	function Do_Select_Category(){
		//alert( $("#select_service").val() );
		$.ajax({
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
		$.ajax({
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
	function Do_Activar_Experto(form){
		//alert(form.active.value);
		if( confirm("Esta accion enviara un correo de confirmacion, ¿desea continuar?") ){
			$.ajax({
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
			$.ajax({
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
			$.ajax({
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
        $.ajax({
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
        $.ajax({
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
        $.ajax({
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
        $.ajax({
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
        $.ajax({
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
        $.ajax({
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
	<div class="wrap">
		<h2>Categorias</h2>
		<div div class="alignleft actions bulkactions">
			<form method="post" onSubmit="return Validar_Nombre(this);">
				<input type="text" name="nombre" placeholder="Ingrese Nombre" value="">
				<input type="submit" name="crear_categoria" value="Crear Categoria">
			</form>
		</div><br><br>
		<h4><?php echo Count($Query);?> Registros encontrados</h4>
		<?php if($Query[0] != null):?>
			<div div class="alignleft bulkactions">
				
			</div>
			<table class="wp-list-table widefat" cellspacing="0">
				<thead>
					<tr valign="top">
						<th>Icono</th>
						<th>Categorias</th>
						<th>Cant Servicios</th>
						<th>Cant Expertos</th>
						<th>Sub Categorias</th>
						<th>Cant Servicios</th>
						<th>Costo Fixcoin</th>
						<th>Acciones</th>
					</tr>
				</thead>

				<tbody id="the-list">
					<?php 
					$alter = "class='alternate'";
					foreach ( $Query as $lista ):
					?>
					<tr valign="top" <?php echo $alter; ?>>
						<td>
							<a href="<?php echo URL_BASE;?>/uploads/categories/<?php echo $lista->imagen;?>" target="_blank" id="link_cat_<?php echo $lista->id;?>">
								<img src="<?php echo URL_BASE;?>/uploads/categories/<?php echo $lista->imagen;?>" height="100" id="img_cat_<?php echo $lista->id;?>">
							</a><br>
							<input type="file" class="form-control-file" id="imagen_cat_<?php echo $lista->id;?>"><br>
							<input type="button" class="btn btn-primary upload" value="Subir" onClick="SubirImagen_Categoria('<?php echo $lista->id;?>', '<?php echo $lista->id;?>');">
						</td>
						<td>
							<form method="post" onSubmit="return Validar_Nombre(this);">
								<input type="hidden" name="id_item" value="<?php echo $lista->id;?>">
								<input type="text" name="nombre" value="<?php echo $lista->Servicio;?>">
								<input type="submit" name="editar_nombre_cat" value="Editar">
							</form>
						</td>
						<td><?php echo $lista->cant_req;?></td>
						<td><?php echo $lista->cant_exp;?></td>
						<td></td>
						<td></td>
						<td></td>
						<td>
							<form method="post" onSubmit="return confirm('Quiere borrar: <?php echo $lista->Servicio;?>?');">
								<input type="hidden" name="id_item" value="<?php echo $lista->id;?>">	
								<input type="submit" name="borrar_cat" value="Borrar Categoria">
							</form>


							<?php if($lista->hidden == "0"):?>
							<form method="post" onSubmit="return confirm('Quiere Suspender: <?php echo $lista->Servicio;?>?');">
								<input type="hidden" name="id_item" value="<?php echo $lista->id;?>">
								<input type="hidden" name="estado" value="1">	
								<input type="submit" name="suspender_cat" value="Suspender Categoria">
							</form>
							<?php else:?>
								<form method="post" onSubmit="return confirm('Quiere Activar: <?php echo $lista->Servicio;?>?');">
								<input type="hidden" name="id_item" value="<?php echo $lista->id;?>">	
								<input type="hidden" name="estado" value="0">
								<input type="submit" name="suspender_cat" value="Activar Categoria">
							</form>
							<?php endif;?>



						</td>
					</tr>

						<?php
						$q_sub = "
						SELECT c.*, if(req.suma,req.suma,0)as suma
						FROM categories c 
						LEFT JOIN (SELECT COUNT(*) as suma, category FROM requests GROUP BY category)req ON c.id = req.category
						WHERE service = '".$lista->id."'
						ORDER BY c.denomination ASC
						";
						$Query2 = plugDB($q_sub, "result");
						foreach ( $Query2 as $lista_sub ):
						?>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>
								<form method="post" onSubmit="return Validar_Nombre(this);">
									<input type="hidden" name="id_item" value="<?php echo $lista_sub->id;?>">
									<input type="text" name="nombre" value="<?php echo $lista_sub->denomination;?>">
									<input type="submit" name="editar_nombre_subcat" value="Editar">
								</form>
							</td>
							<td><?php echo $lista_sub->suma;?></td>
							<td>
								<form method="post">
									<input type="hidden" name="id_item" value="<?php echo $lista_sub->id;?>">
									<input type="number" name="costo_item" value="<?php echo $lista_sub->cost;?>">
									<input type="submit" name="editar_fixcoin_subcat" value="Editar">
								</form>
							</td>
							<td>
								<form method="post" onSubmit="return confirm('Quiere borrar: <?php echo $lista_sub->denomination;?>?');">
									<input type="hidden" name="id_item" value="<?php echo $lista_sub->id;?>">	
									<input type="submit" name="borrar_subcat" value="Borrar Subcategoria">
								</form>
							</td>
						</tr>
						
						<?php endforeach;?>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>
								<form method="post" onSubmit="return Validar_Nombre(this);">
									<input type="hidden" name="id_parent" value="<?php echo $lista->id;?>">
									<input type="text" name="nombre" placeholder="Ingrese nombre">
									<input type="submit" name="crear_subcat" value="Crear Subcategoria">
								</form>
							</td>
						</tr>
					<?php endforeach;?>
				</tbody>
			</table>
		<?php endif;?>
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
	<script type="text/javascript">
		function HolaMundo(){
			alert("Holas");
			$.ajax( {url:"https://google.com"} );
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
        $.ajax({
            url: '<?php echo URL_BASE;?>/fixperto/modCategoryIcon',
            type: 'post',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
				console.log(response);
                if (response != 0) {
                    $("#img_cat_" + _suma).attr("src", response);
                    $("#link_cat_"+ _suma).attr("href", response);
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
	$Colum_No_Mostrar = array("");
	?>
	<div class="wrap">
		<h2>Servicios</h2>
		<div div class="alignleft actions bulkactions">
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
			</form>
		</div><br><br>
		<div div class="alignleft actions bulkactions">
			<form method='post'>
			<input type="submit" class="button action" name="todos" value="Limpiar Filtros">
			</form>
		</div><br><br>

		<h4><?php echo Count($Query);?> Registros encontrados</h4>
		<?php if($Query[0] != null):?>
			<div div class="alignleft bulkactions">
				<form action='<?php bloginfo('template_url'); ?>/admin_db/export_xls.php' target='_blank' method='post'>
					<input type='hidden' name='type' value='servicios'/>
					<input type='hidden' name='xhr' value='<?php echo base64_encode($Qstr); ?>'/>
					<input type="submit" class="button action" id="exportar" name="exportar" value="Exportar a excel" />
				</form>
			</div><br><br>
			<table class="wp-list-table widefat" cellspacing="0">
				<thead>
					<tr valign="top">
						<?php foreach($Query[0] as $key => $value):?>
						<?php if(!in_array($key, $Colum_No_Mostrar)):?><th><?php echo $key; ?></th><?php endif;?>
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
							<form method="post">
								<input type="hidden" name="id" value="<?php echo $lista->id_req;?>">
								<input type="submit" name="editar_servicio" value="Editar" class="button action">
							</form>
						</td>
					</tr>
				<?php endforeach;?>
			</table>
		<?php endif;?>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
	<script type="text/javascript"></script>
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

	<div class="wrap">


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

		<h2>Servicio</h2>
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
					<td>
						<form method="post" action="admin.php?page=clientes" target="_blank">
							<input type="hidden" name="id" value="<?php echo $Query_Cus->id;?>">
							<input type="hidden" name="editar_cliente" value="ok">
							<input type="submit" value="<?php echo $Query_Cus->name;?>" class="button action">
						</form>
					</td>
				</tr>
			<form method="post" name="la_data">
				<?php 
				echo func_tabla_form_servicio(
					$Query, 
					array("category", "region", "customer", "cancellation_type", "status", "emergency", "registry_date", "start_date", "hour", "completed_date"),
					array("id")
				);
				?>

				<tr valign="top" <?php $alter_tabla_1=Do_Alter_Tr($alter_tabla_1);echo $alter_tabla_1;?>>
					<td>completed_date</td>
					<td><input type="datetime-local" name="completed_date" value="<?php echo (new DateTime($Query->completed_date))->format("Y-m-d\\TH:i:s");?>"></td>
				</tr>

				<tr <?php $alter_tabla_1=Do_Alter_Tr($alter_tabla_1);echo $alter_tabla_1;?>>
					<td>Categoria</td>
					<td>
						<select id="select_service" onchange="Do_Select_Category();">
							<?php echo func_select_tabla_id_denomination("services", $Query_Cat->id);?>
						</select>
					</td>
				</tr>

				<tr <?php $alter_tabla_1=Do_Alter_Tr($alter_tabla_1);echo $alter_tabla_1;?>>
					<td>Sub Categoria</td>
					<td>
						<select name="category" id="select_category">
							<?php echo func_select_sub_categorias($Query_Cat->id, $Query->category);?>
						</select>
					</td>
				</tr>

				<tr <?php $alter_tabla_1=Do_Alter_Tr($alter_tabla_1);echo $alter_tabla_1;?>>
					<td>Ciudad</td>
					<td>
						<select id="select_ciudad" onchange="Do_Select_Zona();">
							<?php echo func_select_tabla_id_denomination("cities", $Query_Reg->id);?>	
						</select>
					</td>
				</tr>

				<tr <?php $alter_tabla_1=Do_Alter_Tr($alter_tabla_1);echo $alter_tabla_1;?>>
					<td>Zona</td>
					<td>
						<select id="select_zona" name="region">
							<?php echo func_select_sub_zonas($Query_Reg->id, $Query->region);?>
						</select>
					</td>
				</tr>

				<tr <?php $alter_tabla_1=Do_Alter_Tr($alter_tabla_1);echo $alter_tabla_1;?>>
					<td>Estado</td>
					<td>
						<select name="status">
							<?php echo func_select_estado_servicio(
								array("progress"=>"progress", "acepted"=>"acepted", "scheduled"=>"scheduled", "completed"=>"completed", "rejected"=>"rejected"), 
								$Query->status
							);?>
						</select>
					</td>
				</tr>

				<tr <?php $alter_tabla_1=Do_Alter_Tr($alter_tabla_1);echo $alter_tabla_1;?>>
					<td>
						Fecha Registro
					</td>
					<td>
						<?php echo $Query->registry_date;?>
					</td>
				</tr>

				<tr <?php $alter_tabla_1=Do_Alter_Tr($alter_tabla_1);echo $alter_tabla_1;?>>
					<td>
						Costo del servicio (Fixcoins)
					</td>
					<td>
						<?php $q_costo = plugDB("SELECT cost FROM categories WHERE id = '".$Query->category."'", "row"); echo $q_costo->cost;?>
					</td>
				</tr>

				<tr <?php $alter_tabla_1=Do_Alter_Tr($alter_tabla_1);echo $alter_tabla_1;?>>
					<td>
						<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
						<input type="hidden" name="editar_servicio" value="ok">
						<input type="submit" name="es_usuario" value="Guardar" class="button action">
					</td>
					<td></td>
				</tr>

				</form>
			</tbody>
		</table>

		<?php if( Count($Query_Imagenes) > 0 ):?>
			<h2>Imagenes</h2>
			<?php foreach($Query_Imagenes as $item_imagen):?>
			<a href="<?php echo URL_BASE;?>/uploads/requests/<?php echo $item_imagen->image;?>" target="_blank"><img src="<?php echo URL_BASE;?>/uploads/requests/<?php echo $item_imagen->image;?>" height="200"></a>
			<?php endforeach;?>
		<?php endif;?>


		<h2>Ofertas</h2>
		<table class="wp-list-table widefat" cellspacing="0">
			<thead>
				<tr valign="top">
				<th>Experto</th>
					<?php foreach($Query_Offers[0] as $key => $value):?>
						<?php if(!in_array($key, $exclude_tabla_offers)):?><th><?php echo $key; ?></th><?php endif;?>
					<?php endforeach; ?>
					<th>Acciones</th>
				</tr>
			</thead>
			<tbody id="the-list">
				<?php 
				$alter = "";
				foreach ( $Query_Offers as $lista ):
					if($alter == ""){$alter = "class='alternate'";}else{$alter = "";}
					$Query_Cancel_Offer = plugDB("SELECT c.texto, c.date, ct.denomination as type FROM cancel_offert c LEFT JOIN cancellation_type ct ON c.type=ct.id WHERE offert = '".$lista->id."'", "result");
				?>
				
				<tr valign="top" <?php echo $alter; ?>>
					<td>
						<?php Gen_Btn_Experto($lista->expert);?>
					</td>

					<form method="post">
					<?php foreach($lista as $it => $va):?>
						<?php if(!in_array($it, $exclude_tabla_offers)):?>
							<?php if($it == "start_date"):?>
								<td><input type="datetime-local" name="start_date" value="<?php echo (new DateTime($va))->format("Y-m-d\\TH:i:s");?>"></td>
							<?php elseif($it == "completed_date"):?>
								<td><input type="datetime-local" name="completed_date" value="<?php echo (new DateTime($va))->format("Y-m-d\\TH:i:s");?>"></td>
							<?php elseif($it == "status"):?>
								<td>
									<select name="status">
										<?php echo func_select_estado_servicio(
											array("progress"=>"progress", "acepted"=>"acepted", "scheduled"=>"scheduled", "completed"=>"completed", "rejected"=>"rejected"), 
											$va
										);?>
									</select>
								</td>
							<?php elseif($it == "collaborator"):?>
								<td><select name="collaborator"><?php echo Select_ColByExpert($lista->expert, $lista->collaborator);?></select></td>
							<?php elseif($it == "hour"):?>
								<td><input type="time" name="hour" value="<?php echo $va;?>"></td>
							<?php else:?>
								<td><?php echo $va;?></td>
							<?php endif;?>
						<?php endif;?>
					<?php endforeach; ?>
					<td>
						<input type="hidden" name="id" value="<?php echo $_POST["id"];?>">
						<input type="hidden" name="editar_servicio" value="ok">
						<input type="hidden" name="id_offer" value="<?php echo $lista->id;?>">
						<input type="submit" name="update_offer" value="Guardar" class="button action">
					</td>
					</form>
				</tr>

				<?php if( Count($Query_Cancel_Offer) > 0 ):?>
					<?php 
					foreach ( $Query_Cancel_Offer as $lista_cancel ):
					?>
				<tr>
					<td><h3>Cancelado por el Experto</h3></td>
					<td><?php echo $lista_cancel->texto;?></td>
					<td><?php echo $lista_cancel->date;?></td>
					<td><?php echo $lista_cancel->type;?></td>
				</tr>
				<?php endforeach; ?>
				<?php endif;?>


				<?php endforeach;?>
			</tbody>
		</table>



		<?php if( Count($Query_Chats) > 0 ):?>
		<h2>Chats Activos</h2>
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

		<?php if( Count($Query_Problemas) > 0 ):?>
		<h2>Problemas</h2>
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



	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
	<script type="text/javascript">
	function Do_Select_Category(){
		//alert( $("#select_service").val() );
		$.ajax({
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
		$.ajax({
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
	";
	$Query = plugDB($Qstr, "result");
	?>
	<div class="wrap">
		<h2>Transacciones -> Planes</h2>
		<h4><?php echo Count($Query);?> Registros encontrados</h4>
		<?php if($Query[0] != null):?>
			<div div class="alignleft bulkactions">
				<form action='<?php bloginfo('template_url'); ?>/admin_db/export_xls.php' target='_blank' method='post'>
					<input type='hidden' name='type' value='transacciones_planes'/>
					<input type='hidden' name='xhr' value='<?php echo base64_encode($Qstr); ?>'/>
					<input type="submit" class="button action" id="exportar" name="exportar" value="Exportar a excel" />
				</form>
			</div><br><br>
			<table class="wp-list-table widefat" cellspacing="0">
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
							<td><?php echo $va;?></td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach;?>
			</table>
		<?php endif;?>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
	<script type="text/javascript"></script>
	<?php
}
function page_transacciones_fixcoins(){
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
	";
	$Query = plugDB($Qstr, "result");
	?>
	<div class="wrap">
		<h2>Transacciones -> Fixcoins</h2>
		<h4><?php echo Count($Query);?> Registros encontrados</h4>
		<?php if($Query[0] != null):?>
			<div div class="alignleft bulkactions">
				<form action='<?php bloginfo('template_url'); ?>/admin_db/export_xls.php' target='_blank' method='post'>
					<input type='hidden' name='type' value='transacciones_fixcoins'/>
					<input type='hidden' name='xhr' value='<?php echo base64_encode($Qstr); ?>'/>
					<input type="submit" class="button action" id="exportar" name="exportar" value="Exportar a excel" />
				</form>
			</div><br><br>
			<table class="wp-list-table widefat" cellspacing="0">
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
							<td><?php echo $va;?></td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach;?>
			</table>
		<?php endif;?>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
	<script type="text/javascript"></script>
	<?php
}
//
function page_atencion_cliente(){
	if( isset($_POST["editar_atencion"]) ){
		page_atencion_cliente_editar();
		return;
	}
	//
	$Qstr = "SELECT cs.id, u.name, u.email, cs.date_registry, ty.denomination as tipo, cs.description, st.denomination as status
	FROM customer_support cs
	LEFT JOIN users u ON cs.user = u.id	
	LEFT JOIN type_customer_support ty ON cs.type_customer_support=ty.id
	LEFT JOIN status_customer_support st ON cs.status = st.id
	ORDER BY cs.id DESC
	";
	$Query = plugDB($Qstr, "result");
	?>
	<div class="wrap">
		<h2>Atencion al Cliente</h2>
		<h4><?php echo Count($Query);?> Registros encontrados</h4>
		<?php if($Query[0] != null):?>
			<div div class="alignleft bulkactions">
				<form action='<?php bloginfo('template_url'); ?>/admin_db/export_xls.php' target='_blank' method='post'>
					<input type='hidden' name='type' value='transacciones_fixcoins'/>
					<input type='hidden' name='xhr' value='<?php echo base64_encode($Qstr); ?>'/>
					<input type="submit" class="button action" id="exportar" name="exportar" value="Exportar a excel" />
				</form>
			</div><br><br>
			<table class="wp-list-table widefat" cellspacing="0">
				<thead>
					<tr valign="top">
						<?php foreach($Query[0] as $key => $value):?>
							<th><?php echo $key; ?></th>
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
								<input type="submit" name="editar_atencion" value="Editar" class="button action">
							</form>
						</td>
					</tr>
				<?php endforeach;?>
			</table>
		<?php endif;?>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
	<script type="text/javascript"></script>
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
	<div class="wrap">
		<h2>Cupones de Referidos</h2>
		<h4><?php echo Count($Query);?> Registros encontrados</h4>
		<?php if($Query[0] != null):?>
			<div div class="alignleft bulkactions">
				<form action='<?php bloginfo('template_url'); ?>/admin_db/export_xls.php' target='_blank' method='post'>
					<input type='hidden' name='type' value='transacciones_fixcoins'/>
					<input type='hidden' name='xhr' value='<?php echo base64_encode($Qstr); ?>'/>
					<input type="submit" class="button action" id="exportar" name="exportar" value="Exportar a excel" />
				</form>
			</div><br><br>
			<table class="wp-list-table widefat" cellspacing="0">
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
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
	<script type="text/javascript"></script>
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
	<div class="wrap">
		<h2>Reporte Clientes</h2>
		<h4><?php echo Count($Query);?> Registros encontrados</h4>
		<?php if($Query[0] != null):?>
			<div div class="alignleft bulkactions">
				<form action='<?php bloginfo('template_url'); ?>/admin_db/export_xls.php' target='_blank' method='post'>
					<input type='hidden' name='type' value='transacciones_fixcoins'/>
					<input type='hidden' name='xhr' value='<?php echo base64_encode($Qstr); ?>'/>
					<input type="submit" class="button action" id="exportar" name="exportar" value="Exportar a excel" />
				</form>
			</div><br><br>
			<table class="wp-list-table widefat" cellspacing="0">
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
							<td><?php echo $va;?></td>
						<?php endforeach; ?>
						
					</tr>
				<?php endforeach;?>
			</table>
		<?php endif;?>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
	<script type="text/javascript"></script>
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
function Gen_Btn_Experto($id_expert, $show_tipo = false){
	$Query__Expert = plugDB("SELECT u.name as nombre, u.id as id_user FROM experts e LEFT JOIN users u ON e.user = u.id WHERE e.id = '".$id_expert."'", "row");
	?>
	<form method="post" action="admin.php?page=profesionales" target="_blank">
		<input type="hidden" name="id" value="<?php echo $Query__Expert->id_user;?>">
		<input type="hidden" name="editar_cliente" value="ok">
		<input type="submit" value="<?php echo $Query__Expert->nombre;?><?php if($show_tipo == true) echo " (experto)";?>" class="button action">
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
?>