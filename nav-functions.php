<?php

@include_once (dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR. "wp-config.php");
@include_once (dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR."wp-includes/wp-db.php");
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR ."nav-global.php");


if(!defined('NAV2ME_URLPATH'))
  define('NAV2ME_URLPATH', WP_CONTENT_URL.'/plugins/'.plugin_basename( dirname(__FILE__)) );

if(!session_id())
  session_start();


$cert = no_injection($_POST['cert'],"str");

if(isset($_POST['id']))
  $map_id = no_injection($_POST['id'],"int");
else
  $map_id = "";

if(isset($_POST['aktion']))
  $aktion = no_injection($_POST['aktion'],"str");
else $aktion="";

$loc = $_POST['loc'];        //enthält Location Daten (Adresse, Long/Lat, Icon) zu neuer oder editierter Map
$data = $_POST['data'];      //enthält Info-Daten (Template, Memo, Info-Text) zu neuer oder editierter Map
$option = $_POST['option'];  //enthält Optionen (Breite, Höhe, Karteneinstellungen) zu neuer oder editierter Map


$maptypes_array = array("ROADMAP","SATELLITE","HYBRID","TERRAIN");
$zoomcontrols_array = array("SMALL"=>__( "Small", "nav2me" ),"LARGE"=>__( "Large", "nav2me" ),"DEFAULT"=>__( "Default", "nav2me" ));
$mapcontrols_array = array("HORIZONTAL_BAR"=>__( "Horizontal", "nav2me" ),"DROPDOWN_MENU"=>__( "Drop down", "nav2me" ),"DEFAULT"=>__( "Default", "nav2me" ));


$code = "";



