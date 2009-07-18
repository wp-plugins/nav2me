
   icon_width = Math.round(icon_width * (icon_scale /100));
   icon_height = Math.round(icon_height * (icon_scale /100));

   if(ownicon!="") {
      var myIcon = new GIcon();
      myIcon.image = ownicon;
      //myIcon.shadow = "fingershadow.png";
      myIcon.iconSize = new GSize(icon_width, icon_height);   //berechnen
      //myIcon.shadowSize = new GSize(36, 34);
      myIcon.iconAnchor = new GPoint(Math.round(icon_width/2), icon_height);  //Verschiebung zu Geo Koordinate
      myIcon.infoWindowAnchor = new GPoint(Math.round(icon_width/2), 2);  //Verschiebung zu Geo Koordinate
      //myIcon.infoShadowAnchor = new GPoint(14, 25);
      //myIcon.transparent = "fingertran.png";
      //myIcon.printImage = "fingerie.gif";
      //myIcon.mozPrintImage = "fingerff.gif";

   }


    function n2m_load() {
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map"));

        if(map_type==0)
          map.setMapType(G_NORMAL_MAP);
        else if(map_type==1)
          map.setMapType(G_SATELLITE_MAP);
        else if(map_type==2)
          map.setMapType(G_HYBRID_MAP);

        gdir = new GDirections(map, document.getElementById("directions"));
        GEvent.addListener(gdir, "load", onGDirectionsLoad);
        GEvent.addListener(gdir, "error", handleErrors);

        geocoder = new GClientGeocoder();
        if(document.getElementById("map").style.width!="0px") {
          if(show_mapcontrol==true) {
            if(mapcontrol_type==0)
              map.addControl(new GSmallMapControl());
            else if(mapcontrol_type==1)
              map.addControl(new GLargeMapControl());
            else if(mapcontrol_type==2)
              map.addControl(new GSmallZoomControl());
            else if(mapcontrol_type==3)
              map.addControl(new GScaleControl());
          }
          if(show_typecontrol==true)
            map.addControl(new GMapTypeControl());
        }
        showAddress(start_adr, info_box_txt,show_info_box);
      }
    }

    function setDirections(fromAddress, toAddress, locale) {
      gdir.load("from: " + fromAddress + " to: " + toAddress,
                { "locale": "de" });
    }

    function showAddress(address, popUpHtml, infobox) {
      if (geocoder) {
        if((long!="") && (lat!="")) {
          map.setCenter(new GLatLng(lat, long), zoomlevel);
          if(ownicon!="") {
            markerOptions = { icon:myIcon };
            var marker = new GMarker(new GLatLng(lat, long),markerOption);
          }
          else {
            var marker = new GMarker(new GLatLng(lat, long));
          }

          map.addOverlay(marker);
          if(infobox)
            marker.openInfoWindowHtml(popUpHtml);
        }
        else {
          geocoder.getLatLng(
            address,
            function(point) {
              if (!point) {
                alert(address + " nicht gefunden");
              }
              else {
                map.setCenter(point, zoomlevel);
                if(ownicon!="") {
                  markerOptions = { icon:myIcon };
                  var marker = new GMarker(point,markerOptions);
                }
                else
                  var marker = new GMarker(point);
                map.addOverlay(marker);
                if(infobox)
                  marker.openInfoWindowHtml(popUpHtml);
              }
            }
          );
        }
      }
    }

    function handleErrors(){
           if (gdir.getStatus().code == G_GEO_UNKNOWN_ADDRESS)
             alert("Start- oder auch Zieladresse konnten nicht gefunden werden. Entweder sind sie nicht bekannt, nicht eindeutig oder die Eingabe ist nicht korrekt. Bitte ueberpruefen Sie die Eingabe.\nError code: " + gdir.getStatus().code);
           else if (gdir.getStatus().code == G_GEO_SERVER_ERROR)
             alert("Die Route konnte nicht berechnet werden.\n Error code: " + gdir.getStatus().code);

           else if (gdir.getStatus().code == G_GEO_MISSING_QUERY)
             alert("Bitte geben Sie eine Startadresse ein.\n Error code: " + gdir.getStatus().code);

        //   else if (gdir.getStatus().code == G_UNAVAILABLE_ADDRESS)  <--- Doc bug... this is either not defined, or Doc is wrong
        //     alert("The geocode for the given address or the route for the given directions query cannot be returned due to legal or contractual reasons.\n Error code: " + gdir.getStatus().code);

           else if (gdir.getStatus().code == G_GEO_BAD_KEY)
             alert("Falscher Google Maps Key. \n Error code: " + gdir.getStatus().code);

           else if (gdir.getStatus().code == G_GEO_BAD_REQUEST)
             alert("Die Anfrage konnte nicht geparsed werden.\n Error code: " + gdir.getStatus().code);

           else alert("Unbekannter Fehler. Bitte ueberpruefen Sie die Eingabe.");

        }

        function onGDirectionsLoad(){
          // Use this function to access information about the latest load()
          // results.

          // e.g.
          // document.getElementById("getStatus").innerHTML = gdir.getStatus().code;
          // and yada yada yada...
        }