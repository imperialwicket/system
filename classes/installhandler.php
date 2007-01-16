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
    $this->theme->assign('PHP_OS', PHP_OS);
    $this->theme->assign('PHP_VERSION', phpversion());
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
    $this->theme= new Theme('installer', 'SmartyEngine');
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
      if (! $this->check_root_db_credentials()) 
        return false;
    }
    else {
      /* OK, user is saying that the database is already created.  Let's check */
      if (! $this->check_db_credentials())
        return false;
    }
  }

  private function write_config_file() {

  }

  /**
   * Checks that the supplied root user is able to create
   * a database and grant access to a database user
   */
  private function check_super_db_credentials() {

  }

  public function act_display_database_form() {
    $this->theme= new Theme('installer');
  }

  public function act_install_database() {

  }
}
?>
