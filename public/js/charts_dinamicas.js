
$(document).ready(function(){       

		var token = "{{ csrf_token() }}";


		$.ajax({
        url: "word_cloud",
        type: 'GET',
        data: {"_token": token },
        cache: false,
		        success: function(response_word)
		        {
		            //$('#something').html(response);
		            console.log(response_word);

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


       $( "#boton_word" ).click(function() {
        var val = $( "#opcion_word" ).val();
        //var token = "{{ csrf_token() }}";
        console.log(val);
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
		            console.log(response_word);
		           
		            

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
					
					/*
					if(val==2){
						for (var i = 0; i < 15; i++) {
	              		 	units[i] = response_word[i]["size"];
	    					
						}
					}

					if(val==3){
						for (var i = 0; i < 15; i++) {
	              		 	units[i] = response_word[i]["size"];
	    					
						}
					}*/



					console.log(clientes)
					console.log(units);
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

		    });


        });//Cierre on click
});
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