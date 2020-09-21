<?php

function standardDeviation($data , $period){
    $temp_results = [];
    $total = 0;
    $total2 = 0;
    $count = count($data);

    // avg of all temps on period
    for($i = 1; $i <= $period; $i++){
        $total += $data[$count-$i];
    }
    $avg1 = $total / $period;

    // x - avg = y -> y² = z (each temp minus avg -> squared = result needed for step 3
    for($i = 1; $i <= $period; $i++){
        array_push($temp_results, pow($data[$count-$i] - $avg1, 2));
    }

    // avg of all results from previous step
    foreach($temp_results as $result){
        $total2 += $result;
    }
    $avg2 = $total2 / $period;

    // root of previous result
    return round(sqrt($avg2),2);
}

//standardDeviation([27.7, 31.0, 32.7, 34.7, 35.9, 37.4, 38.2], 7);

function temp_inc_avg($data, $period) {

    $count = count($data);
    $temp_results = 0;
    // difference between all values on last period
    for($i = 1; $i <= $period; $i++){
        $result = $data[$count-$i] - $data[$count-($i+1)];
        if ($result >= 0){
            $temp_results += $result;
        }
    }
    return round($temp_results / $period , 2);
}

//temp_inc_avg([27.7, 31.0, 32.7, 34.7, 35.9, 37.4, 38.2, 39.5, 40.3], 7);

function rel_temp_evo($data, $period) {

    $count = count($data);
    // get the last temperature
    $last_temp_recorded = $data[$count-1];
    // get the temperature from the last period
    $previous_temp = $data[$count-$period-1];
    // get the difference
    $temp_result = $last_temp_recorded - $previous_temp;
    // result in % for the diff between both
    $result = ($temp_result/$previous_temp) * 100;
    return round($result) . "%\n";
}

//rel_temp_evo([27.7, 31.0, 32.7, 34.7, 35.9, 37.4, 38.2, 39.5], 7);
