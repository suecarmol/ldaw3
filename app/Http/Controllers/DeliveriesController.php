<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\PeticionRequest;

use Request;

use App\Delivery;

class DeliveriesController extends Controller {

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
		->where('clients.cost', '>', 0)
		->orderBy('clients.arrival_time')
		->take(2)
		->get();

		$floyd = $this->calculateBestRoute($deliveries);
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

		$trucks_service_time = \DB::collection('proyecto')
		->where('clients.service_time', '>', 0)
		->get();
		$trucks_average_service_time = $this->getAvgServiceTime($trucks_service_time);

		/********
		**** CHARTS
		********/

		//var_dump($bubble[1]['size']);

		//ínception

		$inception_info = \DB::collection('proyecto')
		->select('truck_id', 'route_id', 'clients.name')
		->where('clients.units_delivered', '>', 0)
		->orderBy('clients.units_delivered')
		->get();

		//var_dump($inception_info);
		//como el archivo json ya existe comentare la linea para que no se genere el archivo cada ves que se haga refresh
		$this->inception($inception_info);

		/********
		**** SUGGESTIONS
		********/

		$suggestions = $this->createSuggestions();
		//var_dump($suggestions);

			//llama al metodo voronoi
		$this->voronoi($inception_info);

		return view('deliveries.index')
		->with('average_delivery_units', $average_delivery_units)
		->with('average_weight_per_route', $average_weight_per_route)
		->with('deliveries_out_of_route', $deliveries_out_of_route)
		->with('trucks_average_capacity', $trucks_average_capacity)
		->with('trucks_average_grade', $trucks_average_grade)
		->with('trucks_average_service_time', $trucks_average_service_time)
		->with('suggestions', $suggestions)
		->with('floyd', $floyd);
	}

	public function createSuggestions()
	{

		$date = '2014-05-12 08';
		//not using this right now
		$hour = '';
		$routes = Delivery::where('clients.arrival_time', 'regexp', '/^'.$date.'*./')
		->get();

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
		var_dump($suggestions);

	}



	public function getSuggestions_dinamicas(PeticionRequest $request){
		$date = $request->get('date');
		//var_dump($date);
		//$date = '2014-05-12 08';
		//var_dump($date);
		//var_dump($date);
		//not using this right now
		$hour = '';
		$routes = Delivery::where('clients.arrival_time', 'regexp', '/^'.$date.'*./')
		->get();

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
					->where('capacity', '>', 0)
					->groupBy('company_name')
					->distinct()
					->get();				
			
			$company_data = array();		

			for($i = 0; $i < sizeof($compania); $i++){

				$trucks = \DB::collection('proyecto')
				->select('truck_id')
				->where('company_name', '=', $compania[$i]['_id']['company_name'])
				->get();

				$trucks_data = array();

				for($j = 0; $j < sizeof($trucks); $j++){

					$clients = \DB::collection('proyecto')
					->select('clients.name', 'clients.units_delivered')
					->where('truck_id', '=', $trucks[$j]['truck_id'])
					->get();

					$clients_data = array();

					for($k = 0; $k < sizeof($clients); $k++){
						if(sizeof($clients[$k]) > 1){

							$clients_array = $clients[$k]['clients'];

							for($l = 0; $l < 10; $l++)
							{
								array_push($clients_data, array(
									'name' => $clients_array[$l]['name'],
									'size' => $clients_array[$l]['units_delivered']
								));
							} //for clients_array

						} //for clients

					}

					array_push($trucks_data, array(
							'name' => $trucks[$j]['truck_id'],
							'children' => $clients_data
						));

				}// for trucks

				array_push($company_data, array(
					'name'=> $compania[$i]['company_name'],
					'children' => $trucks_data
				));

			}	//for companies	

			$json = "{ \"name\": \"ldaw3\", \"children\": ";
			$company_data = json_encode($company_data);
			$json .= $company_data . "}";

			//print_r($json);

			$myfile = fopen("js/inception_bk2.json", "w") or die("Unable to open file!");
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
				->groupBy('route_id')
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
				->groupBy('truck_id')
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

	public function dendogram($trucks){
			
		//compania -> Camiones -> Rutas -> Clientes
		//recorre los camiones
		//var_dump(sizeof($inception[0]['clients']));
			$compania = \DB::collection('proyecto')
					->select('company_name')
					->where('capacity', '>', 0)
					->groupBy('company_name')
					->distinct()
					->get();

			$company_data = array();

			for($i = 0; $i < sizeof($compania); $i++){

				$compania_aux = $compania[$i]['company_name'];

				$trucks = \DB::collection('proyecto')
				->select('truck_id')
				->where('company_name', '=', $compania[$i]['company_name'])
				->distinct()
				->get();

				$trucks_data = array();

				for($j = 0; $j < sizeof($trucks); $j++){

					$clients = \DB::collection('proyecto')
					->select('clients.name', 'clients.units_delivered')
					->where('truck_id', '=', $trucks[$j])
					->get();

					$clients_data = array();

					for($k = 0; $k < sizeof($clients); $k++){
						if(sizeof($clients[$k]) > 1){

							$clients_array = $clients[$k]['clients'];

							for($l = 0; $l < 10; $l++)
							{
								array_push($clients_data, array(
									'name' => $clients_array[$l]['name'],
									'size' => $clients_array[$l]['units_delivered']
								));
							} //for clients_array

						} //for clients
						
					}

						array_push($trucks_data, array(
							'name' => $trucks[$j],
							'children' => $clients_data
						));

				}// for trucks

				array_push($company_data, array(
					'name'=> $compania[$i]['company_name'],
					'children' => $trucks_data
				));

			}	//for companies	

			$json = "{ \"name\": \"ldaw3\", \"children\": ";
			/*
			array_push($json, array(
					'name' => 'ldaw3',
					'children' => $company_data
				));*/
			$company_data = json_encode($company_data);

			$json .= $company_data . "}";

			//print_r($json);

			$myfile = fopen("js/camiones_bk.json", "w") or die("Unable to open file!");
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

	public function getAvgServiceTime($trucks_service_time)
	{
		$service_time_sum = 0;

		for ($i=0; $i < sizeof($trucks_service_time); $i++) { 
			$temp_clients = $trucks_service_time[$i]['clients'];
			//var_dump($temp_clients);
			for ($j=0; $j < sizeof($temp_clients); $j++) { 
				$service_time_sum += $temp_clients[$j]['service_time'];
			}
		}


		$average_service_time = round(($service_time_sum / count($trucks_service_time))/3600, 2);

		return $average_service_time;
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
				for($j = 0; $j < sizeof($clients_matrix); $j++)
				{
					if($i == $j)
					{
						$distance_matrix[$i][$j] = array(
							'distance' => 0,
							'clientFrom' => $clients_matrix[$i]['client'],
							'clientTo' => $clients_matrix[$j]['client']
						);	
					}
					else
					{
						$distance_matrix [$i][$j] = array(
							'distance' => $this->vincentyGreatCircleDistance(
								$clients_matrix[$j]['latitude'],
								$clients_matrix[$j]['longitude'],
								$clients_matrix[$i]['latitude'],
								$clients_matrix[$i]['longitude']
							),
							'clientFrom' => $clients_matrix[$i]['client'],
							'clientTo' => $clients_matrix[$j]['client']

						);
					}
				}
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

		for ($k=1; $k < sizeof($distance_matrix); $k++) { 
			for ($i=1; $i < sizeof($distance_matrix); $i++) { 
				for ($j=0; $j < sizeof($distance_matrix); $j++) { 
					$temp = $distance_matrix[$i][$k]['distance'] + $distance_matrix[$k][$j]['distance'];
					//print_r($temp . " < " . $distance_matrix[$i][$j]['distance'] . "? \n");
					if ($temp < $distance_matrix[$i][$j]['distance']) {
						$distance_matrix[$i][$j]['distance'] = $temp;
					}
				}
			}
		}

		return $distance_matrix;

	}
	/*
	public function voronoi(){

		$clientes = \DB::collection('proyecto')
		->select('clients.geo.coordinates')
		->where('clients.units_delivered', '>', 0)
		//->orderBy('clients.units_delivered')
		->get();
		//var_dump($clientes);


		for($i = 0; $i<sizeof($clientes);$i++){
			$temp = $clientes[$i]['clients'];
			for($j = 0; $j<sizeof($temp);$j++){
				$coordenadas [] = array(
					//'client' => $t['name'],
					'longitude' => $temp[$j]['geo']['coordinates'][0],
					'latitude' => $temp[$j]['geo']['coordinates'][1]
				);
			}
		}//cierre for 
		$json = "{\"type\": \"FeatureCollection\", \n \"features\": [";
		//var_dump($json);
	
		//for($i = 0; $i<sizeof($coordenadas);$i++){
		  for($i = 0; $i<50;$i++){
			//if($i == sizeof($coordenadas)-1)
			if($i == 50-1)
				$json .= "{ \"type\": \"Feature\", \"id\": ". $i . ", \"properties\": { }, \"geometry\": { \"type\": \"Point\", \"coordinates\": [ " . $coordenadas[$i]['longitude'].",". $coordenadas[$i]['latitude'] ."] } }\n";
 			else
 				$json .= "{ \"type\": \"Feature\", \"id\": ". $i . ", \"properties\": { }, \"geometry\": { \"type\": \"Point\", \"coordinates\": [ " . $coordenadas[$i]['longitude'].",". $coordenadas[$i]['latitude'] ."] } },\n";
 			
		}

		$json .= "\n] }" ;

		$myfile = fopen("js/voronoi.geojson", "w") or die("Unable to open file!");
			fwrite($myfile, $json);
		fclose($myfile);
		//var_dump($json);

	}*/
	public function voronoi($inception){
		//compania -> Camiones -> Rutas -> Clientes
		//recorre los camiones
		//var_dump(sizeof($inception[0]['clients']));
			$compania = \DB::collection('proyecto')
					->select('company_name')
					->where('capacity', '>', 0)
					->groupBy('company_name')
					->distinct()
					->get();

			//print_r($compania);				
			
			$company_data = array();		

			for($i = 0; $i < sizeof($compania); $i++){
				$trucks = \DB::collection('proyecto')
				->select('truck_id')
				->where('company_name', '=', $compania[$i]['company_name'])
				->get();

				$trucks_data = array();

				for($j = 0; $j < sizeof($trucks); $j++){

					$clients = \DB::collection('proyecto')
					->select('clients.name', 'clients.geo.coordinates')
					->where('truck_id', '=', $trucks[$j]['truck_id'])
					->get();

					$clients_data = array();

					//print_r(json_encode($clients));

					//var_dump(sizeof($clients[0]));

					//var_dump($clients[0]);

					for($k = 1; $k < sizeof($clients); $k++){
						if(sizeof($clients[$k]) > 1){

							$clients_array = $clients[$k]['clients'];

							//print_r($clients_array);

							for($l = 0; $l < sizeof($clients_array); $l++)
							{
								array_push($clients_data, array(
									'name' => $clients_array[$l]['name'],
									'size' => $clients_array[$l]['geo']
								));
							} //for clients_array

						} //for clients

						//array_push($trucks_data, array(
							//'name' => $trucks[$j]['truck_id'],
							//'children' => $clients_data
						//));
					}

				}// for trucks

				array_push($company_data, array(
					'name'=> $compania[$i]['company_name'],
					'children' => $clients_data
				));

			}	//for companies	


			//var_dump($company_data);
			//$json = "{ \"name\": \"ldaw3\", \"children\": ";
			$json = "id,latitude,longitude,name,type,color\n";
			$cont = 1;
			for($i = 0; $i<sizeof($company_data);$i++){
				$temp = $company_data[$i]['children'];
				for($j = 0; $j<sizeof($temp);$j++){
					//var_dump($temp[$j]['size']['coordinates'][0]);
					//var_dump($temp[$j]['name']);
					//var_dump($company_data[$i]['name']);
					if($i==0)
						$json .= "".$cont.",".$temp[$j]['size']['coordinates'][1].",".$temp[$j]['size']['coordinates'][0].",".$temp[$j]['name'].",". $company_data[$i]['name'].","."00529d\n";
					if($i==1)
						$json .= "".$cont.",".$temp[$j]['size']['coordinates'][1].",".$temp[$j]['size']['coordinates'][0].",".$temp[$j]['name'].",". $company_data[$i]['name'].","."BEF781\n";
					if($i==2)
						$json .= "".$cont.",".$temp[$j]['size']['coordinates'][1].",".$temp[$j]['size']['coordinates'][0].",".$temp[$j]['name'].",". $company_data[$i]['name'].","."F7819F\n";
					if($i==3)
						$json .= "".$cont.",".$temp[$j]['size']['coordinates'][1].",".$temp[$j]['size']['coordinates'][0].",".$temp[$j]['name'].",". $company_data[$i]['name'].","."FE2E2E\n";
					if($i==4)
						$json .= "".$cont.",".$temp[$j]['size']['coordinates'][1].",".$temp[$j]['size']['coordinates'][0].",".$temp[$j]['name'].",". $company_data[$i]['name'].","."FFFF00\n";
					
					$cont += 1;
				}
			}
			//var_dump($json);
			/*
			array_push($json, array(
					'name' => 'ldaw3',
					'children' => $company_data
				));*/
			$company_data = json_encode($company_data);

			//$json .= $company_data . "}";

			//print_r($json);

			$myfile = fopen("js/voronoi.csv", "w") or die("Unable to open file!");
			fwrite($myfile, $json);
			fclose($myfile);

		return 0;

	}//Cierre inception

}
