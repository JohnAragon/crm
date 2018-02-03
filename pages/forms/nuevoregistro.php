<?php require_once('../../connections/crm.php');  /// conecta a base de datos ?>
<?php
header("Content-Type: text/html;charset=utf-8");
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
     header("../../logincrm.php"); //redirigir al punto de partida para identificarse  
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


// Mostrar próximo autoconsecutivo
mysql_select_db($database_crm, $crm);
$query_consecutivo = sprintf("SELECT COUNT(*) FROM Cuentas");
$consecutivo= mysql_query($query_consecutivo, $crm) or die(mysql_error());
$row_consecutivo = mysql_fetch_assoc($consecutivo);
$totalRows_consecutivo = mysql_num_rows($consecutivo);
$next_id = $row_consecutivo['COUNT(*)']+1; 


// Se invoca la accion editFormaAction para realizar las operaciones que de aquí en adelante se programan que se incluyen dentro del formularios//
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
// se insertan valores segun lo editado por el usuario. Estos valores bajo inyección SQL se van a la tabla de la base de datos//
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formcuentas")) {// Al momento de oprimir el boton continuar verifica el nombre del formulario y ejecuta los comandos sql
  
  $insertSQL = sprintf("INSERT INTO Cuentas (idCuentas,Nit,nombreEmpresa,ciudadCuenta, dptoCuenta, direccionCuenta,telefonoCuenta,celularCuenta,correoCuenta,tipoCuenta,sectorCuenta, logoCuenta,fechaCreacion,creador) VALUES (%s,%s,%s,%s,%s, %s, %s, %s, %s, %s,%s,%s,%s,%s)",
                       GetSQLValueString($_POST['idempresa'], "text"),// Ingresa el numero de identificación de la empresa con el cual el usuario esta llenando la encuesta
                       GetSQLValueString($_POST['NIT'], "int"),// Ingresa datos que ha digitado el usuario
                       GetSQLValueString($_POST['nombreEmpresa'], "text"),
                       GetSQLValueString($_POST['ciudadCuenta'], "text"),
					   GetSQLValueString($_POST['dptoCuenta'], "text"), 
					   GetSQLValueString($_POST['direccionCuenta'], "text"),  
                       GetSQLValueString($_POST['telefonoCuenta'], "text"),
                       GetSQLValueString($_POST['celularCuenta'], "int"),
                       GetSQLValueString($_POST['correoCuenta'], "text"),
					   GetSQLValueString($_POST['tipoEmpresa'], "text"),
					   GetSQLValueString($_POST['sectorEmpresa'], "text"),	
					   GetSQLValueString($_POST['logo'], "text"),
					   GetSQLValueString($_POST['fechaCreacion'], "date"),
					   GetSQLValueString($_POST['creador'], "text"));
					   
  mysql_select_db($database_crm, $crm);// vinculación de la base de datos para inyeccion sql
  $Result1 = mysql_query($insertSQL, $crm) or die(mysql_error());// Acción a realizar en  la base de datos para inyeccion sql

  $insertGoTo = "nuevoregistro.php";// envía a la siguiente página despues de oprimir el botón continuar
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
 
}	

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
  <title>CRM GENNCO | Oportunidades</title>
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
  <!-- daterange picker -->
  <link rel="stylesheet" href="../../plugins/daterangepicker/daterangepicker.css">
  <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="../../plugins/datepicker/datepicker3.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
