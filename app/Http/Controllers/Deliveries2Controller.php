<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\PeticionRequest;

use Request;

use App\Delivery;

class Deliveries2Controller extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		/********
		**** STATS
		********/
		$deliveries = \DB::collection('proyecto')
		->where('clients.cost', '=', 'null')
		->orderBy('clients.arrival_time')
		->get();

		//var_dump($deliveries);

		$average_deliveries = \DB::collection('proyecto')
		->select('clients.units_delivered')
		->where('clients.units_delivered', '>', 0)
		->get();

		$average_delivery_units = $this->getAvgDeliveryUnits($average_deliveries);

		$average_weight_delivered = \DB::collection('proyecto')
		->select('clients.weight_delivered')
		->where('clients.weight_delivered', '>', 0)
		->get();

		$average_weight_per_route = $this->getAvgWeight($average_weight_delivered);

		$deliveries_out_of_route = \DB::collection('proyecto')
		->where('clients.is_in_route', '=', 1)
		->count();

		$trucks_average_capacity = round(\DB::collection('proyecto')
		->where('capacity', '>', 0)
		->avg('capacity'), 2);

		$trucks_average_grade = round(\DB::collection('proyecto')
		->where('capacity' , '>', 0)
		->avg('average_grade'), 2);

		//$x = $this->calculateBestRoute($deliveries);

		/********
		**** CHARTS
		********/

		//camiones 
		//selecciona solamente los camiones
		//primer documento de la grafica del dendogram
		$trucks = \DB::collection('proyecto')
		->select('truck_id')
		->where('capacity', '>', 0)
		->get();

		//$this->dendogram($trucks);
		//$this->dendogram_2($trucks);
		//$this->dendogram_3($trucks);

		/*
		$clientes = \DB::collection('proyecto')
		->select('clients.name', 'clients.units_delivered')
		->where('clients.units_delivered', '>', 0)
		->orderBy('clients.units_delivered')
		->get();
	
		$word = $this->word_cloud($clientes);
		//var_dump($clientes);
		//var_dump($trucks);
		$nombres = array();
		$unidades = array();

		for($i=0; $i < 15; $i++){
			$nombres[$i] = $word[$i]['text'];
			$unidades[$i] = $word[$i]['size'];
		}*/

		//var_dump($nombres);
		//var_dump($unidades);
		//var_dump($word[0]['text']);
		//bubble chart

			//funcion para el bubble chartq
		/*
		$bubble_clientes = \DB::collection('proyecto')
		->select('clients.name', 'clients.units_delivered')
		->where('clients.units_delivered', '>', 0)
		->orderBy('clients.units_delivered')
		->get();

		$bubble = $this->bubble_chart($bubble_clientes);*/
		//var_dump($bubble[1]['size']);
		

		//ínception

		$inception_info = \DB::collection('proyecto')
		->select('truck_id', 'route_id', 'clients.name')
		->where('clients.units_delivered', '>', 0)
		->orderBy('clients.units_delivered')
		->get();

		//var_dump($inception);
		//como el archivo json ya existe comentare la linea para que no se genere el archivo cada ves que se haga refresh
		$this->inception($inception_info);


		/********
		**** SUGGESTIONS
		********/

		$suggestions = $this->createSuggestions();

		return view('deliveries_2.index')
		->with('average_delivery_units', $average_delivery_units)
		->with('average_weight_per_route', $average_weight_per_route)
		->with('deliveries_out_of_route', $deliveries_out_of_route)
		->with('trucks_average_capacity', $trucks_average_capacity)
		->with('trucks_average_grade', $trucks_average_grade)
		//->with('nombres', $nombres)
		//->with('bubble', $bubble)
		//->with('unidades', $unidades)
		->with('suggestions', $suggestions);
	}

	public function createSuggestions()
	{

		$date = '2014-05-12 08';
		//not using this right now
		$hour = '';
		$routes = Delivery::where('clients.arrival_time', 'regexp', '/^'.$date.'*./')->get();

		$suggestions = array();

		//var_dump($routes);

		for ($i=0; $i < sizeof($routes); $i++) { 
			for($ii = 0; $ii < sizeof($routes); $ii++) { 
				//var_dump($routes[$i]['route_id'] . '!=' . $routes[$ii]['route_id']);
				if($routes[$i]['route_id'] != $routes[$ii]['route_id'])
				{
					$temp_clients = $routes[$ii]['clients'];
					//var_dump($temp_clients);
					for ($j=0; $j < sizeof($temp_clients); $j++) { 
						for ($k=0; $k < sizeof($temp_clients); $k++) {
							//var_dump($temp_clients[$j]['arrival_time']. '||||' . $temp_clients[$k]['arrival_time']);
							if($temp_clients[$j]['name'] == $temp_clients[$k]['name'] && substr($temp_clients[$j]['arrival_time'], 0, 13) == $date && substr($temp_clients[$k]['arrival_time'], 0, 13) == $date)
							{
								//var_dump('hay una coincidencia: ' . 'cliente_1: ' . $temp_clients[$j]['name'] . ' cliente_2: ' . $temp_clients[$k]['name']);
								//var_dump($temp_clients[$j]['arrival_time'] . '||||' . $temp_clients[$k]['arrival_time']);

								$total_weight_delivered = $temp_clients[$j]['weight_delivered'] + $temp_clients[$k]['weight_delivered'];

								$truck1 = \DB::collection('proyecto')
								->select('truck_id', 'capacity', 'average_grade')
								->where('truck_id', '=', $routes[$i]['truck_id'])
								->where('capacity', '>', $total_weight_delivered)
								->get();

								$truck2 = \DB::collection('proyecto')
								->select('truck_id', 'capacity', 'average_grade')
								->where('truck_id', '=', $routes[$ii]['truck_id'])
								->where('capacity', '>', $total_weight_delivered)
								->get();

								//var_dump($total_weight_delivered);
								//var_dump($truck1);
								//var_dump($truck2);

								//choose between the two trucks based on average grade
								if($truck1[0]['average_grade'] > $truck2[0]['average_grade']){
									$chosen_truck = $truck1[0]['truck_id'];
								}
								elseif($truck2[0]['average_grade'] > $truck1[0]['average_grade']){
									$chosen_truck = $truck2[0]['truck_id'];
								}
								else{
									//if the average grades are the same
									$random_selection = mt_rand(1, 2);

									if ($random_selection == 1) {
										$chosen_truck = $truck1[0]['truck_id'];
									}
									else{
										$chosen_truck = $truck2[0]['truck_id'];
									}
								}

								//var_dump($temp_clients[$k]['arrival_time']);

								$suggestions [] = array(
									'route_name_1' => $routes[$i]['route_id'],
									'route_name_2' => $routes[$ii]['route_id'],
									'chosen_truck' => $chosen_truck,
									'client_name' => $temp_clients[$j]['name'],
									'date_1' => date('d/m/Y H:00', strtotime($temp_clients[$j]['arrival_time'])),
									'date_2' => date('d/m/Y H:00', strtotime($temp_clients[$k]['arrival_time']))
								);
							}
						}
					}
				} //end if	
			} // end for ii
		} //end for i

		return $suggestions;

	}

	public function inception($inception){
		//compania -> Camiones -> Rutas -> Clientes
		//recorre los camiones
		//var_dump(sizeof($inception[0]['clients']));
			$compania = \DB::collection('proyecto')
					->select('company_name')
					->where('truck_id', '=', $inception[0]['truck_id'])
					->where('capacity', '>', 0)
					->get();

					//var_dump($compania);
			
			$nombre_comp = "nombre";
			//var_dump($inception);
			$aux_nombre = "nombre";
			$json = "{ \"name\": \"flare\",
 						\"children\": [";

			for($i= 0; $i<sizeof($inception); $i++){
				$aux = \DB::collection('proyecto')
					->select('company_name')
					->where('truck_id', '=', $inception[$i]['truck_id'])
					->where('capacity', '>', 0)
					->get();
				if($nombre_comp==$compania[0]['company_name']){
					//var_dump("la compañia es igual");
					$json .= " {\"name\": \"".$inception[$i]['truck_id']."\", 
						\"children\": [" .
						 " {\"name\": \"".$inception[$i]['route_id']."\", 
							\"children\": ["
						;

						for ($j=0; $j < 15; $j++){
					//var_dump($clientes[1]['clients'][$j]['name']);
						//$aux = sizeof($clientes[1]['clients']);
						//if(sizeof($clientes[1]['clients'])-1 == $j) {
 				  		  if(15-1 == $j) {
						//if(3-1 == $j) {
							if(sizeof($inception)-1==$i)
								$json .= " {\"name\": \"".$inception[$i]['clients'][$j]['name']."\", \"size\": 743}]}]}";
							else
								$json .= " {\"name\": \"".$inception[$i]['clients'][$j]['name']."\", \"size\": 743}]}]},";
    					}//cierre if
    					else {
    						$json .= " {\"name\": \"".$inception[$i]['clients'][$j]['name']."\", \"size\": 743},\n";	
						}//cierre else						
					}//cierre for de clientes
				}
				else{
					if($i==0){
					$json .= "{ \"name\":". "\"".$compania[0]['company_name']. "\"" .",
 						\"children\": [" ;
 					$nombre_comp = $compania[0]['company_name'];
 					}
 					else{
 						$json .= "]},";
 						$json .= "{ \"name\":". "\"".$compania[0]['company_name']. "\"" .",
 						\"children\": [" ;
 						$nombre_comp = $compania[0]['company_name'];


 					}//cierre else anidado

				}//cierre else

			}
			$json .= " ]} ]}";
			//var_dump($json);

	
			$myfile = fopen("js/inception.json", "w") or die("Unable to open file!");
			fwrite($myfile, $json);
			fclose($myfile);

		return 0;

	}//Cierre inception
	
	public function getBubble_chart(PeticionRequest $request){

		$bubble_clientes = \DB::collection('proyecto')
		->select('clients.name', 'clients.units_delivered')
		->where('clients.units_delivered', '>', 0)
		->get();
		//var_dump($clientes);	
			$bubble = array();
			//for($i=0; $i < sizeof($bubble_clientes); $i++){
			for($i=0; $i < 5; $i++){
				$temp_clients = $bubble_clientes[$i]['clients'];
				//var_dump($temp_clients);
				for($j=0; $j < 2; $j++){
					//var_dump($temp_clients[$j]['units_delivered']);
					$bubble[] = array(
						'text'=>$temp_clients[$j]['name'], 
						'count'=>$temp_clients[$j]['units_delivered']
					);
				}//cierre fora anidado para temp_clients			
			}//cierre for 

			$units = array();
			//ordering the array
			foreach ($bubble as $key => $row) {
				$units[$key] = $row['count'];
			}
			array_multisort($units, SORT_DESC, $bubble);

			return $bubble;


	}

	
	public function getBubble_chart_dinamica(PeticionRequest $request){
		 $val = $request->get('val');

		 if($val==1){

				$bubble_clientes = \DB::collection('proyecto')
				->select('clients.name', 'clients.units_delivered')
				->where('clients.units_delivered', '>', 0)
				->get();
				//var_dump($clientes);	
					$bubble = array();
					//for($i=0; $i < sizeof($bubble_clientes); $i++){
					for($i=0; $i < 5; $i++){
						$temp_clients = $bubble_clientes[$i]['clients'];
						//var_dump($temp_clients);
						for($j=0; $j < 2; $j++){
							//var_dump($temp_clients[$j]['units_delivered']);
							$bubble[] = array(
								'text'=>$temp_clients[$j]['name'], 
								'count'=>$temp_clients[$j]['units_delivered']
							);
						}//cierre fora anidado para temp_clients			
					}//cierre for 


					$units = array();
					//ordering the array
					foreach ($bubble as $key => $row) {
						$units[$key] = $row['count'];
					}
					array_multisort($units, SORT_DESC, $bubble);

			}

			if($val==2){

				$bubble_clientes = \DB::collection('proyecto')
				->select('clients.name', 'clients.weight_delivered')
				->where('clients.weight_delivered', '>', 0)
				->get();
				//var_dump($clientes);	
					$bubble = array();
					//for($i=0; $i < sizeof($bubble_clientes); $i++){
					for($i=0; $i < 5; $i++){
						$temp_clients = $bubble_clientes[$i]['clients'];
						//var_dump($temp_clients);
						for($j=0; $j < 2; $j++){
							//var_dump($temp_clients[$j]['units_delivered']);
							$bubble[] = array(
								'text'=>$temp_clients[$j]['name'], 
								'count'=>$temp_clients[$j]['weight_delivered']
							);
						}//cierre fora anidado para temp_clients			
					}//cierre for 

					$units = array();
					//ordering the array
					foreach ($bubble as $key => $row) {
						$units[$key] = $row['count'];
					}
					array_multisort($units, SORT_DESC, $bubble);

			}

			if($val==3){

				$bubble_clientes = \DB::collection('proyecto')
				->select('truck_id', 'average_grade')
				->where('average_grade', '>', 0)
				->get();
				//var_dump($bubble_clientes);	
				$bubble = array();
					//for($i=0; $i < sizeof($bubble_clientes); $i++){
					for($i=0; $i < 10; $i++){
						//var_dump($temp_clients)
							//var_dump($temp_clients[$j]['units_delivered']);
							$bubble[] = array(
								'text'=>$bubble_clientes[$i]['truck_id'], 
								'count'=>$bubble_clientes[$i]['average_grade']
							);	
					}//cierre for 

					//var_dump($bubble);

					$units = array();
					//ordering the array
					foreach ($bubble as $key => $row) {
						$units[$key] = $row['count'];
					}
					array_multisort($units, SORT_DESC, $bubble);
			}


			if($val==4){

				$bubble_clientes = \DB::collection('proyecto')
				->select('truck_id', 'capacity')
				->where('capacity', '>', 0)
				->get();
				//var_dump($bubble_clientes);	
				$bubble = array();
					//for($i=0; $i < sizeof($bubble_clientes); $i++){
					for($i=0; $i < 10; $i++){
						//var_dump($temp_clients)
							//var_dump($temp_clients[$j]['units_delivered']);
							$bubble[] = array(
								'text'=>$bubble_clientes[$i]['truck_id'], 
								'count'=>$bubble_clientes[$i]['capacity']
							);	
					}//cierre for 

					//var_dump($bubble);

					$units = array();
					//ordering the array
					foreach ($bubble as $key => $row) {
						$units[$key] = $row['count'];
					}
					array_multisort($units, SORT_DESC, $bubble);
			}


		return $bubble;


	}
	public function getWord_cloud(PeticionRequest $request){
			//var_dump($clientes);	
			$clientes = \DB::collection('proyecto')
				->select('clients.name', 'clients.units_delivered')
				->where('clients.units_delivered', '>', 0)
				->get();


			$word_cloud = array();

			for($i=0; $i < sizeof($clientes); $i++){
				$temp_clients = $clientes[$i]['clients'];
				//var_dump($temp_clients);
				for($j=0; $j < 10; $j++){
					//var_dump($temp_clients[$j]['units_delivered']);
					$word_cloud[] = array(
						'text'=>$temp_clients[$j]['name'], 
						'size'=>$temp_clients[$j]['units_delivered']
					);
				}//cierre fora anidado para temp_clients			
			}//cierre for 

			$units = array();
			//ordering the array
			foreach ($word_cloud as $key => $row) {
				$units[$key] = $row['size'];
			}
			array_multisort($units, SORT_DESC, $word_cloud);

			return $word_cloud;
	}


	public function getWord_cloud_dinamica(PeticionRequest $request){
			//var_dump($clientes);	

			 $val = $request->get('val');

			 if($val==1){

						$clientes = \DB::collection('proyecto')
							->select('clients.name', 'clients.units_delivered')
							->where('clients.units_delivered', '>', 0)
							->get();

						$word_cloud = array();

						for($i=0; $i < sizeof($clientes); $i++){
							$temp_clients = $clientes[$i]['clients'];
							//var_dump($temp_clients);
							for($j=0; $j < 10; $j++){
								//var_dump($temp_clients[$j]['units_delivered']);
								$word_cloud[] = array(
									'text'=>$temp_clients[$j]['name'], 
									'size'=>$temp_clients[$j]['units_delivered']
								);
							}//cierre fora anidado para temp_clients			
						}//cierre for 

						$units = array();
						//ordering the array
						foreach ($word_cloud as $key => $row) {
							$units[$key] = $row['size'];
						}
						array_multisort($units, SORT_DESC, $word_cloud);
			}

			if($val==2){

				$clientes = \DB::collection('proyecto')
							->select('clients.name', 'clients.weight_delivered')
							->where('clients.weight_delivered', '>', 0)
							->get();

						$word_cloud = array();

						for($i=0; $i < sizeof($clientes); $i++){
							$temp_clients = $clientes[$i]['clients'];
							//var_dump($temp_clients);
							for($j=0; $j < 10; $j++){
								//var_dump($temp_clients[$j]['units_delivered']);
								$word_cloud[] = array(
									'text'=>$temp_clients[$j]['name'], 
									'size'=>$temp_clients[$j]['weight_delivered']* (.009)
								);
							}//cierre fora anidado para temp_clients			
						}//cierre for 

						$units = array();
						//ordering the array
						foreach ($word_cloud as $key => $row) {
							$units[$key] = $row['size'];
						}
						array_multisort($units, SORT_DESC, $word_cloud);

			}


			if($val==3){
				$clientes = \DB::collection('proyecto')
				->select('clients.name', 'clients.weight_delivered', 'route_id')
				->where('clients.weight_delivered', '>', 0)
				->get();

							//var_dump($clientes);
				$word_cloud = array();

					for($i=0; $i < sizeof($clientes); $i++){
							//var_dump($temp_clients[$j]['units_delivered']);
							$word_cloud[] = array(
								'text'=>$clientes[$i]['route_id'], 
								'size'=>sizeof($clientes[$i]['clients'])* (.08)
							);			
					}//cierre for 

					//var_dump($word_cloud);

					$units = array();
					//ordering the array
					foreach ($word_cloud as $key => $row) {
						$units[$key] = $row['size'];
					}
					array_multisort($units, SORT_DESC, $word_cloud);


			}

			if($val==4){


				$clientes = \DB::collection('proyecto')
				->select('route_id', 'truck_id')
				->where('clients.weight_delivered', '>', 0)
				->get();


							//var_dump($clientes);
							
				$word_cloud = array();

					for($i=0; $i < sizeof($clientes); $i++){
							//var_dump($temp_clients[$j]['units_delivered']);
							$word_cloud[] = array(
								'text'=>$clientes[$i]['truck_id'], 
								'size'=>sizeof($clientes[$i]['route_id'])*40
							);			
					}//cierre for */
					//var_dump($word_cloud);
					$units = array();
					//ordering the array
					foreach ($word_cloud as $key => $row) {
						$units[$key] = $row['size'];
					}
					array_multisort($units, SORT_DESC, $word_cloud);

			}

			if($val==5){

					$clientes = \DB::collection('proyecto')
							->select('clients.name', 'clients.service_time')
							->where('clients.units_delivered', '>', 0)
							->get();

						$word_cloud = array();

						for($i=0; $i < sizeof($clientes); $i++){
							$temp_clients = $clientes[$i]['clients'];
							//var_dump($temp_clients);
							for($j=0; $j < 10; $j++){
								//var_dump($temp_clients[$j]['units_delivered']);
								$word_cloud[] = array(
									'text'=>$temp_clients[$j]['name'], 
									'size'=>$temp_clients[$j]['service_time']*(.09)
								);
							}//cierre fora anidado para temp_clients			
						}//cierre for 

						$units = array();
						//ordering the array
						foreach ($word_cloud as $key => $row) {
							$units[$key] = $row['size'];
						}
						array_multisort($units, SORT_DESC, $word_cloud);
			}

			return $word_cloud;
	}

	//Dedogram por peso entregado
	public function dendogram($trucks){

			//recorre los camiones
			$json = "{ \"name\": \"camiones\",
 						\"children\": [";

			for ($i=0; $i < sizeof($trucks); $i++) { 
 			 //for ($i=0; $i < 3; $i++) { 
				//var_dump($trucks[$i]['truck_id']);
				//obtiene los clientes por camion
				$clientes = \DB::collection('proyecto')
					->select('clients.name', 'clients.weight_delivered')
					->where('truck_id', '=', $trucks[$i]['truck_id'])
					->where('clients.weight_delivered', '>', 0)
					->orderBy('clients.weight_delivered')
					->get();

				//var_dump($clientes);
				$peso = array();
				$aux = 0;
				foreach ($clientes as $key => $row) {
					//var_dump($row);
					$peso[$aux] = $row['clients'][$aux]['weight_delivered'];
					$aux++;
				}

				array_multisort($peso, SORT_DESC, $clientes);

				
				//var_dump($clientes);
				$json .= " { \"name\":  \"".$trucks[$i]['truck_id']."\",
 									 \"children\": [ ";	
				//recorre los clientes
				//for ($j=0; $j < sizeof($clientes[1]['clients']); $j++){
 				  for ($j=0; $j < 10; $j++){
					//var_dump($clientes[1]['clients'][$j]['name']);
						//$aux = sizeof($clientes[1]['clients']);
						//if(sizeof($clientes[1]['clients'])-1 == $j) {
 				  		  if(10-1 == $j) {
						//if(3-1 == $j) {
							$json .= " {\"name\": \"".$clientes[0]['clients'][$j]['name']."\", \"size\": 743}";
    					}//cierre if
    					else {
    						$json .= " {\"name\": \"".$clientes[0]['clients'][$j]['name']."\", \"size\": 743},";	
						}//cierre else						
				}//cierre for de clientes

				if(sizeof($trucks) -1 == $i){
				//if(3-1 == $i){
					$json .= "] }";
				}
				else{
					$json .= "] },";
				}
				//var_dump($clientes);
			}

			$json .= " ] }";
			//ar_dump($json);
			$myfile = fopen("js/camiones.json", "w") or die("Unable to open file!");
			fwrite($myfile, $json);
			fclose($myfile);

		return 0;

	}//close dendogram


	//dendogram por unidades entregadas
	public function dendogram_2($trucks){

			//recorre los camiones
			$json = "{ \"name\": \"camiones\",
 						\"children\": [";

			for ($i=0; $i < sizeof($trucks); $i++) { 
 			 //for ($i=0; $i < 3; $i++) { 
				//var_dump($trucks[$i]['truck_id']);
				//obtiene los clientes por camion
				$clientes = \DB::collection('proyecto')
					->select('clients.name', 'clients.units_delivered')
					->where('truck_id', '=', $trucks[$i]['truck_id'])
					->where('clients.units_delivered', '>', 0)
					->orderBy('clients.units_delivered')
					->get();
					//var_dump($clientes);

					$peso = array();
				$aux = 0;
				foreach ($clientes as $key => $row) {
					//var_dump($row);
					$peso[$aux] = $row['clients'][$aux]['units_delivered'];
					$aux++;
				}

				array_multisort($peso, SORT_DESC, $clientes);

				//var_dump($clientes);

				//var_dump($clientes);
				$json .= " { \"name\":  \"".$trucks[$i]['truck_id']."\",
 									 \"children\": [ ";	
				//recorre los clientes
				//for ($j=0; $j < sizeof($clientes[1]['clients']); $j++){
 				  for ($j=0; $j < 10; $j++){
					//var_dump($clientes[1]['clients'][$j]['name']);
						//$aux = sizeof($clientes[1]['clients']);
						//if(sizeof($clientes[1]['clients'])-1 == $j) {
 				  		  if(10-1 == $j) {
						//if(3-1 == $j) {
							$json .= " {\"name\": \"".$clientes[0]['clients'][$j]['name']."\", \"size\": 743}";
    					}//cierre if
    					else {
    						$json .= " {\"name\": \"".$clientes[0]['clients'][$j]['name']."\", \"size\": 743},";	
						}//cierre else						
				}//cierre for de clientes

				if(sizeof($trucks) -1 == $i){
				//if(3-1 == $i){
					$json .= "] }";
				}
				else{
					$json .= "] },";
				}
				//var_dump($clientes);
			}

			$json .= " ] }";
			//ar_dump($json);
			$myfile = fopen("js/camiones2.json", "w") or die("Unable to open file!");
			fwrite($myfile, $json);
			fclose($myfile);

		return 0;

	}//close dendogram

	public function dendogram_3($trucks){

			//recorre los camiones
			$json = "{ \"name\": \"camiones\",
 						\"children\": [";

			for ($i=0; $i < sizeof($trucks); $i++) { 
 			 //for ($i=0; $i < 3; $i++) { 
				//var_dump($trucks[$i]['truck_id']);
				//obtiene los clientes por camion
				$clientes = \DB::collection('proyecto')
					->select('clients.name', 'clients.service_time')
					->where('truck_id', '=', $trucks[$i]['truck_id'])
					->where('clients.service_time', '>', 0)
					->orderBy('clients.service_time')
					->get();

				$peso = array();
				$aux = 0;
				foreach ($clientes as $key => $row) {
					//var_dump($row);
					$peso[$aux] = $row['clients'][$aux]['service_time'];
					$aux++;
				}

				array_multisort($peso, SORT_DESC, $clientes);

				//var_dump($clientes);

				//var_dump($clientes);
				$json .= " { \"name\":  \"".$trucks[$i]['truck_id']."\",
 									 \"children\": [ ";	
				//recorre los clientes
				//for ($j=0; $j < sizeof($clientes[1]['clients']); $j++){
 				  for ($j=0; $j < 5; $j++){
					//var_dump($clientes[1]['clients'][$j]['name']);
						//$aux = sizeof($clientes[1]['clients']);
						//if(sizeof($clientes[1]['clients'])-1 == $j) {
 				  		  if(5-1 == $j) {
						//if(3-1 == $j) {
							$json .= " {\"name\": \"".$clientes[0]['clients'][$j]['name']."\", \"size\": 743}";
    					}//cierre if
    					else {
    						$json .= " {\"name\": \"".$clientes[0]['clients'][$j]['name']."\", \"size\": 743},";	
						}//cierre else						
				}//cierre for de clientes

				if(sizeof($trucks) -1 == $i){
				//if(3-1 == $i){
					$json .= "] }";
				}
				else{
					$json .= "] },";
				}
				//var_dump($clientes);
			}

			$json .= " ] }";
			//ar_dump($json);
			$myfile = fopen("js/camiones3.json", "w") or die("Unable to open file!");
			fwrite($myfile, $json);
			fclose($myfile);

		return 0;

	}//close dendogram

	public function getAvgWeight($average_weight_delivered)
	{
		$weight_sum = 0;

		for ($i=0; $i < sizeof($average_weight_delivered); $i++) { 
			$temp_clients = $average_weight_delivered[$i]['clients'];
			for ($j=0; $j < sizeof($temp_clients); $j++) { 
				$weight_sum += $temp_clients[$j]['weight_delivered'];
			}
		}

		$average_weight_per_route = round($weight_sum / count($average_weight_delivered), 2);

		return $average_weight_per_route;
	}

	public function getAvgDeliveryUnits($average_deliveries)
	{
		$delivery_units_sum = 0;

		for ($i=0; $i < sizeof($average_deliveries); $i++) { 
			$temp_clients = $average_deliveries[$i]['clients'];
			//var_dump($temp_clients);
			for ($j=0; $j < sizeof($temp_clients); $j++) { 
				$delivery_units_sum += $temp_clients[$j]['units_delivered'];
			}
		}


		$average_delivery_units = round($delivery_units_sum / count($average_deliveries), 2);

		return $average_delivery_units;
	}

	public function calculateBestRoute($deliveries)
	{

		//var_dump($deliveries);
		$clients_matrix = array();
		$distance_matrix = array();
		$best_distances_matrix = array();

		//filling the clients matrix with lat/long coordinates
		foreach ($deliveries as $delivery) {
			$temp = $delivery['clients'];
			foreach ($temp as $t) {
				$clients_matrix [] = array(
					'client' => $t['name'],
					'longitude' => $t['geo']['coordinates'][0],
					'latitude' => $t['geo']['coordinates'][1]
				);
			}
		}

		//call the vincenty function to measure distances
		for ($i=1; $i < sizeof($clients_matrix); $i++) { 
			//check if lat/long coordinates are not 0
			if($clients_matrix[$i]['latitude'] != 0 || $clients_matrix[$i]['longitude'] != 0){
				$distance_matrix [] =
					$this->vincentyGreatCircleDistance(
						$clients_matrix[$i-1]['latitude'],
						$clients_matrix[$i-1]['longitude'],
						$clients_matrix[$i]['latitude'],
						$clients_matrix[$i]['longitude']
					);
					//'clientTo' => $clients_matrix[$i]['client'],
					//'clientFrom' => $clients_matrix[$i-1]['client']	
			}
		}

		$best_distances_matrix = $this->floydWarshall($distance_matrix);

		return $best_distances_matrix;

	}

	public function vincentyGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
	{
		//convert from degrees to radians
		$latFrom = deg2rad($latitudeFrom);
		$longFrom = deg2rad($longitudeFrom);
		$latTo = deg2rad($latitudeTo);
		$longTo = deg2rad($longitudeTo);

		$lonDelta = $longTo - $longFrom;
		$a = pow(cos($latTo) * sin($lonDelta), 2) +
		pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
		$b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

		$angle = atan2(sqrt($a), $b);			

		//returns result in meters
		return $angle * $earthRadius;
	}

	public function floydWarshall($distance_matrix)
	{	
		$floydWarshall_matrix = array();
		//replicating the distance matrix
		for ($i=0; $i < sizeof($distance_matrix); $i++) { 
			$floydWarshall_matrix [] = $distance_matrix[$i]; 
		}

		var_dump($floydWarshall_matrix);

		for ($k=0; $k < sizeof($floydWarshall_matrix); $k++) { 
			for ($i=0; $i < sizeof($floydWarshall_matrix); $i++) { 
				for ($j=0; $j < sizeof($floydWarshall_matrix); $j++) { 
					$temp = $floydWarshall_matrix[$i][$k] + $floydWarshall_matrix[$k][$j];
					if ($temp < $floydWarshall_matrix[$i][$j]) {
						$floydWarshall_matrix[$i][$j] = $temp;
					}
				}
			}
		}

		return $floydWarshall_matrix;

	}

}
