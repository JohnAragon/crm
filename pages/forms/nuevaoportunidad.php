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

//funci�n para cierre inactivo de sesi�n y limpieza de cookies
 function tiempo() {
   if (isset($_SESSION['LAST_LOGIN']) && (time() - $_SESSION['LAST_LOGIN'] > 1800)) {
     if (isset($_COOKIE[session_name()])) {
       setcookie(session_name(), "", time() - 3600, "/");
       //limpiamos completamente el array superglobal
       session_unset();
       //Eliminamos la sesi�n (archivo) del servidor
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



// Se invoca la accion editFormaAction para realizar las operaciones que de aquí en adelante se programan que se incluyen dentro del formularios//
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
// se insertan valores segun lo editado por el usuario. Estos valores bajo inyecci�n SQL se van a la tabla de la base de datos//
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formoport")) {// Al momento de oprimir el boton continuar verifica el nombre del formulario y ejecuta los comandos sql
  $insertSQL = sprintf("INSERT INTO Oportunidad (idOportunidad,Cuentas_idCuentas,Contacto_idContacto,Asesores_idAsesor,estadoOportunidad,montoOportunidad,tipoOportunidad,fechaCreacion) VALUES (%s,%s,%s,%s,%s,%s,%s,%s)",
                       GetSQLValueString($_POST['idOportunidad'], "text"),// Ingresa el numero de identificaci�n de la empresa con el cual el usuario esta llenando la encuesta
                       GetSQLValueString($_POST['Cuentas_idCuentas'], "text"),
					             GetSQLValueString($_POST['Contacto_idContacto'], "text"),
					             GetSQLValueString($_POST['Asesores_idAsesor'], "text"),
                       GetSQLValueString($_POST['estadoOportunidad'], "text"),
                       GetSQLValueString($_POST['montoOportunidad'], "int"),// Ingresa datos que ha digitado el usuario
                       GetSQLValueString($_POST['tipoOportunidad'], "text"),    
                       GetSQLValueString($_POST['fechaCreacion'], "date"));

  mysql_select_db($database_crm, $crm);// vinculaci�n de la base de datos para inyeccion sql
  $Result1 = mysql_query($insertSQL, $crm) or die(mysql_error());// Acción a realizar en  la base de datos para inyeccion sql

  $insertGoTo = "nuevaoportunidad.php";// envía a la siguiente página despues de oprimir el botón continuar
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));

}

// Mostrar próximo autoconsecutivo
mysql_select_db($database_crm, $crm);
$query_consecutivo = sprintf("SELECT COUNT(*) FROM Oportunidad");
$consecutivo= mysql_query($query_consecutivo, $crm) or die(mysql_error());
$row_consecutivo = mysql_fetch_assoc($consecutivo);
$totalRows_consecutivo = mysql_num_rows($consecutivo);
$next_id = $row_consecutivo['COUNT(*)']+1;

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


