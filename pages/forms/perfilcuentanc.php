<?php require_once('../../connections/crm.php');  /// conecta a base de datos ?>
<?php
// Función para el cierre de sesión 
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
    //Para que el usuario salga completamente se limpian todas las variables
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "../../logincrm.php";//Envía a la página de inicio de sesión de administrador
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}

ini_set( 'session.cookie_httponly', 1 );

//función para cierre inactivo de sesión y limpieza de cookies
 function tiempo() {  
   if (isset($_SESSION['LAST_LOGIN']) && (time() - $_SESSION['LAST_LOGIN'] > 1800)) {  
     if (isset($_COOKIE[session_name()])) {  
       setcookie(session_name(), "", time() - 3600, "/");  
       //limpiamos completamente el array superglobal    
       session_unset();  
       //Eliminamos la sesión (archivo) del servidor   
       session_destroy();  
     }  
     header("../../logincrm.html"); //redirigir al punto de partida para identificarse  
     exit;  
   }  
   //...  
 }  




// *** Restringe el acceso a la pagina sin login previamente hecho
if (!isset($_SESSION)) {
    session_start();
}

//Identifica tipo de string que estan dentro del formulario//
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

//Busqueda datos de usuario
$colname_usuario = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_usuario = $_SESSION['MM_Username'];
}
mysql_select_db($database_crm, $crm);
$query_usuario = sprintf("SELECT * FROM Asesores WHERE identificacion = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $crm) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);

if($row_usuario['Rol']!='HABILITADO'){//Validar si el usuario se encuentra habilitado para acceder a CRM
$MM_authorizedUsers = "usuariovalido";//Se asigna nombre de sesion de usuario
$MM_donotCheckaccess = "false";//Se deshabilita el checking de acceso	
}
else
{
	header("Location:../../noautorizado.php");
}

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
	// Por seguridad, no se inicia si el usuario no esta autorizado

  $isValid = False; 
  //Cuando el usuario tiene ingreso, la variable Session MM_Username es igual a la de acceso del usuario, es decir su número de identificación.
  //Por otro lado, se sabe que el usario no esta con Login si la variable esta en blanco.
  if (!empty($UserName)) { 
    //Se puede restringir el acceso a ciertos ID, mediante el login accedido.
    // Convierte los strings restringidos en arrays.
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "logincrm.php";// Si el usuario no esta autorizado a ver esta pagina lo devolvera a la pagina principal de acceso a login
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
  
  //*** Termina validación usuario ***//  
}

//Busqueda datos cuenta

if (isset($_GET['cuenta'])) {
  $colname_cuenta=$_GET['cuenta'];
}

mysql_select_db($database_crm, $crm);
$query_cuenta = sprintf("SELECT * FROM Cuentas WHERE idCuentas = %s", GetSQLValueString($colname_cuenta, "text"));
$cuenta= mysql_query($query_cuenta, $crm) or die(mysql_error());
$row_cuenta = mysql_fetch_assoc($cuenta);
$totalRows_cuenta = mysql_num_rows($cuenta);

//Busqueda datos propuesta

mysql_select_db($database_crm, $crm);
$query_oportunidad = sprintf("SELECT * FROM Oportunidad WHERE Cuentas_idCuentas = %s", GetSQLValueString($colname_cuenta, "text"));
$oportunidad= mysql_query($query_oportunidad, $crm) or die(mysql_error());
$row_oportunidad = mysql_fetch_assoc($oportunidad);
$totalRows_oportunidad = mysql_num_rows($oportunidad);

//Busqueda datos contacto

mysql_select_db($database_crm, $crm);
$query_contacto = sprintf("SELECT * FROM Contactos WHERE Cuentas_idCuentas = %s", GetSQLValueString($colname_cuenta, "text"));
$contacto= mysql_query($query_contacto, $crm) or die(mysql_error());
$row_contacto = mysql_fetch_assoc($contacto);
$totalRows_contacto = mysql_num_rows($contacto);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
// se insertan valores segun lo editado por el usuario. Estos valores bajo inyección SQL se van a la tabla de la base de datos//
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formcontacto")) {// Al momento de oprimir el boton continuar verifica el nombre del formulario y ejecuta los comandos sql
  $insertSQL = sprintf("INSERT INTO Contactos (idContacto,nombreContacto,cargoContacto,correoContacto,telefonoContacto,Cuentas_idCuentas,Estado) VALUES (%s,%s, %s, %s, %s, %s,%s)",
                       GetSQLValueString($_POST['codigoContacto'], "text"),// Ingresa el numero de identificación de la empresa con el cual el usuario esta llenando la encuesta
                       GetSQLValueString($_POST['nombreContacto'], "text"),// Ingresa datos que ha digitado el usuario
					   GetSQLValueString($_POST['cargoContacto'], "text"),
                       GetSQLValueString($_POST['correoContacto'], "text"),
                       GetSQLValueString($_POST['telefonoContacto'], "int"), 
                       GetSQLValueString($_POST['idCuentas'], "text"),
					   GetSQLValueString($_POST['Estado'], "text"));
  mysql_select_db($database_crm, $crm);// vinculación de la base de datos para inyeccion sql
  $Result1 = mysql_query($insertSQL, $crm) or die(mysql_error());// Acción a realizar en  la base de datos para inyeccion sql

  $insertGoTo = "perfilcuentanc.php";// envía a la siguiente página despues de oprimir el botón continuar
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
 
}

