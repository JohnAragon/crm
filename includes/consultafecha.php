<?php

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
$query_ventaspos2 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-02-01' AND '".$row_año['YEAR(NOW())']."-02-29' AND estadoOportunidad LIKE '%%Positiva%%'");
$ventaspos2= mysql_query($query_ventaspos2, $crm) or die(mysql_error());
$row_ventaspos2 = mysql_fetch_assoc($ventaspos2);
$totalRows_ventaspos2 = mysql_num_rows($ventaspos2);

mysql_select_db($database_crm, $crm);
$query_ventaspos3 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-03-01' AND '".$row_año['YEAR(NOW())']."-03-31' AND estadoOportunidad LIKE '%%Positiva%%'");
$ventaspos3= mysql_query($query_ventaspos3, $crm) or die(mysql_error());
$row_ventaspos3 = mysql_fetch_assoc($ventaspos3);
$totalRows_ventaspos3 = mysql_num_rows($ventaspos3);

mysql_select_db($database_crm, $crm);
$query_ventaspos4 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-04-01' AND '".$row_año['YEAR(NOW())']."-04-30' AND estadoOportunidad LIKE '%%Positiva%%'");
$ventaspos4= mysql_query($query_ventaspos4, $crm) or die(mysql_error());
$row_ventaspos4 = mysql_fetch_assoc($ventaspos4);
$totalRows_ventaspos4 = mysql_num_rows($ventaspos4);

mysql_select_db($database_crm, $crm);
$query_ventaspos5 = sprintf ("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-05-01' AND '".$row_año['YEAR(NOW())']."-05-31' AND estadoOportunidad LIKE '%%Positiva%%'");
$ventaspos5= mysql_query($query_ventaspos5, $crm) or die(mysql_error());
$row_ventaspos5 = mysql_fetch_assoc($ventaspos5);
$totalRows_ventaspos5 = mysql_num_rows($ventaspos5);

mysql_select_db($database_crm, $crm);
$query_ventaspos6 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-06-01' AND '".$row_año['YEAR(NOW())']."-06-30' AND estadoOportunidad LIKE '%%Positiva%%'");
$ventaspos6= mysql_query($query_ventaspos6, $crm) or die(mysql_error());
$row_ventaspos6 = mysql_fetch_assoc($ventaspos6);
$totalRows_ventaspos6 = mysql_num_rows($ventaspos6);

mysql_select_db($database_crm, $crm);
$query_ventaspos7 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-07-01' AND '".$row_año['YEAR(NOW())']."-07-31' AND estadoOportunidad LIKE '%%Positiva%%'");
$ventaspos7= mysql_query($query_ventaspos7, $crm) or die(mysql_error());
$row_ventaspos7 = mysql_fetch_assoc($ventaspos7);
$totalRows_ventaspos7 = mysql_num_rows($ventaspos7);

mysql_select_db($database_crm, $crm);
$query_ventaspos8 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-08-01' AND '".$row_año['YEAR(NOW())']."-08-31' AND estadoOportunidad LIKE '%%Positiva%%'");
$ventaspos8= mysql_query($query_ventaspos8, $crm) or die(mysql_error());
$row_ventaspos8 = mysql_fetch_assoc($ventaspos8);
$totalRows_ventaspos8 = mysql_num_rows($ventaspos8);

mysql_select_db($database_crm, $crm);
$query_ventaspos9 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-09-01' AND '".$row_año['YEAR(NOW())']."-09-30' AND estadoOportunidad LIKE '%%Positiva%%'");
$ventaspos9= mysql_query($query_ventaspos9, $crm) or die(mysql_error());
$row_ventaspos9 = mysql_fetch_assoc($ventaspos9);
$totalRows_ventaspos9 = mysql_num_rows($ventaspos9);

mysql_select_db($database_crm, $crm);
$query_ventaspos10 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-10-01' AND '".$row_año['YEAR(NOW())']."-10-31' AND estadoOportunidad LIKE '%%Positiva%%'");
$ventaspos10= mysql_query($query_ventaspos10, $crm) or die(mysql_error());
$row_ventaspos10 = mysql_fetch_assoc($ventaspos10);
$totalRows_ventaspos10 = mysql_num_rows($ventaspos10);

