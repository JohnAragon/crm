<?php
 require_once('connections/crm.php');//Conecta a la base de datos con usuario de visitante


//Funcion para evaluar strings
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
//Termina función de validación



// *** Validar usuario para iniciar sesión
if (!isset($_SESSION)) {
    session_start();
}
//Funcion del formulario para validar
$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}
//Toma la cedula que ingresa el usuario y verifica que este en la base de datos
if (isset($_POST['identificacion'])) {
  $loginUsername=$_POST['identificacion'];
  $password=$_POST['contrasenaAsesor'];
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "inicio.php";//Direcciona a la pagina de inicio de la prueba si la contraseña 
  $MM_redirectLoginFailed = "erroracceso.php";//Direcciona a la pagina de usuario invalido
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_crm, $crm);
  
  $LoginRS__query=sprintf("SELECT identificacion, contrasenaAsesor,Rol FROM Asesores WHERE identificacion=%s AND contrasenaAsesor=%s",// consulta para verificar la contraseña en la base de datos
    GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $crm) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = "";
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //si la version es menor que 5.1 crea dos variables y asigna el usuario como sesion 
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );//Redirecciona si es exitosa la clave
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );// Redirecciona si la contraseña no es correcta
  }
}


?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>CRM GENNCO | Log in</title>
    <!-- Cargar logo de la compañia en las pestañas del navegador -->
  <link rel="shortcut icon" type="image/x-icon" href="../../dist/img/favicon.ico">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/iCheck/square/blue.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b><img src="dist/img/logocrm.png"></b></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Ingrese su usuario y contraseña</p>
     <!--Fromulario para ingresar datos de usuario -->
    <form action="<?php echo $loginFormAction?>" method="post">
    <!--Fromulario para ingresar datos de usuario -->
      <div class="form-group has-feedback"><span class="glyphicon glyphicon-envelope form-control-feedback ion-person"></span>
      </div>
      <span class="form-group has-feedback">
      <input type="text" class="form-control" placeholder="Ingrese su usuario" required title="Debe ingresar su ID de usuario" name="identificacion">
      </span>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Ingrese su contraseña" required title="Debe ingresar su contraseña asignada" name="contrasenaAsesor">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
     
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Conectar</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

    
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<div class="login-box">
  <!-- /.login-logo -->
  <div class="login-box-body">	
     <center> <a href="../intra_gennco/Login/menu_gennco.html">    <button type="submit" class="btn btn-primary btn-block btn-flat">REGRESAR A LA PAGINA PRINCIPAL</button> </a></center>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.2.3 -->
<script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../../bootstrap/js/bootstrap.min.js"></script>


</body>
</html>