<link rel="stylesheet" href="../../dist/css/skins/_all-skins.min.css">

	<!-- Script para validación de listas desplegables-->
    <link href="../../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css"/>
	<script src="../../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
  
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
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
   <a href="../../inicio.php" class="logo"> 
       <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b><image src="../../dist/img/logocrm.png" style=" width: 50px; height: 50px"></b></span>	
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><image src="../../dist/img/logocrm.png" style="width:50px; height: 50px" ><b>CRM</b>GENNCO </span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Messages: style can be found in dropdown.less-->
          <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-envelope-o"></i>
              <span class="label label-success"></span>
            </a>
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
          <li class="dropdown tasks-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-flag-o"></i>
              <span class="label label-danger"></span>
            </a>
            <ul class="dropdown-menu">
            </ul>
          </li>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="../../<?php echo $row_usuario['foto'] ?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo $row_usuario['nombreAsesor']?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="../../<?php echo $row_usuario['foto'] ?>" class="img-circle" alt="User Image">

                <p>
                <?php echo $row_usuario['cargoAsesor']?>
                </p>
              </li>  
              <!-- Menu Footer-->
              <li class="user-footer">
               
                <div class="pull-right">
                  <a href="<?php echo $logoutAction ?>" class="btn btn-default btn-flat">Cerrar Sesión</a>
                </div>
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
        <div class="pull-left image">
          <img src="../../<?php echo $row_usuario['foto'] ?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo $row_usuario['nombreAsesor']?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
  
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">MENÚ PRINCIPAL</li>
        <li class="treeview">
          <a href="../../inicio.php">
            <i class="fa fa-dashboard"></i> <span>Página Principal</span>
          </a>
        
          </li>
                <li class="treeview active">
          <a href="#">
            <i class="fa fa-edit"></i> <span>Cuentas</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="active"><a href="nuevoregistro.php"><i class="fa fa-circle-o"></i> Crear cuentas</a></li>
             <li><a href="cuentascreadas.php"><i class="fa fa-circle-o"></i> Cuentas Creadas</a></li>
          </ul>
        </li>
           <li class="treeview ">
          <a href="#">
            <i class="fa fa-edit"></i> <span>Oportunidades</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
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
      <h1>
        Módulo de Cuentas</h1>
      <ol class="breadcrumb"><li><a href="../../inicio.php">Inicio</a></li>
        <li><a href="#">Cuentas</a></li>
        <li class="active">Crear Nueva Cuenta</li>
      </ol>
    </section>
    
     <section class="content">
      <div class="row">
        <div class="col-md-3"> 
          
          <!-- Profile Image -->
          <div class="box box-primary">
           <div class="box-body box-profile">
           <center><span class="ion-folder" style="font-size: 90px" ></span></center>
              <center><h3>Nueva Cuenta</h3></center>
                
               
              <a href="cuentascreadas.php" class="btn btn-primary btn-block"><b>Volver a listado de cuentas</b></a> </div>
              
               
           </div>
           
            <!-- /.box-body --> 
          </div>
          <!-- /.box --> 
  
        <div class="col-md-9">
          <!-- general form elements disabled -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Nuevo registro de cuenta</h3>
            </div>
            <!-- /.box-header -->
            
            <div class="box-body">
            <!--Formulario para crear una cuenta, la acción que toma esta estipulada en php con la funcion editFormAction, para poder insertar los datos-->
              <form role="form" name="formcuentas" action="<?php echo $editFormAction?>" method="post" id="formcuentas" enctype="multipart/form-data">
                <!-- text input -->
                <div class="form-group">
                  <label>Código Empresa</label>
                  <!-- En el input de cada entrada el name es el identificador para inserta sql en php  -->
                 
                  <input type="text" class="form-control"  name="idempresa"  title="Ingrese el codigo de la empresa" style="color:#CCC" value="GENN-CT-<?php echo $next_id?>"  readonly>
                 
                 </div>
                <!-- text input -->
                 <div class="form-group">
                 <span id="sprytextfield1">
                  <label>NIT</label>
                  <input type="text" class="form-control" placeholder="Ingrese el nit de la empresa" name="NIT" onKeyPress="return controltag(event)">
                <span class="textfieldRequiredMsg">Debe ingresar el NIT de la empresa</span></span>
                </div> 
                 <!-- text input -->
                <div class="form-group">
                  <span id="sprytextfield2">
                  <label>Nombre de la empresa</label>
                  <input type="text" class="form-control" placeholder="Ingrese el nombre de la empresa" name="nombreEmpresa"   >
                  <span class="textfieldRequiredMsg">Debe ingresar el nombre de de la empresa</span></span>
                </div>
                  <!-- text input -->
                  <div class="form-group">
                   <span id="sprytextfield3">
                  <label>Ciudad</label>
                  <input type="text" class="form-control" placeholder="Ingrese la ciudad de la empresa" name="ciudadCuenta"  >
                  <span class="textfieldRequiredMsg">Debe ingresar la ciudad donde esta ubicada la empresa</span></span>
                </div> 
                  <!-- text input -->
                  <div class="form-group">
                  <span id="sprytextfield4">
                  <label>Departamento</label>
                  <input type="text" class="form-control" placeholder="Ingrese el departamento de la empresa" name="dptoCuenta"  >
                  <span class="textfieldRequiredMsg">Debe ingresar la departamento donde esta ubicada la empresa</span></span>
                </div> 
                <!-- text input -->
                  <div class="form-group">
                   <span id="sprytextfield5">
                  <label>Dirección</label>
                  <input type="text" class="form-control" placeholder="Ingrese la dirección de la empresa" name="direccionCuenta"  >
                  <span class="textfieldRequiredMsg">Debe ingresar la direccion de la empresa</span></span>
                </div> 
              <!-- text input -->
                <div class="form-group">
                <span id="sprytextfield6">
                  <label>Teléfono </label>
                  <input class="form-control" type="text" placeholder="Ingrese un número teléfonico de la empresa" name="telefonoCuenta">
                  <span class="textfieldRequiredMsg">Debe ingresar un número teléfonico </span></span>
                </div>
                
                <div class="form-group">
                <span id="sprytextfield7">
                  <label>Celular </label>
                  <input class="form-control" type="text" placeholder="Ingrese un número celular de la empresa" name="celularCuenta"  onKeyPress="return controltag(event)">
                  <span class="textfieldRequiredMsg">Debe ingresar un número de Celular</span></span>
                </div>
                 <!-- text input -->
                <div class="form-group">
                <span id="sprytextfield8">
                  <label>Correo Electrónico </label>
                  <input class="form-control" type="text" placeholder="Ingrese un correo de contacto de la empresa" name="correoCuenta" >
                  <span class="textfieldRequiredMsg">Debe ingresar un correo electrónico o el formato no es valido</span></span>
                </div>
                  <div class="form-group">
                  <label>Tipo empresa </label>
                  <span id="spryselect1">
                  
                          <select class="form-control" name="tipoEmpresa">
                            <option>Seleccione el tipo de empresa...</option>
                            <option value="Pyme">Pyme</option>
                            <option value="Pequeña">Pequeña</option>
                            <option value="Mediana">Mediana</option>
                            <option value="Grande">Grande</option>
                            <option value="Pública">Pública</option>
                          </select>
                       <span class="selectRequiredMsg">Seleccione el tipo de empresa</span></span>
                </div>
                <div class="form-group">
                  <label>Sector empresa</label>
                   <span id="spryselect2">
                          <select class="form-control" name="sectorEmpresa" >
                         	<option>Seleccione el sector...</option>
                  		   <option value="Construcción">Construcción</option>
                            <option value="Agropecurio y Agroindustría ">Agropecuaria y agroindustría </option>
                            <option value="Administración pública">Administración pública</option>
                            <option value="Industría Farmaceutica y salud">Industría Farmaceutica y salud</option>
                            <option value="Industría de alimentos">Industría de Alimentos </option>
                            <option value="Industría">Industría</option>
                            <option value="Seguridad y vigilancia ">Seguridad y vigilancia</option>
                            <option value="Aseo">Aseo</option>
                            <option value="Turismo y hotelería">Turismo y hotelería</option>
                            <option value="Comercio">Comercio</option>
                            <option value="Educación">Educación</option>
                            <option value="Sistemas y telecomunicaciones">Sistema y telecomunicaciones</option>
                            <option value="Logística, transporte e industía automotriz">Logística, transporte e industía automotriz</option>
                            <option value="Servicios Financieros">Servicios Financieros</option>
                            <option value="Servicios "> Servicios </option>
                            <option value="Minero y Energético">Minero y energético</option>
                            <option value="Gobierno">Gobierno</option>
                            <option value="Salud">Salud</option>
                            <option value="Hidrocarburos">Hidrocarburos</option>
                          </select>
                       <span class="selectRequiredMsg">Seleccione el sector de la empresa</span></span>
                </div>
                
                 <!-- Date -->
                 
                      <div class="form-group">
                        <label>Fecha de Creación</label>
        
                        <div class="input-group date">
                          <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                          </div>
                          <span id="sprytextfield9">
                          <input type="datetime" class="form-control pull-right" id="datepicker" name="fechaCreacion" placeholder="AAAA-MM-DD">
                          <span class="textfieldRequiredMsg">Debe ingresar una fecha o el formato es inválido</span></span>
                          
                        </div>
                       
                        
                        <!-- /.input group -->
                      </div>
                      <!-- /.form group -->
               <div class="box-footer">
                <button type="submit" class="btn btn-default" onClick="document.getElementById('formcuentas').reset()">Borrar</button>
                <input type="hidden"  id="logo" name="creador" value="<?php echo $row_usuario['idAsesor']?>">
                <input type="hidden"  id="logo" name="logo" value="dist/img/cuentas/company.jpg">
                <button type="submit" class="btn btn-info pull-right">Crear</button>
                <input type="hidden" name="MM_insert" value="formcuentas" />
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
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.3.6
    </div>
    <strong>Copyright &copy; 2014-2016 <a href="http://almsaeedstudio.com">Almsaeed Studio</a>.</strong> All rights
    reserved.
