<?php
define('MIN_PHP_VERSION', '5.1.0');

/**
 * The class which responds to installer actions
 */
class InstallHandler extends ActionHandler {
  
  private $theme= null;

  /**
   * Gathers information about the system in order to make sure
   * requirements for install are met
   *
   * @returns bool  are all requirements met?
   */
  private function meets_all_requirements() {
    $requirements_met= true;
    
    /* Check that directory to write config.php is writeable */
    $local_writeable= is_writeable(HABARI_PATH);
    $this->theme->assign('local_writeable', $local_writeable);
    if (! $local_writeable)
      $requirements_met= false;
    
    /* Check versions of PHP */
    $php_version_ok= version_compare(phpversion(), MIN_PHP_VERSION, '>=');
    $this->theme->assign('php_version_ok', $php_version_ok);
    $this->theme->assign('PHP_OS', PHP_OS);;
    $this->theme->assign('PHP_VERSION',  phpversion());
    if (! $php_version_ok)
      $requirements_met= false;

    /* Check for PDO extension */
    $pdo_extension_ok= extension_loaded('pdo');
    $this->theme->assign('pdo_extension_ok', $pdo_extension_ok);
    if (! $pdo_extension_ok)
      $requirements_met= false;

    return $requirements_met;
  }

  public function act_begin_install() {
    $this->theme= new Theme('installer', 'SmartyEngine', HABARI_PATH . '/system/installer/');
    if (! $this->meets_all_requirements()) {
      $this->theme->display('requirements');
      return true;
    }

    /* 
     * OK, so requirements are met.  Let's see if 
     * the user has posted some database setup info...
     */
    $this->handler_vars= array_merge($this->handler_vars, $_POST);
    if (! $this->install_db()) {
      $this->theme->display('db_setup');
      return true;
    }
  }

  private function install_db() {
    /* If there was nothing posted, just return false */
    if (! isset($this->handler_vars['db_user']))
      return false;
    
    $db_root_user= $this->handler_vars['db_root_user'];
    $db_root_pass= $this->handler_vars['db_root_pass'];
    $db_host= $this->handler_vars['db_host'];
    $db_type= $this->handler_vars['db_type'];
    $db_schema= $this->handler_vars['db_schema'];
    $db_user= $this->handler_vars['db_user'];
    $db_pass= $this->handler_vars['db_pass'];
    
    foreach (array('db_root_user', 'db_root_pass', 'db_host', 'db_type', 'db_schema', 'db_user', 'db_pass') as $key) {
      $this->theme->assign($key, $$key);
    };

    /* 
     * OK, user has choice to either install the database
     * via the super (administrator) user, /or/ install the 
     * database tables into a pre-created database (for instance,
     * if they are on a shared host that creates the DB for them)
     */
    $install_method= $this->handler_vars['install_method'];
    $install_via_root= ($install_method == 'root');

    if ($install_via_root) {
      /* OK, user is saying they have root access and can install the schema directly.  Let's check */
      if (! $this->check_root_db_credentials()) {
        $this->theme->assign('form_errors', array('db_root_user'=>'Bad root user credentials.'));
        return false;
      }
      else {
        if (empty($db_user)) {
          $this->theme->assign('form_errors', array('db_user'=>'User is required.'));
          return false;
        }
        if (empty($db_pass)) {
          $this->theme->assign('form_errors', array('db_pass'=>'Password is required.'));
          return false;
        }
        if (empty($db_schema)) {
          $this->theme->assign('form_errors', array('db_schema'=>'Name for database is required.'));
          return false;
        }
        /* Alright, we're in with root priveleges, so create the database and db user */
        $create_queries= $this->get_create_schema_with_user($db_type, $db_schema, $db_host, $db_user, $db_pass);
        DB::begin_transaction();
        foreach ($create_queries as $query) {
          if (! DB::query($query)) {
            $this->handler_vars['form_errors']= array('db_host'=>'Could not create schema.');
            DB::rollback();
            return false;
          }
        }
        DB::commit();
        /* OK, schema and user created.  Let's install the DB tables now. */ 
      }
    }
    else {
      /* OK, user is saying that the database is already created.  Let's check */
      if (! $this->check_db_credentials())
        return false;
    }
  }

  /**
   * Returns an RDMBS-specific CREATE SCHEMA plus user SQL expression(s)
   * 
   * @param db_type     type of RDMBS
   * @param db_schema   name of database schema
   * @param db_host     database server host
   * @param db_user     db user name
   * @param db_pass     db user pass
   * @return  string[]  array of SQL queries to execute
   */
  private function get_create_schema_with_user($db_type, $db_schema, $db_host, $db_user, $db_pass) {
    $queries= array();
    switch ($db_type) {
      case 'mysql':
        $queries[]= 'CREATE DATABASE `' . $db_schema . '`;';
        $queries[]= 'GRANT ALL ON `' . $db_schema . '`.* TO \'' . $db_user . '\'@\'' . $db_host . '\' ' . 
                    'IDENTIFIED BY \'' . $db_pass . '\';';
        break;
      default:
        die('currently unsupported.');
    }
    return $queries;
  }

  private function write_config_file() {
    //$file= fopen(HABARI_PATH . '/config.php', 'w');
  }

  /**
   * Checks that the supplied root user is able to create
   * a database and grant access to a database user
   *
   * @return  bool  Did the root user credentials check out?
   */
  private function check_root_db_credentials() {
    $db_root_user= $this->handler_vars['db_root_user'];
    $db_root_pass= $this->handler_vars['db_root_pass'];
    $db_host= $this->handler_vars['db_host'];
    $db_type= $this->handler_vars['db_type'];
    $db_schema= $this->handler_vars['db_schema'];

    if (!empty($db_root_user) && empty($db_host)) {
      $this->theme->assign('form_errors', array('db_host'=>'Host is required.'));
      return false;
    }
    
    /* Create a PDO connection string based on the database type */
    $connect_string= $db_type . ':host=' . $db_host . ';dbname=';// . $db_schema  
    /* Attempt to connect to the database host */
    return DB::connect($connect_string, $db_root_user, $db_root_pass);
  }
}
?>