//Busqueda de datos de la cuenta
if (isset($_GET['cuenta'])) {
$colname_cuenta=$_GET['cuenta'];
}
							
mysql_select_db($database_crm, $crm);
$query_cuenta = sprintf("SELECT * FROM Cuentas WHERE idCuentas = %s", GetSQLValueString($colname_cuenta, "text"));
$cuenta= mysql_query($query_cuenta, $crm) or die(mysql_error());
$row_cuenta = mysql_fetch_assoc($cuenta);
$totalRows_cuenta = mysql_num_rows($cuenta);

//Conteo de las propuestas
mysql_select_db($database_crm, $crm);
$query_conteo = sprintf("SELECT COUNT(*) FROM Oportunidad WHERE Oportunidad.Cuentas_idCuentas  = %s AND Oportunidad.estadoOportunidad LIKE '%%Abierta%%' ", GetSQLValueString($colname_cuenta, "text"));
$conteo = mysql_query($query_conteo, $crm) or die(mysql_error());
$row_conteo = mysql_fetch_assoc($conteo);
$totalRows_conteo = mysql_num_rows($conteo);

mysql_select_db($database_crm, $crm);
$query_conteo1 = sprintf("SELECT COUNT(*) FROM Oportunidad WHERE Oportunidad.Cuentas_idCuentas = %s AND Oportunidad.estadoOportunidad LIKE '%%Positiva%%' ", GetSQLValueString($colname_cuenta, "text"));
$conteo1 = mysql_query($query_conteo1, $crm) or die(mysql_error());
$row_conteo1 = mysql_fetch_assoc($conteo1);
$totalRows_conteo1 = mysql_num_rows($conteo1);	

mysql_select_db($database_crm, $crm);
$query_conteo2 = sprintf("SELECT COUNT(*) FROM Oportunidad WHERE Oportunidad.Cuentas_idCuentas = %s AND Oportunidad.estadoOportunidad LIKE '%%Negativa%%' ", GetSQLValueString($colname_cuenta, "text"));
$conteo2 = mysql_query($query_conteo2, $crm) or die(mysql_error());
$row_conteo2 = mysql_fetch_assoc($conteo2);
$totalRows_conteo2 = mysql_num_rows($conteo2);	

mysql_select_db($database_crm, $crm);
$query_conteo3 = sprintf("SELECT COUNT(*) FROM Oportunidad WHERE Oportunidad.Cuentas_idCuentas = %s ", GetSQLValueString($colname_cuenta, "text"));
$conteo3 = mysql_query($query_conteo3, $crm) or die(mysql_error());
$row_conteo3 = mysql_fetch_assoc($conteo3);
$totalRows_conteo3 = mysql_num_rows($conteo3);	