<script type="text/javascript" src="jquery-3.1.0.min.js"></script>
                  <script>
  $(document).ready(function(){
        var consulta;
        //hacemos focus al campo de búsqueda
        $("#busqueda").focus();

        //comprobamos si se pulsa una tecla
        $("#busqueda").keyup(function(e){

              //obtenemos el texto introducido en el campo de búsqueda
              consulta = $("#busqueda").val();
              //hace la búsqueda
              $.ajax({
                    type: "POST",
                    url: "buscar.php",
                    data: "b="+consulta,
                    dataType: "html",
                    beforeSend: function(){
                    //imagen de carga
                    $("#resultado").html("<p align='center'><img src='ajax-loader.gif' /></p>");
                    },
                    error: function(){
                    alert("error petición ajax");
                    },
                    success: function(data){
                    $("#resultado").empty();
                    $("#resultado").append(data);
                    }
              });
        });
});
  </script>
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
<!-- Script para validación de listas desplegables-->
<link href="../../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css"/>
<script src="../../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>

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
              <span class="label label-warning"></span>
            </a>
            <ul class="dropdown-menu">
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
        <li class="header">MEN&Uacute; PRINCIPAL</li>
        <li class="treeview">
          <a href="../../inicio.php">
            <i class="fa fa-dashboard"></i> <span>Pagina Principal</span>
          </a>

          </li>
          <li class="treeview">
          <a href="#">
            <i class="fa fa-edit"></i> <span>Cuentas</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li ><a href="nuevoregistro.php"><i class="fa fa-circle-o"></i> Crear cuentas</a></li>
             <li><a href="cuentascreadas.php"><i class="fa fa-circle-o"></i> Cuentas Creadas</a></li>
          </ul>
        </li>
           <li class="treeview active">
          <a href="#">
            <i class="fa fa-edit"></i> <span>Oportunidades</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="active" ><a href="nuevaoportunidad.php"><i class="fa fa-circle-o"></i> Crear Nueva oportunidad</a></li>
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
      <h1> Módulo de Oportunidades</h1>
      <ol class="breadcrumb"><li><a href="../../inicio.php">Inicio</a></li>
        <li><a href="#">Oportunidades</a></li>
        <li class="active">Nueva oportunidad</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
           <div class="row">
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
           <div class="box-body box-profile">
           <center><span class="ion-android-add-circle" style="font-size: 90px" ></span></center>
              <center><h3>Nueva Oportunidad</h3></center>
              <a href="oportunidadescreadas.php" class="btn btn-primary btn-block"><b>Volver a listado oportunidades</b></a> </div>
           </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

         <div class="col-md-9">
          <!-- general form elements disabled -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Crear una nueva oportunidad</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <form role="form" action="<?php echo $editFormAction?>" method="POST" name="formoport">
              <!-- text input -->
                <div class="form-group">
                  <label>Oportunidad N° </label>
                  <input type="text" class="form-control"  name="idOportunidad" value="GENN-OP-<?php echo $next_id?>" readonly>
                </div>
                 <label>Ingrese empresa a buscar </label>
               <div class="input-group input-group-sm">

                <input type="text" class="form-control" id="busqueda">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-info btn-flat">Buscar</button>
                    </span>

              </div>
                 <div id="resultado"></div>


                <!-- text input -->
                  <div class="form-group">
                 <input type="hidden" name="Asesores_idAsesor" value="<?php echo $row_usuario['idAsesor']?>"></input>
                  <label>Monto Propuesta</label>
                 <span id="sprytextfield1">
                  <input type="text" class="form-control"  placeholder="Ingrese el valor acordado en la propuesta comercial" name="montoOportunidad" onKeyPress="return controltag(event)">
                   <span class="textfieldRequiredMsg">Debe ingresar el monto acordado en la propuesta</span></span>
                  </div>

              <div class="form-group">
                <label>Estado</label>
                <select class="form-control select" name="estadoOportunidad">
                    <option selected="selected" value="Abierta">Oportunidad Abierta</option>

                </select>
              </div>


                 <div class="form-group">
              <span id="spryselect1">
                   <label>Tipo de propuesta</label>
                <select class="form-control select" name="tipoOportunidad">
               		 <option>Seleccione Tipo de Oportunidad...</option>
                    <option value="RIESGO PSICOSOCIAL">Riesgo Psicosocial</option>
                    <option  value="CLIMA LABORAL">Clima Laboral</option>
                    <option value="EVALUACION DESEMPEÑO">Evaluación de desempeño</option>
                    <option value="CAPACITACION">Capacitación</option>
                    <option value="PROGRAMAS ESPECIFICOS">Programas Específicos</option>
                    <option value="RESPONSABILIDAD SOCIAL">Responsabilidad Social</option>
                    <option value="COACHING">Coaching</option>
                    <option value="OUTDOORS">Outdoors</option>
                </select>
                 <span class="selectRequiredMsg">Debe ingresar el tipo de Propuesta</span></span>
              </div>
                <!-- Date -->
                      <div class="form-group">
                        <label>Fecha de Creación</label>

                        <div class="input-group date">
                          <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                          </div>
                          <span id="sprytextfield2">
                          <input type="datetime" class="form-control pull-right " id="datepicker" name="fechaCreacion" placeholder="AAAA-MM-DD" >
                          <span class="textfieldRequiredMsg">Debe ingresar una fecha o el formato es inválido</span></span>
                        </div>
                        <!-- /.input group -->

              <!-- /.form-group -->

               <div class="box-footer">
                <button type="reset" class="btn btn-default" onClick="document.getElementById('formoport').reset()">Borrar</button>
                <button type="submit" class="btn btn-info pull-right">Crear</button>
                <input type="hidden" name="MM_insert" value="formoport" />
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
<!-- ./wrapper -->
<script type="text/javascript">
	  <!-- ejecuta validadores de campo en el formulario-->
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "date", {format:"yyyy-mm-dd"},"validation_type",{hint:"Formato fecha: AAAA-MM-DD"});
</script>
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

</body>
</html>
