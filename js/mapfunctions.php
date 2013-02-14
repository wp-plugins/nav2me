var map = new Array();
var n2maddress = new Array();
var n2m_info = new Array();
var n2m_mapOptions = new Array();
var n2m_islatlong = new Array();

var directionsDisplay = new Array();
var directionsService = new Array();

function n2m_initialize(id) {

  map[id] = new google.maps.Map(document.getElementById("nav2me_canvas_"+id), n2m_mapOptions[id]);


  var infowindow = new google.maps.InfoWindow({ content: "<p class='n2m_txtbox' id='n2m_txtbox_"+id+"'>"+n2m_info[id]+"</p>" });
  var marker;

  if(n2m_islatlong[id]==false) {
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode( { 'address': n2maddress[id] }, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                                map[id].setCenter(results[0].geometry.location);
                                marker = new google.maps.Marker({
                                        map: map[id],
                                        position: results[0].geometry.location,
                                        animation: google.maps.Animation.DROP
                                });
                                infowindow.open(map[id],marker);
                        } else {
                                try {
                                        console.log("Geocode was not successful for the following reason: " + status);
                                } catch (er) { }
                        }
      });
  }
  else {
    marker = new google.maps.Marker({
                                       position: map[id].getCenter(),
                                       map: map[id],
                                       title: 'Nav2Me'
    });
    infowindow.open(map[id],marker);
  }
  directionsDisplay[id] = new google.maps.DirectionsRenderer();
  directionsDisplay[id].setMap(map[id]);
  directionsDisplay[id].setPanel(document.getElementById("nav2me_dirpanel_"+id));


}


function n2m_calcRoute(id) {

  var lat_long = n2m_mapOptions[id].center;

  var start = jQuery('#n2m_start_addr_'+id).val();
  start = jQuery.trim(start);

  var end;

  if(start!="") {
    if(n2m_islatlong[id])
      end = lat_long;
    else
      end = n2maddress[id];

    var waypts = [];

    var request = {
      origin:start,
      destination:end,
      waypoints: waypts,
      optimizeWaypoints: true,
      travelMode: google.maps.TravelMode.DRIVING
    };

    directionsService[id] = new google.maps.DirectionsService();

    directionsService[id].route(request, function(result, status) {
      if (status == google.maps.DirectionsStatus.OK) {
        directionsDisplay[id].setDirections(result);
      }
    });

    document.getElementById("nav2me_dirpanel_"+id).style.display = 'block';

  }
  else
    alert("Specify start address");

}