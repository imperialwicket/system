<?php
/**
 * URL writer.  Uses rules to construct pretty URLs for use
 * by the system and especially the theme's template engine
 * 
 * @package Habari
 */
class URLWriter extends Singleton {
  private $rules= null; // static collection of rules (pulled from RewriteController)
 
  /**
   * Enables singleton working properly
   * 
   * @see singleton.php
   */
  static public function instance() {
    return parent::instance(get_class());
  }

 
  /**
   * A simple caching mechanism to avoid reloading rule array
   */
  private function load_rules() {
    if (URLWriter::instance()->rules != NULL)
      return;
    URLWriter::instance()->rules= RewriteRules::get_active();
  }

  /** 
   * Builds the required pretty URL given a supplied
   * rule name and a set of placeholder replacement
   * values.
   * 
   * <code>
   * URLWriter::build_url('display_posts_by_date', 
   *  array('year'=>'2000'
   *    , 'month'=>'05'
   *    , 'day'=>'01');
   * </code>
   * 
   * @param rule  string identifier for the rule which would build the URL
   * @param args  (optional) array of placeholder replacement values
   */
  static public function build_url($rule_name, $args= array()) {
    $writer= URLWriter::instance();
    $writer->load_rules();
    if (isset($writer->rules[$rule_name])) {
      $rule= $writer->rules[$rule_name];
      $url= $rule->build_str;
      foreach ($rule->named_args as $replace) {
        $url= str_replace('{$' . $replace . '}', $args[$replace], $url);
        /* 
         * Remove from the argument list so we can append 
         * any outlier args as query string args
         */
        unset($args[$replace]);
      }
      /*
       * OK, now append any outliers passed in to the function
       * as query string arguments
       */
      if (count($args) > 0) {
        $url.= '?';
        foreach ($args as $key=>$value)
          $url .= $key . '=' . $value;
      }
      return Controller::get_base_url() . $url;
    }   
  }
}
?>
