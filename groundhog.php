<?php

namespace App;

use App\Src\Database;
use App\Src\Master;

require './src/master.php';
require './src/database.php';

class Groundhog extends Master
{

  private $db;

  private $inputReader = true;

  public function __construct($argv, $argc)
  {
    parent::__construct();
    echo PHP_EOL;
    if ($argc !== 2 || !is_integer(intval($argv[1])) || intval($argv[1]) <= 1) {
      $this->echo("SYNOPSIS : php groundhog.php period(int > 1)", 'red');
      exit();
    }
    $this->db = new Database();
    $this->db->setPeriod(intval($argv[1]));
    $this->echo("period : " . $this->db->getPeriod() . " days", "blue");
    $this->temperatureReader();
  }

  private function temperatureReader()
  {
    while ($this->inputReader) {
      echo $this->color["yellow"];
      $input = readline("temperature : ");
      if ($input === "STOP") {
        $this->echo("Global tendency switched " . $this->db->getSwitch() . " times", "blue");
        $this->inputReader = false;
        print_r($this->db->aberrations);
      } else if (!$this->db->temperatureValidation($input)) {
        $this->echo("Float required", 'red');
      } else {
        $this->db->temperatureAnalysis();
        $formatedData = $this->db->formatData();
        $this->echo($formatedData);
      }
    }
  }
}

$groundhog = new Groundhog($argv, $argc);

?>