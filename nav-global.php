<?php
  $use_session = true;              // Session-ID zum Speichern der CSS-Dateien nutzen (Sicherheit)



 function no_injection($input,$typ) {

   if($typ=="int") {
     if(!is_numeric($input))
       $input = "";
   }
   else {
     $input = str_ireplace("select","",$input);
     $input = str_ireplace("insert","",$input);
     $input = str_ireplace("delete","",$input);
     $input = str_ireplace("truncate","",$input);
     $input = str_ireplace("drop","",$input);
     $input = str_ireplace("update","",$input);
     $input = str_ireplace("table","",$input);
   }


   return $input;
 }



?>