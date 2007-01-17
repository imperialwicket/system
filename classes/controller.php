<?php
/**
 * Class which handles incoming requests and drives the 
 * MVC strategy for building the model and assigning to 
 * a view.
 * 
 * @package habari 
 */
class Controller extends Singleton {
  public $base_url= '';        // base url for site
  private $stub= '';            // stub supplied by rewriter
  private $action= '';          // action name (string)
  private $handler= null;       // the action handler object

  /**
   * Enables singleton working properly
   * 
   * @see singleton.php
   */
  static public function instance() {
    return parent::instance(get_class());
  }

  /**
   * Returns the base URL
   *
   * @return string base URL
   */
  public function get_base_url() {
    return Controller::instance()->base_url;
  }

  /**
   * Returns the stub in its entirety
   *
   * @return  string  the URL incoming stub
   */
  public function get_stub() {
    return Controller::instance()->stub;
  }

  /**
   * Returns the action
   *
   * @return  string name of action
   */
  public function get_action() {
    return Controller::instance()->action;
  }
  
  /**
   * Returns the action handler
   * 
   * @return  object  handler object
   */
  public function get_handler() {
    return Controller::instance()->handler;
  }

  /**
   * Parses the requested URL.  Automatically 
   * translates URLs coming in from mod_rewrite and parses
   * out any action and parameters in the slug.
   */
  static public function parse_request() {
    /* Local scope variable caching */
    $controller= Controller::instance();

    /* Grab the base URL from the DB */
    $controller->base_url= Options::get('base_url');

    /* Start with the entire URL coming from web server... */
    $start_url= ( isset($_SERVER['REQUEST_URI']) 
      ? $_SERVER['REQUEST_URI'] 
      : $_SERVER['SCRIPT_NAME'] . 
        ( isset($_SERVER['PATH_INFO']) 
        ? $_SERVER['PATH_INFO'] 
        : '') . 
        ( (isset($_SERVER['QUERY_STRING']) && ($_SERVER['QUERY_STRING'] != '')) 
          ? '?' . $_SERVER['QUERY_STRING'] 
          : ''));
    
    /* Strip out the base URL from the requested URL */
    $start_url= str_replace($controller->base_url, '', $start_url);
    
    /* Trim off any trailing slashes */
    $start_url= rtrim($start_url, '/');

    /* Remove the querystring from the URL */
    if ( strpos($start_url, '?') !== FALSE )
      list($start_url, )= explode('?', $start_url);

    $controller->stub= $start_url;

    /* Grab the URL filtering rules from DB */
    $rules= Controller::get_rules();

    /* 
     * Run the stub through the regex matcher
     */
    $pattern_matches= array();
    foreach ($rules as $rule) {
      if ( 1 == preg_match(
                $rule->parse_regex
                , $controller->stub
                , $pattern_matches) ) {

        /* OK, we have a matching rule.  Set the action and create a handler */
        $controller->action= $rule->action;
        $controller->handler= new $rule->handler();

        /* Insert the regexed submatches as the named parameters */
        $submatches_count= count($pattern_matches);
        $controller->handler->handler_vars['entire_match']= $pattern_matches[0]; // The entire matched string is returned at index 0
        for ($j=1;$j<$submatches_count;++$j) {
          $controller->action->handler_vars[$rule->named_args[($j - 1)]]= $pattern_matches[$j];
        }
        
        /* Also, we musn't forget to add the GET and POST vars into the action's settings array */
        $controller->action->settings= array_merge($controller->action->settings, $_GET, $_POST);
        break;
     }
    }
  }

  /**
   * Handle the requested action by firing off the matched action(s)
   */
  public function dispatch_request() {
    /* OK, set the wheels in motion... */
    Controller::instance()->action->act();
  }