mysql_select_db($database_crm, $crm);
$query_ventaspos11 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-11-01' AND '".$row_año['YEAR(NOW())']."-11-30' AND estadoOportunidad LIKE '%%Positiva%%'");
$ventaspos11= mysql_query($query_ventaspos11, $crm) or die(mysql_error());
$row_ventaspos11 = mysql_fetch_assoc($ventaspos11);
$totalRows_ventaspos11 = mysql_num_rows($ventaspos11);

mysql_select_db($database_crm, $crm);
$query_ventaspos12 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-12-01' AND '".$row_año['YEAR(NOW())']."-12-30' AND estadoOportunidad LIKE '%%Positiva%%'");
$ventaspos12= mysql_query($query_ventaspos12, $crm) or die(mysql_error());
$row_ventaspos12 = mysql_fetch_assoc($ventaspos12);
$totalRows_ventaspos12 = mysql_num_rows($ventaspos12);

//Busqueda valor propuestas negativas
mysql_select_db($database_crm, $crm);
$query_negativa = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-01-01' AND '".$row_año['YEAR(NOW())']."-01-31' AND estadoOportunidad LIKE '%%Negativa%%'");
$negativa= mysql_query($query_negativa, $crm) or die(mysql_error());
$row_negativa = mysql_fetch_assoc($negativa);
$totalRows_negativa = mysql_num_rows($negativa);

mysql_select_db($database_crm, $crm);
$query_negativa1 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-02-01' AND '".$row_año['YEAR(NOW())']."-02-29' AND estadoOportunidad LIKE '%%Negativa%%'");
$negativa1= mysql_query($query_negativa1, $crm) or die(mysql_error());
$row_negativa1 = mysql_fetch_assoc($negativa1);
$totalRows_negativa1 = mysql_num_rows($negativa1);

mysql_select_db($database_crm, $crm);
$query_negativa2 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-03-01' AND '".$row_año['YEAR(NOW())']."-03-31' AND estadoOportunidad LIKE '%%Negativa%%'");
$negativa2= mysql_query($query_negativa2, $crm) or die(mysql_error());
$row_negativa2 = mysql_fetch_assoc($negativa2);
$totalRows_negativa2 = mysql_num_rows($negativa2);

mysql_select_db($database_crm, $crm);
$query_negativa3 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-04-01' AND '".$row_año['YEAR(NOW())']."-04-30' AND estadoOportunidad LIKE '%%Negativa%%'");
$negativa3= mysql_query($query_negativa3, $crm) or die(mysql_error());
$row_negativa3 = mysql_fetch_assoc($negativa3);
$totalRows_negativa3 = mysql_num_rows($negativa3);

mysql_select_db($database_crm, $crm);
$query_negativa4 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-05-01' AND '".$row_año['YEAR(NOW())']."-05-31' AND estadoOportunidad LIKE '%%Negativa%%'");
$negativa4= mysql_query($query_negativa4, $crm) or die(mysql_error());
$row_negativa4 = mysql_fetch_assoc($negativa4);
$totalRows_negativa4 = mysql_num_rows($negativa4);

mysql_select_db($database_crm, $crm);
$query_negativa5 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-06-01' AND '".$row_año['YEAR(NOW())']."-06-30' AND estadoOportunidad LIKE '%%Negativa%%'");
$negativa5= mysql_query($query_negativa5, $crm) or die(mysql_error());
$row_negativa5 = mysql_fetch_assoc($negativa5);
$totalRows_negativa5 = mysql_num_rows($negativa5);

mysql_select_db($database_crm, $crm);
$query_negativa6 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-07-01' AND '".$row_año['YEAR(NOW())']."-07-31' AND estadoOportunidad LIKE '%%Negativa%%'");
$negativa6= mysql_query($query_negativa6, $crm) or die(mysql_error());
$row_negativa6 = mysql_fetch_assoc($negativa6);
$totalRows_negativa6 = mysql_num_rows($negativa6);

mysql_select_db($database_crm, $crm);
$query_negativa7= sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-08-01' AND '".$row_año['YEAR(NOW())']."-08-31' AND estadoOportunidad LIKE '%%Negativa%%'");
$negativa7= mysql_query($query_negativa7, $crm) or die(mysql_error());
$row_negativa7 = mysql_fetch_assoc($negativa7);
$totalRows_negativa7 = mysql_num_rows($negativa7);

