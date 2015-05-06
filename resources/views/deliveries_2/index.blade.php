@extends('app')
  @section('content')
       <!-- Map -->
    <section id="rutas" class="rutas">
     
      <h1>Gr&aacute;ficas</h1>


        <!--tab-->
        <div class="tab-graficas" id="navv">
          <ul class="nav nav-tabs" role="tablist" id="tabgraficas">
                <li class="active"><a href="#dendogram">Dendogram</a></li>
                <li><a href="#word_cloud">Word Cloud</a></li>
                <li><a href="#bubble_chart">Bubble Chart</a></li>
                <li><a href="#inception">Inception</a></li>
            </ul>




            <div class="tab-content">

                  <div class="container theme-showcase tab-pane fade in active" role="main">

                          <h2>10 clientes con más unidades entregadas por camion</h2>

                          <select class="form-control" id="opcion_dendo">
                            <option value="1">Unidades Entregadas</option>
                            <option value="2">Peso Entregado</option>
                            <option value="3">Tiempo de Servicio</option>
                          </select>
                          <br>
                          <button type="button" class="btn btn-success" id="boton_dendo">Graficar!</button>
                          <div id="dendogram">

                          </div>
                        
                  </div> <!-- /container-->


                  <!--container del word_cloud-->
                   <div role="main" class="container theme-showcase tab-pane fade" id="word_cloud">
                        <h2>word</h2>
                        
                  </div><!-- /container-->


                   <!--container de las bubble_chart-->
                   <div role="main" class="container theme-showcase tab-pane fade bubbleChart" id="bubble_chart">
                            <h2>bubble</h2>
                  </div><!-- /container-->

                   <!--container de las bubble_chart-->
                   <div role="main" class="container theme-showcase tab-pane fade" id="inception">
                      <h2>inception</h2>
                
                  </div><!-- /container-->

            </div><!--tab-content-->
        </div><!--cierre-->

        <!--bubble chart-->
                            
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
           <h2>Calificaci&oacute;n promedio de los camiones</h2>
           <h3> {{ $trucks_average_grade }} estrellas de 5 </h3>
          </div>  
          <div id="st6" class="container" >
           <h2>Stats</h2>
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

<!--Scripts De las gráficas Staticas-->
        <!--bubble chart-->
    <script>
        $(document).ready(function () {
          var obj = {};
          var clientes = [];
          var text = 'text';
          var size = 'size';
           @for ($i = 0; $i < sizeof($bubble); $i++)
                          obj = {text: '{{$bubble[$i]['text']}}', count: '{{$bubble[$i]['size']}}'};
                          clientes.push(obj);
          @endfor
          //console.log(clientes);
          //console.log(clientes);s
          var bubbleChart = new d3.svg.BubbleChart({
            supportResponsive: true,
            //container: => use @default
            size: 600,
            //viewBoxSize: => use @default
            innerRadius: 600 / 3.5,
            //outerRadius: => use @default
            radiusMin: 50,
            //radiusMax: use @default
            //intersectDelta: use @default
            //intersectInc: use @default
            //circleColor: use @default
            //en items va el arreglo de objetos json
            data: {
              items: clientes,
              eval: function (item) {return item.count;},
              classed: function (item) {return item.text.split(" ").join("");}
            },
            plugins: [
              {
                name: "central-click",
                options: {
                  text: "(See more detail)",
                  style: {
                    "font-size": "12px",
                    "font-style": "italic",
                    "font-family": "Source Sans Pro, sans-serif",
                    //"font-weight": "700",
                    "text-anchor": "middle",
                    "fill": "white"
                  },
                  attr: {dy: "65px"},
                  centralClick: function() {
                    alert("Here is more details!!");
                  }
                }
              },
              {
                name: "lines",
                options: {
                  format: [
                    {// Line #0
                      textField: "count",
                      classed: {count: true},
                      style: {
                        "font-size": "28px",
                        "font-family": "Source Sans Pro, sans-serif",
                        "text-anchor": "middle",
                        fill: "white"
                      },
                      attr: {
                        dy: "0px",
                        x: function (d) {return d.cx;},
                        y: function (d) {return d.cy;}
                      }
                    },
                    {// Line #1
                      textField: "text",
                      classed: {text: true},
                      style: {
                        "font-size": "14px",
                        "font-family": "Source Sans Pro, sans-serif",
                        "text-anchor": "middle",
                        fill: "white"
                      },
                      attr: {
                        dy: "20px",
                        x: function (d) {return d.cx;},
                        y: function (d) {return d.cy;}
                      }
                    }
                  ],
                  centralFormat: [
                    {// Line #0
                      style: {"font-size": "50px"},
                      attr: {}
                    },
                    {// Line #1
                      style: {"font-size": "30px"},
                      attr: {dy: "40px"}
                    }
                  ]
                }
              }]
          });
        });
    </script>
    <!--bubble chart -->
    
   <!--Nuevo dendogram-->

      <script id="dendogram_script">

                var margin = {top: 20, right: 120, bottom: 20, left: 120},
                    width = 960 - margin.right - margin.left,
                    height = 800 - margin.top - margin.bottom;
                    
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

                d3.json("js/camiones.json", function(error, flare) {
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

                d3.select(self.frameElement).style("height", "800px");

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


   <!--Nuevo dendogram-->
   
    <!--Dendogram
    <script>
          var radius = 2200 / 2;

          var cluster = d3.layout.cluster().size([360, radius - 120]);

          var diagonal = d3.svg.diagonal.radial().projection(function(d) { return [d.y, d.x / 180 * Math.PI]; });

          var svg = d3.select("#dendogram").append("svg").attr("width", radius * 2).attr("height", radius * 2).append("g")
              .attr("transform", "translate(" + radius + "," + radius + ")");

          d3.json("js/camiones.json", function(error, root) {
            var nodes = cluster.nodes(root);

            var link = svg.selectAll("path.link1")
                .data(cluster.links(nodes))
              .enter().append("path")
                .attr("class", "link1")
                .attr("d", diagonal);

            var node = svg.selectAll("g.node1")
                .data(nodes)
              .enter().append("g")
                .attr("class", "node1")
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
    Dendogram--> 
   
   
    <!--word_cloud-->
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
              d3.select("#word_cloud").append("svg")
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
    <!--word_cloud-->

         <!--inception-->
    <script>
        var margin = 20,
            diameter = 960;

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

        d3.json("js/inception.json", function(error, root) {
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