//Conteo de la busqueda de contactos existentes de la cuenta

mysql_select_db($database_crm, $crm);
$query_conteo4 = sprintf("SELECT COUNT(*) FROM Contactos WHERE Contactos.Cuentas_idCuentas = %s ", GetSQLValueString($colname_cuenta, "text"));
$conteo4 = mysql_query($query_conteo4, $crm) or die(mysql_error());
$row_conteo4 = mysql_fetch_assoc($conteo4);
$totalRows_conteo4 = mysql_num_rows($conteo4);	

// Seleccionar tareas del asesor 
mysql_select_db($database_crm, $crm);
$query_tareas = sprintf("SELECT * FROM Tarea AS tr INNER JOIN Asesores AS ase ON tr.Asesores_idAsesor=ase.idAsesor WHERE  Oportunidad_idOportunidad=%s ORDER BY fechaProgramada DESC", GetSQLValueString($row_oportunidad['idOportunidad'],"text"));
$tareas= mysql_query($query_tareas, $crm) or die(mysql_error());
$row_tareas = mysql_fetch_assoc($tareas);
$totalRows_tareas = mysql_num_rows($tareas);

//Busqueda de tareas pendientes a punto de vencerse
mysql_select_db($database_crm, $crm);
$query_alerta = sprintf("SELECT Cuentas_idCuentas,Oportunidad_idOportunidad, nombreEmpresa, TIMESTAMPDIFF( DAY ,fechaProgramada, CURRENT_DATE()) as diferencia FROM Tarea as tr INNER JOIN Cuentas as ct ON tr.Cuentas_idCuentas=ct.idCuentas WHERE tr.Asesores_idAsesor=%s AND tr.Realizada='Programada' AND (TIMESTAMPDIFF(DAY ,fechaProgramada, CURRENT_DATE())>-2)", GetSQLValueString($row_usuario['idAsesor'], "text"));
$alerta = mysql_query($query_alerta, $crm) or die(mysql_error());
$row_alerta = mysql_fetch_assoc($alerta);
$totalRows_alerta = mysql_num_rows($alerta);	
 ?>