mysql_select_db($database_crm, $crm);
$query_negativa8 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-09-30' AND '".$row_año['YEAR(NOW())']."-09-30' AND estadoOportunidad LIKE '%%Negativa%%'");
$negativa8= mysql_query($query_negativa8, $crm) or die(mysql_error());
$row_negativa8 = mysql_fetch_assoc($negativa8);
$totalRows_negativa8 = mysql_num_rows($negativa8);

mysql_select_db($database_crm, $crm);
$query_negativa9 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-10-01' AND '".$row_año['YEAR(NOW())']."-10-30' AND estadoOportunidad LIKE '%%Negativa%%'");
$negativa9= mysql_query($query_negativa9, $crm) or die(mysql_error());
$row_negativa9 = mysql_fetch_assoc($negativa9);
$totalRows_negativa9 = mysql_num_rows($negativa9);

mysql_select_db($database_crm, $crm);
$query_negativa10 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-11-01' AND '".$row_año['YEAR(NOW())']."-11-31' AND estadoOportunidad LIKE '%%Negativa%%'");
$negativa10= mysql_query($query_negativa10, $crm) or die(mysql_error());
$row_negativa10 = mysql_fetch_assoc($negativa10);
$totalRows_negativa10 = mysql_num_rows($negativa10);

mysql_select_db($database_crm, $crm);
$query_negativa11 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-12-01' AND '".$row_año['YEAR(NOW())']."-12-31' AND estadoOportunidad LIKE '%%Negativa%%'");
$negativa11= mysql_query($query_negativa11, $crm) or die(mysql_error());
$row_negativa11 = mysql_fetch_assoc($negativa11);
$totalRows_negativa11 = mysql_num_rows($negativa11);

//Busqueda valor propuestas Abiertas
mysql_select_db($database_crm, $crm);
$query_abierta = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-01-01' AND '".$row_año['YEAR(NOW())']."-01-31' AND estadoOportunidad LIKE '%%Abierta%%'");
$abierta= mysql_query($query_abierta, $crm) or die(mysql_error());
$row_abierta = mysql_fetch_assoc($abierta);
$totalRows_abierta = mysql_num_rows($abierta);

mysql_select_db($database_crm, $crm);
$query_abierta1 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-02-01' AND '".$row_año['YEAR(NOW())']."-02-29' AND estadoOportunidad LIKE '%%Abierta%%'");
$abierta1= mysql_query($query_abierta1, $crm) or die(mysql_error());
$row_abierta1 = mysql_fetch_assoc($abierta1);
$totalRows_abierta1 = mysql_num_rows($abierta1);

mysql_select_db($database_crm, $crm);
$query_abierta2 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-03-01' AND '".$row_año['YEAR(NOW())']."-03-31' AND estadoOportunidad LIKE '%%Abierta%%'");
$abierta2= mysql_query($query_abierta2, $crm) or die(mysql_error());
$row_abierta2 = mysql_fetch_assoc($abierta2);
$totalRows_abierta2 = mysql_num_rows($abierta2);

mysql_select_db($database_crm, $crm);
$query_abierta3 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-04-01' AND '".$row_año['YEAR(NOW())']."-04-30' AND estadoOportunidad LIKE '%%Abierta%%'");
$abierta3= mysql_query($query_abierta3, $crm) or die(mysql_error());
$row_abierta3 = mysql_fetch_assoc($abierta3);
$totalRows_abierta3 = mysql_num_rows($abierta3);

mysql_select_db($database_crm, $crm);
$query_abierta4 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-05-01' AND '".$row_año['YEAR(NOW())']."-05-31' AND estadoOportunidad LIKE '%%Abierta%%'");
$abierta4= mysql_query($query_abierta4, $crm) or die(mysql_error());
$row_abierta4 = mysql_fetch_assoc($abierta4);
$totalRows_abierta4 = mysql_num_rows($abierta4);

