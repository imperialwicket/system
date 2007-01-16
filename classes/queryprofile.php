<?php
/**
 * Class to assist in profiling queries
 *
 * @package Habari
 */
class QueryProfile {
  public $start_time;   // time that query started execution
  public $end_time;     // time that query ended execution
  public $query_text;   // SQL text
  public $error_text;   // Error message, if any

  /**
   * Constructor for the query profile.  Automatically sets the 
   * start time for the query
   *
   * @param query SQL being executed
   */
  public function __construct($query) {
    $this->query_text= $query;
  }

  public function start() {
    $this->start_time= $this->get_time_in_microseconds();
  }

  public function stop() {
    $this->end_time= $this->get_time_in_microseconds();
  }

  public function __get($name) {
    switch ($name) {
      case 'total_time':
        return $this->end_time - $this->start_time;
      default:
        return $this->$name;
    }
  }

  /**
   * Returns an integer representing the current time
   * in microseconds from Epoch
   *
   * @return  int the number of microseconds since epoch.
   */
  static public function get_time_in_microseconds() {
    list($sec, $ms)= explode('.', microtime(true));
    return (1000*$sec + $ms);
  }
  
}
