<?php require_once('connections/crm.php'); //Conexión a base de datos y usuario permitido 

?>	
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
	
  $logoutGoTo = "logincrm.php";//Envía a la página de inicio de sesión de administrador
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
     header("logincrm.php"); //redirigir al punto de partida para identificarse  
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

if($row_usuario['estado']=='HABILITADO'){//Validar si el usuario se encuentra habilitado para acceder a CRM
$MM_authorizedUsers = "usuariovalido";//Se asigna nombre de sesion de usuario
$MM_donotCheckaccess = "false";//Se deshabilita el checking de acceso	
}
else
{
	header("Location:noautorizado.php");
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

//Busqueda conteo nuevas oportunidades 
mysql_select_db($database_crm, $crm);
$query_oportunidades = sprintf("SELECT COUNT( * ) FROM Oportunidad WHERE Asesores_idAsesor = %s AND DATE_SUB( CURDATE( ) , INTERVAL 30 DAY ) <= fechaCreacion",GetSQLValueString($row_usuario['idAsesor'], "text"));
$oportunidades = mysql_query($query_oportunidades, $crm) or die(mysql_error());
$row_oportunidades = mysql_fetch_assoc($oportunidades);
$totalRows_oportunidades = mysql_num_rows($oportunidades);

//Busqueda conteo ultimas ventas
mysql_select_db($database_crm, $crm);
$query_ventas = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE Asesores_idAsesor = %s AND DATE_SUB( CURDATE( ) , INTERVAL 30 DAY ) <= fechaActualizacion AND estadoOportunidad LIKE '%%Positiva%%' ",GetSQLValueString($row_usuario['idAsesor'], "text"));
$ventas = mysql_query($query_ventas, $crm) or die(mysql_error());
$row_ventas = mysql_fetch_assoc($ventas);
$totalRows_ventas = mysql_num_rows($ventas);

//Busqueda conteo nuevas cuentas 
mysql_select_db($database_crm, $crm);
$query_clientes = sprintf("SELECT COUNT( * ) FROM Cuentas WHERE creador = %s AND DATE_SUB( CURDATE( ) , INTERVAL 30 DAY ) <= fechaCreacion ",GetSQLValueString($row_usuario['idAsesor'], "text"));
$clientes = mysql_query($query_clientes, $crm) or die(mysql_error());
$row_clientes = mysql_fetch_assoc($clientes);
$totalRows_clientes = mysql_num_rows($clientes);

//Busqueda conteo propuestas negativas
mysql_select_db($database_crm, $crm);
$query_negativas = sprintf("SELECT COUNT( * ) FROM Oportunidad WHERE Asesores_idAsesor = %s AND DATE_SUB( CURDATE( ) , INTERVAL 30 DAY ) <= fechaActualizacion AND estadoOportunidad LIKE '%%Negativa%%' ",GetSQLValueString($row_usuario['idAsesor'], "text"));
$negativas = mysql_query($query_negativas, $crm) or die(mysql_error());
$row_negativas = mysql_fetch_assoc($negativas);
$totalRows_negativas = mysql_num_rows($negativas);

//Busqueda de tareas pendientes
mysql_select_db($database_crm, $crm);
$query_alta = sprintf("SELECT * FROM Tarea AS tr INNER JOIN Cuentas AS ct ON ct.idCuentas=tr.Cuentas_idCuentas  WHERE Asesores_idAsesor=%s AND prioridad LIKE '%%Alta%%' AND Realizada LIKE '%%Programada%%' ORDER BY fechaProgramada ASC", GetSQLValueString($row_usuario['idAsesor'], "text"));
$alta = mysql_query($query_alta, $crm) or die(mysql_error());
$row_alta = mysql_fetch_assoc($alta);
$totalRows_alta = mysql_num_rows($alta);


//Busqueda de tareas pendientes a punto de vencerse
mysql_select_db($database_crm, $crm);
$query_alerta = sprintf("SELECT Cuentas_idCuentas,Oportunidad_idOportunidad, nombreEmpresa, TIMESTAMPDIFF( DAY ,fechaProgramada, CURRENT_DATE()) as diferencia FROM Tarea as tr INNER JOIN Cuentas as ct ON tr.Cuentas_idCuentas=ct.idCuentas WHERE tr.Asesores_idAsesor=%s AND tr.Realizada='Programada' AND (TIMESTAMPDIFF(DAY ,fechaProgramada, CURRENT_DATE())>-2)", GetSQLValueString($row_usuario['idAsesor'], "text"));
$alerta = mysql_query($query_alerta, $crm) or die(mysql_error());
$row_alerta = mysql_fetch_assoc($alerta);
$totalRows_alerta = mysql_num_rows($alerta);

//Busqueda año en curso 
mysql_select_db($database_crm, $crm);
$query_año = sprintf("SELECT YEAR(NOW())");
$año= mysql_query($query_año, $crm) or die(mysql_error());
$row_año = mysql_fetch_assoc($año);
$totalRows_año = mysql_num_rows($año);

//*************Recolección de resumen mensual  de propuestas******//// 

//Busqueda propuestas positivas
mysql_select_db($database_crm, $crm);
$query_ventaspos1 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-01-01' AND '".$row_año['YEAR(NOW())']."-01-31' AND estadoOportunidad LIKE '%%Positiva%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$ventaspos1= mysql_query($query_ventaspos1, $crm) or die(mysql_error());
$row_ventaspos1 = mysql_fetch_assoc($ventaspos1);
$totalRows_ventaspos1 = mysql_num_rows($ventaspos1);

mysql_select_db($database_crm, $crm);
$query_ventaspos2 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-02-01' AND '".$row_año['YEAR(NOW())']."-02-29' AND estadoOportunidad LIKE '%%Positiva%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$ventaspos2= mysql_query($query_ventaspos2, $crm) or die(mysql_error());
$row_ventaspos2 = mysql_fetch_assoc($ventaspos2);
$totalRows_ventaspos2 = mysql_num_rows($ventaspos2);

mysql_select_db($database_crm, $crm);
$query_ventaspos3 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-03-01' AND '".$row_año['YEAR(NOW())']."-03-31' AND estadoOportunidad LIKE '%%Positiva%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$ventaspos3= mysql_query($query_ventaspos3, $crm) or die(mysql_error());
$row_ventaspos3 = mysql_fetch_assoc($ventaspos3);
$totalRows_ventaspos3 = mysql_num_rows($ventaspos3);

mysql_select_db($database_crm, $crm);
$query_ventaspos4 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-04-01' AND '".$row_año['YEAR(NOW())']."-04-30' AND estadoOportunidad LIKE '%%Positiva%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$ventaspos4= mysql_query($query_ventaspos4, $crm) or die(mysql_error());
$row_ventaspos4 = mysql_fetch_assoc($ventaspos4);
$totalRows_ventaspos4 = mysql_num_rows($ventaspos4);

mysql_select_db($database_crm, $crm);
$query_ventaspos5 = sprintf ("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-05-01' AND '".$row_año['YEAR(NOW())']."-05-31' AND estadoOportunidad LIKE '%%Positiva%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$ventaspos5= mysql_query($query_ventaspos5, $crm) or die(mysql_error());
$row_ventaspos5 = mysql_fetch_assoc($ventaspos5);
$totalRows_ventaspos5 = mysql_num_rows($ventaspos5);

mysql_select_db($database_crm, $crm);
$query_ventaspos6 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-06-01' AND '".$row_año['YEAR(NOW())']."-06-30' AND estadoOportunidad LIKE '%%Positiva%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$ventaspos6= mysql_query($query_ventaspos6, $crm) or die(mysql_error());
$row_ventaspos6 = mysql_fetch_assoc($ventaspos6);
$totalRows_ventaspos6 = mysql_num_rows($ventaspos6);

mysql_select_db($database_crm, $crm);
$query_ventaspos7 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-07-01' AND '".$row_año['YEAR(NOW())']."-07-31' AND estadoOportunidad LIKE '%%Positiva%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$ventaspos7= mysql_query($query_ventaspos7, $crm) or die(mysql_error());
$row_ventaspos7 = mysql_fetch_assoc($ventaspos7);
$totalRows_ventaspos7 = mysql_num_rows($ventaspos7);

mysql_select_db($database_crm, $crm);
$query_ventaspos8 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-08-01' AND '".$row_año['YEAR(NOW())']."-08-31' AND estadoOportunidad LIKE '%%Positiva%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$ventaspos8= mysql_query($query_ventaspos8, $crm) or die(mysql_error());
$row_ventaspos8 = mysql_fetch_assoc($ventaspos8);
$totalRows_ventaspos8 = mysql_num_rows($ventaspos8);

mysql_select_db($database_crm, $crm);
$query_ventaspos9 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-09-01' AND '".$row_año['YEAR(NOW())']."-09-30' AND estadoOportunidad LIKE '%%Positiva%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$ventaspos9= mysql_query($query_ventaspos9, $crm) or die(mysql_error());
$row_ventaspos9 = mysql_fetch_assoc($ventaspos9);
$totalRows_ventaspos9 = mysql_num_rows($ventaspos9);

mysql_select_db($database_crm, $crm);
$query_ventaspos10 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-10-01' AND '".$row_año['YEAR(NOW())']."-10-31' AND estadoOportunidad LIKE '%%Positiva%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$ventaspos10= mysql_query($query_ventaspos10, $crm) or die(mysql_error());
$row_ventaspos10 = mysql_fetch_assoc($ventaspos10);
$totalRows_ventaspos10 = mysql_num_rows($ventaspos10);

mysql_select_db($database_crm, $crm);
$query_ventaspos11 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-11-01' AND '".$row_año['YEAR(NOW())']."-11-30' AND estadoOportunidad LIKE '%%Positiva%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$ventaspos11= mysql_query($query_ventaspos11, $crm) or die(mysql_error());
$row_ventaspos11 = mysql_fetch_assoc($ventaspos11);
$totalRows_ventaspos11 = mysql_num_rows($ventaspos11);

mysql_select_db($database_crm, $crm);
$query_ventaspos12 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-12-01' AND '".$row_año['YEAR(NOW())']."-12-30' AND estadoOportunidad LIKE '%%Positiva%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$ventaspos12= mysql_query($query_ventaspos12, $crm) or die(mysql_error());
$row_ventaspos12 = mysql_fetch_assoc($ventaspos12);
$totalRows_ventaspos12 = mysql_num_rows($ventaspos12);

//Busqueda valor propuestas negativas
mysql_select_db($database_crm, $crm);
$query_negativa = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-01-01' AND '".$row_año['YEAR(NOW())']."-01-31' AND estadoOportunidad LIKE '%%Negativa%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$negativa= mysql_query($query_negativa, $crm) or die(mysql_error());
$row_negativa = mysql_fetch_assoc($negativa);
$totalRows_negativa = mysql_num_rows($negativa);

mysql_select_db($database_crm, $crm);
$query_negativa1 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-02-01' AND '".$row_año['YEAR(NOW())']."-02-29' AND estadoOportunidad LIKE '%%Negativa%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$negativa1= mysql_query($query_negativa1, $crm) or die(mysql_error());
$row_negativa1 = mysql_fetch_assoc($negativa1);
$totalRows_negativa1 = mysql_num_rows($negativa1);

mysql_select_db($database_crm, $crm);
$query_negativa2 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-03-01' AND '".$row_año['YEAR(NOW())']."-03-31' AND estadoOportunidad LIKE '%%Negativa%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$negativa2= mysql_query($query_negativa2, $crm) or die(mysql_error());
$row_negativa2 = mysql_fetch_assoc($negativa2);
$totalRows_negativa2 = mysql_num_rows($negativa2);

mysql_select_db($database_crm, $crm);
$query_negativa3 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-04-01' AND '".$row_año['YEAR(NOW())']."-04-30' AND estadoOportunidad LIKE '%%Negativa%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$negativa3= mysql_query($query_negativa3, $crm) or die(mysql_error());
$row_negativa3 = mysql_fetch_assoc($negativa3);
$totalRows_negativa3 = mysql_num_rows($negativa3);

mysql_select_db($database_crm, $crm);
$query_negativa4 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-05-01' AND '".$row_año['YEAR(NOW())']."-05-31' AND estadoOportunidad LIKE '%%Negativa%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$negativa4= mysql_query($query_negativa4, $crm) or die(mysql_error());
$row_negativa4 = mysql_fetch_assoc($negativa4);
$totalRows_negativa4 = mysql_num_rows($negativa4);

mysql_select_db($database_crm, $crm);
$query_negativa5 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-06-01' AND '".$row_año['YEAR(NOW())']."-06-30' AND estadoOportunidad LIKE '%%Negativa%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$row_negativa5 = mysql_fetch_assoc($negativa5);
$totalRows_negativa5 = mysql_num_rows($negativa5);

mysql_select_db($database_crm, $crm);
$query_negativa6 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-07-01' AND '".$row_año['YEAR(NOW())']."-07-31' AND estadoOportunidad LIKE '%%Negativa%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$negativa6= mysql_query($query_negativa6, $crm) or die(mysql_error());
$row_negativa6 = mysql_fetch_assoc($negativa6);
$totalRows_negativa6 = mysql_num_rows($negativa6);

mysql_select_db($database_crm, $crm);
$query_negativa7= sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-08-01' AND '".$row_año['YEAR(NOW())']."-08-31' AND estadoOportunidad LIKE '%%Negativa%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$negativa7= mysql_query($query_negativa7, $crm) or die(mysql_error());
$row_negativa7 = mysql_fetch_assoc($negativa7);
$totalRows_negativa7 = mysql_num_rows($negativa7);

mysql_select_db($database_crm, $crm);
$query_negativa8 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-09-30' AND '".$row_año['YEAR(NOW())']."-09-30' AND estadoOportunidad LIKE '%%Negativa%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$negativa8= mysql_query($query_negativa8, $crm) or die(mysql_error());
$row_negativa8 = mysql_fetch_assoc($negativa8);
$totalRows_negativa8 = mysql_num_rows($negativa8);

mysql_select_db($database_crm, $crm);
$query_negativa9 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-10-01' AND '".$row_año['YEAR(NOW())']."-10-31' AND estadoOportunidad LIKE '%%Negativa%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$negativa9= mysql_query($query_negativa9, $crm) or die(mysql_error());
$row_negativa9 = mysql_fetch_assoc($negativa9);
$totalRows_negativa9 = mysql_num_rows($negativa9);

mysql_select_db($database_crm, $crm);
$query_negativa10 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-11-01' AND '".$row_año['YEAR(NOW())']."-11-30' AND estadoOportunidad LIKE '%%Negativa%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$negativa10= mysql_query($query_negativa10, $crm) or die(mysql_error());
$row_negativa10 = mysql_fetch_assoc($negativa10);
$totalRows_negativa10 = mysql_num_rows($negativa10);

mysql_select_db($database_crm, $crm);
$query_negativa11 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-12-01' AND '".$row_año['YEAR(NOW())']."-12-31' AND estadoOportunidad LIKE '%%Negativa%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$negativa11= mysql_query($query_negativa11, $crm) or die(mysql_error());
$row_negativa11 = mysql_fetch_assoc($negativa11);
$totalRows_negativa11 = mysql_num_rows($negativa11);

//Busqueda valor propuestas Abiertas
mysql_select_db($database_crm, $crm);
$query_abierta = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-01-01' AND '".$row_año['YEAR(NOW())']."-01-31' AND estadoOportunidad LIKE '%%Abierta%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$abierta= mysql_query($query_abierta, $crm) or die(mysql_error());
$row_abierta = mysql_fetch_assoc($abierta);
$totalRows_abierta = mysql_num_rows($abierta);

mysql_select_db($database_crm, $crm);
$query_abierta1 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-02-01' AND '".$row_año['YEAR(NOW())']."-02-29' AND estadoOportunidad LIKE '%%Abierta%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$abierta1= mysql_query($query_abierta1, $crm) or die(mysql_error());
$row_abierta1 = mysql_fetch_assoc($abierta1);
$totalRows_abierta1 = mysql_num_rows($abierta1);

mysql_select_db($database_crm, $crm);
$query_abierta2 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-03-01' AND '".$row_año['YEAR(NOW())']."-03-31' AND estadoOportunidad LIKE '%%Abierta%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$abierta2= mysql_query($query_abierta2, $crm) or die(mysql_error());
$row_abierta2 = mysql_fetch_assoc($abierta2);
$totalRows_abierta2 = mysql_num_rows($abierta2);

mysql_select_db($database_crm, $crm);
$query_abierta3 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-04-01' AND '".$row_año['YEAR(NOW())']."-04-30' AND estadoOportunidad LIKE '%%Abierta%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$abierta3= mysql_query($query_abierta3, $crm) or die(mysql_error());
$row_abierta3 = mysql_fetch_assoc($abierta3);
$totalRows_abierta3 = mysql_num_rows($abierta3);

mysql_select_db($database_crm, $crm);
$query_abierta4 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-05-01' AND '".$row_año['YEAR(NOW())']."-05-31' AND estadoOportunidad LIKE '%%Abierta%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$abierta4= mysql_query($query_abierta4, $crm) or die(mysql_error());
$row_abierta4 = mysql_fetch_assoc($abierta4);
$totalRows_abierta4 = mysql_num_rows($abierta4);

mysql_select_db($database_crm, $crm);
$query_abierta5 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-06-01' AND '".$row_año['YEAR(NOW())']."-06-30' AND estadoOportunidad LIKE '%%Abierta%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$abierta5= mysql_query($query_abierta5, $crm) or die(mysql_error());
$row_abierta5 = mysql_fetch_assoc($abierta5);
$totalRows_abierta5 = mysql_num_rows($abierta5);

mysql_select_db($database_crm, $crm);
$query_abierta6 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-07-01' AND '".$row_año['YEAR(NOW())']."-07-31' AND estadoOportunidad LIKE '%%Negativa%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$abierta6= mysql_query($query_abierta6, $crm) or die(mysql_error());
$row_abierta6 = mysql_fetch_assoc($abierta6);
$totalRows_abierta6 = mysql_num_rows($abierta6);

mysql_select_db($database_crm, $crm);
$query_abierta7= sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-08-01' AND '".$row_año['YEAR(NOW())']."-08-31' AND estadoOportunidad LIKE '%%Abierta%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$abierta7= mysql_query($query_abierta7, $crm) or die(mysql_error());
$row_abierta7 = mysql_fetch_assoc($abierta7);
$totalRows_abierta7 = mysql_num_rows($abierta7);

mysql_select_db($database_crm, $crm);
$query_abierta8 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-09-01' AND '".$row_año['YEAR(NOW())']."-09-30' AND estadoOportunidad LIKE '%%Abierta%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$abierta8= mysql_query($query_abierta8, $crm) or die(mysql_error());
$row_abierta8 = mysql_fetch_assoc($abierta8);
$totalRows_abierta8 = mysql_num_rows($abierta8);

mysql_select_db($database_crm, $crm);
$query_abierta9 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-10-01' AND '".$row_año['YEAR(NOW())']."-10-31' AND estadoOportunidad LIKE '%%Abierta%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$abierta9= mysql_query($query_abierta9, $crm) or die(mysql_error());
$row_abierta9 = mysql_fetch_assoc($abierta9);
$totalRows_abierta9 = mysql_num_rows($abierta9);

mysql_select_db($database_crm, $crm);
$query_abierta10 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-11-01' AND '".$row_año['YEAR(NOW())']."-11-30' AND estadoOportunidad LIKE '%%Abierta%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$abierta10= mysql_query($query_abierta10, $crm) or die(mysql_error());
$row_abierta10 = mysql_fetch_assoc($abierta10);
$totalRows_abierta10 = mysql_num_rows($abierta10);

mysql_select_db($database_crm, $crm);
$query_abierta11 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-12-01' AND '".$row_año['YEAR(NOW())']."-12-31' AND estadoOportunidad LIKE '%%Abierta%%' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$abierta11= mysql_query($query_abierta11, $crm) or die(mysql_error());
$row_abierta11 = mysql_fetch_assoc($abierta11);
$totalRows_abierta11 = mysql_num_rows($abierta11);

//*************Termina datos de resumen mensual  de propuestas******//// 



//*************Recollección de resumen mensual  de tareas******//// 


//Conteo de actividades de correo
mysql_select_db($database_crm, $crm);
$query_correo1 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-01-01' AND '".$row_año['YEAR(NOW())']."-01-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo1= mysql_query($query_correo1, $crm) or die(mysql_error());
$row_correo1 = mysql_fetch_assoc($correo1);
$totalRows_correo1 = mysql_num_rows($correo1);

mysql_select_db($database_crm, $crm);
$query_correo2 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-02-01' AND '".$row_año['YEAR(NOW())']."-02-29' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo2= mysql_query($query_correo2, $crm) or die(mysql_error());
$row_correo2 = mysql_fetch_assoc($correo2);
$totalRows_correo2 = mysql_num_rows($correo2);

mysql_select_db($database_crm, $crm);
$query_correo3 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-03-01' AND '".$row_año['YEAR(NOW())']."-03-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo3= mysql_query($query_correo3, $crm) or die(mysql_error());
$row_correo3 = mysql_fetch_assoc($correo3);
$totalRows_correo3 = mysql_num_rows($correo3);

mysql_select_db($database_crm, $crm);
$query_correo4 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-04-01' AND '".$row_año['YEAR(NOW())']."-04-30' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo4= mysql_query($query_correo4, $crm) or die(mysql_error());
$row_correo4 = mysql_fetch_assoc($correo4);
$totalRows_correo4 = mysql_num_rows($correo4);

mysql_select_db($database_crm, $crm);
$query_correo5 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-05-01' AND '".$row_año['YEAR(NOW())']."-05-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo5= mysql_query($query_correo5, $crm) or die(mysql_error());
$row_correo5 = mysql_fetch_assoc($correo5);
$totalRows_correo5 = mysql_num_rows($correo5);

mysql_select_db($database_crm, $crm);
$query_correo6 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-06-01' AND '".$row_año['YEAR(NOW())']."-06-30' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo6= mysql_query($query_correo6, $crm) or die(mysql_error());
$row_correo6 = mysql_fetch_assoc($correo6);
$totalRows_correo6 = mysql_num_rows($correo6);

mysql_select_db($database_crm, $crm);
$query_correo7 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-07-01' AND '".$row_año['YEAR(NOW())']."-07-30' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo7= mysql_query($query_correo7, $crm) or die(mysql_error());
$row_correo7 = mysql_fetch_assoc($correo7);
$totalRows_correo7 = mysql_num_rows($correo7);

mysql_select_db($database_crm, $crm);
$query_correo8 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-08-01' AND '".$row_año['YEAR(NOW())']."-08-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo8= mysql_query($query_correo8, $crm) or die(mysql_error());
$row_correo8 = mysql_fetch_assoc($correo8);
$totalRows_correo8 = mysql_num_rows($correo8);

mysql_select_db($database_crm, $crm);
$query_correo9 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-09-01' AND '".$row_año['YEAR(NOW())']."-09-30' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo9= mysql_query($query_correo9, $crm) or die(mysql_error());
$row_correo9 = mysql_fetch_assoc($correo9);
$totalRows_correo9 = mysql_num_rows($correo9);


mysql_select_db($database_crm, $crm);
$query_correo10 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-10-01' AND '".$row_año['YEAR(NOW())']."-10-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo10= mysql_query($query_correo10, $crm) or die(mysql_error());
$row_correo10 = mysql_fetch_assoc($correo10);
$totalRows_correo10 = mysql_num_rows($correo10);

mysql_select_db($database_crm, $crm);
$query_correo11 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-11-01' AND '".$row_año['YEAR(NOW())']."-11-30' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo11= mysql_query($query_correo11, $crm) or die(mysql_error());
$row_correo11 = mysql_fetch_assoc($correo11);
$totalRows_correo11 = mysql_num_rows($correo11);

mysql_select_db($database_crm, $crm);
$query_correo12 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-12-01' AND '".$row_año['YEAR(NOW())']."-12-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo12= mysql_query($query_correo12, $crm) or die(mysql_error());
$row_correo12 = mysql_fetch_assoc($correo12);
$totalRows_correo12 = mysql_num_rows($correo12);

//Conteo de actividades de llamadas
mysql_select_db($database_crm, $crm);
$query_llamada1 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-01-01' AND '".$row_año['YEAR(NOW())']."-01-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$llamada1= mysql_query($query_llamada1, $crm) or die(mysql_error());
$row_llamada1 = mysql_fetch_assoc($llamada1);
$totalRows_llamada1 = mysql_num_rows($llamada1);

mysql_select_db($database_crm, $crm);
$query_llamada2 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-02-01' AND '".$row_año['YEAR(NOW())']."-02-29'AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$llamada2= mysql_query($query_llamada2, $crm) or die(mysql_error());
$row_llamada2 = mysql_fetch_assoc($llamada2);
$totalRows_llamada2 = mysql_num_rows($llamada2);

mysql_select_db($database_crm, $crm);
$query_llamada3 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-03-01' AND '".$row_año['YEAR(NOW())']."-03-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$llamada3= mysql_query($query_llamada3, $crm) or die(mysql_error());
$row_llamada3 = mysql_fetch_assoc($llamada3);
$totalRows_llamada3 = mysql_num_rows($llamada3);

mysql_select_db($database_crm, $crm);
$query_llamada4 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-04-01' AND '".$row_año['YEAR(NOW())']."-04-30' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$llamada4= mysql_query($query_llamada4, $crm) or die(mysql_error());
$row_llamada4 = mysql_fetch_assoc($llamada4);
$totalRows_llamada4 = mysql_num_rows($llamada4);

mysql_select_db($database_crm, $crm);
$query_llamada5 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-05-01' AND '".$row_año['YEAR(NOW())']."-05-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$llamada5= mysql_query($query_llamada5, $crm) or die(mysql_error());
$row_llamada5 = mysql_fetch_assoc($llamada5);
$totalRows_llamada5 = mysql_num_rows($llamada5);

mysql_select_db($database_crm, $crm);
$query_llamada6 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-06-01' AND '".$row_año['YEAR(NOW())']."-06-30' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$llamada6= mysql_query($query_llamada6, $crm) or die(mysql_error());
$row_llamada6 = mysql_fetch_assoc($llamada6);
$totalRows_llamada6 = mysql_num_rows($llamada6);

mysql_select_db($database_crm, $crm);
$query_llamada7 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-07-01' AND '".$row_año['YEAR(NOW())']."-07-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$llamada7= mysql_query($query_llamada7, $crm) or die(mysql_error());
$row_llamada7 = mysql_fetch_assoc($llamada7);
$totalRows_llamada7 = mysql_num_rows($llamada7);

mysql_select_db($database_crm, $crm);
$query_llamada8 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-08-01' AND '".$row_año['YEAR(NOW())']."-08-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$llamada8= mysql_query($query_llamada8, $crm) or die(mysql_error());
$row_llamada8 = mysql_fetch_assoc($llamada8);
$totalRows_llamada8 = mysql_num_rows($llamada8);

mysql_select_db($database_crm, $crm);
$query_llamada9 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-09-01' AND '".$row_año['YEAR(NOW())']."-09-30' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$llamada9= mysql_query($query_llamada9, $crm) or die(mysql_error());
$row_llamada9 = mysql_fetch_assoc($llamada9);
$totalRows_llamada9 = mysql_num_rows($llamada9);

mysql_select_db($database_crm, $crm);
$query_llamada10 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-10-01' AND '".$row_año['YEAR(NOW())']."-10-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$llamada10= mysql_query($query_llamada10, $crm) or die(mysql_error());
$row_llamada10 = mysql_fetch_assoc($llamada10);
$totalRows_llamada10 = mysql_num_rows($llamada10);

mysql_select_db($database_crm, $crm);
$query_llamada11 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN ".$row_año['YEAR(NOW())']."-11-01 AND ".$row_año['YEAR(NOW())']."-11-30 AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$llamada11= mysql_query($query_llamada11, $crm) or die(mysql_error());
$row_llamada11 = mysql_fetch_assoc($llamada11);
$totalRows_llamada11 = mysql_num_rows($llamada11);

mysql_select_db($database_crm, $crm);
$query_llamada12 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-12-01' AND '".$row_año['YEAR(NOW())']."-12-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$llamada12= mysql_query($query_llamada12, $crm) or die(mysql_error());
$row_llamada12 = mysql_fetch_assoc($llamada12);
$totalRows_llamada12 = mysql_num_rows($llamada12);



//Conteo de actividades de correos

mysql_select_db($database_crm, $crm);
$query_correo1 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-01-01' AND '".$row_año['YEAR(NOW())']."-01-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo1= mysql_query($query_correo1, $crm) or die(mysql_error());
$row_correo1 = mysql_fetch_assoc($correo1);
$totalRows_correo1 = mysql_num_rows($correo1);

mysql_select_db($database_crm, $crm);
$query_correo2 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-02-01' AND '".$row_año['YEAR(NOW())']."-02-29' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo2= mysql_query($query_correo2, $crm) or die(mysql_error());
$row_correo2 = mysql_fetch_assoc($correo2);
$totalRows_correo2 = mysql_num_rows($correo2);

mysql_select_db($database_crm, $crm);
$query_correo3 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-03-01' AND '".$row_año['YEAR(NOW())']."-03-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo3= mysql_query($query_correo3, $crm) or die(mysql_error());
$row_correo3 = mysql_fetch_assoc($correo3);
$totalRows_correo3 = mysql_num_rows($correo3);

mysql_select_db($database_crm, $crm);
$query_correo4 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-04-01' AND '".$row_año['YEAR(NOW())']."-04-30' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo4= mysql_query($query_correo4, $crm) or die(mysql_error());
$row_correo4 = mysql_fetch_assoc($correo4);
$totalRows_correo4 = mysql_num_rows($correo4);

mysql_select_db($database_crm, $crm);
$query_correo5 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-05-01' AND '".$row_año['YEAR(NOW())']."-05-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo5= mysql_query($query_correo5, $crm) or die(mysql_error());
$row_correo5 = mysql_fetch_assoc($correo5);
$totalRows_correo5 = mysql_num_rows($correo5);

mysql_select_db($database_crm, $crm);
$query_correo6 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-06-01' AND '".$row_año['YEAR(NOW())']."-06-30' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo6= mysql_query($query_correo6, $crm) or die(mysql_error());
$row_correo6 = mysql_fetch_assoc($correo6);
$totalRows_correo6 = mysql_num_rows($correo6);

mysql_select_db($database_crm, $crm);
$query_correo7 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-07-01' AND '".$row_año['YEAR(NOW())']."-07-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo7= mysql_query($query_correo7, $crm) or die(mysql_error());
$row_correo7 = mysql_fetch_assoc($correo7);
$totalRows_correo7 = mysql_num_rows($correo7);

mysql_select_db($database_crm, $crm);
$query_correo8 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-08-01' AND '".$row_año['YEAR(NOW())']."-08-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo8= mysql_query($query_correo8, $crm) or die(mysql_error());
$row_correo8 = mysql_fetch_assoc($correo8);
$totalRows_correo8 = mysql_num_rows($correo8);

mysql_select_db($database_crm, $crm);
$query_correo9= sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-09-01' AND '".$row_año['YEAR(NOW())']."-09-30' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo9= mysql_query($query_correo9, $crm) or die(mysql_error());
$row_correo9 = mysql_fetch_assoc($correo9);
$totalRows_correo9 = mysql_num_rows($correo9);

mysql_select_db($database_crm, $crm);
$query_correo10= sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-10-01' AND '".$row_año['YEAR(NOW())']."-10-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo10= mysql_query($query_correo10, $crm) or die(mysql_error());
$row_correo10 = mysql_fetch_assoc($correo10);
$totalRows_correo10 = mysql_num_rows($correo10);

mysql_select_db($database_crm, $crm);
$query_correo11= sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-11-01' AND '".$row_año['YEAR(NOW())']."-11-30' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo11= mysql_query($query_correo11, $crm) or die(mysql_error());
$row_correo11 = mysql_fetch_assoc($correo11);
$totalRows_correo11 = mysql_num_rows($correo11);

mysql_select_db($database_crm, $crm);
$query_correo12= sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-12-01' AND '".$row_año['YEAR(NOW())']."-12-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$correo12= mysql_query($query_correo12, $crm) or die(mysql_error());
$row_correo12 = mysql_fetch_assoc($correo12);
$totalRows_correo12 = mysql_num_rows($correo12);

//Conteo de actividades de visitas

mysql_select_db($database_crm, $crm);
$query_visita1 = sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-01-01' AND '".$row_año['YEAR(NOW())']."-01-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$visita1= mysql_query($query_visita1, $crm) or die(mysql_error());
$row_visita1 = mysql_fetch_assoc($visita1);
$totalRows_visita1 = mysql_num_rows($visita1);

mysql_select_db($database_crm, $crm);
$query_visita2 = sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-02-01' AND '".$row_año['YEAR(NOW())']."-02-29' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$visita2= mysql_query($query_visita2, $crm) or die(mysql_error());
$row_visita2 = mysql_fetch_assoc($visita2);
$totalRows_visita2 = mysql_num_rows($visita2);

mysql_select_db($database_crm, $crm);
$query_visita3 = sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-03-01' AND '".$row_año['YEAR(NOW())']."-03-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$visita3= mysql_query($query_visita3, $crm) or die(mysql_error());
$row_visita3 = mysql_fetch_assoc($visita3);
$totalRows_visita3 = mysql_num_rows($visita3);

mysql_select_db($database_crm, $crm);
$query_visita4 = sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-04-01' AND '".$row_año['YEAR(NOW())']."-04-30' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$visita4= mysql_query($query_visita4, $crm) or die(mysql_error());
$row_visita4 = mysql_fetch_assoc($visita4);
$totalRows_visita4 = mysql_num_rows($visita4);

mysql_select_db($database_crm, $crm);
$query_visita5 = sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-05-01' AND '".$row_año['YEAR(NOW())']."-05-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$visita5= mysql_query($query_visita5, $crm) or die(mysql_error());
$row_visita5 = mysql_fetch_assoc($visita5);
$totalRows_visita5 = mysql_num_rows($visita5);

mysql_select_db($database_crm, $crm);
$query_visita6 = sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-06-01' AND '".$row_año['YEAR(NOW())']."-06-30' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$visita6= mysql_query($query_visita6, $crm) or die(mysql_error());
$row_visita6 = mysql_fetch_assoc($visita6);
$totalRows_visita6 = mysql_num_rows($visita6);

mysql_select_db($database_crm, $crm);
$query_visita7 = sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-07-01' AND '".$row_año['YEAR(NOW())']."-07-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$visita7= mysql_query($query_visita7, $crm) or die(mysql_error());
$row_visita7 = mysql_fetch_assoc($visita7);
$totalRows_visita7 = mysql_num_rows($visita7);

mysql_select_db($database_crm, $crm);
$query_visita8 = sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-08-01' AND '".$row_año['YEAR(NOW())']."-08-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$visita8= mysql_query($query_visita8, $crm) or die(mysql_error());
$row_visita8 = mysql_fetch_assoc($visita8);
$totalRows_visita8 = mysql_num_rows($visita8);

mysql_select_db($database_crm, $crm);
$query_visita9= sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-09-01' AND '".$row_año['YEAR(NOW())']."-09-30' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$visita9= mysql_query($query_visita9, $crm) or die(mysql_error());
$row_visita9 = mysql_fetch_assoc($visita9);
$totalRows_visita9 = mysql_num_rows($visita9);

mysql_select_db($database_crm, $crm);
$query_visita10= sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-10-01' AND '".$row_año['YEAR(NOW())']."-10-31' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$visita10= mysql_query($query_visita10, $crm) or die(mysql_error());
$row_visita10 = mysql_fetch_assoc($visita10);
$totalRows_visita10 = mysql_num_rows($visita10);

mysql_select_db($database_crm, $crm);
$query_visita11= sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-11-01' AND '".$row_año['YEAR(NOW())']."-11-30' AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$visita11= mysql_query($query_correo11, $crm) or die(mysql_error());
$row_visita11 = mysql_fetch_assoc($visita11);
$totalRows_visita11 = mysql_num_rows($visita11);

mysql_select_db($database_crm, $crm);
$query_visita12= sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-12-01' AND '".$row_año['YEAR(NOW())']."-12-31'AND Asesores_idAsesor= %s",GetSQLValueString($row_usuario['idAsesor'], "text"));
$visita12= mysql_query($query_visita12, $crm) or die(mysql_error());
$row_visita12 = mysql_fetch_assoc($visita12);
$totalRows_visita12 = mysql_num_rows($visita12);

// Se invoca la accion editFormaAction para realizar las operaciones que de aquí en adelante se programan que se incluyen dentro del formularios de tareas pendientes//
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// modifica datos en la tabla Tarea y en el campo Realizada y detalleTarea, esto con el fin de actualizar el estado de la tarea
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "modificatarea")) {// Al momento de oprimir el boton continuar verifica el nombre del formulario y ejecuta los comandos sql
  $updateSQL = sprintf("UPDATE Tarea SET Realizada=%s,detalleTarea=%s WHERE idTarea=%s",
                       GetSQLValueString($_POST['Realizada'], "text"),
					   GetSQLValueString($_POST['detalle'], "text"),
                       GetSQLValueString($_POST['idTarea'], "int"));

  mysql_select_db($database_crm, $crm);// vinculación de la base de datos para inyeccion sql
  $Result1 = mysql_query($updateSQL, $crm) or die(mysql_error());// Acción a realizar en  la base de datos para inyeccion sql

  $updateGoTo = "inicio.php";//Redirige nuevamente a la misma página 
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}






