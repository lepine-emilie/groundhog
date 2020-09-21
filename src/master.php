<?php

namespace App\Src;

abstract class Master
{
  protected $color = array();

  function __construct()
  {
    $this->color['none'] = "";
    $this->color['stop'] = "\033[0m";
    $this->color['yellow'] = "\033[33m";
    $this->color['red'] = "\033[31m";
    $this->color['blue'] = "\033[34m";
  }

  protected function echo($content, $color = "none")
  {
    echo $this->color[$color] . $content . $this->color['stop'] . PHP_EOL . PHP_EOL;
  }
}

?>