//===== neue Map anlegen (Formular erzeugen) =====
if($aktion == "show_mapform") {
  if($use_session) {
    if($cert!=session_id()) {
      echo "SESSERR SHOW";
      exit();
    }
  }

  $zoom_levels = "";
  for($i=0; $i<20; $i++) {
    $zoom_levels .= "<option value='$i'>$i</option>";
  }

  $maptypes = "";
  foreach ($maptypes_array as $maptype) {
    $maptypes .= '<option value="'.$maptype.'">'.$maptype.'</option>';
  }

  $zoomcontrols = "";
  foreach ($zoomcontrols_array as $key => $zoomcontrol) {
    $zoomcontrols .= '<option value="'.$key.'">'.$zoomcontrol.'</option>';
  }

  $mapcontrols = "";
  foreach ($mapcontrols_array as $key => $mapcontrol) {
    $mapcontrols .= '<option value="'.$key.'">'.$mapcontrol.'</option>';
  }

  $code = "<form action='' method='post' name='mapform' id='mapform'>".
          "<h3 style='margin-bottom:5px;'>".__( "Specify map data", "nav2me" )."</h3>".
          "<table style='margin-bottom:10px;'>".
          "<tr><td><strong>".__( "Address", "nav2me" ).":</strong></td><td><input type='text' value='' name='loc[0]' style='width:350px;' /></td></tr>".
          "<tr><td><strong>".__( "Lat / Long", "nav2me" ).":</strong></td><td><input type='text' value='' name='loc[1]' style='width:170px;' /> / <input type='text' value='' name='loc[2]' style='width:170px;' /> (".__( "optional", "nav2me" ).")</td></tr>".
          //"<tr><td><strong>".__( "Icon", "nav2me" ).":</strong></td><td><input type='hidden' value='' name='loc[3]' style='width:350px;' /></td></tr>".
          "<tr><td></td><td><input type='hidden' value='' name='loc[3]' style='width:350px;' /></td></tr>".

          "<tr><td colpsn='2' style='font-size:1px; height:5px;'>&nbsp;</td></tr>".

          "<tr style='padding-top:30px;'><td colspan='2'>".
            "<strong>".__( "Width", "nav2me" ).":</strong> <input type='text' value='400' name='option[0]' style='width:45px;margin-right:20px;' /><strong>".__( "Height", "nav2me" ).":</strong> <input type='text' value='300' name='option[1]' style='width:45px;margin-right:20px;' />".
            "<strong>".__( "Zoom", "nav2me" ).":</strong> <select id='zoom' name='zoom' onchange='jQuery(\"#zoom-level\").val(this.value)' style='margin-right:20px;'>$zoom_levels</select> <input type='hidden' id='zoom-level' value='0' name='option[2]' />".
            "<strong>".__( "Map type", "nav2me" ).":</strong> <select id='typ' name='typ' onchange='jQuery(\"#maptyp\").val(this.value)' style='margin-right:20px;'>$maptypes</select> <input type='hidden' id='maptyp' value='ROADMAP' name='option[3]' /><br />".
          "</td></tr>".
          "<tr><td colspan='2'>".
            "<strong>".__( "Display", "nav2me" )."</strong><br />".
            "<strong>".__( "ZoomControl", "nav2me" ).":</strong><input type='checkbox' value='false' name='option[4]' style='margin-left: 5px;' onclick='set_checkbox_value(this)' /> <select id='control' name='control' onchange='jQuery(\"#zoomcontrol\").val(this.value)' style='margin-right:20px;'>$zoomcontrols</select> <input type='hidden' id='zoomcontrol' value='SMALL' name='option[5]' />".
            "<strong>".__( "MapControl", "nav2me" ).":</strong><input type='checkbox' value='false' name='option[6]' style='margin-left: 5px;' onclick='set_checkbox_value(this)' /> <select id='control' name='control' onchange='jQuery(\"#mapcontrol\").val(this.value)' style='margin-right:20px;'>$mapcontrols</select> <input type='hidden' id='mapcontrol' value='HORIZONTAL_BAR' name='option[7]' />".
            "<strong>".__( "PanControl", "nav2me" ).":</strong><input type='checkbox' value='false' name='option[8]' style='margin-left: 8px;' onclick='set_checkbox_value(this)' />".
          "</td></tr>".
          "<tr style='padding-bottom:30px;'><td colspan='2'>".
            "<strong>".__( "ScaleControl", "nav2me" ).":</strong><input type='checkbox' value='false' name='option[9]' style='margin-left: 5px;' onclick='set_checkbox_value(this)' />".
            "<strong style='margin-left:20px;'>".__( "StreetViewControl", "nav2me" ).":</strong><input type='checkbox' value='false' name='option[10]' style='margin-left: 5px;' onclick='set_checkbox_value(this)' />".
            "<strong style='margin-left:20px;'>".__( "OverviewMapControl", "nav2me" ).":</strong><input type='checkbox' value='false' name='option[11]' style='margin-left: 5px;' onclick='set_checkbox_value(this)' />".
          "</td></tr>".

          "<tr><td colpsn='2' style='font-size:1px; height:5px;'>&nbsp;</td></tr>".
          "<tr><td style='vertical-align:top'><strong>".__( "Info", "nav2me" ).":</strong></td><td><textarea name='data[0]' style='width:250px;height:100px;'></textarea></td></tr>".
          "<tr><td style='vertical-align:top'><strong>".__( "Template", "nav2me" ).":</strong></td><td style='vertical-align:top;'><textarea name='data[1]' style='width:250px;height:100px;float:left;margin-right:5px;'>[addr][button]\r\n[map]\r\n[dir]</textarea>".__( "Use", "nav2me" )."<br />[addr]<br />[button]<br />[map]<br />[dir]</td></tr>".
          "<tr><td style='vertical-align:top'><strong>".__( "Memo", "nav2me" ).":</strong></td><td><textarea name='data[2]' style='width:250px;height:100px;'></textarea></td></tr>".
          "</table>".
          "<input type='button' value='".__( "Save", "nav2me" )."' onclick='add_map()' style='width:80px;margin-right:10px;' /><input type='button' value='".__( "Cancel", "nav2me" )."' onclick='close_fancy()' style='width:80px;' />".
          "</form>";

  echo $code;
  exit();

}


//===== MAP Speichern =====
if($aktion == "save_map") {
  if($use_session) {
    if($cert!=session_id()) {
      echo "SESSERR SAVE";
      exit();
    }
  }


  $address = $loc[0];
  $lat_long = str_replace(",",".",$loc[1]).",".str_replace(",",".",$loc[2]);
  $icon = $loc[3];
  $info = $data[0];
  $templ = $data[1];
  $memo = $data[2];

  $options = serialize($option);

  $befehl = "INSERT INTO ".$wpdb->prefix ."nav2me_maps (location_adr,location_coord,location_info,location_icon,template,memo,options) VALUES ('$address','$lat_long','$info','$icon','$templ','$memo','$options')";
  $results = $wpdb->get_results($befehl);

}


