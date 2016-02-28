<?php

function getBusCriteria($num_of_seats)
{
	if ($num_of_seats == '6') {
		$bus_query['fare_field'] = 'sienna_fare';
		$bus_query['seating_criterial'] = "seating_arrangement = '6'";
	} elseif ($num_of_seats == 11) {
		$bus_query['fare_field'] = 'executive_fare';
		$bus_query['seating_criterial'] = "seating_arrangement = '11'";
	} elseif ($num_of_seats < 20) {
		$bus_query['fare_field'] = 'hiace_fare';
		$bus_query['seating_criterial'] = "(seating_arrangement = '14' OR seating_arrangement = '15')";
	} else {
		$bus_query['fare_field'] = 'luxury_fare';
		$bus_query['seating_criterial'] = "seating_arrangement > '20'";
	}
	return $bus_query;
}

?>