mysql_select_db($database_crm, $crm);
$query_abierta5 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-06-01' AND '".$row_año['YEAR(NOW())']."-06-30' AND estadoOportunidad LIKE '%%Abierta%%'");
$abierta5= mysql_query($query_abierta5, $crm) or die(mysql_error());
$row_abierta5 = mysql_fetch_assoc($abierta5);
$totalRows_abierta5 = mysql_num_rows($abierta5);

mysql_select_db($database_crm, $crm);
$query_abierta6 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-07-01' AND '".$row_año['YEAR(NOW())']."-07-31' AND estadoOportunidad LIKE '%%Negativa%%'");
$abierta6= mysql_query($query_abierta6, $crm) or die(mysql_error());
$row_abierta6 = mysql_fetch_assoc($abierta6);
$totalRows_abierta6 = mysql_num_rows($abierta6);

mysql_select_db($database_crm, $crm);
$query_abierta7= sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-08-01' AND '".$row_año['YEAR(NOW())']."-08-31' AND estadoOportunidad LIKE '%%Abierta%%'");
$abierta7= mysql_query($query_abierta7, $crm) or die(mysql_error());
$row_abierta7 = mysql_fetch_assoc($abierta7);
$totalRows_abierta7 = mysql_num_rows($abierta7);

mysql_select_db($database_crm, $crm);
$query_abierta8 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-09-01' AND '".$row_año['YEAR(NOW())']."-09-30' AND estadoOportunidad LIKE '%%Abierta%%'");
$abierta8= mysql_query($query_abierta8, $crm) or die(mysql_error());
$row_abierta8 = mysql_fetch_assoc($abierta8);
$totalRows_abierta8 = mysql_num_rows($abierta8);

mysql_select_db($database_crm, $crm);
$query_abierta9 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-10-01' AND '".$row_año['YEAR(NOW())']."-10-31' AND estadoOportunidad LIKE '%%Abierta%%'");
$abierta9= mysql_query($query_abierta9, $crm) or die(mysql_error());
$row_abierta9 = mysql_fetch_assoc($abierta9);
$totalRows_abierta9 = mysql_num_rows($abierta9);

mysql_select_db($database_crm, $crm);
$query_abierta10 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-11-01' AND '".$row_año['YEAR(NOW())']."-11-30' AND estadoOportunidad LIKE '%%Abierta%%'");
$abierta10= mysql_query($query_abierta10, $crm) or die(mysql_error());
$row_abierta10 = mysql_fetch_assoc($abierta10);
$totalRows_abierta10 = mysql_num_rows($abierta10);

mysql_select_db($database_crm, $crm);
$query_abierta11 = sprintf("SELECT SUM(montoOportunidad) FROM Oportunidad WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-12-01' AND '".$row_año['YEAR(NOW())']."-12-31' AND estadoOportunidad LIKE '%%Abierta%%'");
$abierta11= mysql_query($query_abierta11, $crm) or die(mysql_error());
$row_abierta11 = mysql_fetch_assoc($abierta11);
$totalRows_abierta11 = mysql_num_rows($abierta11);

//*************Termina datos de resumen mensual  de propuestas******//// 





//*************Recollección de resumen mensual  de tareas******//// 


//Conteo de actividades de correo
mysql_select_db($database_crm, $crm);
$query_correo1 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-01-01' AND '".$row_año['YEAR(NOW())']."-01-31'");
$correo1= mysql_query($query_correo1, $crm) or die(mysql_error());
$row_correo1 = mysql_fetch_assoc($correo1);
$totalRows_correo1 = mysql_num_rows($correo1);

mysql_select_db($database_crm, $crm);
$query_correo2 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-02-01' AND '".$row_año['YEAR(NOW())']."-02-29'");
$correo2= mysql_query($query_correo2, $crm) or die(mysql_error());
$row_correo2 = mysql_fetch_assoc($correo2);
$totalRows_correo2 = mysql_num_rows($correo2);

mysql_select_db($database_crm, $crm);
$query_correo3 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-03-01' AND '".$row_año['YEAR(NOW())']."-03-31'");
$correo3= mysql_query($query_correo3, $crm) or die(mysql_error());
$row_correo3 = mysql_fetch_assoc($correo3);
$totalRows_correo3 = mysql_num_rows($correo3);

