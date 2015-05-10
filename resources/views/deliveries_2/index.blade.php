@extends('app')
  @section('content')
       <!-- Map -->
    <section id="rutas" class="rutas">

        <!--tab-->
        <div class="tab-graficas" id="navv">
          <ul class="nav nav-tabs" role="tablist" id="tabgraficas">
                <li class="active"><a href="#dendogram">Dendogram</a></li>
                <li><a href="#word_cloud">Word Cloud</a></li>
                <li><a href="#bubble_chart">Bubble Chart</a></li>
                <li><a href="#inception">Inception</a></li>
            </ul>




            <div class="tab-content">

                  <div class="container theme-showcase tab-pane fade in active" role="main" id="dendogram">
                  </div> <!-- /container-->


                  <!--container del word_cloud-->
                   <div role="main" class="container theme-showcase tab-pane fade" id="word_cloud">
                        <div class="form-group" style="margin-top: 5px;">
                          <div class="col-xs-8">
                            <select class="form-control input-sm" id="opcion_word">
                              <option value="1">Clientes con mas unidades</option>
                              <option value="2">Clientes con mas peso entregado</option>
                              <option value="3">Rutas con mas clientes</option>
                              <option value="4">Camiones con mas rutas</option>
                              <option value="5">Clientes con mayor tiempo de entrega</option>
                            </select>
                          </div>  
                        <button type="button" class="btn btn-success" id="boton_word">Graficar</button>
                      </div>

                      
                  </div><!-- /container-->


                   <!--container de las bubble_chart-->
                   <div role="main" class="container theme-showcase tab-pane fade bubbleChart" id="bubble_chart">
                      <div class="form-group" style="margin-top: 5px;">
                          <div class="col-xs-8">
                            <select class="form-control input-sm" id="opcion_bubble">
                              <option value="1">Clientes por unidades</option>
                              <option value="2">Clientes por peso entregado</option>
                              <option value="3">Camiones con mejor calificacion</option>
                              <option value="4">Camiones con mayor capacidad</option>
                            </select>
                          </div>  
                        <button type="button" class="btn btn-success" id="boton_bubble">Graficar</button>
                      </div>
                  </div><!-- /container-->

                   <!--container de las bubble_chart-->
                   <div role="main" class="container theme-showcase tab-pane fade" id="inception">                
                  </div><!-- /container-->

            </div><!--tab-content-->
        </div><!--cierre-->

        <!--bubble chart-->
                            
    </section>


    <!-- stats -->
    <!-- The circle icons use Font Awesome's stacked icon classes. For more information, visit http://fontawesome.io/examples/ -->
    <section id="stats" class="container">

         <div id="st">
            <h2>Entregas fuera de ruta</h2>
            <h3>{{ $deliveries_out_of_route }}</h3>
          </div>
          <div id="st2" >
           <h2>Promedio de peso por ruta</h2>
           <h3>{{ $average_weight_per_route }} kilogramos</h3>
          </div>
          <div class="clear"></div>
          <div id="st3" >
            <h2>Unidades promedio entregadas por ruta</h2>
            <h3>{{ $average_delivery_units }} unidades </h3>
          </div>  
          <div id="st4" >
           <h2>Calificaci&oacute;n promedio</h2>
           <h3> {{ $trucks_average_grade }} estrellas de 5 </h3>
          </div>  
          <div class="clear"></div>
          <div id="st5" >
           <h2>Tiempo promedio de servicio</h2>
           <h3>{{ $trucks_average_service_time }} horas</h3>
          </div>  
          <div id="st6" >
           <h2>Capacidad promedio de los camiones</h2>
           <h3>{{ $trucks_average_capacity }} kilogramos</h3>
          </div>
          <div class="clear"></div>
    </section>


   

    <!-- Portfolio -->
    <section id="scenarios" class="scenarios">
        <div class="container">
           <h1>Escenarios</h1>
        </div>
        <!-- /.container -->
    </section>

    <section id="suggestions" class="suggestions">
        <div class="container">
           <h2>Sugerencias</h2>
            {!! Form::open(array('class' => 'form-horizontal')) !!}
              <div class="form-group">
              <div class="col-xs-8">
              {!! Form::text('date', null, [
                'id' => 'datetimepicker4',
                'class' => 'form-control',
                'data-provide' => 'datepicker',
                'placeholder' => 'Fecha'
              ]) !!}
              </div>
              {!! Form::submit('Actualizar', array('class' => 'btn btn-success')) !!}
              </div>
            {!! Form::close() !!}
            @if(sizeof($suggestions) > 0)
              <div class="table-responsive">
                <table id="suggestions_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr >
                      <th>Cliente</th>
                      <th>Ruta 1</th>
                      <th>Ruta 2</th>
                      <th>Fecha 1</th>
                      <th>Fecha 2</th>
                      <th>Cami&oacute;n a Compartir</th>
                    </tr>
                  </thead>
                  <tbody>
                   @for($i = 0; $i < sizeof($suggestions); $i++)
                      <tr >
                        <td> {{ $suggestions[$i]['client_name'] }} </td>
                        <td> {{ $suggestions[$i]['route_name_1'] }} </td>
                        <td> {{ $suggestions[$i]['route_name_2'] }} </td>
                        <td> {{ $suggestions[$i]['date_1'] }} </td>
                        <td> {{ $suggestions[$i]['date_2'] }} </td>
                        <td> {{ $suggestions[$i]['chosen_truck'] }} </td>
                      </tr>
                    @endfor
                  </tbody>
                </table>
              </div>
            @else
              <h3>No hay sugerencias disponibles</h3>  
            @endif  
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

