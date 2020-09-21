<?php

namespace App\Src;

class Database
{

  private $period;

  private $currentTemperature;

  private $data = array();

  private $temperatures = array();

  private $currentTendency;

  public $aberrations = array();

  private $switch = 0;

  public function temperatureValidation($temperature)
  {
    if (is_numeric($temperature) && is_float((floatval($temperature)))) {
      $this->currentTemperature = $temperature;
      return true;
    }
    return false;
  }

  public function temperatureAnalysis()
  {
    array_push($this->temperatures, $this->currentTemperature);
    $count = count($this->temperatures);
    if ($count < $this->period) {
      $this->data[$count]["-g"] = "nan";
      $this->data[$count]["-r"] = "nan";
      $this->data[$count]["-s"] = "nan";
    } elseif ($count === $this->period) {
      $this->data[$count]["-g"] = "nan";
      $this->data[$count]["-r"] = "nan";
      $this->data[$count]["-s"] = $this->standardDeviation();
    } else {
      $this->data[$count]["-g"] = $this->temp_inc_avg();
      $this->data[$count]["-r"] = $this->rel_temp_evo();
      $this->data[$count]["-s"] = $this->standardDeviation();
      $this->checkTendency($count);
      $this->checkAberrations($count);
    }
  }

  public function getData($key = false)
  {
    if (!$key) {
      return $this->data;
    }
    return $this->data[$key];
  }

  public function formatData($data = false)
  {
    if (!$data) {
      $data = $this->data[count($this->temperatures)];
    }
    $formatedData = "-g : " . $data["-g"] . "     -r : " . $data["-r"] . "     -s : " . $data["-s"];
    if (isset($data['a switch occurs'])) {
      $formatedData .= "     a switch occurs";
    }
    return $formatedData;
  }

  public function getPeriod()
  {
    return $this->period;
  }

  public function setPeriod($period)
  {
    $this->period = $period;
  }

  public function getCurrentTemperature()
  {
    return $this->currentTemperature;
  }

  public function getSwitch()
  {
    return $this->switch;
  }

  private function standardDeviation()
  {
    $temp_results = [];
    $total = 0;
    $total2 = 0;
    $count = count($this->temperatures);

    // avg of all temps on period
    for ($i = 1; $i <= $this->period; $i++) {
      $total += $this->temperatures[$count - $i];
    }
    $avg1 = $total / $this->period;

    // x - avg = y -> y² = z (each temp minus avg -> squared = result needed for step 3
    for ($i = 1; $i <= $this->period; $i++) {
      array_push($temp_results, pow($this->temperatures[$count - $i] - $avg1, 2));
    }

    // avg of all results from previous step
    foreach ($temp_results as $result) {
      $total2 += $result;
    }
    $avg2 = $total2 / $this->period;

    // root of previous result
    return round(sqrt($avg2), 2);
  }

  private function temp_inc_avg()
  {

    $count = count($this->temperatures);
    $temp_results = 0;
    // difference between all values on last period
    for ($i = 1; $i <= $this->period; $i++) {
      $result = $this->temperatures[$count - $i] - $this->temperatures[$count - ($i + 1)];
      if ($result >= 0) {
        $temp_results += $result;
      }
    }
    return round($temp_results / $this->period, 2);
  }

  private function rel_temp_evo()
  {

    $count = count($this->temperatures);
    // get the last temperature
    $last_temp_recorded = $this->temperatures[$count - 1];
    // get the temperature from the last period
    $previous_temp = $this->temperatures[$count - $this->period - 1];
    // get the difference
    $temp_result = $last_temp_recorded - $previous_temp;
    // result in % for the diff between both
    $result = ($temp_result / $previous_temp) * 100;
    return round($result) . "%";
  }

  private function checkTendency($count)
  {
    if (!isset($this->currentTendency)) {
      $this->currentTendency = $this->get_sign($this->data[$count]["-r"]);
    } else {
      if ($this->currentTendency !== $this->get_sign($this->data[$count]["-r"])) {
        $this->data[$count]["a switch occurs"] = true;
        $this->currentTendency = $this->get_sign($this->data[$count]["-r"]);
        $this->switch += 1;
      }
    }
  }

  private function get_sign($number)
  {
    return ( $number >= 0 ) ? "+" : "-"; 
  }

  private function checkAberrations($count)
  {
    $diff = $this->temperatures[$count-1] - $this->currentTemperature;
    $this->aberrations[$count] = $diff - $this->data[$count]["-g"]; 
  }
}
