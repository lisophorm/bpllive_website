$(function() {	    
	    
var map;
var bpllive = new google.maps.LatLng(-26.155556, 28.030833);

var MY_MAPTYPE_ID = 'custom_style';

function initialize() {

  var featureOpts = [
    { "elementType": "labels.text", "stylers": [ { "invert_lightness": true }, { "weight": 0.1 }, { "visibility": "on" }, { "color": "#00b2ff" } ] },{ "elementType": "geometry", "stylers": [ { "invert_lightness": true }, { "hue": "#0099ff" }, { "weight": 1.8 }, { "saturation": -3 } ] },{ "elementType": "labels.icon", "stylers": [ { "visibility": "off" } ] }
  ];

  var mapOptions = {
    zoom: 15,
    center: bpllive,
    mapTypeControlOptions: {
      mapTypeIds: [google.maps.MapTypeId.ROADMAP, MY_MAPTYPE_ID]
    },
    disableDefaultUI: true,
    mapTypeId: MY_MAPTYPE_ID
  };

  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);

  var styledMapOptions = {
    name: 'Custom Style'
  };

  var customMapType = new google.maps.StyledMapType(featureOpts, styledMapOptions);

  map.mapTypes.set(MY_MAPTYPE_ID, customMapType);
  
  var image = '/assets/img/flag.png';
  var marker = new google.maps.Marker({
      position: bpllive,
      map: map,
      icon: image
  });

}

google.maps.event.addDomListener(window, 'load', initialize);

});