  /**
   * Return a set of active URL filtering rules
   *
   * 
   * @note  We return the rules, as opposed to storing the rules as
   *        as either a global variable or a class member because once
   *        processed, the rules aren't valuable anymore and should go
   *        out of scope and therefore the memory gets released.
   * @todo  Store filters in database so users can freely edit
   */
  private function get_rules() {
   /**
     * Below is a sample set of regular expressions which 
     * are used to match against the incoming stub.
     * Rules are matched from top to bottom here, 
     * as this is likely to be the order in which 
     * pages naturally will be queried against the app
     * but the order of rules could easily be set in DB
     */
    $rules= array();
    $rules['display_posts_at_page']= array(
        'parse_regex'=>'/^page\/([\d]+)[\/]{0,1}$/i'
      , 'build_str'=>'page/{$page}'
      , 'handler'=>'UserThemeHandler'
      , 'action'=>'display_posts'
      , 'named_args'=>array('page')
    );
    $rules['display_posts_by_date']= array(
        'parse_regex'=>'/([1,2]{1}[\d]{3})\/([\d]{2})\/([\d]{2})[\/]{0,1}$/'
      , 'build_str'=>'{$year}/{$month}/{$day}'
      , 'handler'=>'UserThemeHandler'
      , 'action'=>'DisplayPostsByDate'
      , 'named_args'=>array('year','month','day')
    );
    $rules['display_posts_by_month']= array(
        'parse_regex'=>'/([1,2]{1}[\d]{3})\/([\d]{2})[\/]{0,1}$/' 
      , 'build_str'=>'{$year}/{$month}'
      , 'handler'=>'UserThemeHandler'
      , 'action'=>'DisplayPostsByMonth'
      , 'named_args'=>array('year','month')
    );
    $rules['display_posts_by_year']= array(
        'parse_regex'=>'/([1,2]{1}[\d]{3})[\/]{0,1}$/'
      , 'build_str'=>'{$year}'
      , 'handler'=>'UserThemeHandler'
      , 'action'=>'DisplayPostsByYear'
      , 'named_args'=>array('year')
    );
    $rules['display_feed_by_type']= array(
        'parse_regex'=>'/^feed\/(atom|rs[sd])[\/]{0,1}$/i'
      , 'build_str'=>'feed/{$feed_type}'
      , 'handler'=>'UserThemeHandler'
      , 'action'=>'DisplayFeed'
      , 'named_args'=>array('feed_type')
    );
    $rules['display_posts_by_tag']= array(
        'parse_regex'=>'/^tag\/([^\/]*)[\/]{0,1}$/i'
      , 'build_str'=>'tag/{$tag}'
      , 'handler'=>'UserThemeHandler'
      , 'action'=>'DisplayPostsByTag'
      , 'named_args'=>array('tag')
    );
    $rules['display_admin_page']= array(
        'parse_regex'=>'/^admin\/([^\/]*)[\/]{0,1}$/i'
      , 'build_str'=>'admin/{$action}'
      , 'handler'=>'UserThemeHandler'
      , 'action'=>'DisplayAdminPage'
      , 'named_args'=>array('action')
    );
    $rules['display_user_page']= array(
        'parse_regex'=>'/^user\/([^\/]*)[\/]{0,1}$/i'
      , 'build_str'=>'user/{$action}'
      , 'handler'=>'UserThemeHandler'
      , 'action'=>'DisplayUserPage'
      , 'named_args'=>array('action')
    );
    $rules['display_posts_by_slug']= array(
        'parse_regex'=>'/([^\/]+)[\/]{0,1}$/i'
      , 'build_str'=>'{$slug}'
      , 'handler'=>'UserThemeHandler'
      , 'action'=>'DisplayPostBySlug'
      , 'named_args'=>array('slug')
    );
    $rules['index']= array(
        'parse_regex'=>'//'
      , 'build_str'=>''
      , 'handler'=>'UserThemeHandler'
      , 'action'=>'DisplayPosts'
      , 'named_args'=>array()
    );
    $rule_classes= array();
    foreach ($rules as $key=>$rule) {
      $current_rule= new RewriteRule();
      foreach ($rule as $property=>$value)
        $current_rule->$property= $value;
      $rule_classes[]= $current_rule;
    }
    return $rule_classes;
  }
}

/**
 * Helper class to encapsulate rewrite rule data
 */
class RewriteRule {
  public $name;                 // name of the rule
  public $parse_regex;          // regex expression for incoming matching
  public $build_str;            // string with optional placeholders for outputting URL
  public $handler;        // name of action handler class
  public $action;               // name of action that handler should execute
  public $named_args= array();  //
}
?>