//===== Map editieren (Formular erzeugen) =====
if($aktion == "edit_map") {
  if($use_session) {
    if($cert!=session_id()) {
      echo "SESSERR SHOWEDIT";
      exit();
    }
  }

  $befehl = "SELECT * FROM ".$wpdb->prefix ."nav2me_maps WHERE ID=$map_id";
  $results = $wpdb->get_results($befehl);


  foreach ($results as $result) {
    $address = $result->location_adr;
    $coords = explode(",",$result->location_coord);
    $info = $result->location_info;
    $icon = $result->location_icon;
    $template = $result->template;
    $memo = $result->memo;
    $options = unserialize($result->options);
  }

  $zoom_levels = "";
  for($i=0; $i<20; $i++) {
    if($options[2]==$i)
      $zoom_levels .= "<option value='$i' selected>$i</option>";
    else
      $zoom_levels .= "<option value='$i'>$i</option>";
  }

  if($options[5]=="true")
    $checkparam = "checked ";
  else
    $checkparam = "";

  $maptypes = "";
  foreach ($maptypes_array as $maptype) {
    if($maptype==$options[3])
      $maptypes .= '<option value="'.$maptype.'" selected>'.$maptype.'</option>';
    else
      $maptypes .= '<option value="'.$maptype.'">'.$maptype.'</option>';
  }


  $zoomcontrols = "";
  foreach ($zoomcontrols_array as $key => $zoomcontrol) {
    if($key==$options[5])
      $zoomcontrols .= '<option value="'.$key.'" selected>'.$zoomcontrol.'</option>';
    else
      $zoomcontrols .= '<option value="'.$key.'">'.$zoomcontrol.'</option>';
  }
  if($options[4]=="true")
    $zoomcontrols_chk = "checked ";
  else
    $zoomcontrols_chk = "";


  $mapcontrols = "";
  foreach ($mapcontrols_array as $key => $mapcontrol) {
    if($key==$options[7])
      $mapcontrols .= '<option value="'.$key.'" selected>'.$mapcontrol.'</option>';
    else
      $mapcontrols .= '<option value="'.$key.'">'.$mapcontrol.'</option>';
  }
  if($options[6]=="true")
    $mapcontrols_chk = "checked ";
  else
    $mapcontrols_chk = "";


  if($options[8]=="true")
    $pancontrol_chk = "checked ";
  else
    $pancontrol_chk = "";

  if($options[9]=="true")
    $scalecontrol_chk = "checked ";
  else
    $scalecontrol_chk = "";

  if($options[10]=="true")
    $sviewcontrol_chk = "checked ";
  else
    $sviewcontrol_chk = "";

  if($options[11]=="true")
    $overviewcontrol_chk = "checked ";
  else
     $overviewcontrol_chk = "";

  $code = "<form action='' method='post' name='mapform' id='mapform'>".
          "<h3 style='margin-bottom:5px;'>".__( "Edit map data", "nav2me" )."</h3>".
          "<table style='margin-bottom:10px;'>".
          "<tr><td><strong>".__( "Address", "nav2me" ).":</strong></td><td><input type='text' value='$address' name='loc[0]' style='width:350px;' /></td></tr>".
          "<tr><td><strong>".__( "Lat / Long", "nav2me" ).":</strong></td><td><input type='text' value='$coords[0]' name='loc[1]' style='width:170px;' /> / <input type='text' value='$coords[1]' name='loc[2]' style='width:170px;' /> (".__( "optional", "nav2me" ).")</td></tr>".
          //"<tr><td><strong>".__( "Icon", "nav2me" ).":</strong></td><td><input type='text' value='$icon' name='loc[3]' style='width:350px;' /></td></tr>".
          "<tr><td></td><td><input type='hidden' value='$icon' name='loc[3]' style='width:350px;' /></td></tr>".

          "<tr><td colpsn='2' style='font-size:1px; height:5px;'>&nbsp;</td></tr>".

          "<tr style='padding-top:30px;'><td colspan='2'>".
            "<strong>".__( "Width", "nav2me" ).":</strong> <input type='text' value='$options[0]' name='option[0]' style='width:45px;margin-right:20px;' /><strong>".__( "Height", "nav2me" ).":</strong> <input type='text' value='$options[1]' name='option[1]' style='width:45px;margin-right:20px;' />".
            "<strong>".__( "Zoom", "nav2me" ).":</strong> <select id='zoom' name='zoom' onchange='jQuery(\"#zoom-level\").val(this.value)' style='margin-right:20px;'>$zoom_levels</select> <input type='hidden' id='zoom-level' value='$options[2]' name='option[2]' />".
            "<strong>".__( "Map type", "nav2me" ).":</strong> <select id='typ' name='typ' onchange='jQuery(\"#maptyp\").val(this.value)' style='margin-right:20px;'>$maptypes</select> <input type='hidden' id='maptyp' value='$options[3]' name='option[3]' /><br />".
          "</td></tr>".
          "<tr><td colspan='2'>".
            "<strong>".__( "Display", "nav2me" )."</strong><br />".
            "<strong>".__( "ZoomControl", "nav2me" ).":</strong><input type='checkbox' value='$options[4]' name='option[4]' style='margin-left: 5px;' onclick='set_checkbox_value(this)' $zoomcontrols_chk/> <select id='control' name='control' onchange='jQuery(\"#zoomcontrol\").val(this.value)' style='margin-right:20px;'>$zoomcontrols</select> <input type='hidden' id='zoomcontrol' value='$options[5]' name='option[5]' />".
            "<strong>".__( "MapControl", "nav2me" ).":</strong><input type='checkbox' value='$options[6]' name='option[6]' style='margin-left: 5px;' onclick='set_checkbox_value(this)' $mapcontrols_chk/> <select id='control' name='control' onchange='jQuery(\"#mapcontrol\").val(this.value)' style='margin-right:20px;'>$mapcontrols</select> <input type='hidden' id='mapcontrol' value='$options[7]' name='option[7]' />".
            "<strong>".__( "PanControl", "nav2me" ).":</strong><input type='checkbox' value='$options[8]' name='option[8]' style='margin-left: 8px;' onclick='set_checkbox_value(this)' $pancontrol_chk/>".
          "</td></tr>".
          "<tr style='padding-bottom:30px;'><td colspan='2'>".
            "<strong>".__( "ScaleControl", "nav2me" ).":</strong><input type='checkbox' value='$options[9]' name='option[9]' style='margin-left: 5px;' onclick='set_checkbox_value(this)' $scalecontrol_chk/>".
            "<strong style='margin-left:20px;'>".__( "StreetViewControl", "nav2me" ).":</strong><input type='checkbox' value='$options[10]' name='option[10]' style='margin-left: 5px;' onclick='set_checkbox_value(this)' $sviewcontrol_chk/>".
            "<strong style='margin-left:20px;'>".__( "OverviewMapControl", "nav2me" ).":</strong><input type='checkbox' value='$options[11]' name='option[11]' style='margin-left: 5px;' onclick='set_checkbox_value(this)' $overviewcontrol_chk/>".
          "</td></tr>".

          "<tr><td colpsn='2' style='font-size:1px; height:5px;'>&nbsp;</td></tr>".
          "<tr><td style='vertical-align:top'><strong>".__( "Info", "nav2me" ).":</strong></td><td><textarea name='data[0]' style='width:250px;height:100px;'>$info</textarea></td></tr>".
          "<tr><td style='vertical-align:top'><strong>".__( "Template", "nav2me" ).":</strong></td><td><textarea name='data[1]' style='width:250px;height:100px;float:left;margin-right:5px;'>$template</textarea>".__( "Use", "nav2me" )."<br />[addr]<br />[button]<br />[map]<br />[dir]</td></tr>".
          "<tr><td style='vertical-align:top'><strong>".__( "Memo", "nav2me" ).":</strong></td><td><textarea name='data[2]' style='width:250px;height:100px;'>$memo</textarea></td></tr>".
          "</table>".
          "<input type='button' value='".__( "Save", "nav2me" )."' onclick='update_map($map_id)' style='width:80px;margin-right:10px;' /><input type='button' value='".__( "Cancel", "nav2me" )."' onclick='close_fancy()' style='width:80px;' />".
          "</form>";

  echo $code;
  exit();

}


