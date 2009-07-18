<?php
/*
Plugin Name: Nav2Me
Plugin URI: http://www.stegasoft.de/
Description: einfacher Routenplaner zum eigenen Standort
Version: 0.4
Author: Stephan Gaertner
Author URI: http://www.stegasoft.de
*/

$table_style = "border:solid 1px #606060;border-collapse:collapse;padding:2px;";

$nvversion = 0.4;


//============= INCLUDES ==========================================================
@include_once (dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR. "wp-config.php");
@include_once (dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR."wp-includes/wp-db.php");

$version = get_bloginfo('version');

if($version<2.6) {
  $n2m_plugin_dir = str_replace( '\\', '/', dirname( __FILE__ ) );
  if( preg_match( '#(/'.PLUGINDIR.'.*)#i', $n2m_plugin_dir, $treffer ) )
    $n2m_plugin_dir = $treffer[1];
  else
    $n2m_plugin_dir = '/'.PLUGINDIR;

  $n2m_plugin_dir = get_bloginfo('url').$n2m_plugin_dir."??";
}
else {
  define('NAV2ME_URLPATH', WP_CONTENT_URL.'/plugins/'.plugin_basename( dirname(__FILE__)) );
  $n2m_plugin_dir = NAV2ME_URLPATH;
}

$nav2me_options = get_option( "nav2me_options" );

//============= Code für Admin-Kopf erzeugen ============================
function navjs2adminhead() {
  global $n2m_plugin_dir,$nav2me_options;

  $jscript_includes = "\n";
  /*
  if(trim($nav2me_options['n2m_gmapkey'])!="") {
    $jscript_includes .= "<script src=\"http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=".$nav2me_options['n2m_gmapkey']."\" type=\"text/javascript\"></script>\n";
    $jscript_includes .= "<script src=\"$n2m_plugin_dir/functions.js\" type=\"text/javascript\"></script>\n";
  }
  */
  $jscript_includes .= "<link rel='stylesheet' href='$n2m_plugin_dir/styles.css' type='text/css' />\n\n";

  echo $jscript_includes;
}
add_action('admin_head', 'navjs2adminhead');



//============= Code für Template-Kopf erzeugen ============================
function navjs2head() {
  global $n2m_plugin_dir,$nav2me_options;

  $jscript_includes = "\n";
  $jscript_includes .= "<script src=\"http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=".$nav2me_options['n2m_gmapkey']."\" type=\"text/javascript\"></script>\n";
  $jscript_includes .= "<script language='JavaScript' src=\"$n2m_plugin_dir/vars.js\" type=\"text/javascript\"></script>\n";
  $jscript_includes .= "<script language='JavaScript' src=\"$n2m_plugin_dir/functions.js\" type=\"text/javascript\"></script>\n";
  $jscript_includes .= "<link rel='stylesheet' href='$n2m_plugin_dir/styles.css' type='text/css' />\n\n";

  echo $jscript_includes;
}
add_action('wp_head', 'navjs2head');



//============= Plugin - Button einbauen =====================================
add_action('admin_menu', 'nav2me_page');
function nav2me_page() {
    add_submenu_page('plugins.php', __('Nav2Me'), __('Nav2Me'), 10, 'nav2meadmin', 'nav2me_options_page');
}


//============= Tabellen/Optionen loeschen ===================================
if($nav2me_options["n2m_deinstall"] == "yes")
  register_deactivation_hook(__FILE__, 'nav2me_deinstall');
function nav2me_deinstall() {
  global $wpdb;
  delete_option('nav2me_options');
}


//============ Platzhalter ersetzen =========================================
function nav2me_replace_tag($content) {

  if ( stristr( $content, '[nav2me]' )) {
    $replace= nav2me_GetMapCode();
    $content= str_replace ("[nav2me]", $replace, $content);
  }// end nav2me

  return $content;
}
add_filter('the_content', 'nav2me_replace_tag');



//============= Map-Code erzeugen ===========================================
function nav2me_GetMapCode() {

  $n2m_template_form_file = dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR."wp-content/plugins/nav2me/template_form.htm";
  $n2m_template_form = implode("",file($n2m_template_form_file));

  $n2m_template_page_file = dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR."wp-content/plugins/nav2me/template_page.htm";
  $n2m_template_page = implode("",file($n2m_template_page_file));

  $code = parse_templates($n2m_template_form,$n2m_template_page);

  return $code;
}