mysql_select_db($database_crm, $crm);
$query_correo4 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-04-01' AND '".$row_año['YEAR(NOW())']."-04-30'");
$correo4= mysql_query($query_correo4, $crm) or die(mysql_error());
$row_correo4 = mysql_fetch_assoc($correo4);
$totalRows_correo4 = mysql_num_rows($correo4);

mysql_select_db($database_crm, $crm);
$query_correo5 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-05-01' AND '".$row_año['YEAR(NOW())']."-05-31'");
$correo5= mysql_query($query_correo5, $crm) or die(mysql_error());
$row_correo5 = mysql_fetch_assoc($correo5);
$totalRows_correo5 = mysql_num_rows($correo5);

mysql_select_db($database_crm, $crm);
$query_correo6 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-06-01' AND '".$row_año['YEAR(NOW())']."-06-30'");
$correo6= mysql_query($query_correo6, $crm) or die(mysql_error());
$row_correo6 = mysql_fetch_assoc($correo6);
$totalRows_correo6 = mysql_num_rows($correo6);

mysql_select_db($database_crm, $crm);
$query_correo7 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-07-01' AND '".$row_año['YEAR(NOW())']."-07-30'");
$correo7= mysql_query($query_correo7, $crm) or die(mysql_error());
$row_correo7 = mysql_fetch_assoc($correo7);
$totalRows_correo7 = mysql_num_rows($correo7);

mysql_select_db($database_crm, $crm);
$query_correo8 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-08-01' AND '".$row_año['YEAR(NOW())']."-08-31'");
$correo8= mysql_query($query_correo8, $crm) or die(mysql_error());
$row_correo8 = mysql_fetch_assoc($correo8);
$totalRows_correo8 = mysql_num_rows($correo8);

mysql_select_db($database_crm, $crm);
$query_correo9 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-09-01' AND '".$row_año['YEAR(NOW())']."-09-30'");
$correo9= mysql_query($query_correo9, $crm) or die(mysql_error());
$row_correo9 = mysql_fetch_assoc($correo9);
$totalRows_correo9 = mysql_num_rows($correo9);


mysql_select_db($database_crm, $crm);
$query_correo10 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-10-01' AND '".$row_año['YEAR(NOW())']."-10-31'");
$correo10= mysql_query($query_correo10, $crm) or die(mysql_error());
$row_correo10 = mysql_fetch_assoc($correo10);
$totalRows_correo10 = mysql_num_rows($correo10);

mysql_select_db($database_crm, $crm);
$query_correo11 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-11-01' AND '".$row_año['YEAR(NOW())']."-11-30'");
$correo11= mysql_query($query_correo11, $crm) or die(mysql_error());
$row_correo11 = mysql_fetch_assoc($correo11);
$totalRows_correo11 = mysql_num_rows($correo11);

mysql_select_db($database_crm, $crm);
$query_correo12 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-12-01' AND '".$row_año['YEAR(NOW())']."-12-31'");
$correo12= mysql_query($query_correo12, $crm) or die(mysql_error());
$row_correo12 = mysql_fetch_assoc($correo12);
$totalRows_correo12 = mysql_num_rows($correo12);

//Conteo de actividades de llamadas
mysql_select_db($database_crm, $crm);
$query_llamada1 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-01-01' AND '".$row_año['YEAR(NOW())']."-01-31'");
$llamada1= mysql_query($query_llamada1, $crm) or die(mysql_error());
$row_llamada1 = mysql_fetch_assoc($llamada1);
$totalRows_llamada1 = mysql_num_rows($llamada1);

mysql_select_db($database_crm, $crm);
$query_llamada2 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-02-01' AND '".$row_año['YEAR(NOW())']."-02-29'");
$llamada2= mysql_query($query_llamada2, $crm) or die(mysql_error());
$row_llamada2 = mysql_fetch_assoc($llamada2);
$totalRows_llamada2 = mysql_num_rows($llamada2);

mysql_select_db($database_crm, $crm);
$query_llamada3 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-03-01' AND '".$row_año['YEAR(NOW())']."-03-31'");
$llamada3= mysql_query($query_llamada3, $crm) or die(mysql_error());
$row_llamada3 = mysql_fetch_assoc($llamada3);
$totalRows_llamada3 = mysql_num_rows($llamada3);