//===== Map updaten =====
if($aktion == "upd_map") {

  if($use_session) {
    if($cert!=session_id()) {
      echo "SESSERR UPD";
      exit();
    }
  }

  $address = $loc[0];
  $lat_long = str_replace(",",".",$loc[1]).",".str_replace(",",".",$loc[2]);
  $icon = $loc[3];
  $info = $data[0];
  $templ = $data[1];
  $memo = $data[2];

  $options = serialize($option);

  if($map_id!="") {
    $befehl = "UPDATE ".$wpdb->prefix ."nav2me_maps SET location_adr='$address',location_coord='$lat_long',location_info='$info',location_icon='$icon',template='$templ',memo='$memo',options='$options' WHERE ID=$map_id";
    $results = $wpdb->get_results($befehl);
  }
  else
    $code .= "UPD ID";

}




//===== Map löschen =====
if($aktion == "del_map") {

  if($use_session) {
    if($cert!=session_id()) {
      echo "SESSERR DEL";
      exit();
    }
  }

  if($map_id!="") {
    $befehl = "DELETE FROM ".$wpdb->prefix ."nav2me_maps WHERE ID=$map_id";
    $results = $wpdb->get_results($befehl);
  }

}




//===== Maps auslesen und Tabelle erzeugen =====

if($use_session) {
  if($cert!=session_id()) {
    echo "SESSERR ANZ";
    exit();
  }
}

