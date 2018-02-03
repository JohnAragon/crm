<?php

# FileName="Connection_php_mysql.htm"

# Type="MYSQL"

# HTTP="true"

$hostname_crm = "localhost";

$database_crm = "genncoco_crm";

$username_crm = "genncoco_usercrm";

$password_crm = "asesor123";

$crm = mysql_pconnect($hostname_crm, $username_crm, $password_crm) or trigger_error(mysql_error(),E_USER_ERROR);

mysql_set_charset('utf8'); 

?>