mysql_select_db($database_crm, $crm);
$query_llamada4 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-04-01' AND '".$row_año['YEAR(NOW())']."-03-30'");
$llamada4= mysql_query($query_llamada4, $crm) or die(mysql_error());
$row_llamada4 = mysql_fetch_assoc($llamada4);
$totalRows_llamada4 = mysql_num_rows($llamada4);

mysql_select_db($database_crm, $crm);
$query_llamada5 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-05-01' AND '".$row_año['YEAR(NOW())']."-05-31'");
$llamada5= mysql_query($query_llamada5, $crm) or die(mysql_error());
$row_llamada5 = mysql_fetch_assoc($llamada5);
$totalRows_llamada5 = mysql_num_rows($llamada5);

mysql_select_db($database_crm, $crm);
$query_llamada6 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-06-01' AND '".$row_año['YEAR(NOW())']."-06-30'");
$llamada6= mysql_query($query_llamada6, $crm) or die(mysql_error());
$row_llamada6 = mysql_fetch_assoc($llamada6);
$totalRows_llamada6 = mysql_num_rows($llamada6);

mysql_select_db($database_crm, $crm);
$query_llamada6 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-06-01' AND '".$row_año['YEAR(NOW())']."-06-30'");
$llamada6= mysql_query($query_llamada6, $crm) or die(mysql_error());
$row_llamada6 = mysql_fetch_assoc($llamada6);
$totalRows_llamada6 = mysql_num_rows($llamada6);

mysql_select_db($database_crm, $crm);
$query_llamada7 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-07-01' AND '".$row_año['YEAR(NOW())']."-07-31'");
$llamada7= mysql_query($query_llamada7, $crm) or die(mysql_error());
$row_llamada7 = mysql_fetch_assoc($llamada7);
$totalRows_llamada7 = mysql_num_rows($llamada7);

mysql_select_db($database_crm, $crm);
$query_llamada8 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-08-01' AND '".$row_año['YEAR(NOW())']."-08-31'");
$llamada8= mysql_query($query_llamada8, $crm) or die(mysql_error());
$row_llamada8 = mysql_fetch_assoc($llamada8);
$totalRows_llamada8 = mysql_num_rows($llamada8);

mysql_select_db($database_crm, $crm);
$query_llamada9 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-09-01' AND '".$row_año['YEAR(NOW())']."-09-30'");
$llamada9= mysql_query($query_llamada9, $crm) or die(mysql_error());
$row_llamada9 = mysql_fetch_assoc($llamada9);
$totalRows_llamada9 = mysql_num_rows($llamada9);

mysql_select_db($database_crm, $crm);
$query_llamada10 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-10-01' AND '".$row_año['YEAR(NOW())']."-10-31'");
$llamada10= mysql_query($query_llamada10, $crm) or die(mysql_error());
$row_llamada10 = mysql_fetch_assoc($llamada10);
$totalRows_llamada10 = mysql_num_rows($llamada10);

mysql_select_db($database_crm, $crm);
$query_llamada11 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN ".$row_año['YEAR(NOW())']."-11-01 AND ".$row_año['YEAR(NOW())']."-11-30");
$llamada11= mysql_query($query_llamada11, $crm) or die(mysql_error());
$row_llamada11 = mysql_fetch_assoc($llamada11);
$totalRows_llamada11 = mysql_num_rows($llamada11);

mysql_select_db($database_crm, $crm);
$query_llamada12 = sprintf("SELECT COUNT(*) FROM Llamada WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-12-01' AND '".$row_año['YEAR(NOW())']."-12-31'");
$llamada12= mysql_query($query_llamada12, $crm) or die(mysql_error());
$row_llamada12 = mysql_fetch_assoc($llamada12);
$totalRows_llamada12 = mysql_num_rows($llamada12);



//Conteo de actividades de correos

mysql_select_db($database_crm, $crm);
$query_correo1 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-01-01' AND '".$row_año['YEAR(NOW())']."-01-31'");
$correo1= mysql_query($query_correo1, $crm) or die(mysql_error());
$row_correo1 = mysql_fetch_assoc($correo1);
$totalRows_correo1 = mysql_num_rows($correo1);

