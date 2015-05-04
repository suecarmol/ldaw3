@extends('app')
	@section('content')
		   <!-- Map -->
    <section id="rutas" class="rutas">
     
      <h1>Gr&aacute;ficas</h1>

      <script>
             var fill = d3.scale.category20();
             var cont = 0;
             var clientes = [];
             var units = [];
             var aux = false;
            @for ($i = 0; $i < sizeof($nombres); $i++)
                  clientes[{{$i}}] = "{{$nombres[$i]}}";
            @endfor
            @for ($i = 0; $i < sizeof($unidades); $i++)
                  units[{{$i}}] = "{{$unidades[$i]}}";
            @endfor

              d3.layout.cloud().size([1250, 720])
                  .words(clientes.map(function(d) {
                    //console.log(cont);
                    if(!aux){
                      aux = true;
                      cont += 1;
                      return {text: d, size: units[cont]};
                    }
                    else{
                      cont += 1;
                      return {text: d, size: units[cont]};
                    }  
                
                  }))
                  .padding(5)
                  .rotate(function() { return ~~(Math.random() * 2) * 90; })
                  .font("Impact")
                  .fontSize(function(d) { return d.size; })
                  .on("end", draw)
                  .start();
              function draw(words) {
                d3.select("#rutas").append("svg")
                    .attr("width", 1250)
                    .attr("height", 720)
                  .append("g")
                    .attr("transform", "translate(565,275)")
                  .selectAll("text")
                    .data(words)
                  .enter().append("text")
                    .style("font-size", function(d) { return d.size + "px"; })
                    .style("font-family", "Impact")
                    .style("fill", function(d, i) { return fill(i); })
                    .attr("text-anchor", "middle")
                    .attr("transform", function(d) {
                      return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
                    })
                    .text(function(d) { return d.text; });
              }
      </script>


<!--
        <script>


        var radius = 2200 / 2;

        var cluster = d3.layout.cluster().size([360, radius - 120]);

        var diagonal = d3.svg.diagonal.radial().projection(function(d) { return [d.y, d.x / 180 * Math.PI]; });

        var svg = d3.select("#rutas").append("svg").attr("width", radius * 2).attr("height", radius * 2).append("g")
            .attr("transform", "translate(" + radius + "," + radius + ")");

        d3.json("js/camiones.json", function(error, root) {
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
        </script>-->
        <!--
      <div id="charts_container" class="charts_container">

          <div id="charts_left_arrow_container" class="charts_left_arrow_container"> </div>

          <div id="chart_container" class="chart_container"> </div>

          <div id="charts_right_arrow_container" class="charts_right_arrow_container"> </div>

      </div>   -->
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


   

    <!-- Scenarios -->
    <section id="scenarios" class="scenarios">
        <div class="container">
           <h1>Escenarios</h1>
        </div>
        <!-- /.container -->
    </section>

    <section id="suggestions" class="suggestions">
        <div class="container">
           <h1>Sugerencias</h1>
           <div id="suggestions1" class="suggestions_box_1" style="width: 50%; height: 100%; float: left;">
            @if(sizeof($suggestions) > 0)
              <table style="border: 1px solid black; width: 100%;">
                <thead>
                  <tr style="border: 1px solid black;">
                    <th style="border: 1px solid black;">Cliente</th>
                    <th style="border: 1px solid black;" colspan="5">Ruta 1</th>
                    <th style="border: 1px solid black;" colspan="5">Ruta 2</th>
                    <th style="border: 1px solid black;" colspan="12">Fecha 1</th>
                    <th style="border: 1px solid black;" colspan="12">Fecha 2</th>
                    <th style="border: 1px solid black;" >Cami&oacute;n a Compartir</th>
                  </tr>
                </thead>
                <tbody>
                 @for($i = 0; $i < sizeof($suggestions); $i++)
                    <tr style="border: 1px solid black;">
                      <td style="border: 1px solid black;"> {{ $suggestions[$i]['client_name'] }} </td>
                      <td style="border: 1px solid black;" colspan="5"> {{ $suggestions[$i]['route_name_1'] }} </td>
                      <td style="border: 1px solid black;" colspan="5"> {{ $suggestions[$i]['route_name_2'] }} </td>
                      <td style="border: 1px solid black;" colspan="12"> {{ $suggestions[$i]['date_1'] }} </td>
                      <td style="border: 1px solid black;" colspan="12"> {{ $suggestions[$i]['date_2'] }} </td>
                      <td style="border: 1px solid black;"> {{ $suggestions[$i]['chosen_truck'] }} </td>
                    </tr>
                  @endfor
                </tbody>
              </table>
            @else
              <h3>No hay sugerencias disponibles</h3>  
            @endif  
           </div>
           <div id="suggestions2" class="suggestions_box_2" style="width: 50%; height: 100%; float: right;">

           </div>
        </div>
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