<?php
header("Content-Type: text/html;charset=utf-8");
      $buscar = $_POST['b'];
        
      if(!empty($buscar)) {
            buscar($buscar);
      }
        
      function buscar($b) {
            $con = mysql_connect('localhost','genncoco_usercrm', 'asesor123');
            mysql_select_db('genncoco_crm', $con);
        
            $sql = mysql_query("SELECT * FROM Cuentas WHERE nombreEmpresa LIKE '%".$b."%' LIMIT 1" ,$con);
            $contar = @mysql_num_rows($sql);
            $sql_1 = mysql_query("SELECT * FROM Contactos INNER JOIN Cuentas ON cuentas_idCuentas=idCuentas AND Estado='Activo' WHERE nombreEmpresa LIKE '%".$b."%'" ,$con);
            $mostrar = @mysql_num_rows($sql_1);
              
            if($contar == 0){
            echo  "<button type='button' class='btn btn-block btn-danger btn-flat'>No se han encontrado cuentas relacionadas para <b>".$b."</b> </button> <br>";
            }else{
              while($row=mysql_fetch_array($sql)){
                $nombre = $row['nombreEmpresa'];
                $prefijo = $row['idCuentas'];
                echo"<div class='form-group'>";
                 echo" <label>Codigo Empresa</label>";
                 echo "<input type='text' readonly class='form-control' name='Cuentas_idCuentas' value='".$prefijo."'>";
                echo"</div>";
                   
                 echo"<div class='form-group' id='resultado'>";
                 echo" <label>Nombre Empresa</label>";
                 echo "<input type='text' readonly class='form-control' name='idempresa' id='resultado' value='".$nombre."'>";
                echo"</div>";
                
                
            }
       }
        
              
            if($mostrar == 0){
                  echo "<button type='button' class='btn btn-block btn-danger btn-flat'>No se han encontrado contactos para la cuenta <b>".$b."</b> </button> <br>";
            }else{
              while($row=mysql_fetch_array($sql_1)){ 
                echo"<div class='form-group'>";
                echo"<label>Elija el contacto de la empresa</label>";
                echo"<select class='form-control' name='Contacto_idContacto'>";
                do{
                     echo"<option value='".$row['idContacto']."'>".$row['nombreContacto']."</option>";
                } while($row=mysql_fetch_array($sql_1));
                echo"</select>";
                echo"</div>";
            
              
                
                
            }
            
       } 
      }   
        
?>