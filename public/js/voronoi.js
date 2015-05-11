d3.json('js/voronoi.geojson', function(pointjson){
	main(pointjson); 
});


function main(pointjson) {
        
	//Google Map 初期化
	var map = new google.maps.Map(document.getElementById('map_canvas'), {
		zoom: 11,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		center: new google.maps.LatLng(19.4889, -99.1836),       
	});

		
	var overlay = new google.maps.OverlayView(); //OverLayオブジェクトの作成

	//オーバレイ追加
	overlay.onAdd = function () {

		var layer = d3.select(this.getPanes().overlayLayer).append("div").attr("class", "SvgOverlay");
		var svg = layer.append("svg");
		var svgoverlay = svg.append("g").attr("class", "AdminDivisions");
		
		//再描画時に呼ばれるコールバック    
		overlay.draw = function () {
			var markerOverlay = this;
			var overlayProjection = markerOverlay.getProjection();
	
			//Google Mapの投影法設定
			var googleMapProjection = function (coordinates) {
				var googleCoordinates = new google.maps.LatLng(coordinates[1], coordinates[0]);
				var pixelCoordinates = overlayProjection.fromLatLngToDivPixel(googleCoordinates);
				return [pixelCoordinates.x + 4000, pixelCoordinates.y + 4000];
			}

			//Latitud : 19.4889
			//Longitud : -99.1836
			//母点位置情報
			var pointdata = pointjson.features;
			
			//ピクセルポジション情報
			var positions = [];

			pointdata.forEach(function(d) {		
				positions.push(googleMapProjection(d.geometry.coordinates)); //位置情報→ピクセル
			});
	
			//ボロノイ変換関数
			var polygons = d3.geom.voronoi(positions);
			
			var pathAttr ={
				"d":function(d, i) { return "M" + polygons[i].join("L") + "Z"},
				stroke:"red",
				fill:"none"			
			};

			//境界表示
			svgoverlay.selectAll("path")
				.data(pointdata)
				.attr(pathAttr)
				.enter()
				.append("svg:path")
				.attr("class", "cell")
				.attr(pathAttr)
				
			var circleAttr = {
				    "cx":function(d, i) { return positions[i][0]; },
				    "cy":function(d, i) { return positions[i][1]; },
				    "r":2,
				    fill:"red"			
			}
	
			//母点表示
			svgoverlay.selectAll("circle")
				.data(pointdata)
				.attr(circleAttr)
				.enter()
				.append("svg:circle")
				.attr(circleAttr)
	  
		};

	};

	//作成したSVGを地図にオーバーレイする
	overlay.setMap(map);
	
		
};