mysql_select_db($database_crm, $crm);
$query_correo2 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-02-01' AND '".$row_año['YEAR(NOW())']."-02-29'");
$correo2= mysql_query($query_correo2, $crm) or die(mysql_error());
$row_correo2 = mysql_fetch_assoc($correo2);
$totalRows_correo2 = mysql_num_rows($correo2);

mysql_select_db($database_crm, $crm);
$query_correo3 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-03-01' AND '".$row_año['YEAR(NOW())']."-03-31'");
$correo3= mysql_query($query_correo3, $crm) or die(mysql_error());
$row_correo3 = mysql_fetch_assoc($correo3);
$totalRows_correo3 = mysql_num_rows($correo3);

mysql_select_db($database_crm, $crm);
$query_correo4 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-04-01' AND '".$row_año['YEAR(NOW())']."-04-30'");
$correo4= mysql_query($query_correo4, $crm) or die(mysql_error());
$row_correo4 = mysql_fetch_assoc($correo4);
$totalRows_correo4 = mysql_num_rows($correo4);

mysql_select_db($database_crm, $crm);
$query_correo5 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-05-01' AND '".$row_año['YEAR(NOW())']."-05-31'");
$correo5= mysql_query($query_correo5, $crm) or die(mysql_error());
$row_correo5 = mysql_fetch_assoc($correo5);
$totalRows_correo5 = mysql_num_rows($correo5);

mysql_select_db($database_crm, $crm);
$query_correo6 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-06-01' AND '".$row_año['YEAR(NOW())']."-06-30'");
$correo6= mysql_query($query_correo6, $crm) or die(mysql_error());
$row_correo6 = mysql_fetch_assoc($correo6);
$totalRows_correo6 = mysql_num_rows($correo6);

mysql_select_db($database_crm, $crm);
$query_correo7 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-07-01' AND '".$row_año['YEAR(NOW())']."-07-31'");
$correo7= mysql_query($query_correo7, $crm) or die(mysql_error());
$row_correo7 = mysql_fetch_assoc($correo7);
$totalRows_correo7 = mysql_num_rows($correo7);

mysql_select_db($database_crm, $crm);
$query_correo8 = sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-08-01' AND '".$row_año['YEAR(NOW())']."-08-31'");
$correo8= mysql_query($query_correo8, $crm) or die(mysql_error());
$row_correo8 = mysql_fetch_assoc($correo8);
$totalRows_correo8 = mysql_num_rows($correo8);

mysql_select_db($database_crm, $crm);
$query_correo9= sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-09-01' AND '".$row_año['YEAR(NOW())']."-09-30'");
$correo9= mysql_query($query_correo9, $crm) or die(mysql_error());
$row_correo9 = mysql_fetch_assoc($correo9);
$totalRows_correo9 = mysql_num_rows($correo9);

mysql_select_db($database_crm, $crm);
$query_correo10= sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-10-01' AND '".$row_año['YEAR(NOW())']."-10-31'");
$correo10= mysql_query($query_correo10, $crm) or die(mysql_error());
$row_correo10 = mysql_fetch_assoc($correo10);
$totalRows_correo10 = mysql_num_rows($correo10);

mysql_select_db($database_crm, $crm);
$query_correo11= sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-11-01' AND '".$row_año['YEAR(NOW())']."-11-30'");
$correo11= mysql_query($query_correo11, $crm) or die(mysql_error());
$row_correo11 = mysql_fetch_assoc($correo11);
$totalRows_correo11 = mysql_num_rows($correo11);

mysql_select_db($database_crm, $crm);
$query_correo12= sprintf("SELECT COUNT(*) FROM Correo WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-12-01' AND '".$row_año['YEAR(NOW())']."-12-31'");
$correo12= mysql_query($query_correo12, $crm) or die(mysql_error());
$row_correo12 = mysql_fetch_assoc($correo12);
$totalRows_correo12 = mysql_num_rows($correo12);

//Conteo de actividades de visitas

mysql_select_db($database_crm, $crm);
$query_visita1 = sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-01-01' AND '".$row_año['YEAR(NOW())']."-01-31'");
$visita1= mysql_query($query_visita1, $crm) or die(mysql_error());
$row_visita1 = mysql_fetch_assoc($visita1);
$totalRows_visita1 = mysql_num_rows($visita1);