?>
<!DOCTYPE html>
<html>
<head>
<meta charset http-equiv="content-type" content="text/html; charset= utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>CRM GENNCO | Inicio</title>
  <link rel="shortcut icon" type="image/x-icon" href="dist/img/favicon.ico">
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
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/iCheck/flat/blue.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- ChartJS 1.0.1 Trae bibliotecas de gráficos-->
<script src="plugins/chartjs/Chart.min.js"></script>
<!-- FastClick -->
<script src="plugins/fastclick/fastclick.js"></script>
<!-- Slimscroll -->
<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>

<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>

<!-- page script , configuración de graficos-->
<!--GRAFICO DE VENTAS-->
<script>
  $(function () {
    /* ChartJS
     * -------
     * Here we will create a few charts using ChartJS
     */

    //--------------
    //- AREA CHART -
    //--------------

    // Get context with jQuery - using jQuery's .get() method.
    var areaChartCanvas = $("#barChart").get(0).getContext("2d");
    // This will get the first returned node in the jQuery collection.
    var areaChart = new Chart(areaChartCanvas);

    var areaChartData = {
      labels: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
      datasets: [
        {
          label: "Negativas",
          fillColor: "rgba(215, 40, 40, 0.9)",
          strokeColor: "rgba(210, 214, 222, 1)",
          pointColor: "rgba(210, 214, 222, 1)",
          pointStrokeColor: "#c1c7d1",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(220,220,220,1)",
          data: [<?php echo $row_negativa['SUM(montoOportunidad)']?>,<?php echo $row_negativa1['SUM(montoOportunidad)']?>,<?php echo $row_negativa2['SUM(montoOportunidad)']?>,<?php echo $row_negativa3['SUM(montoOportunidad)']?>,<?php echo $row_negativa4['SUM(montoOportunidad)']?>,<?php echo $row_negativa5['SUM(montoOportunidad)']?>,<?php echo $row_negativa6['SUM(montoOportunidad)']?>,<?php echo $row_negativa6['SUM(montoOportunidad)']?>,<?php echo $row_negativa7['SUM(montoOportunidad)']?>,<?php echo $row_negativa8['SUM(montoOportunidad)']?>,<?php echo $negativa10['SUM(montoOportunidad)']?>,<?php echo $row_negativa11['SUM(montoOportunidad)']?>]
        },
        {
          label: "Positivas",
          fillColor: "rgba(0, 171, 126, 1)",
          strokeColor: "rgba(60,141,188,0.8)",
          pointColor: "#3b8bba",
          pointStrokeColor: "rgba(60,141,188,1)",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(60,141,188,1)",
          data: [<?php echo $row_ventaspos1['SUM(montoOportunidad)']?>,<?php echo $row_ventaspos2['SUM(montoOportunidad)']?>,<?php echo $row_ventaspos3['SUM(montoOportunidad)']?>,<?php echo $row_ventaspos4['SUM(montoOportunidad)']?>,<?php echo $row_ventaspos5['SUM(montoOportunidad)']?>,<?php echo $row_ventaspos6['SUM(montoOportunidad)']?>,<?php echo $ventaspos7['SUM(montoOportunidad)']?>,<?php echo $row_ventaspos8['SUM(montoOportunidad)']?>,<?php echo $row_ventaspos9['SUM(montoOportunidad)']?>,<?php echo $row_ventapos10['SUM(montoOportunidad)']?>,<?php echo $row_ventaspos11['SUM(montoOportunidad)']?>,<?php echo $row_ventaspos12['SUM(montoOportunidad)']?>]
		},
		{
          label: "Abiertas",
          fillColor: "rgba(255, 255, 0, 1)",
          strokeColor: "rgba(60,141,188,0.8)",
          pointColor: "#3b8bba",
          pointStrokeColor: "rgba(60,141,188,1)",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(60,141,188,1)",
          data: [<?php echo $row_abierta['SUM(montoOportunidad)']?>,<?php echo $row_abierta1['SUM(montoOportunidad)']?>,<?php echo $row_abierta2['SUM(montoOportunidad)']?>,<?php echo $row_abierta3['SUM(montoOportunidad)']?>,<?php echo $row_abierta4['SUM(montoOportunidad)']?>,<?php echo $row_abierta5['SUM(montoOportunidad)']?>,<?php echo $row_abierta6['SUM(montoOportunidad)']?>,<?php echo $row_abierta7['SUM(montoOportunidad)']?>,<?php echo $row_abierta8['SUM(montoOportunidad)']?>,<?php echo $row_abierta9['SUM(montoOportunidad)']?>,<?php echo $row_abierta10['SUM(montoOportunidad)']?>,<?php echo $row_abierta11['SUM(montoOportunidad)']?>]
        }
      ]
    };

 
    var areaChartOptions = {
      //Boolean - If we should show the scale at all
      showScale: true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines: false,
      //String - Colour of the grid lines
      scaleGridLineColor: "rgba(0,0,0,.05)",
      //Number - Width of the grid lines
      scaleGridLineWidth: 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines: true,
      //Boolean - Whether the line is curved between points
      bezierCurve: true,
      //Number - Tension of the bezier curve between points
      bezierCurveTension: 0.3,
      //Boolean - Whether to show a dot for each point
      pointDot: false,
      //Number - Radius of each point dot in pixels
      pointDotRadius: 4,
      //Number - Pixel width of point dot stroke
      pointDotStrokeWidth: 1,
      //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
      pointHitDetectionRadius: 20,
      //Boolean - Whether to show a stroke for datasets
      datasetStroke: true,
      //Number - Pixel width of dataset stroke
      datasetStrokeWidth: 3,
      //Boolean - Whether to fill the dataset with a color
      datasetFill: true,
      //String - A legend template
      legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
      //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
      maintainAspectRatio: true,
      //Boolean - whether to make the chart responsive to window resizing
      responsive: true
    };




    //-------------
    //- BAR CHART -
    //-------------
    var barChartCanvas = $("#barChart").get(0).getContext("2d");
    var barChart = new Chart(barChartCanvas);
    var barChartData = areaChartData;
   
    var barChartOptions = {
      //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
      scaleBeginAtZero: true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines: true,
      //String - Colour of the grid lines
      scaleGridLineColor: "rgba(0,0,0,.05)",
      //Number - Width of the grid lines
      scaleGridLineWidth: 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines: true,
      //Boolean - If there is a stroke on each bar
      barShowStroke: false,
      //Number - Pixel width of the bar stroke
      barStrokeWidth: 3,
      //Number - Spacing between each of the X value sets
      barValueSpacing: 5,
      //Number - Spacing between data sets within X values
      barDatasetSpacing: 1,
      //String - A legend template
      legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
      //Boolean - whether to make the chart responsive
      responsive: true,
      maintainAspectRatio: true
    };

    barChartOptions.datasetFill = false;
    barChart.Bar(barChartData, barChartOptions);
	
	 
	
	
  });