<!--Scripts De las gráficas Estaticas-->
    
   <!--Nuevo dendogram-->

      <script>

                var margin = {top: 5, right: 120, bottom: 5, left: 100},
                    width = 880 - margin.right - margin.left,
                    height = 620 - margin.top - margin.bottom;
                    
                var i = 0,
                    duration = 750,
                    root1;

                var tree1 = d3.layout.tree()
                    .size([height, width]);

                var diagonal = d3.svg.diagonal()
                    .projection(function(d) { return [d.y, d.x]; });

                var svg2 = d3.select("#dendogram").append("svg")
                    .attr("width", width + margin.right + margin.left)
                    .attr("height", height + margin.top + margin.bottom)
                  .append("g")
                    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

                d3.json("js/camiones_bk.json", function(error, flare) {
                  root1 = flare;
                  root1.x0 = height / 2;
                  root1.y0 = 0;

                  function collapse(d) {
                    if (d.children) {
                      d._children = d.children;
                      d._children.forEach(collapse);
                      d.children = null;
                    }
                  }

                  root1.children.forEach(collapse);
                  update(root1);
                });

                d3.select(self.frameElement).style("height", "700px");

                function update(source) {

                  // Compute the new tree1 layout.
                  var nodes = tree1.nodes(root1).reverse(),
                      links = tree1.links(nodes);

                  // Normalize for fixed-depth.
                  nodes.forEach(function(d) { d.y = d.depth * 180; });

                  // Update the nodes…
                  var node = svg2.selectAll("g.node1")
                      .data(nodes, function(d) { return d.id || (d.id = ++i); });

                  // Enter any new nodes at the parent's previous position.
                  var nodeEnter = node.enter().append("g")
                      .attr("class", "node1")
                      .attr("transform", function(d) { return "translate(" + source.y0 + "," + source.x0 + ")"; })
                      .on("click", click);

                  nodeEnter.append("circle")
                      .attr("r", 1e-6)
                      .style("fill", function(d) { return d._children ? "lightsteelblue" : "#fff"; });

                  nodeEnter.append("text")
                      .attr("x", function(d) { return d.children || d._children ? -10 : 10; })
                      .attr("dy", ".35em")
                      .attr("text-anchor", function(d) { return d.children || d._children ? "end" : "start"; })
                      .text(function(d) { return d.name; })
                      .style("fill-opacity", 1e-6);

                  // Transition nodes to their new position.
                  var nodeUpdate = node.transition()
                      .duration(duration)
                      .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; });

                  nodeUpdate.select("circle")
                      .attr("r", 4.5)
                      .style("fill", function(d) { return d._children ? "lightsteelblue" : "#fff"; });

                  nodeUpdate.select("text")
                      .style("fill-opacity", 1);

                  // Transition exiting nodes to the parent's new position.
                  var nodeExit = node.exit().transition()
                      .duration(duration)
                      .attr("transform", function(d) { return "translate(" + source.y + "," + source.x + ")"; })
                      .remove();

                  nodeExit.select("circle")
                      .attr("r", 1e-6);

                  nodeExit.select("text")
                      .style("fill-opacity", 1e-6);

                  // Update the links…
                  var link = svg2.selectAll("path.link1")
                      .data(links, function(d) { return d.target.id; });

                  // Enter any new links at the parent's previous position.
                  link.enter().insert("path", "g")
                      .attr("class", "link1")
                      .attr("d", function(d) {
                        var o = {x: source.x0, y: source.y0};
                        return diagonal({source: o, target: o});
                      });

                  // Transition links to their new position.
                  link.transition()
                      .duration(duration)
                      .attr("d", diagonal);

                  // Transition exiting nodes to the parent's new position.
                  link.exit().transition()
                      .duration(duration)
                      .attr("d", function(d) {
                        var o = {x: source.x, y: source.y};
                        return diagonal({source: o, target: o});
                      })
                      .remove();

                  // Stash the old positions for transition.
                  nodes.forEach(function(d) {
                    d.x0 = d.x;
                    d.y0 = d.y;
                  });
                }

                // Toggle children on click.
                function click(d) {
                  if (d.children) {
                    d._children = d.children;
                    d.children = null;
                  } else {
                    d.children = d._children;
                    d._children = null;
                  }
                  update(d);
                }

      </script> 
      <!--cierre Nuevo dendogram-->

    <!--inception-->
    <script>
        var margin = 10,
            diameter = 610;

        var color = d3.scale.linear()
            .domain([-1, 5])
            .range(["hsl(152,80%,80%)", "hsl(228,30%,40%)"])
            .interpolate(d3.interpolateHcl);

        var pack = d3.layout.pack()
            .padding(2)
            .size([diameter - margin, diameter - margin])
            .value(function(d) { return d.size; })

        var svg = d3.select("#inception").append("svg")
            .attr("width", diameter)
            .attr("height", diameter)
          .append("g")
            .attr("transform", "translate(" + diameter / 2 + "," + diameter / 2 + ")");

        d3.json("js/inception_bk2.json", function(error, root) {
          if (error) return console.error(error);

          var focus = root,
              nodes = pack.nodes(root),
              view;

          var circle = svg.selectAll("circle")
              .data(nodes)
            .enter().append("circle")
              .attr("class", function(d) { return d.parent ? d.children ? "node2" : "node2 node--leaf1" : "node2 node--root1"; })
              .style("fill", function(d) { return d.children ? color(d.depth) : null; })
              .on("click", function(d) { if (focus !== d) zoom(d), d3.event.stopPropagation(); });

          var text = svg.selectAll("text")
              .data(nodes)
            .enter().append("text")
              .attr("class", "label")
              .style("fill-opacity", function(d) { return d.parent === root ? 1 : 0; })
              .style("display", function(d) { return d.parent === root ? null : "none"; })
              .text(function(d) { return d.name; });

          var node = svg.selectAll("circle,text");

          d3.select("#inception")
              .style("background", color(-1))
              .on("click", function() { zoom(root); });

          zoomTo([root.x, root.y, root.r * 2 + margin]);

          function zoom(d) {
            var focus0 = focus; focus = d;

            var transition = d3.transition()
                .duration(d3.event.altKey ? 7500 : 750)
                .tween("zoom", function(d) {
                  var i = d3.interpolateZoom(view, [focus.x, focus.y, focus.r * 2 + margin]);
                  return function(t) { zoomTo(i(t)); };
                });

            transition.selectAll("text")
              .filter(function(d) { return d.parent === focus || this.style.display === "inline"; })
                .style("fill-opacity", function(d) { return d.parent === focus ? 1 : 0; })
                .each("start", function(d) { if (d.parent === focus) this.style.display = "inline"; })
                .each("end", function(d) { if (d.parent !== focus) this.style.display = "none"; });
          }

          function zoomTo(v) {
            var k = diameter / v[2]; view = v;
            node.attr("transform", function(d) { return "translate(" + (d.x - v[0]) * k + "," + (d.y - v[1]) * k + ")"; });
            circle.attr("r", function(d) { return d.r * k; });
          }
        });

        d3.select(self.frameElement).style("height", diameter + "px");

    </script>
    <!--cierre inception-->
    
  @stop 