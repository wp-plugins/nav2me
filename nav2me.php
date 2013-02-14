<?php
/*
Plugin Name: Nav2Me
Plugin URI: http://www.stegasoft.de/
Description: Simple Google Maps Routing plugin
Version: 1.0
Author: Stephan Gaertner
Author URI: http://www.stegasoft.de
Min WP Version: 3.3
*/


$nav2me_version = "1.0";


//============= INCLUDES ==========================================================
@include_once (dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR. "wp-config.php");
@include_once (dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR."wp-includes/wp-db.php");

define('NAV2ME_URLPATH', WP_CONTENT_URL.'/plugins/'.plugin_basename( dirname(__FILE__)) );

$nav2me_options = get_option( "nav2me_options" );


//===== jQuery aktivieren =====
function nav2me_init() {
  wp_enqueue_script( 'jquery' );
  load_plugin_textdomain('nav2me',false, dirname( plugin_basename( __FILE__ ) ) .'/lang/');
  if(!session_id())
    session_start();
}
add_action('init', 'nav2me_init');




//===== Maps-Tabelle erstellen =====
register_activation_hook(__FILE__, 'nav2me_install');
function nav2me_install() {
  global $wpdb;

  $install_query = "CREATE TABLE " . $wpdb->prefix ."nav2me_maps (ID bigint(20) unsigned NOT NULL auto_increment, location_adr varchar(255) NOT NULL, location_coord text NOT NULL, location_info text NOT NULL, location_icon varchar(255) NOT NULL, template text NOT NULL, options text NOT NULL, memo text NOT NULL, PRIMARY KEY (ID))";
  // nur erstellen, wenn Tabelle noch nicht existiert
  include_once (ABSPATH."/wp-admin/upgrade-functions.php");
  @maybe_create_table($wpdb->prefix . "nav2me_maps", $install_query);

}


//===== Tabellen/Optionen loeschen =====
if($nav2me_options["deinstall"] == "yes")
  register_deactivation_hook(__FILE__, 'nav2me_deinstall');
function nav2me_deinstall() {
  global $wpdb,$wpabsd_options;
  delete_option('nav2me_options');
  $wpdb->query("DROP TABLE " . $wpdb->prefix ."nav2me_maps");
  $wpdb->query("OPTIMIZE TABLE $wpdb->options");
}



//============= Code für Admin-Kopf erzeugen ============================
function nav2me_adminhead() {
  global $nav2me_options,$nav2me_version;

  wp_register_script('tablesorter', plugins_url().'/nav2me/js/jquery.tablesorter.min.js',array( 'jquery'),'1.3.4',true);
  wp_enqueue_script('tablesorter', plugins_url().'/nav2me/js/jquery.tablesorter.min.js',array( 'jquery'),'1.3.4',true);


  wp_register_script('fancy', plugins_url().'/nav2me/js/fancybox/jquery.fancybox.js',array( 'jquery'),'1.3.4',true);
  wp_enqueue_script('fancy', plugins_url().'/nav2me/js/fancybox/jquery.fancybox.js',array( 'jquery'),'1.3.4',true);
  wp_register_style('fancystyle', plugins_url().'/nav2me/js/fancybox/jquery.fancybox.css');
  wp_enqueue_style('fancystyle');


  wp_register_style('nav2mestyle', plugins_url().'/nav2me/styles.css');
  wp_enqueue_style('nav2mestyle');


  $css_code = '<style type="text/css">'."\n".
              '<!--'."\n".
              '.tablesorter .header {background-image: url("'.plugins_url().'/nav2me/images/sort_bg_kl.png");}'."\n".
              '.tablesorter .headerSortUp { background-image: url("'.plugins_url().'/nav2me/images/sort_asc_kl.png");}'."\n".
              '.tablesorter .headerSortDown {background-image: url("'.plugins_url().'/nav2me/images/sort_desc_kl.png");}'."\n".
              "=-->\n".
              "</style>\n";

  echo $css_code;

}
add_action('admin_head', 'nav2me_adminhead');