</script>

<!--GRAFICO DE ACTIVIDADES-->

<div class="wrapper">
  <header class="main-header">
    <!-- Logo -->
    <a href="inicio.php" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>
        <image src="dist/img/logocrm.png" style=" width: 50px; height: 50px">
        </b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg">
        <image src="dist/img/logocrm.png" style="width:50px; height: 50px" >
        <b>CRM</b>GENNCO </span> </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"> <span class="sr-only">Toggle navigation</span> </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Messages: style can be found in dropdown.less-->
          <li class="dropdown messages-menu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-envelope-o"></i> <span class="label label-success"></span> </a>
            <ul class="dropdown-menu">
            
            </ul>
          </li>
          <!-- Notifications: style can be found in dropdown.less -->
          <li class="dropdown notifications-menu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-bell-o"></i> <span class="label label-warning"><?php if($totalRows_alerta > 0){echo $totalRows_alerta;}?></span> </a>
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
          <li class="dropdown user user-menu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <img src="<?php echo $row_usuario['foto'] ?>" class="user-image" alt="User Image"> <span class="hidden-xs"><?php echo $row_usuario['nombreAsesor']?></span> </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header"> <img src="<?php echo $row_usuario['foto'] ?>" class="img-circle" alt="User Image">
                <p> <?php echo $row_usuario['cargoAsesor']?> </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-right"> <a href="<?php echo $logoutAction?>" class="btn btn-default btn-flat">Cerrar Sesión</a> </div>
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
        <div class="pull-left image"> <img src="<?php echo $row_usuario['foto'] ?>" class="img-circle" alt="User Image"> </div>
        <div class="pull-left info">
          <p><?php echo $row_usuario['nombreAsesor'] ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a> </div>
      </div>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">MEN&Uacute; PRINCIPAL</li>
        <li class="treeview active"> <a href="inicio.php"> <i class="fa fa-dashboard"></i> <span>Pagina Principal</span> </a> </li>
        <li class="treeview"> <a href="#"> <i class="fa fa-edit"></i> <span>Cuentas</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span> </a>
          <ul class="treeview-menu">
            <li><a href="pages/forms/nuevoregistro.php"><i class="fa fa-circle-o"></i> Crear cuentas</a></li>
            <li><a href="pages/forms/cuentascreadas.php"><i class="fa fa-circle-o"></i> Cuentas Creadas</a></li>
          </ul>
        </li>
        <li class="treeview "> <a href="#"> <i class="fa fa-edit"></i> <span>Oportunidades</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span> </a>
          <ul class="treeview-menu">
            <li class="active"><a href="pages/forms/nuevaoportunidad.php"><i class="fa fa-circle-o"></i> Crear Nueva oportunidad</a></li>
            <li><a href="pages/forms/oportunidadescreadas.php"><i class="fa fa-circle-o"></i> Oportunidades Creadas</a></li>
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
      <h1> Inicio <small>Pagina Principal</small> </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li class="active">Pagina Principal</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
          <div class="inner">
            <h3><?php echo $row_oportunidades['COUNT( * )'];?></h3>
            <p>Nuevas Oportunidades</p>
          </div>
          <div class="icon"> <i class="ion ion-bag"></i> </div>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green">
          <div class="inner">
            <h3>$ <?php echo $row_ventas['SUM(montoOportunidad)'];?><sup style="font-size: 20px"></sup></h3>
            <p>Ultimas Ventas</p>
          </div>
          <div class="icon"> <i class="ion ion-stats-bars"></i> </div>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3><?php echo $row_clientes['COUNT( * )'];?></h3>
            <p>Nuevas Cuentas</p>
          </div>
          <div class="icon"> <i class="ion-filing"></i> </div>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-red">
          <div class="inner">
            <h3><?php echo $row_negativas['COUNT( * )'];?></h3>
            <p> Ultimas Propuestas negativas</p>
          </div>
          <div class="icon"> <i class="ion-close-circled"></i> </div>
        </div>
      </div>
      <!-- ./col -->
    </div>
    <!-- /.row -->
    <!-- Main row -->
    <div class="row">
      <!-- Left col -->
      <section class="col-lg-7 connectedSortable">
        <!-- BAR CHART -->
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title">Ventas</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
            <!-- /.pull-rigth -->
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <div class="chart">
              <canvas id="barChart" style="height:250px"></canvas>
              <!-- /.Despliega gráficos de barras -->
              <!-- /.consolida los datos configurados en el areachart -->
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /separate -->
        <div class="box box-success">
        <div class="box-header with-border"> <i class="fa fa-th"></i>
          <h3 class="box-title">Reportes de actividades</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i> </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i> </button>
          </div>
          <!-- /.pull-rigth -->
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="chart">
            <canvas id="barChart2" style="height:200px" ></canvas>
            <!-- /.Despliega gráficos de barras -->
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </section>
      <!-- /.Left col -->
      <!-- right col (We are only adding the ID to make the widgets sortable)-->
      <section class="col-lg-5 connectedSortable">
        <!-- Tareas por hacer-->
        <div class="box box-primary">
          <div class="box-header"> <i class="ion ion-clipboard"></i>
            <h3 class="box-title">Tareas por hacer</h3>
            <div class="box-tools pull-right">
              <ul class="pagination pagination-sm inline">
                <li class="active"><a href="#">Alta</a></li>
                <li ><a href="media.php	">Media</a></li>
                <li><a href="baja.php">Baja</a></li>
              </ul>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <ul class="todo-list">
              <?php
                  if($totalRows_alta >0){ 
                    do{
                               echo  "<li>";
                               echo "<form action='".$editFormAction."' method='post' name='modificatarea'>";
                               echo "<input type='checkbox' value='Completa' name='Realizada' required>";
                               echo "<span class='text'>La oportunidad <a href='pages/forms/perfiloportunidad.php? oportunidad=".$row_alta['Oportunidad_idOportunidad']."'>".$row_alta['Oportunidad_idOportunidad']."</a> de la empresa <b>".$row_alta['nombreEmpresa']."</b>. Tiene la siguiente actividad pendiente:".$row_alta['actividad'].". Para la fecha";
							   echo "<label class='label label-primary'><i class='fa fa-clock-o'></i>" .$row_alta['fechaProgramada']."</label>";
                               echo  "</form>"; 
							   echo "<div class='tools'>";
                               echo "<input type='hidden' name='idTarea' value='".$row_alta['idTarea']."'/>";
							   echo "<input type='hidden' name='detalle' value='La tarea fue realizada exitosamente.'/>";
                               echo "<input type='hidden' name='MM_update' value='modificatarea' />";
                               echo "<input type='submit' value='Realizada' class='btn btn-block btn-success btn-xs'>";
							   echo "<a href='pages/forms/cancelaractividad.php? oportunidad=".$row_alta['Oportunidad_idOportunidad']."&tarea=".$row_alta['idTarea']."'><button type='button' class='btn btn-block btn-danger btn-xs'>Cancelar</button></a></span>";
                              echo "</div>";
							 
                              echo"</li>";
                    } while ($row_alta = mysql_fetch_assoc($alta));
				  }
				  else
				  {
					echo "<li>";
					echo"<button type='button' class='btn btn-block btn-primary btn-lg ion-plus-circled'> No hay tareas de prioridad alta pendientes</button>";  
				    echo"</li>";
				  }
				?>
         </ul>
            </div>
            <!-- /.box-body -->
            </div>
       
          <!-- /.box -->  
         
         
        </section>
        <!-- right col -->
      </div>
      <!-- /.row (main row) -->
   
    <!-- /.content -->
  </div>
  
    </section>
  
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.3.6
    </div>
    <strong>Copyright &copy; 2014-2016 <a href="http://almsaeedstudio.com">Almsaeed Studio</a>.</strong> All rights
    reserved.
  </footer>

 </div>

