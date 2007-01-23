<?php
/**
 * Habari UserHandler Class
 *
 * Requires PHP 5.0.4 or later
 * @package Habari
 */
class UserHandler extends ActionHandler {

  private $theme= null;             // The active user theme

	/**
	 * Checks a user's credentials, and creates a session for them
	 */
	public function act_login() {
	  if (! User::authenticate($this->handler_vars['name'], $this->handler_vars['pass'])) {
      //$url->settings['error'] = "badlogin";
			// unset the password the use tried
			$this->handler_vars['pass']= '';
    
      /* Since we failed, display the theme's login template */
      $this->theme= new Theme();
      $this->display('login');
      return true;     
		}
    /* OK, so they authenticated.  What now?  Redirect to admin dashboard? */
    $redirect= URLWriter::build_url('admin', array('page'=>'dashboard'));
    Utils::redirect($redirect);
	}

	/**
	* function logout
	* terminates a user's session, and deletes the Habari cookie
	* @param string the Action that was in the URL rule
	* @param array An associative array of settings found in the URL by the URL
	*/
	public function logout($settings) {
		global $url;
		
		// get the user from their cookie
		if ( $user = user::identify() )
		{
			// delete the cookie, and destroy the object
			$user->forget();
			$user = null;
		}
		new ThemeHandler( 'logout', $settings );
	}

  /**
   * Helper function which automatically assigns all handler_vars
   * into the theme and displays a theme template
   * 
   * @param template_name Name of template to display (note: not the filename)
   */
  protected function display($template_name) {
    /* 
     * Assign internal variables into the theme (and therefore into the theme's template
     * engine.  See Theme::assign().
     */
    foreach ($this->handler_vars as $key=>$value)
      $this->theme->assign($key, $value);
    $this->theme->display($template_name);
  }

}
?>
