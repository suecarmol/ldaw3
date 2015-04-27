@extends('app')
	@section('content')
		   <!-- Map -->
    <section id="rutas" class="rutas">
      <div id="panel">
    <b>Start: </b>
    <select id="start" onchange="calcRoute();">
      <option value="chicago, il">Chicago</option>
      <option value="st louis, mo">St Louis</option>
      <option value="joplin, mo">Joplin, MO</option>
      <option value="oklahoma city, ok">Oklahoma City</option>
 
    </select>
    <b>End: </b>
    <select id="end" onchange="calcRoute();">
      <option value="chicago, il">Chicago</option>
      <option value="st louis, mo">St Louis</option>
      <option value="joplin, mo">Joplin, MO</option>
      <option value="oklahoma city, ok">Oklahoma City</option>

    </select>
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