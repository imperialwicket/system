<?php
/**
 * Singleton base class for subclassing generic singleton pattern
 * classes
 */
class Singleton {
  static private $instance= NULL; // Single instance of class available.
  
  /**
   * Returns the single shared static instance variable
   * which facilitates the Singleton pattern
   *
   * @note  each subclass should implement an instance() method which
   * passes the class name to the parent::instance() function
   * @return object instance
   */
  static public function instance($class) {
    if (self::$instance == NULL) {
      self::$instance= new $class();
    }
    return self::$instance;
  }

  /** Prevent instance construction and cloning (copying of object instance) */
  protected final function __construct() {}
  private final function __clone() {}
}
?>