//============= Code für Template-Kopf erzeugen ============================
function nav2me_head() {
  global $wptic_plugin_dir,$wptic_options,$wpdb;

  $jscript_includes = "\n\n<!-- ***** Nav2Me ***** -->\n";
  $jscript_includes .= "<script src=\"".plugins_url()."/nav2me/js/mapfunctions.php\" type=\"text/javascript\"></script>\n";
  $jscript_includes .= "<!-- ********************* -->\n\n";

  $lang = get_bloginfo('language');
  $mapjs = 'http://maps.googleapis.com/maps/api/js?sensor=true&language='. $lang;
  wp_register_script('gmap_api', $mapjs );
  wp_print_scripts('gmap_api');


  echo $jscript_includes;
}
add_action('wp_head', 'nav2me_head');



//===== Plugin - Button einbauen =====
add_action('admin_menu', 'nav2me_page');
function nav2me_page() {
    add_submenu_page('plugins.php', __('Nav2Me'), __('Nav2Me'), 10, 'nav2me_dadmin', 'nav2me_options_page');
}


//===== Ausgabe-Funktion =====
function nav2me_get_map($id) {
  global $wpdb,$nav2me_options;

  $code = "";

  $befehl = "SELECT * FROM ".$wpdb->prefix ."nav2me_maps WHERE ID=$id";
  $results = $wpdb->get_results($befehl);

  foreach ($results as $result) {
    $address = $result->location_adr;
    $coords = $result->location_coord;
    $info = $result->location_info;
    $icon = $result->location_icon;
    $options = unserialize($result->options);
    $template = $result->template;
  }


  //====== AUSGABE GENERIEREN ==============================
  //========================================================

  $info = nl2br($info);
  $info = str_replace("\n\r","",$info);
  $info = str_replace("\n","",$info);
  $info = str_replace("\r","",$info);

  if($coords != ",") {
    $coords = explode(",",$coords);
    $n2m_islatlong = "true";
  }
  else {
    $coords = array(0,0);
    $n2m_islatlong = "false";
  }

  //==== MAP OPTIONS ====
  $options_array = array("zoom: ".$options[2],"mapTypeId: google.maps.MapTypeId.".$options[3]);

  array_push ($options_array, "center: new google.maps.LatLng($coords[0],$coords[1])");

  if($options[4]=="true") {
    array_push ($options_array, "zoomControl: true","zoomControlOptions: {style: google.maps.ZoomControlStyle.".$options[5]."}");
  }
  else
    array_push ($options_array, "zoomControl: false");

  if($options[6]=="true") {
    array_push ($options_array,"mapTypeControl: true","mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.".$options[7]."}");
  }
  else
    array_push ($options_array,"mapTypeControl: false");

  array_push ($options_array,"panControl: $options[8]");
  array_push ($options_array,"scaleControl: $options[9]");
  array_push ($options_array,"streetViewControl: $options[10]");
  array_push ($options_array,"overviewMapControl: $options[11]");

  $option_code = "n2m_mapOptions[$id] = {". implode(",",$options_array). "};";

  if(is_numeric($options[0]))
    $width = "$options[0]px";
  else
    $width = "$options[0]";

  if(is_numeric($options[1]))
    $height = "$options[1]px";
  else
    $height = preg_replace('![^0-9]!', '', $options[1])."px"; //entfernt alle Zeichen außer Zahlen, da ggf. Prozentangaben nicht funktionieren, daher Umwandlung % => px

  $button_params = get_code_params ($template,"[button]");
  $button_value = $button_params['value'];
  if(trim($button_value)=="")
    $button_value = "OK";


  $dir_params = get_code_params ($template,"[dir]");
  $dir_height = $dir_params['height'];
  if(is_numeric($dir_height))
    $dir_height = "$dir_heightpx";
  else
    $dir_height = preg_replace('![^0-9]!', '', $dir_height)."px"; //entfernt alle Zeichen außer Zahlen, da ggf. Prozentangaben nicht funktionieren, daher Umwandlung % => px
  if(trim($dir_height)=="px")
    $dir_height = "500px";

  $dir_width = $dir_params['width'];
  if(trim($dir_width)=="")
    $dir_width = $width;

  $template = nl2br($template);
  $template = str_replace("[addr]",'<input type="text" class="n2m_addr" id="n2m_start_addr_'.$id.'" name="n2m_start_addr_'.$id.'" value="" />',$template);
  $template = str_replace($button_params['code'],'<input type="button" class="n2m_button" id="n2m_button_'.$id.'" name="n2m_button_'.$id.'" value="'.$button_params['value'].'" onclick="n2m_calcRoute('.$id.')" />',$template);
  $template = str_replace("[map]",'<div class="nav2me_canvas" id="nav2me_canvas_'.$id.'" style="width:'.$width.'; height:'.$height.';"></div>',$template);
  $template = str_replace($dir_params['code'],'<div id="nav2me_dirpanel_'.$id.'" style="width:'.$width.';height:'.$dir_height.';overflow:auto; display:none;" class="nav2me_dirpanel"></div>',$template);

  $code .= "\n\n<!-- ***** Nav2Me Begin ***** -->\n".
                 $template.
                 '<script type="text/javascript">'."\n".
                 'n2maddress['.$id.']="'.$address.'";'."\n".
                 'n2m_info['.$id.']="'.$info.'";'."\n".
                 "$option_code\n".
                 'n2m_islatlong['.$id.']='.$n2m_islatlong.';'."\n".
                 'n2m_initialize('.$id.');'."\n".
                 '</script>'."\n";
        $code .= "\n<!-- ***** Nav2Me END***** -->\n\n";

  echo $code;
}