<!DOCTYPE html>
<html>
	<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>CRM GENNCO | Cuentas</title>
	<link rel="shortcut icon" type="image/x-icon" href="../../dist/img/favicon.ico">
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap 3.3.6 -->
	<link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">
	<!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
	<link rel="stylesheet" href="../../dist/css/skins/_all-skins.min.css">
	<!-- DataTables -->
	<link rel="stylesheet" href="../../plugins/datatables/dataTables.bootstrap.css">
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

	<!-- Script para bloquear caracteres, solo para que se puedan escribir numeros -->
	<script type="text/javascript"> function controltag(e) {
        tecla = (document.all) ? e.keyCode : e.which;
        if (tecla==8) return true;
        else if (tecla==0||tecla==9)  return true;
       // patron =/[0-9\s]/;// -> solo letras
        patron =/[0-9\s]/;// -> solo numeros
        te = String.fromCharCode(tecla);
        return patron.test(te);
    }
	</script>
	<!-- Script para validar campos de textos -->
	<link href="../../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
	<script src="../../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
	</head>

	<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
      <header class="main-header"> 
    <!-- Logo --> 
    <a href="../../inicio.php" class="logo"> 
        <!-- mini logo for sidebar mini 50x50 pixels --> 
        <span class="logo-mini"><b>
        <image src="../../dist/img/logocrm.png" style=" width: 50px; height: 50px">
        </b></span> 
        <!-- logo for regular state and mobile devices --> 
        <span class="logo-lg">
    <image src="../../dist/img/logocrm.png" style="width:50px; height: 50px" >
    <b>CRM</b>GENNCO </span> </a>
    <nav class="navbar navbar-static-top"> 
          <!-- Sidebar toggle button--> 
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </a>
          <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
              <!-- Messages: style can be found in dropdown.less-->
              <li class="dropdown messages-menu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-envelope-o"></i> <span class="label label-success"></span> </a>
                <ul class="dropdown-menu">
                </ul>
          </li>
                  <!-- Notifications: style can be found in dropdown.less -->
          <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning"><?php if($totalRows_alerta > 0){echo $totalRows_alerta;}?></span>
            </a>
            <ul class="dropdown-menu">
             <?php if($totalRows_alerta > 0){?>
              <li class="header">Tiene <?php echo $totalRows_alerta?> tarea(s) por vencerse</li>
              <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu">
                <?php
				do{
                  echo"<li>
                    <a href='pages/forms/perfiloportunidad.php? oportunidad=".$row_alerta['Oportunidad_idOportunidad']."'>
                      <i class='fa fa-calendar text-red'></i> La oportunidad ".$row_alerta['Oportunidad_idOportunidad']." de <br> la empresa ".$row_alerta['nombreEmpresa'].".<br> Tiene una tarea por vencerse.
                    </a>
                  </li>";
				} while ($row_alerta = mysql_fetch_assoc($alerta));
				 ?>
                 </ul>
              </li>
              <li class="footer"><a href="#"></a></li>
              <?php } ?>
            </ul>
          </li>
          <!-- Tasks: style can be found in dropdown.less -->
              <li class="dropdown tasks-menu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-flag-o"></i> <span class="label label-danger"></span> </a>
                <ul class="dropdown-menu">
                </ul>
          </li>
              <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <img src="../../<?php echo $row_usuario['foto'] ?>" class="user-image" alt="User Image"> <span class="hidden-xs"><?php echo $row_usuario['nombreAsesor']?></span> </a>
            <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header"> <img src="../../<?php echo $row_usuario['foto'] ?>" class="img-circle" alt="User Image">
                <p> <?php echo $row_usuario['cargoAsesor']?> </p>
              </li>
                  <!-- Menu Footer-->
                  <li class="user-footer">
                <div class="pull-right"> <a href="<?php echo $logoutAction ?>" class="btn btn-default btn-flat">Cerrar Sesión</a> </div>
              </li>
                </ul>
          </li>
            </ul>
      </div>
        </nav>
  </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar"> 
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar"> 
          <!-- Sidebar user panel -->
          <div class="user-panel">
        <div class="pull-left image"> <img src="../../<?php echo $row_usuario['foto'] ?>" class="img-circle" alt="User Image"> </div>
        <div class="pull-left info">
              <p><?php echo $row_usuario['nombreAsesor']?></p>
              <a href="#"><i class="fa fa-circle text-success"></i> Online</a> </div>
      </div>
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
        <li class="header">MENÚ PRINCIPAL</li>
        <li class="treeview"> <a href="../../inicio.php"> <i class="fa fa-dashboard"></i> <span>Pagina Principal</span> </a>
              <ul class="treeview-menu">
            <li><a href="../../inicio.php"><i class="fa fa-circle-o"></i> Pagina Principal</a></li>
          </ul>
            <li class="treeview active"> <a href="#"> <i class="fa fa-edit"></i> <span>Cuentas</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span> </a>
              <ul class="treeview-menu">
                <li ><a href="nuevoregistro.php"><i class="fa fa-circle-o"></i> Crear cuentas</a></li>
                <li ><a href="cuentascreadas.php"><i class="fa fa-circle-o"></i> Cuentas Creadas</a></li>
              </ul>
            </li>
        <li class="treeview"> <a href="#"> <i class="fa fa-edit"></i> <span>Oportunidades</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span> </a>
              <ul class="treeview-menu">
            <li ><a href="nuevaoportunidad.php"><i class="fa fa-circle-o"></i> Crear Nueva oportunidad</a></li>
            <li><a href="oportunidadescreadas.php"><i class="fa fa-circle-o"></i> Oportunidades Creadas</a></li>
          </ul>
            </li>
      </ul>
        </section>
    
    <!-- /.sidebar --> 
  </aside>
      
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper"> 
    <!-- Content Header (Page header) -->
    <section class="content-header">
          <h1> Perfil de la cuenta </h1>
          <ol class="breadcrumb">
        <li><a href="../../inicio.php"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li><a href="#">Cuentas</a></li>
        <li><a href="cuentascreadas.php">Cuentas Creadas</a></li>
        <li> <a href="perfilcuenta.php?cuenta=<?php echo $row_cuenta['idCuentas']?>">Perfil Cuentas</a></li>
        <li class="active" >Nuevo Contacto</li>
      </ol>
        </section>
    
    <!-- Main content -->
    <section class="content">
    <div class="row">
          <div class="col-md-3"> 
        
        <!-- Profile Image -->
        <div class="box box-primary">
              <div class="box-body box-profile"> <img class="profile-user-img img-responsive img-circle" src="../../<?php echo $row_cuenta['logoCuenta']?>" alt="User profile picture">
            <h3 class="profile-username text-center"><?php echo $row_cuenta['nombreEmpresa']?></h3>
            <p class="text-muted text-center">Nit:<?php echo $row_cuenta['Nit']?></p>
            <ul class="list-group list-group-unbordered">
                  <li class="list-group-item"> <b>Propuestas Creadas</b> <a class="pull-right"><?php echo $row_conteo3['COUNT(*)']; ?></a> </li>
                  <li class="list-group-item"> <b>Propuestas Abiertas</b> <a class="pull-right"><?php echo $row_conteo['COUNT(*)']; ?></a> </li>
                  <li class="list-group-item"> <b>Propuestas Cerradas Positivamente</b> <a class="pull-right"><?php echo $row_conteo1['COUNT(*)']; ?></a> </li>
                  <li class="list-group-item"> <b>Propuestas Cerradas Negativamente</b> <a class="pull-right"><?php echo $row_conteo2['COUNT(*)']; ?></a> </li>
                </ul>
            <a href="perfilcuenta.php?cuenta=<?php echo $row_cuenta['idCuentas']?>" class="btn btn-primary btn-block"><b>Volver a la cuenta</b></a> </div>
              <!-- /.box-body --> 
            </div>
        <!-- /.box --> 
        
        <!-- About Me Box -->
        <div class="box box-primary">
              <div class="box-header with-border">
            <h3 class="box-title">Información empresa</h3>
          </div>
              <!-- /.box-header -->
              <div class="box-body"> <strong><i class="fa fa-book margin-r-5"></i> Codigo CRM</strong>
            <p class="text-muted"> <?php echo $row_cuenta["idCuentas"]?> </p>
            <hr>
            <strong><i class="fa fa-map-marker margin-r-5"></i> Dirección</strong>
            <p class="text-muted"><?php echo $row_cuenta['direccionCuenta']?></p>
            <hr>
            <strong><i class="margin-r-5 ion-ios-email"></i>Correo</strong>
            <p> <?php echo $row_cuenta['correoCuenta']?></p>
            <hr>
            <strong><i class="fa fa-file-text-o margin-r-5 ion-android-call"></i> Teléfonos</strong>
            <p class="ion-iphone"> Celular: <?php echo $row_cuenta['celularCuenta']?></p>
            <p class="ion-ios-telephone"> Fijo: <?php echo $row_cuenta['telefonoCuenta']?></p>
            <hr>
            <strong><i class="fa fa-file-text-o margin-r-5"></i> Creado desde:</strong>
            <p><?php echo $row_cuenta['fechaCreacion']?></p>
          </div>
              
              <!-- /.box-body --> 
            </div>
        <!-- /.box --> 
      </div>
          <!-- /.col -->
          <div class="col-md-9">
        <div class="nav-tabs-custom">
              <ul class="nav nav-tabs">
            <li><a href="perfilcuenta.php?cuenta=<?php echo $row_cuenta['idCuentas']?>">Contactos Creados</a></li>
            <li class="active" ><a href="#" data-toggle="tab">Nuevo contacto</a></li>
          </ul>
              <div class="active tab-pane" id="perfilcuentanc.php">
            <div class="box">
                  <div class="box-header">
                <h3 class="box-title">Ingresar un nuevo contacto</h3>
              </div>
                  <!-- /.box-header -->
                  <div class="box-body">
                <form role="form" action="<?php echo $editFormAction ?>" name="formcontacto" method="post" id="formcontacto">
                      
                      <!-- text input -->
                      <div class="form-group">
                    <label>Codigo Contacto</label>
                    <?php
					// generar consecutivo para el contacto de la empresa
					$id=$row_cuenta['idCuentas']; 
					$guion="-";
					$cons=$row_conteo4['COUNT(*)']+1;
					?>
                    <input type="text" class="form-control" placeholder="Ingrese codigo del nuevo contacto" name="codigoContacto" readonly value="<?php echo $id.$guion.$cons// concatena los valores de las variables que sea asignan en el consectivo?>">
                  </div>
                      <!-- text input -->
                      <div class="form-group"> <span id="sprytextfield1">
                        <label>Nombre de contacto</label>
                        <input type="text" class="form-control" placeholder="Ingrese nombre del nuevo contacto" name="nombreContacto">
                        <span class="textfieldRequiredMsg">Debe ingresar el nombre del contacto</span></span> </div>
                      <!-- text input -->
                      <div class="form-group"> <span id="sprytextfield2">
                        <label>Cargo</label>
                        <input type="text" class="form-control" placeholder="Ingrese cargo del contacto" name="cargoContacto" >
                        <span class="textfieldRequiredMsg">Debe ingresar el cargo del contacto</span></span> </div>
                      <div class="form-group"> <span id="sprytextfield3">
                        <label>Correo Electrónico </label>
                        <input class="form-control" type="text" placeholder="Ingrese correo electrónico contacto" name="correoContacto" >
                        <span class="textfieldRequiredMsg">Debe ingresar el correo electronico del contacto</span></span> </div>
                      <!-- text input -->
                      <div class="form-group"> <span id="sprytextfield4">
                        <label>Celular</label>
                        <input class="form-control" type="text" placeholder="Ingrese Celular Contacto" name="telefonoContacto"  onKeyPress="return controltag(event)" >
                        <span class="textfieldRequiredMsg">Debe ingresar el celular de contacto</span></span> </div>
                      <input class="form-control" type="hidden" name="idCuentas" value="<?php echo $row_cuenta['idCuentas']?>">
                      <input class="form-control" type="hidden" name="Estado" value="Activo">
                      <div class="box-footer">
                    <button type="reset" class="btn btn-default" onClick="document.getElementById('formcontacto').reset()">Borrar</a></button>
                    <input type="hidden" name="MM_insert" value="formcontacto" />
                    <button type="submit" class="btn btn-info pull-right">Crear</button>
                  </div>
                    </form>
              </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<footer class="main-footer">
      <div class="pull-right hidden-xs"> <b>Version</b> 2.3.6 </div>
      <strong>Copyright &copy; 2014-2016 <a href="http://almsaeedstudio.com">Almsaeed Studio</a>.</strong> All rights
      reserved. </footer>
</div>
<!-- ./wrapper --> 

<script>
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3","email");
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");

</script> 
<!-- jQuery 2.2.3 --> 
<script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script> 
<!-- Bootstrap 3.3.6 --> 
<script src="../../bootstrap/js/bootstrap.min.js"></script> 

<!-- SlimScroll --> 
<script src="../../plugins/slimScroll/jquery.slimscroll.min.js"></script> 
<!-- FastClick --> 
<script src="../../plugins/fastclick/fastclick.js"></script> 
<!-- AdminLTE App --> 
<script src="../../dist/js/app.min.js"></script> 
<!-- AdminLTE for demo purposes --> 
<script src="../../dist/js/demo.js"></script> 
<!-- page script -->

</body>
</html>