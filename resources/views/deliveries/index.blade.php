@extends('app')
	@section('content')
		   <!-- Map -->
    <section id="rutas" class="rutas">
     
      <h1>Gr&aacute;ficas</h1>

        <script>


        var radius = 960 / 2;

        var cluster = d3.layout.cluster().size([360, radius - 120]);

        var diagonal = d3.svg.diagonal.radial().projection(function(d) { return [d.y, d.x / 180 * Math.PI]; });

        var svg = d3.select("body").append("svg").attr("width", radius * 2).attr("height", radius * 2).append("g")
            .attr("transform", "translate(" + radius + "," + radius + ")");

        d3.json("js/flare.json", function(error, root) {
          var nodes = cluster.nodes(root);

          var link = svg.selectAll("path.link")
              .data(cluster.links(nodes))
            .enter().append("path")
              .attr("class", "link")
              .attr("d", diagonal);

          var node = svg.selectAll("g.node")
              .data(nodes)
            .enter().append("g")
              .attr("class", "node")
              .attr("transform", function(d) { return "rotate(" + (d.x - 90) + ")translate(" + d.y + ")"; })

          node.append("circle")
              .attr("r", 4.5);

          node.append("text")
              .attr("dy", ".31em")
              .attr("text-anchor", function(d) { return d.x < 180 ? "start" : "end"; })
              .attr("transform", function(d) { return d.x < 180 ? "translate(8)" : "rotate(180)translate(-8)"; })
              .text(function(d) { return d.name; });
        });

        d3.select(self.frameElement).style("height", radius * 2 + "px");
        </script>
      <div id="charts_container" class="charts_container">

          <div id="charts_left_arrow_container" class="charts_left_arrow_container"> </div>

          <div id="chart_container" class="chart_container"> </div>

          <div id="charts_right_arrow_container" class="charts_right_arrow_container"> </div>

      </div>   
    <!--
     <div id="map-canvas"></div>-->
    </section>


    <!-- stats -->
    <!-- The circle icons use Font Awesome's stacked icon classes. For more information, visit http://fontawesome.io/examples/ -->
    <section id="stats" class="container">

          <div id="st" class="container">
            <h2>Unidades promedio entregadas por ruta</h2>
            <h3>{{ $average_delivery_units }} unidades </h3>
          </div>
          <div id="st2" class="container" >
           <h2>Promedio de peso por ruta</h2>
           <h3>{{ $average_weight_per_route }} kilogramos</h3>
          </div>
          <div class="clear"></div>
          <div id="st3" class="container" >
            <h2>Entregas fuera de ruta</h2>
            <h3>{{ $deliveries_out_of_route }}</h3>
          </div>	
          <div id="st4" class="container" >
           <h2>Capacidad promedio de los camiones</h2>
           <h3>{{ $trucks_average_capacity }}</h3>
          </div>  
          <div class="clear"></div>
          <div id="st5" class="container" >
           <h2>Stats</h2>
          </div>	
          <div id="st6" class="container" >
           <h2>Stats</h2>
          </div>
          <div class="clear"></div>
    </section>


   

    <!-- Portfolio -->
    <section id="scenarios" class="scenarios">
        <div class="container">
           <h1>Scenarios</h1>
        </div>
        <!-- /.container -->
    </section>

    <section id="suggestions" class="suggestions">
        <div class="container">
           <h2>Suggestions</h2>
        </div>
        <!-- /.container -->
    </section>

     <!-- Portfolio -->
    <section id="newbusiness" class="newbusiness">
      <!--
        <div id="map-canvas-nb"></div>-->
    <div id="map-legend"> 
      <div id="map_options_header"> Opciones del mapa </div>
       <div id="search_map_button" class="search_map_button"> 
        <div id="search_map_text" class="search_map_text"> Buscar</div>
      </div>
     <div id="clear_map_button" class="clear_map_button"> 
        <div id="clear_map_text" class="clear_map_text"> Clear</div>
      </div>
      <div class="clear"></div>
    </div>
        <!-- /.container -->
    </section>

	@stop	