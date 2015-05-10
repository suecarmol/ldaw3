
$(document).ready(function(){       
		//Date time picker
		$(function () {
	        $('#datetimepicker1').datetimepicker({
	        		format: 'YYYY-MM-DD HH'
	        });
    	});
		var token = "{{ csrf_token() }}";

		//Gráfica word cloud
		$.ajax({
        url: "word_cloud",
        type: 'GET',
        data: {"_token": token },
        cache: false,
		        success: function(response_word)
		        {
		            //$('#something').html(response);
		            //console.log(response_word);

		             var cont = 0;
              		 var clientes = [];
              		 var units = [];
              		 var aux = false;
              		 for (var i = 0; i < 15; i++) {
              		 	clientes[i] = response_word[i]["text"];
    					
					}

					for (var i = 0; i < 15; i++) {
              		 	units[i] = response_word[i]["size"];
    					
					}


					//console.log(clientes)
					   var fill = d3.scale.category20();
             
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

			                 }//Cierre d3
/*
               var aux = false;
              @for ($i = 0; $i < sizeof($nombres); $i++)
                    clientes[{{$i}}] = "{{$nombres[$i]}}";
              @endfor
              @for ($i = 0; $i < sizeof($unidades); $i++)
                    units[{{$i}}] = "{{$unidades[$i]}}";
              @endfor*/
		           

		        	}
		    });

	//word cloud dinamica
       $( "#boton_word" ).click(function() {
        var val = $( "#opcion_word" ).val();
        //var token = "{{ csrf_token() }}";
        //console.log(val);
        $( "svg" ).remove();

		            	/*
		            var head = '<h2>Clientes con mas unidades entregadas</h2>';
		            var br = '<br>';
		            var select = '<select class="form-control" id="opcion_word"> <option value="1">Clientes por unidades</option> <option value="2">Clientes por peso entregado</option> <option value="3">Rutas con mas clientes</option> <option value="4">Camiones por mas rutas</option> </select>';
		            var boton= $('<button type="button" class="btn btn-success" id="boton_word">Graficar</button>');
		            $("#word_cloud").append(head, br, select,br ,boton,br);*/

       				$.ajax({
        url: "word_cloud_dinamica",
        type: 'GET',
        data: {"val": val, "_token": token },
        cache: false,
		        success: function(response_word)
		        {
		            //$('#something').html(response);
		            //console.log(response_word);
		           
		            

		             var cont = 0;
              		 var clientes = [];
              		 var units = [];
              		 var aux = false;

              		 for (var i = 0; i < 15; i++) {
              		 	clientes[i] = response_word[i]["text"];
    					
					}

					
						for (var i = 0; i < 15; i++) {
	              		 	units[i] = response_word[i]["size"];
	    					
						}

					//console.log(clientes)
					//console.log(units);
					   var fill = d3.scale.category20();
             
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

			                 }//Cierre d3

		         }

		    });//cirre peiticon


        });//Cierre on click


			//Gráfica word cloud
		$.ajax({
		        url: "bubble_chart",
		        type: 'GET',
		        data: {"_token": token },
		        cache: false,
		        success: function(response_word)
		        {
		        	  //console.log(response_word);
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
			              items: response_word,
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
			          });//cierre bubble chart

		        }//cierre success
		    });//cierre peticion bubble estatica 


			//bubble dinamica
       $( "#boton_bubble" ).click(function() {
        var val = $( "#opcion_bubble" ).val();
        //var token = "{{ csrf_token() }}";
        //console.log(val);
        $( "svg" ).remove();

		            	/*
		            var head = '<h2>Clientes con mas unidades entregadas</h2>';
		            var br = '<br>';
		            var select = '<select class="form-control" id="opcion_word"> <option value="1">Clientes por unidades</option> <option value="2">Clientes por peso entregado</option> <option value="3">Rutas con mas clientes</option> <option value="4">Camiones por mas rutas</option> </select>';
		            var boton= $('<button type="button" class="btn btn-success" id="boton_word">Graficar</button>');
		            $("#word_cloud").append(head, br, select,br ,boton,br);*/        
       		$.ajax({
		        url: "bubble_chart_dinamica",
		        type: 'GET',
		        data: {"val": val, "_token": token },
		        cache: false,
		        success: function(response_word)
		        {
		            //$('#something').html(response);
		            //console.log("bubble chart dinamica ");
		            //console.log(response_word);
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
			              items: response_word,
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
			          });//cierre bubble chart
		           

		        }//cierre succes

		    });//cirre peiticon


        });//Cierre on click


		//sugerencias dinamicas
       $( "#acutaliza_sugerencia" ).click(function() {
	        var val = $( "#date" ).val();
	        //val = val.toString();
	        //var token = "{{ csrf_token() }}";
	        console.log(val);
	        $("#suggestions_table").fadeOut();
	        //$('#suggestions_table').dataTable();
	        //$('#suggestions_table_wrapper').fadeOut();
        
       		$.ajax({
		        url: "suggestions_dinamicas",
		        type: 'GET',
		        data: {"date": val, "_token": token },
		        cache: false,
			        success: function(response_word)

			        {
			        		console.log(response_word);
			           		var table = '<table id="suggestions_table" class="table table-striped table-bordered" cellspacing="0" width="100%">'+'<thead>'+'<tr >'+'<th>Cliente</th>'+'<th>Ruta 1</th>'+'<th>Ruta 2</th>'+'<th>Fecha 1</th>'+'<th>Fecha 2</th>'+'<th>Cami&oacute;n a Compartir</th>';
			           		table += '<tbody>';
			           		for(var i= 0; i<response_word.length;i++){
			           			table += '<tr>';
			           			table +='<td>'+ response_word[i]['client_name'] + '</td>';
			           			table +='<td>'+ response_word[i]['route_name_1'] + '</td>';
			           			table +='<td>'+ response_word[i]['route_name_2'] + '</td>';
			           			table +='<td>'+ response_word[i]['date_1'] + '</td>';
			           			table +='<td>'+ response_word[i]['date_2'] + '</td>';
			           			table +='<td>'+ response_word[i]['chosen_truck'] + '</td>';
			           			table += '</tr>';
			           		}

			       			table += '</tbody>' + '</table>';
			       			//console.log(table)
			       			$('#suggestions_table').replaceWith(table);
			       			$('#suggestions_table').dataTable();


			        }
		    });//cirre peiticon


        });//Cierre on click
});//cierre on document ready
	/*
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
              </div>*/
/*


               var fill = d3.scale.category20();
             
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


*/