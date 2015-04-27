
var areaPolygon;
var poly;
var map;
var markerPins = [];
var polygonPins = [];
var isInArea;

function initialize() {
  var mapOptions = {
    zoom: 6,
    // Center the map on Mexico City
    //19.432608, -99.133209
    center: new google.maps.LatLng(19.432608, -99.133209)
  };

  map = new google.maps.Map(document.getElementById('map-canvas-nb'), mapOptions);

  var polyOptions = {
    strokeColor: '#F75C54',
    strokeOpacity: 1.0,
    strokeWeight: 3
   };
  poly = new google.maps.Polyline(polyOptions);
  poly.setMap(map);

  // Add a listener for the click event
  google.maps.event.addListener(map, 'click', addLatLng);
  getMarkers();

  map.controls[google.maps.ControlPosition.RIGHT_CENTER].push(
  document.getElementById('map-legend'));
  
}

function addLatLng(event) {
	 
	$('#clear_map_button').click(function(){

		  hideMarkers();
		  areaPolygon.setMap(null);
		  marker.setMap(null);
		  polygonPins.pop();
		  poly.setPath(polygonPins);
		  poly.setMap(null);

		});
	  
	  $('#search_map_button').click(function(){
		  
		  hideMarkers();
		  //if the size is bigger than 3, then we have a polygon
		  if(polygonPins.length >= 3){
			  
			  drawAreaMarks();
		  }
		  else
		  {
			alert("Se debe dibujar un poligono en el mapa para hacer la búsqueda");  
		  }

	  });
	
	
	
  hideMarkers();
  var path = poly.getPath();

  path.push(event.latLng);
  
  // Add a new marker at the new plotted point on the polyline.
  var marker = new google.maps.Marker({
    position: event.latLng,
    map: map
  });
  
  polygonPins.push(marker.position);
  
  drawPolygon(polygonPins);
  areaPolygon.setMap(null);

}

function drawAreaMarks(){
	
	$.ajax(
			{
				type: 'GET',
				url: 'js/coords.json',
				//url: 'http://hana-mexbalia:8001/mexbalia/Geo_Agg/UI/Stores_Coords.xsjs?polygon=POLYGON((-110.7421875+22.10599879975055%2C-98.876953125+31.203404950917395%2C-82.7490234375+16.80454107638345%2C-97.55859375+11.178401873711785%2C-110.7421875+22.10599879975055))&categories=(1%2C2%2C3%2C4%2C5)',
				success: function(data){
					
						for(d in data)
						{
							var markerLatLng = new google.maps.LatLng(data[d].latitude, data[d].longitude);
							
							//invoke google maps function to show if a coordinate is inside the polygon
							isInArea = google.maps.geometry.poly.containsLocation(markerLatLng, areaPolygon);
							
							if(isInArea)
							{
						
							var marker = new google.maps.Marker({
								position: markerLatLng,
								map: map,
								icon: 'images/green-dot.png'
							});
							markerPins.push(marker);
							}
						}
					 		
				}
				
				
			});
	
}

function getMarkers(){
	
	$.ajax(
			{
				type: 'GET',
				url: 'js/coords.json',
				//url: 'http://hana-mexbalia:8001/mexbalia/Geo_Agg/UI/Stores_Coords.xsjs?polygon=POLYGON((-110.7421875+22.10599879975055%2C-98.876953125+31.203404950917395%2C-82.7490234375+16.80454107638345%2C-97.55859375+11.178401873711785%2C-110.7421875+22.10599879975055))&categories=(1%2C2%2C3%2C4%2C5)',
				success: function(data){
					
					for(d in data){
						var markerLatLng = new google.maps.LatLng(data[d].latitude, (data[d].longitude));
						
					    var marker = new google.maps.Marker({
					      position: markerLatLng,
					      map: map,
					      icon: 'images/green-dot.png'
					    });
					    markerPins.push(marker);
					 }
						
				}
				
				
			});
	
}

//hide markers when user starts drawing the polygon
function hideMarkers(){
	
	for(i in markerPins){
		markerPins[i].setMap(null);
	}
}

function drawPolygon (path) {
  
  areaPolygon = new google.maps.Polygon({
    paths: path,
    strokeColor: '#FF0000',
    strokeOpacity: 0.4,
    strokeWeight: 2,
    fillColor: '#FF0000',
    fillOpacity: 0.1
  });

  areaPolygon.setMap(map);

}

google.maps.event.addDomListener(window, 'load', initialize);