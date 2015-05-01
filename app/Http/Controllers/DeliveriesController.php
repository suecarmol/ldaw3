<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

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

		$trucks_average_capacity = \DB::collection('proyecto')
		->where('capacity', '>', 0)
		->avg('capacity');

		//$x = $this->calculateBestRoute($deliveries);

		/********
		**** CHARTS
		********/

		//camiones 
		//selecciona solamente los camiones
		$trucks = \DB::collection('proyecto')
		->select('truck_id')
		->where('capacity', '>', 0)
		->get();

		$clientes = \DB::collection('proyecto')
		->select('clients.name', 'clients.units_delivered')
		->where('clients.units_delivered', '>', 0)
		->orderBy('clients.units_delivered')
		->get();
		
		//$this->dendogram($trucks);
		$word = $this->word_cloud($clientes);
		//var_dump($clientes);
		//var_dump($trucks);
		$nombres = array();
		$unidades = array();

		for($i=0; $i < 15; $i++){
			$nombres[$i] = $word[$i]['text'];
			$unidades[$i] = $word[$i]['size'];
		}

		//var_dump($nombres);
		//var_dump($unidades);
		//var_dump($word[0]['text']);


		/********
		**** SUGGESTIONS
		********/

		$this->createSuggestions();

		return view('deliveries.index')
		->with('average_delivery_units', $average_delivery_units)
		->with('average_weight_per_route', $average_weight_per_route)
		->with('deliveries_out_of_route', $deliveries_out_of_route)
		->with('trucks_average_capacity', $trucks_average_capacity)
		->with('nombres', $nombres)
		->with('unidades', $unidades);
	}

	public function createSuggestions()
	{
		$routes = Delivery::where('clients.arrival_time', 'regexp', '/^2014-06-26*./')->get();

		for ($i=0; $i < sizeof($routes); $i++) { 
			
		}


		//var_dump($routes);

	}


	public function word_cloud($clientes){
			//var_dump($clientes);	

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

	public function dendogram($trucks){

			//recorre los camiones
			$json = "{ \"name\": \"camiones\",
 						\"children\": [";

			for ($i=0; $i < sizeof($trucks); $i++) { 
 			 //for ($i=0; $i < 3; $i++) { 
				//var_dump($trucks[$i]['truck_id']);
				//obtiene los clientes por camion
				$clientes = \DB::collection('proyecto')
					->select('clients.name')
					->where('truck_id', '=', $trucks[$i]['truck_id'])
					//->where('clients.weight_delivered', '>', 0)
					->get();

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
							$json .= " {\"name\": \"".$clientes[1]['clients'][$j]['name']."\", \"size\": 743}";
    					}//cierre if
    					else {
    						$json .= " {\"name\": \"".$clientes[1]['clients'][$j]['name']."\", \"size\": 743},";	
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