</footer>
</div>
 <script type="text/javascript">
<!-- ejecuta validadores de campo en el formulario--> 
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
var spryselect2 = new Spry.Widget.ValidationSelect("spryselect2");
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5");
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6");
var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7");
var sprytextfield8 = new Spry.Widget.ValidationTextField("sprytextfield8","email");
var sprytextfield9 = new Spry.Widget.ValidationTextField("sprytextfield9", "date", {format:"yyyy-mm-dd"},"validation_type",{hint:"Formato fecha: AAAA-MM-DD"});
</script>
<!-- ./wrapper -->

<!-- jQuery 2.2.3 --> 
<script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script> 
<!-- Bootstrap 3.3.6 --> 
<script src="../../bootstrap/js/bootstrap.min.js"></script> 
<!-- DataTables --> 
<script src="../../plugins/datatables/jquery.dataTables.min.js"></script> 
<script src="../../plugins/datatables/dataTables.bootstrap.min.js"></script> 
<!-- SlimScroll --> 
<script src="../../plugins/slimScroll/jquery.slimscroll.min.js"></script> 
<!-- FastClick --> 
<script src="../../plugins/fastclick/fastclick.js"></script> 
<!-- AdminLTE App --> 
<script src="../../dist/js/app.min.js"></script> 
<!-- AdminLTE for demo purposes --> 
<script src="../../dist/js/demo.js"></script> 
<!-- page script --> 
<!-- Select2 -->
<script src="../../plugins/select2/select2.full.min.js"></script>
<!-- InputMask -->
<script src="../../plugins/input-mask/jquery.inputmask.js"></script>
<script src="../../plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="../../plugins/input-mask/jquery.inputmask.extensions.js"></script>


</html>
