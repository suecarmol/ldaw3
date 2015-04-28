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
		//
		$deliveries = \DB::collection('proyecto')
		->where('clients.cost', '=', 'null')
		->orderBy('clients.arrival_time')
		->get();

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

		//var_dump($deliveries);
		//$x = $this->calculateBestRoute($deliveries);
		//var_dump($x);
		return view('deliveries.index')
		->with('average_delivery_units', $average_delivery_units)
		->with('average_weight_per_route', $average_weight_per_route)
		->with('deliveries_out_of_route', $deliveries_out_of_route)
		->with('trucks_average_capacity', $trucks_average_capacity);	
	}

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
