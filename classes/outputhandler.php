<?php
/**
 * Default handler which responds to display requests
 */
class OutputHandler extends ActionHandler {
  public $theme= null;          // The Theme object to use for display
  
  /**
   * Default constructor
   */
  public function __construct() {
    $this->theme= new Theme();
  }
}