//===== Platzhalter ersetzen =====
//------------ [nav2me] ----------------------------------------------
function nav2me_get_params($atts) {
  global $wpdb,$nav2me_options;

  $code = "";

  extract(shortcode_atts(array('id'=>1), $atts));

  $befehl = "SELECT * FROM ".$wpdb->prefix ."nav2me_maps WHERE ID=$id";
  $results = $wpdb->get_results($befehl);

  foreach ($results as $result) {
    $address = $result->location_adr;
    $coords = $result->location_coord;
    $info = $result->location_info;
    $icon = $result->location_icon;
    $options = unserialize($result->options);
    $template = $result->template;
  }


  //====== AUSGABE GENERIEREN ==============================
  //========================================================
  $info = nl2br($info);
  $info = str_replace("\n\r","",$info);
  $info = str_replace("\n","",$info);
  $info = str_replace("\r","",$info);

  if($coords != ",") {
    $coords = explode(",",$coords);
    $n2m_islatlong = "true";
  }
  else {
    $coords = array(0,0);
    $n2m_islatlong = "false";
  }

  //==== MAP OPTIONS ====
  $options_array = array("zoom: ".$options[2],"mapTypeId: google.maps.MapTypeId.".$options[3]);

  array_push ($options_array, "center: new google.maps.LatLng($coords[0],$coords[1])");

  if($options[4]=="true") {
    array_push ($options_array, "zoomControl: true","zoomControlOptions: {style: google.maps.ZoomControlStyle.".$options[5]."}");
  }
  else
    array_push ($options_array, "zoomControl: false");

  if($options[6]=="true") {
    array_push ($options_array,"mapTypeControl: true","mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.".$options[7]."}");
  }
  else
    array_push ($options_array,"mapTypeControl: false");

  array_push ($options_array,"panControl: $options[8]");
  array_push ($options_array,"scaleControl: $options[9]");
  array_push ($options_array,"streetViewControl: $options[10]");
  array_push ($options_array,"overviewMapControl: $options[11]");

  $option_code = "n2m_mapOptions[$id] = {". implode(",",$options_array). "};";

  if(is_numeric($options[0]))
    $width = "$options[0]px";
  else
    $width = "$options[0]";

  if(is_numeric($options[1]))
    $height = "$options[1]px";
  else
    $height = preg_replace('![^0-9]!', '', $options[1])."px"; //entfernt alle Zeichen außer Zahlen, da ggf. Prozentangaben nicht funktionieren, daher Umwandlung % => px


  $button_params = get_code_params ($template,"[button]");
  $button_value = $button_params['value'];
  if(trim($button_value)=="")
    $button_value = "OK";


  $dir_params = get_code_params ($template,"[dir]");
  $dir_height = $dir_params['height'];
  if(is_numeric($dir_height))
    $dir_height = "$dir_heightpx";
  else
    $dir_height = preg_replace('![^0-9]!', '', $dir_height)."px"; //entfernt alle Zeichen außer Zahlen, da ggf. Prozentangaben nicht funktionieren, daher Umwandlung % => px
  if(trim($dir_height)=="px")
    $dir_height = "500px";

  $dir_width = $dir_params['width'];
  if(trim($dir_width)=="")
    $dir_width = $width;


  $template = nl2br($template);
  $template = str_replace("[addr]",'<input type="text" class="n2m_addr" id="n2m_start_addr_'.$id.'" name="n2m_start_addr_'.$id.'" value="" />',$template);
  $template = str_replace($button_params['code'],'<input type="button"  class="n2m_button" id="n2m_button_'.$id.'" name="n2m_button_'.$id.'" value="'.$button_value.'" onclick="n2m_calcRoute('.$id.')" />',$template);
  $template = str_replace("[map]",'<div class="nav2me_canvas" id="nav2me_canvas_'.$id.'" style="width:'.$width.'; height:'.$height.';"></div>',$template);
  $template = str_replace($dir_params['code'],'<div class="nav2me_dirpanel" style="width:'.$dir_width.';height:'.$dir_height.';overflow:auto; display:none;" id="nav2me_dirpanel_'.$id.'"></div>',$template);

  $code .= "\n\n<!-- ***** Nav2Me Begin ***** -->\n".
                 $template.
                 '<script type="text/javascript">'."\n".
                 'n2maddress['.$id.']="'.$address.'";'."\n".
                 'n2m_info['.$id.']="'.$info.'";'."\n".
                 "$option_code\n".
                 'n2m_islatlong['.$id.']='.$n2m_islatlong.';'."\n".
                 'n2m_initialize('.$id.');'."\n".
                 '</script>'."\n";
        $code .= "\n<!-- ***** Nav2Me END***** -->\n\n";

  return $code;
}
add_shortcode('nav2me', 'nav2me_get_params');