//============= Seite für Plugin-Administration aufbauen ====================
function nav2me_options_page() {
  global $wpdb,$n2m_plugin_dir;

  if (defined('WPLANG')) {
    $lang = WPLANG;
  }
  if (empty($lang)) {
    $lang = 'de_DE';
  }

  if(!@include_once "lang/".$lang.".php")
    include_once "lang/en_EN.php";


  // Read in existing option value from database
  $nav2me_options = get_option( "nav2me_options" );
  $n2m_deinstall = $nav2me_options["n2m_deinstall"];
  $n2m_gmapkey = $nav2me_options["n2m_gmapkey"];
  $n2m_name = $nav2me_options["n2m_name"];
  $n2m_show_name = $nav2me_options["n2m_show_name"];
  $n2m_street = $nav2me_options["n2m_street"];
  $n2m_show_street = $nav2me_options["n2m_show_street"];
  $n2m_postalcode = $nav2me_options["n2m_postalcode"];
  $n2m_show_postalcode = $nav2me_options["n2m_show_postalcode"];
  $n2m_town = $nav2me_options["n2m_town"];
  $n2m_show_town = $nav2me_options["n2m_show_town"];
  $n2m_country = $nav2me_options["n2m_country"];
  $n2m_show_country = $nav2me_options["n2m_show_country"];

  $n2m_template_form_file = dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR."wp-content/plugins/nav2me/template_form.htm";
  $n2m_template_form = implode("",file($n2m_template_form_file));

  $n2m_template_page_file = dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR."wp-content/plugins/nav2me/template_page.htm";
  $n2m_template_page = implode("",file($n2m_template_page_file));

  $n2m_long = $nav2me_options["n2m_long"];
  $n2m_lat = $nav2me_options["n2m_lat"];
  $n2m_ownicon = $nav2me_options["n2m_ownicon"];
  $n2m_iconname = $nav2me_options["n2m_iconname"];
  $n2m_scaleicon = $nav2me_options["n2m_scaleicon"];
  $n2m_zoomlevel = $nav2me_options["n2m_zoomlevel"];
  $n2m_show_typecontrol = $nav2me_options["n2m_show_typecontrol"];
  $n2m_show_mapcontrol = $nav2me_options["n2m_show_mapcontrol"];
  $n2m_mapcontrol_type = $nav2me_options["n2m_mapcontrol_type"];
  $n2m_maptype = $nav2me_options["n2m_maptype"];

  // See if the user has posted us some information
  // If they did, this hidden field will be set to 'Y'
  if( $_POST[ 'nav2me_submit_hidden' ] == "Y" ) {

    // Read their posted value
    $n2m_deinstall = $_POST[ 'n2m_deinstall' ];
    $n2m_gmapkey = $_POST[ 'n2m_gmapkey' ];
    $n2m_name = $_POST[ 'n2m_name' ];
    $n2m_show_name = $_POST[ 'n2m_show_name' ];
    $n2m_street = $_POST[ 'n2m_street' ];
    $n2m_show_street = $_POST[ 'n2m_show_street' ];
    $n2m_postalcode = $_POST[ 'n2m_postalcode' ];
    $n2m_show_postalcode = $_POST[ 'n2m_show_postalcode' ];
    $n2m_town = $_POST[ 'n2m_town' ];
    $n2m_show_town = $_POST[ 'n2m_show_town' ];
    $n2m_country = $_POST[ 'n2m_country' ];
    $n2m_show_country = $_POST[ 'n2m_show_country' ];
    $n2m_template_form = $_POST['n2m_template_form'];
    $n2m_template_page = $_POST['n2m_template_page'];
    $n2m_long = grad2dec($_POST[ 'n2m_long' ]);
    $n2m_lat = grad2dec($_POST[ 'n2m_lat' ]);
    $n2m_ownicon = grad2dec($_POST[ 'n2m_ownicon' ]);
    $n2m_iconname = $_POST[ 'n2m_iconname' ];
    $n2m_scaleicon = trim($_POST[ 'n2m_scaleicon' ]);
    if(($n2m_scaleicon=="") || (!is_numeric($n2m_scaleicon)))
      $n2m_scaleicon = "100";

    $n2m_zoomlevel = $_POST["n2m_zoomlevel"];
    $n2m_show_typecontrol = $_POST["n2m_show_typecontrol"];
    $n2m_show_mapcontrol = $_POST["n2m_show_mapcontrol"];
    $n2m_mapcontrol_type = $_POST["n2m_mapcontrol_type"];
    $n2m_maptype = $_POST["n2m_maptype"];

    // Save the posted value in the database
    $nav2me_options["n2m_deinstall"] = $n2m_deinstall;
    $nav2me_options["n2m_gmapkey"] = $n2m_gmapkey;
    $nav2me_options["n2m_name"] = $n2m_name;
    $nav2me_options["n2m_show_name"] = $n2m_show_name;
    $nav2me_options["n2m_street"] = $n2m_street;
    $nav2me_options["n2m_show_street"] = $n2m_show_street;
    $nav2me_options["n2m_postalcode"] = $n2m_postalcode;
    $nav2me_options["n2m_show_postalcode"] = $n2m_show_postalcode;
    $nav2me_options["n2m_town"] = $n2m_town;
    $nav2me_options["n2m_show_town"] = $n2m_show_town;
    $nav2me_options["n2m_country"] = $n2m_country;
    $nav2me_options["n2m_show_country"] = $n2m_show_country;
    $nav2me_options["n2m_long"] = $n2m_long;
    $nav2me_options["n2m_lat"] = $n2m_lat;
    $nav2me_options["n2m_ownicon"] = $n2m_ownicon;
    $nav2me_options["n2m_iconname"] = $n2m_iconname;
    $nav2me_options["n2m_scaleicon"] = $n2m_scaleicon;
    $nav2me_options["n2m_zoomlevel"] = $n2m_zoomlevel;
    $nav2me_options["n2m_show_typecontrol"] = $n2m_show_typecontrol;
    $nav2me_options["n2m_show_mapcontrol"] = $n2m_show_mapcontrol;
    $nav2me_options["n2m_mapcontrol_type"] = $n2m_mapcontrol_type;
    $nav2me_options["n2m_maptype"] = $n2m_maptype;

    update_option( "nav2me_options", $nav2me_options );

    //------------ Daten in vars.js schreiben -------------------------------
    $datei = dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR."wp-content/plugins/nav2me/vars.js";
    $inhalt = "var map;\n".
              "var gdir;\n".
              "var geocoder = null;\n".
              "var addressMarker;\n".
              "var zoomlevel = $n2m_zoomlevel;\n";
    if($n2m_long=="")
      $inhalt .= "var long = '';\n";
    else
      $inhalt .= "var long = $n2m_long;\n";
    if($n2m_lat=="")
      $inhalt .= "var lat = '';\n";
    else
      $inhalt .= "var lat = $n2m_lat;\n";

    if(($n2m_ownicon=="yes") && ($n2m_iconname!="")) {
      $icon_size = getimagesize($n2m_plugin_dir."/icons/".$n2m_iconname);
      $inhalt .= "var ownicon = '$n2m_plugin_dir/icons/$n2m_iconname';\n".
                 "var icon_width = ".$icon_size[0].";\n".
                 "var icon_height = ".$icon_size[1].";\n".
                 "var icon_scale = ".$n2m_scaleicon.";\n";
    }
    else
      $inhalt .= "var ownicon = '';\n";

    $inhalt .= "var start_adr = ".get_start_adr().";\n".
               "var info_box_txt = ".set_info_box().";\n";

    if(($n2m_show_name=="yes") || ($n2m_show_street=="yes") || ($n2m_show_postalcode=="yes") || ($n2m_show_town=="yes") || ($n2m_show_country=="yes"))
      $inhalt .= "var show_info_box = true;\n";
    else
      $inhalt .= "var show_info_box = false;\n";

    if($n2m_show_typecontrol=="yes")
      $inhalt .= "var show_typecontrol = true;\n";
    else
      $inhalt .= "var show_typecontrol = false;\n";
    if($n2m_show_mapcontrol=="yes")
      $inhalt .= "var show_mapcontrol = true;\n";
    else
      $inhalt .= "var show_mapcontrol = false;\n";

    $inhalt .= "var mapcontrol_type = $n2m_mapcontrol_type;\n".
               "var map_type = $n2m_maptype;\n";

    $fp = fopen($datei,"w");
    fwrite($fp,$inhalt);
    fclose($fp);

    //-------------- Templates speichern ----------------------------------------
    $template_err_msg = "";
    if(stristr($n2m_template_form, '[btn') === FALSE)
      $template_err_msg .= $tmpl_err_1_w;
    if(stristr($n2m_template_form, '[adr]') === FALSE)
      $template_err_msg .= $tmpl_err_2_w;
    if(stristr($n2m_template_page, '[form]') === FALSE)
      $template_err_msg .= $tmpl_err_3_w;
    if($template_err_msg!="")
      $template_err_msg = '<br /><br /><span style="color:#9F0000;">'.$template_err_msg.'</span>';

    $datei = dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR."wp-content/plugins/nav2me/template_form.htm";
    $fp = fopen($datei,"w");
    fwrite($fp,$n2m_template_form);
    fclose($fp);

    $datei = dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR."wp-content/plugins/nav2me/template_page.htm";
    $fp = fopen($datei,"w");
    fwrite($fp,$n2m_template_page);
    fclose($fp);

    // Put an options updated message on the screen

    ?>
    <div class="updated"><p><strong><?php echo $istgespeichert_w.$template_err_msg; ?></strong></p></div>
    <?php

  } //bei Formularversand


  if($n2m_deinstall=="yes")
    $n2m_deinstall_check = " checked";
  else
    $n2m_deinstall_check = "";

  if($n2m_show_name=="yes")
    $n2m_show_name_check = " checked";
  else
    $n2m_show_name_check = "";

  if($n2m_show_street=="yes")
    $n2m_show_street_check = " checked";
  else
    $n2m_show_street_check = "";

  if($n2m_show_postalcode=="yes")
    $n2m_show_postalcode_check = " checked";
  else
    $n2m_show_postalcode_check = "";

  if($n2m_show_town=="yes")
    $n2m_show_town_check = " checked";
  else
    $n2m_show_town_check = "";

  if($n2m_show_country=="yes")
    $n2m_show_country_check = " checked";
  else
    $n2m_show_country_check = "";

  if($n2m_show_mapcontrol=="yes")
    $n2m_show_mapcontrol_check = " checked";
  else
    $n2m_show_mapcontrol_check = "";

  if($n2m_show_typecontrol=="yes")
    $n2m_show_typecontrol_check = " checked";
  else
    $n2m_show_typecontrol_check = "";

    //---- Icon auslesen -------
    $icon_files = "";
    $verz=opendir (dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR."wp-content/plugins/nav2me/icons");
    while ($file = readdir ($verz)) {
      if(($file!="..") and ($file!=".")) {
        if((strtolower(strrchr($file,".")) == ".png") || (strtolower(strrchr($file,".")) == ".jpg") || (strtolower(strrchr($file,".")) == ".gif")) {
          if($n2m_iconname==$file)
            $icon_files .= '<option value="'.$file.'" style="background-image:url('.$n2m_plugin_dir.'/icons/'.$file.');background-repeat:no-repeat;background-position:right;" selected>'.$file.'</option>';
          else
            $icon_files .= '<option value="'.$file.'" style="background-image:url('.$n2m_plugin_dir.'/icons/'.$file.');background-repeat:no-repeat;background-position:right;">'.$file.'</option>';
        }

      }
    } //while

  if($n2m_ownicon=="yes") {
    $n2m_ownicon_check = " checked";
  }
  else
    $n2m_ownicon_check = "";

  if(($n2m_scaleicon=="") || (!is_numeric($n2m_scaleicon)))
    $n2m_scaleicon = "100";

  //------ create zoom level --------
  $zoom_levels = "";
  for($i=0; $i<20; $i++) {
    if($n2m_zoomlevel==$i)
      $zoom_levels .= '<option value="'.$i.'" selected>'.$i.'</option>';
    else
      $zoom_levels .= '<option value="'.$i.'">'.$i.'</option>';
  }

  //------ create map control types --------
  $mapcontrol_types = "";
  $types_arr = Array("SmallMapControl","LargeMapControl","SmallZoomControl");
  for($i=0; $i<3; $i++) {
    if($n2m_mapcontrol_type==$i)
      $mapcontrol_types .= '<option value="'.$i.'" selected>'.$types_arr[$i].'</option>';
    else
      $mapcontrol_types .= '<option value="'.$i.'">'.$types_arr[$i].'</option>';
  }

  //------ create map types --------
  $map_types = "";
  $types_arr = Array("G_NORMAL_MAP","G_SATELLITE_MAP","G_HYBRID_MAP");
  for($i=0; $i<3; $i++) {
    if($n2m_maptype==$i)
      $map_types .= '<option value="'.$i.'" selected>'.$types_arr[$i].'</option>';
    else
      $map_types .= '<option value="'.$i.'">'.$types_arr[$i].'</option>';
  }

  //============ Now display the options editing screen ===========================
  echo "<div class=\"wrap\">";

  // header
  echo "<h2>" . __( "Nav2Me Administration", "n2m_trans_domain" ) . "</h2>";

  // options form

  ?>

  <form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
  <input type="hidden" name="nav2me_submit_hidden" value="Y" />

  <table border="0" cellpadding="3" cellspacing="0">
   <tr><td colspan="3"><b><?php echo $allgemeines_w; ?>:</b></td></tr>
   <tr>
    <td style="width:140px;">
    <?php echo $deinstall_w; ?>:</td>
    <td colspan="2"><input type="checkbox" name="n2m_deinstall" value="yes"<?php echo $n2m_deinstall_check; ?> />
    <?php echo $deinstall_hinweis_w; ?></td>
   </tr>
   <tr>
    <td><?php echo $gmkey_w; ?>:</td>
    <td colspan="2"><input type="text" name="n2m_gmapkey" value="<?php echo $n2m_gmapkey; ?>" class="fe_txt" style="width:750px;"/></td>
   </tr>
   </table>

   <table border="0" cellpadding="3" cellspacing="0">
   <tr>
    <td colspan="2"><br /><b><?php echo $n2m_param_w; ?>:</b></td>
    <td align="left"><br /><?php echo $show_w; ?></td>
    <td align="left"><br />&nbsp; &nbsp;<?php echo $template_w; ?></td>
   </tr>
   <tr>
    <td style="width:140px;"><?php echo $name_w; ?>:</td>
    <td style="width:260px;"><input type="text" name="n2m_name" value="<?php echo $n2m_name; ?>" class="fe_txt" /></td>
    <td style="width:40px;" align="center"><input type="checkbox" name="n2m_show_name" value="yes"<?php echo $n2m_show_name_check; ?> /></td>
    <td style="width:310px;" rowspan="6" valign="top"><div id="tab_box"><div id="tab1" class="tab_aktiv" onclick="this.className='tab_aktiv';document.getElementById('tab2').className='tab';document.getElementById('n2m_template_form').className='t_box_aktiv';document.getElementById('n2m_template_page').className='t_box';document.getElementById('info_box').innerHTML='<?php echo $template_info_form_w; ?>'"><?php echo $form_w; ?></div><div id="tab2" class="tab" onclick="this.className='tab_aktiv';document.getElementById('tab1').className='tab';document.getElementById('n2m_template_form').className='t_box';document.getElementById('n2m_template_page').className='t_box_aktiv';document.getElementById('info_box').innerHTML='<?php echo $template_info_page_w; ?>'"><?php echo $page_w; ?></div><div style="clear:both; height:0px;width:0px;">&nbsp;</div><textarea name="n2m_template_form" id="n2m_template_form" class="t_box_aktiv"><?php echo $n2m_template_form; ?></textarea><textarea name="n2m_template_page" id="n2m_template_page" class="t_box"><?php echo $n2m_template_page; ?></textarea></div></td>
    <td  style="width:300px;"rowspan="6" valign="top"><div id="info_box"><?php echo $template_info_form_w; ?></div></td>
   </tr>
   <tr>
    <td><?php echo $street_w; ?>:</td>
    <td><input type="text" name="n2m_street" value="<?php echo $n2m_street; ?>" class="fe_txt" /></td>
    <td align="center"><input type="checkbox" name="n2m_show_street" value="yes"<?php echo $n2m_show_street_check; ?> /></td>
   </tr>
   <tr>
    <td><?php echo $postalcode_w; ?>:</td>
    <td><input type="text" name="n2m_postalcode" value="<?php echo $n2m_postalcode; ?>" class="fe_txt" /></td>
    <td align="center"><input type="checkbox" name="n2m_show_postalcode" value="yes"<?php echo $n2m_show_postalcode_check; ?> /></td>
   </tr>
   <tr>
    <td><?php echo $town_w; ?>:</td>
    <td><input type="text" name="n2m_town" value="<?php echo $n2m_town; ?>" class="fe_txt" /></td>
    <td align="center"><input type="checkbox" name="n2m_show_town" value="yes"<?php echo $n2m_show_town_check; ?> /></td>
   </tr>
   <tr>
    <td><?php echo $country_w; ?>:</td>
    <td><input type="text" name="n2m_country" value="<?php echo $n2m_country; ?>" class="fe_txt" /></td>
    <td align="center"><input type="checkbox" name="n2m_show_country" value="yes"<?php echo $n2m_show_country_check; ?> /></td>
   </tr>

   <tr>
    <td><?php echo $lonlat_w; ?>:</td>
    <td colspan="4">
     <input type="text" name="n2m_long" value="<?php echo $n2m_long; ?>" class="fe_txt" style="width:116px" /> /
     <input type="text" name="n2m_lat" value="<?php echo $n2m_lat; ?>" class="fe_txt" style="width:116px" />
    </td>
   </tr>
  </table>


  <table border="0" cellpadding="3" cellspacing="0">
   <tr><td colspan="3"><br /><b><?php echo $mapstyle_w; ?></b></td></tr>

   <tr>
    <td><?php echo $map_type_w; ?>:</td>
    <td align="left" colspan="2">
    <select name="n2m_maptype" size="1" class="fe_txt" style="width:140px;"><?php echo $map_types; ?></select>
    </td>
   </tr>

   <tr>
    <td><?php echo $ownicon_w; ?>:</td>
    <td align="left">
    <input type="checkbox" name="n2m_ownicon" value="yes"<?php echo $n2m_ownicon_check; ?> onclick="if(this.checked) {document.getElementById('url_box').style.visibility='visible';} else {document.getElementById('url_box').style.visibility='hidden';}" style="float:left;" />
    </td>
    <td align="left">
    <?php if($n2m_ownicon=="yes") { ?>
      <div id="url_box" style="visibility:visible;float:left;"><select name="n2m_iconname" size="1" class="fe_txt" style="width:150px;"><?php echo $icon_files; ?></select> <?php echo $scale_icon_w; ?> <input type="text" name="n2m_scaleicon" value="<?php echo $n2m_scaleicon; ?>" class="fe_txt" style="width:50px;">%</div>
    <?php } else { ?>
      <div id="url_box" style="visibility:hidden;float:left;"><select name="n2m_iconname" size="1" class="fe_txt" style="width:150px;"><?php echo $icon_files; ?></select> <?php echo $scale_icon_w; ?> <input type="text" name="n2m_scaleicon" value="<?php echo $n2m_scaleicon; ?>" class="fe_txt" style="width:50px;">%</div>
    <?php } ?>

    </td>
   </tr>
   <tr>
    <td><?php echo $zoomlevel_w; ?>:</td>
    <td align="left" colspan="2">
    <select name="n2m_zoomlevel" size="1" class="fe_txt" style="width:50px;"><?php echo $zoom_levels; ?></select>
    </td>
   </tr>

   <tr>
    <td><?php echo $show_mapcontrol_w; ?>:</td>
    <td align="left">
    <input type="checkbox" name="n2m_show_mapcontrol" value="yes"<?php echo $n2m_show_mapcontrol_check; ?> onclick="if(this.checked) {document.getElementById('maptype_box').style.visibility='visible';} else {document.getElementById('maptype_box').style.visibility='hidden';}" style="float:left;" />
    </td>
    <td>
    <?php if($n2m_show_mapcontrol=="yes") { ?>
      <div id="maptype_box" style="visibility:visible;float:left;"><select name="n2m_mapcontrol_type" size="1" class="fe_txt" style="width:150px;"><?php echo $mapcontrol_types; ?></select></div>
    <?php } else { ?>
      <div id="maptype_box" style="visibility:hidden;float:left;"><select name="n2m_mapcontrol_type" size="1" class="fe_txt" style="width:150px;"><?php echo $mapcontrol_types; ?></select></div>
    <?php } ?>
    </td>
   </tr>

   <tr>
    <td><?php echo $show_typecontrol_w; ?>:</td>
    <td align="left" colspan="2">
    <input type="checkbox" name="n2m_show_typecontrol" value="yes"<?php echo $n2m_show_typecontrol_check; ?> />
    </td>
   </tr>

  </table>

  <hr />

  <p class="submit">
  <input type="submit" name="Submit" value="<?php echo $speichern_w; ?>" />
  </p>

  </form>

  <br />
  <?php echo $fußnote_w; ?>


  </div>

  <?
}