<script>
  $(function () {
    /* ChartJS
     * -------
     * Here we will create a few charts using ChartJS
     */

    //--------------
    //- AREA CHART -
    //--------------

    // Get context with jQuery - using jQuery's .get() method.
    var areaChartCanvas = $("#barChart2").get(0).getContext("2d");
    // This will get the first returned node in the jQuery collection.
    var areaChart = new Chart(areaChartCanvas);

    var areaChartData = {
      labels: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
      datasets: [
        {
          label: "Visitas",
          fillColor: "#38447F",
          strokeColor: "rgba(60,141,188,0.8)",
          pointColor: "rgba(210, 214, 222, 1)",
          pointStrokeColor: "#c1c7d1",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(220,220,220,1)",
          data: [<?php echo $row_visita1['COUNT(*)']?>, <?php echo $row_visita2['COUNT(*)']?>, <?php echo $row_visita3['COUNT(*)']?>, <?php echo $row_visita4['COUNT(*)']?>, <?php echo $row_visita5['COUNT(*)']?>, <?php echo $row_visita6['COUNT(*)']?>, <?php echo $row_visita7['COUNT(*)']?>,<?php echo $row_visita8['COUNT(*)']?>, <?php echo $row_visita9['COUNT(*)']?>, <?php echo $row_visita10['COUNT(*)']?>, <?php echo $row_visita11['COUNT(*)']?>, <?php echo $row_visita12['COUNT(*)']?>]
        },
        {
          label: "Llamadas",
          fillColor: "#3859FC",
          strokeColor: "rgba(60,141,188,0.8)",
          pointColor: "#3b8bba",
          pointStrokeColor: "rgba(60,141,188,1)",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(60,141,188,1)",
          data: [<?php echo $row_llamada1['COUNT(*)']?>,<?php echo $row_llamada2['COUNT(*)']?>,<?php echo $row_llamada3['COUNT(*)']?>, <?php echo $row_llamada4['COUNT(*)']?>, <?php echo $row_llamada5['COUNT(*)']?>, <?php echo $row_llamada6['COUNT(*)']?>,<?php echo $row_llamada7['COUNT(*)']?>,<?php echo $row_llamada8['COUNT(*)']?>, <?php echo $row_llamada9['COUNT(*)']?>, <?php echo $row_llamada10['COUNT(*)']?>, <?php echo $row_llamada11['COUNT(*)']?>, <?php echo $row_llamada12['COUNT(*)']?>]
        },
		{
          label: "Correos",
          fillColor: "rgba(255, 189, 0, 1)",
          strokeColor: "rgba(60,141,188,0.8)",
          pointColor: "#3b8bba",
          pointStrokeColor: "rgba(60,141,188,1)",	
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(60,141,188,1)",
          data: [<?php echo $row_correo1['COUNT(*)']?>, <?php echo $row_correo2['COUNT(*)']?>, <?php echo $row_correo3['COUNT(*)']?>, <?php echo $row_correo4['COUNT(*)']?>, <?php echo $row_correo5['COUNT(*)']?>, <?php echo $row_correo6['COUNT(*)']?>,<?php echo $row_correo7['COUNT(*)']?>, <?php echo $row_correo8['COUNT(*)']?>, <?php echo $row_correo9['COUNT(*)']?>, <?php echo $row_correo10['COUNT(*)']?>, <?php echo $row_correo11['COUNT(*)']?>, <?php echo $row_correo12['COUNT(*)']?>]
        }
      ]
    };

    var areaChartOptions = {
      //Boolean - If we should show the scale at all
      showScale: true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines: false,
      //String - Colour of the grid lines
      scaleGridLineColor: "rgba(0,0,0,.05)",
      //Number - Width of the grid lines
      scaleGridLineWidth: 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines: true,
      //Boolean - Whether the line is curved between points
      bezierCurve: true,
      //Number - Tension of the bezier curve between points
      bezierCurveTension: 0.3,
      //Boolean - Whether to show a dot for each point
      pointDot: false,
      //Number - Radius of each point dot in pixels
      pointDotRadius: 4,
      //Number - Pixel width of point dot stroke
      pointDotStrokeWidth: 1,
      //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
      pointHitDetectionRadius: 20,
      //Boolean - Whether to show a stroke for datasets
      datasetStroke: true,
      //Number - Pixel width of dataset stroke
      datasetStrokeWidth: 3,
      //Boolean - Whether to fill the dataset with a color
      datasetFill: true,
      //String - A legend template
      legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
      //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
      maintainAspectRatio: true,
      //Boolean - whether to make the chart responsive to window resizing
      responsive: true
    };
	//-------------
    //- BAR CHART -
    //-------------
    var barChartCanvas = $("#barChart2").get(0).getContext("2d");
    var barChart = new Chart(barChartCanvas);
    var barChartData = areaChartData;

    var barChartOptions = {
      //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
      scaleBeginAtZero: true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines: true,
      //String - Colour of the grid lines
      scaleGridLineColor: "rgba(0,0,0,.05)",
      //Number - Width of the grid lines
      scaleGridLineWidth: 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines: true,
      //Boolean - If there is a stroke on each bar
      barShowStroke: false,
      //Number - Pixel width of the bar stroke
      barStrokeWidth: 3,
      //Number - Spacing between each of the X value sets
      barValueSpacing: 5,
      //Number - Spacing between data sets within X values
      barDatasetSpacing: 1,
      //String - A legend template
      legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
      //Boolean - whether to make the chart responsive
      responsive: true,
      maintainAspectRatio: true
    };

    barChartOptions.datasetFill = true;
    barChart.Bar(barChartData, barChartOptions);
	
	
  });
</script>

</body>
</html>