mysql_select_db($database_crm, $crm);
$query_visita2 = sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-02-01' AND '".$row_año['YEAR(NOW())']."-02-29'");
$visita2= mysql_query($query_visita2, $crm) or die(mysql_error());
$row_visita2 = mysql_fetch_assoc($visita2);
$totalRows_visita2 = mysql_num_rows($visita2);

mysql_select_db($database_crm, $crm);
$query_visita3 = sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-03-01' AND '".$row_año['YEAR(NOW())']."-03-31'");
$visita3= mysql_query($query_visita3, $crm) or die(mysql_error());
$row_visita3 = mysql_fetch_assoc($visita3);
$totalRows_visita3 = mysql_num_rows($visita3);

mysql_select_db($database_crm, $crm);
$query_visita4 = sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-04-01' AND '".$row_año['YEAR(NOW())']."-04-30'");
$visita4= mysql_query($query_visita4, $crm) or die(mysql_error());
$row_visita4 = mysql_fetch_assoc($visita4);
$totalRows_visita4 = mysql_num_rows($visita4);

mysql_select_db($database_crm, $crm);
$query_visita5 = sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-05-01' AND '".$row_año['YEAR(NOW())']."-05-31'");
$visita5= mysql_query($query_visita5, $crm) or die(mysql_error());
$row_visita5 = mysql_fetch_assoc($visita5);
$totalRows_visita5 = mysql_num_rows($visita5);

mysql_select_db($database_crm, $crm);
$query_visita6 = sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-06-01' AND '".$row_año['YEAR(NOW())']."-06-30'");
$visita6= mysql_query($query_visita6, $crm) or die(mysql_error());
$row_visita6 = mysql_fetch_assoc($visita6);
$totalRows_visita6 = mysql_num_rows($visita6);

mysql_select_db($database_crm, $crm);
$query_visita7 = sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-07-01' AND '".$row_año['YEAR(NOW())']."-07-31'");
$visita7= mysql_query($query_visita7, $crm) or die(mysql_error());
$row_visita7 = mysql_fetch_assoc($visita7);
$totalRows_visita7 = mysql_num_rows($visita7);

mysql_select_db($database_crm, $crm);
$query_visita8 = sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-08-01' AND '".$row_año['YEAR(NOW())']."-08-31'");
$visita8= mysql_query($query_visita8, $crm) or die(mysql_error());
$row_visita8 = mysql_fetch_assoc($visita8);
$totalRows_visita8 = mysql_num_rows($visita8);

mysql_select_db($database_crm, $crm);
$query_visita9= sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-09-01' AND '".$row_año['YEAR(NOW())']."-09-30'");
$visita9= mysql_query($query_visita9, $crm) or die(mysql_error());
$row_visita9 = mysql_fetch_assoc($visita9);
$totalRows_visita9 = mysql_num_rows($visita9);

mysql_select_db($database_crm, $crm);
$query_visita10= sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-10-01' AND '".$row_año['YEAR(NOW())']."-10-31'");
$visita10= mysql_query($query_visita10, $crm) or die(mysql_error());
$row_visita10 = mysql_fetch_assoc($visita10);
$totalRows_visita10 = mysql_num_rows($visita10);

mysql_select_db($database_crm, $crm);
$query_visita11= sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-11-01' AND '".$row_año['YEAR(NOW())']."-11-30'");
$visita11= mysql_query($query_correo11, $crm) or die(mysql_error());
$row_visita11 = mysql_fetch_assoc($visita11);
$totalRows_visita11 = mysql_num_rows($visita11);

mysql_select_db($database_crm, $crm);
$query_visita12= sprintf("SELECT COUNT(*) FROM Visita WHERE fechaActualizacion BETWEEN '".$row_año['YEAR(NOW())']."-12-01' AND '".$row_año['YEAR(NOW())']."-12-31'");
$visita12= mysql_query($query_visita12, $crm) or die(mysql_error());
$row_visita12 = mysql_fetch_assoc($visita12);
$totalRows_visita12 = mysql_num_rows($visita12);

?>