//$content="text et text [button value="test" id="1"]; $shortcode="[button]"
function get_code_params ($content,$shortcode) {

  $param_array = array();

  $shortcode_k = str_replace ("]","",$shortcode);

  $start_pos = stripos($content,$shortcode_k);
  if($start_pos!==FALSE) {
    $end_pos = stripos($content,"]",$start_pos);

    $extracted_shortcode = substr ($content , $start_pos , $end_pos-$start_pos);

    $param_array['code'] = $extracted_shortcode."]";

    $extracted_shortcode_e = explode(" ",$extracted_shortcode);

    for($i=1; $i<count($extracted_shortcode_e);$i++) {
      if(trim($extracted_shortcode_e[$i])!="") {
        $short_code_e = explode("=",$extracted_shortcode_e[$i]);
        $param_array[$short_code_e[0]] = str_replace("\"","",$short_code_e[1]);
      }
    }

  }

  return $param_array;


}




//===== Admin-Seite erstellen =====
function nav2me_options_page() {
  global $wpdb,$nav2me_options,$nav2me_version;


  if(isset($_POST[ 'nav2me_submit_hidden' ]) && $_POST[ 'nav2me_submit_hidden' ] == "Y" ) {

    //----- Formular-Daten lesen -----
    if(isset($_POST[ 'nav2me_deinstall' ]))
      $nav2me_deinstall = $_POST[ 'nav2me_deinstall' ];
    else
      $nav2me_deinstall = "";

    //----- Daten in DB speichern -----
    $nav2me_options["deinstall"] = $nav2me_deinstall;

    update_option( "nav2me_options", $nav2me_options );
  }

  $nav2me_options = get_option( "nav2me_options" );

  $nav2me_deinstall = $nav2me_options["deinstall"];

  if($nav2me_deinstall=="yes")
    $nav2me_deinstall_check = " checked";
  else
    $nav2me_deinstall_check = "";


  //===== Admin-Formular aufbauen und anzeigen =====
  echo "<div class=\"wrap\">";

  // header
  echo "<h2>Nav2Me $nav2me_version " . __( "Administration", "nav2me" ) ."</h2>";

  // options form

  ?>

  <form name="nav2meform1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
  <input type="hidden" name="nav2me_submit_hidden" value="Y" />

  <table class="formtable">
   <tr><td colspan="3"><br /><h3><?php echo __( "Miscellaneous", "nav2me" ); ?>:</h3><br />&nbsp;</td></tr>
   <tr>
    <td style="width:140px;"><b><?php echo __( "Deinstall", "nav2me" ); ?>:</b></td>
    <td><input type="checkbox" name="nav2me_deinstall" value="yes"<?php echo $nav2me_deinstall_check; ?> />
    <?php echo __( "if checked, all plugin options will be removed from database after disabling it", "nav2me" ); ?></td>
   </tr>
  </table>

  <p class="submit">
  <input type="submit" name="Submit" value="<?php echo __( "Save", "nav2me" ); ?>" />
  </p>

  </form>

  <hr />


  <div id="mapListContent" style="margin-bottom:15px;"><div style="float:left; margin:5px 10px 5px 0;"><?php echo __( "Loading", "nav2me" ); ?></div><img src="<?php echo plugins_url()."/nav2me/images/loader.gif"; ?>" width="31" height="31" alt="Loader" /></div>

  <div style="margin-right:5px;float:left;"><?php echo __( "For updates just visite <a href='http://www.stegasoft.de/' target='_blank'>www.stegasoft.de</a>. Or follow on","nav2me"); ?></div><a href="https://twitter.com/SteGaSoft" target="_blank" style="text-decoration:none;"><img src="<?php echo plugins_url()."/nav2me/images/twitter-icon.png"; ?>" style="width:18px;height:18px;" alt="SteGaSoft Twitter" title="SteGaSoft Twitter" /></a>
  <div style="clear:left">&nbsp;</div>
  <div style="margin:7px 5px 0 0;float:left;"><?php echo __( "Do you like this plugin? So what's about a donation via","nav2me"); ?></div>
  <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="7GWD2L5DHXQF2">
<input type="image" src="http://www.stegasoft.de/img/paypal-btn.png" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
</form>

  <div style="clear:left">&nbsp;</div>



  </div> <!-- wrapper -->

  <script type="text/javascript">


  jQuery(document).ready( function() {



  });


  jQuery.post("<?php echo plugins_url() ."/nav2me/nav-functions.php"; ?>",{cert: "<?php echo session_id(); ?>"}, function(data) {
        jQuery('#mapListContent').html(data);
        jQuery(function ($) {
          $.tablesorter.addParser({
            id: 'germandate',
            is: function(s) {
              return false;
            },
            format: function(s) {
              var a = s.split('.');
              a[1] = a[1].replace(/^[0]+/g,"");
              return new Date(a.reverse().join("/")).getTime();
            },
            type: 'numeric'
          });

          $("#mapListTable").tablesorter({ headers:{0: {sorter:false},5: {sorter:false} } });
          $("#mapListTable").trigger("update");
        });
   });



   function show_mapform(id) {
     if(id==-1) {
       jQuery.post("<?php echo plugins_url() ."/nav2me/nav-functions.php"; ?>",{aktion: "show_mapform", cert: "<?php echo session_id(); ?>"}, function(data) {
         jQuery.fancybox(
                  data,
                  {
                        'autoDimensions'        : false,
                        'width'                         : 600,
                        'height'                        : 'auto',
                        'transitionIn'                : 'none',
                        'transitionOut'                : 'none',
                  }
         );
       });
     }
     else {
       jQuery.post("<?php echo plugins_url() ."/nav2me/nav-functions.php"; ?>",{aktion: "edit_map", cert: "<?php echo session_id(); ?>", id: id}, function(data) {
         jQuery.fancybox(
                  data,
                  {
                        'autoDimensions'        : false,
                        'width'                         : 600,
                        'height'                        : 'auto',
                        'transitionIn'                : 'none',
                        'transitionOut'                : 'none',
                  }
         );
       });

     }

   }


   function add_map() {

     var loc = new Array();
     var data = new Array();
     var option = new Array();

     jQuery("input[name^=loc]").each(function() {
       loc.push(jQuery(this).val());
     });
     jQuery("textarea[name^=data]").each(function() {
       data.push(jQuery(this).val());
     });
     jQuery("input[name^=option]").each(function() {
       option.push(jQuery(this).val());
     });


     jQuery.post("<?php echo plugins_url() ."/nav2me/nav-functions.php"; ?>",{aktion: "save_map", cert: "<?php echo session_id(); ?>", loc: loc, data: data, option: option}, function(data) {
        jQuery('#mapListContent').html(data);
        jQuery(function ($) {
          $.tablesorter.addParser({
            id: 'germandate',
            is: function(s) {
              return false;
            },
            format: function(s) {
              var a = s.split('.');
              a[1] = a[1].replace(/^[0]+/g,"");
              return new Date(a.reverse().join("/")).getTime();
            },
            type: 'numeric'
          });

          $("#mapListTable").tablesorter({ headers:{0: {sorter:false},5: {sorter:false} } });
          $("#mapListTable").trigger("update");
        });

     });
     close_fancy();
   }


   function update_map(id) {

     var loc = new Array();
     var data = new Array();
     var option = new Array();

     jQuery("input[name^=loc]").each(function() {
       loc.push(jQuery(this).val());
     });
     jQuery("textarea[name^=data]").each(function() {
       data.push(jQuery(this).val());
     });
     jQuery("input[name^=option]").each(function() {
       option.push(jQuery(this).val());
     });


     jQuery.post("<?php echo plugins_url() ."/nav2me/nav-functions.php"; ?>",{aktion: "upd_map", cert: "<?php echo session_id(); ?>", loc: loc, data: data, option: option, id: id}, function(data) {
        jQuery('#mapListContent').html(data);
        jQuery(function ($) {
          $.tablesorter.addParser({
            id: 'germandate',
            is: function(s) {
              return false;
            },
            format: function(s) {
              var a = s.split('.');
              a[1] = a[1].replace(/^[0]+/g,"");
              return new Date(a.reverse().join("/")).getTime();
            },
            type: 'numeric'
          });

          $("#mapListTable").tablesorter({ headers:{0: {sorter:false},5: {sorter:false} } });
          $("#mapListTable").trigger("update");
        });

     });
     close_fancy();
   }


   function delete_map(id) {

     if(confirm("<?php echo __( "Delete map ID=", "nav2me" ); ?>"+id+" ?")) {
       jQuery.post("<?php echo plugins_url() ."/nav2me/nav-functions.php"; ?>",{aktion: "del_map", cert: "<?php echo session_id(); ?>", id: id}, function(data) {
        jQuery('#mapListContent').html(data);
        jQuery(function ($) {
          $.tablesorter.addParser({
            id: 'germandate',
            is: function(s) {
              return false;
            },
            format: function(s) {
              var a = s.split('.');
              a[1] = a[1].replace(/^[0]+/g,"");
              return new Date(a.reverse().join("/")).getTime();
            },
            type: 'numeric'
          });

          $("#mapListTable").tablesorter({ headers:{0: {sorter:false},5: {sorter:false} } });
          $("#mapListTable").trigger("update");
        });

       });
     }
     close_fancy();
   }


   function close_fancy() {
     parent.jQuery.fancybox.close();
   }


   function set_checkbox_value(obj) {
     if(jQuery(obj).is(":checked")) {
       jQuery(obj).val("true");
     }
     else {
       jQuery(obj).val("false");
     }
   }

  </script>


  <?php

} //function wpabsd_options_page




?>