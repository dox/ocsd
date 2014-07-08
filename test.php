<!DOCTYPE html>
<html>
  <head>
    <title>Simple Map</title>
    
    <style>
      html, body, #map-canvas {
        height: 100%;
        margin: 0px;
        padding: 0px
      }
    </style>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB5A8xxvvZ292FvbVNP7JgUrUGUJ_6x-zI"></script>
    <script>
var map;
function initialize() {
  var mapOptions = {
    zoom: 8,
    center: new google.maps.LatLng(-34.397, 150.644)
  };
  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);
}

google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>
  <body>
    <div id="map-canvas"></div>
  </body>
</html>