$befehl = "SELECT * FROM ".$wpdb->prefix ."nav2me_maps ORDER BY ID";
$results = $wpdb->get_results($befehl);



$code .= '<p><h3 style="display:inline;margin-right:20px;">'. __( "Maps", "nav2me" ).'</h3> <a href="javascript:this.blur()" style="cursor:pointer;" onclick="show_mapform(-1);">'. __( "Add map", "nav2me" ).'</a></p>'.
         '<table border="1" id="mapListTable" class="tablesorter widefat">'.
         '<thead>'.
         '<tr>'.
         '<th style="width:30px;">ID</th>'.
         '<th align="left" style="">'. __( "Address", "nav2me" ).'</th>'.
         '<th align="left" style="width:150px;">'. __( "Coords", "nav2me" ).'</th>'.
         '<th align="center" style="">'. __( "Info", "nav2me" ).'</th>'.
         //'<th align="center" style="">'. __( "Icon", "nav2me" ).'</th>'.
         '<th align="center" style="">'. __( "Memo", "nav2me" ).'</th>'.
         '<th align="center" style="width:80px;">&nbsp;</th>'.
        '</tr>'.
        '</thead>'.
        '<tbody>';

foreach ($results as $result) {

    $info = nl2br($result->location_info);

    $code .= '<tr onmouseover="this.style.backgroundColor=\'#EFEFEF\';" onmouseout="this.style.backgroundColor=\'#F9F9F9\';">'.
              '<td style="text-align:right;">'.$result->ID.'</td>'.
              '<td>'.$result->location_adr.'</td>'.
              '<td>'.$result->location_coord.'</td>'.
              '<td>'.$info.'</td>'.
              //'<td>'.$result->location_icon.'</td>'.
              '<td>'.$result->memo.'</td>'.
              '<td style="text-align:center;vertical-align:middle;">'.
              '<input type="button" value="" style="width:35px; height:25px;background-image:url('.NAV2ME_URLPATH.'/images/edit.png);background-repeat:no-repeat;background-position:8px 4px;margin-right:15px;" title="'. __( "Edit map", "nav2me" ).'" onclick="show_mapform('.$result->ID.');" />'.
              '<input type="button" value="" style="width:35px; height:25px;background-image:url('.NAV2ME_URLPATH.'/images/cross.png);background-repeat:no-repeat;background-position:8px 4px;margin-right:0px;" title="'. __( "Delete map", "nav2me" ).'" onclick="delete_map('.$result->ID.');" />'.
              '</td>'.
            '</tr>';
}

  $code .= '</tbody>'.
           '</table><br />'.
           __( "Insert map in posts or pages with <b>[nav2me id=<em>ID</em>]</b>", "nav2me" );





echo $code;



?>