function get_start_adr() {
  $ret = "";
  $nav2me_options = get_option( "nav2me_options" );

  $ret = "'".$nav2me_options['n2m_street'].",".$nav2me_options['n2m_postalcode']." ".$nav2me_options['n2m_town'].",".$nav2me_options['n2m_country']."'";

  return $ret ;
}


function set_info_box() {
  $ret = "";
  $nav2me_options = get_option( "nav2me_options" );

  if($nav2me_options["n2m_show_name"]=="yes")
    $ret .= $nav2me_options["n2m_name"]."<br />";
  if($nav2me_options["n2m_show_street"]=="yes")
    $ret .= $nav2me_options["n2m_street"]."<br />";
  if($nav2me_options["n2m_show_postalcode"]=="yes")
    $ret .= $nav2me_options["n2m_postalcode"]." ";
  if($nav2me_options["n2m_show_town"]=="yes")
    $ret .= $nav2me_options["n2m_town"]."<br />";
  if($nav2me_options["n2m_show_country"]=="yes")
    $ret .= $nav2me_options["n2m_country"];

  $ret = "'".$ret."'";

 return $ret;
}


function parse_templates($code_form,$code_page) {
  global $nav2me_options;
  $map_code =    '<div id="map"></div>';
  $map_code_hidden = '<div id="map" style="width:0px;height:0px;margin:0px;padding:0px;border:0px;visibility:hidden;"></div>';
  $dir_code =    '<div id="directions"></div>';
  $form_code_a = '<form action="#" name="form1" id="form1">'.
                 '<input type="hidden" size="25" id="toAddress" name="to" value="'.$nav2me_options['n2m_street'].','.$nav2me_options['n2m_postalcode'].' '.$nav2me_options['n2m_town'].'" />';
  $form_code_e = '</form>';
  $adr_code =    '<input type="text" size="25" id="fromAddress" name="from" value="" />';

  $form_code = str_replace("[adr]",$adr_code,$code_form);
  $start_pos = strpos($form_code,"btn=");
  $btn_value = substr($form_code,$start_pos+4,strlen($form_code)-$start_pos-4);
  $end_pos = strpos($btn_value,"]");
  $btn_value = substr($btn_value,0,$end_pos);

  $btn_code =    '<input name="route_btn" id="route_btn" type="button" value="'.$btn_value.'" onclick="setDirections(document.form1.from.value, document.form1.to.value, \'de\'); return false" />';

  $form_code = str_replace("[btn=".$btn_value."]",$btn_code,$form_code);

  $form_code = $form_code_a.$form_code.$form_code_e;

  $code_page = str_replace("[form]",$form_code,$code_page);

  if(stristr($code_page, '[map]') === FALSE)
    $code_page = str_replace("[dir]",$map_code_hidden.$dir_code,$code_page);
  else {
    $code_page = str_replace("[dir]",$dir_code,$code_page);
    $code_page = str_replace("[map]",$map_code,$code_page);
  }

  $code_page .= '<script type="text/javascript">'.
                'n2m_load();window.onunload = "GUnload()";'.
                '</script>';

  return $code_page;

}


function grad2dec($koord) {
  if((stristr($koord,".")===false) && (trim($koord)!="")){
    $koord = trim($koord);
    $koord_elements = explode(" ",$koord);
    $new_koord = $koord_elements[0] + $koord_elements[1]/60 + $koord_elements[2]/3600;
    return $new_koord;
  }
  else
    return $koord;

}




?>