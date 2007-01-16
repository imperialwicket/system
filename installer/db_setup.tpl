<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="en"/>
    <meta name="robots" content="no index,no follow" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="stylesheet" type="text/css" media="all" href="themes/installer/style.css" />
    <title>Installing habari</title>
  </head>
  <body>
    <div id="container">
      <div id="header">
        <h1>Install <em>habari</em></h1>
      </div>
      <div id="page">
        <form action="" method="post">
          <h2>Installation Method</h2>
          <p class="instructions">
          You can either install the habari database yourself (if you have appropriate permissions to do so), or
          you can alternately install the habari tables into a database that has already been installed for you
          (for instance, if you are on a shared web host and do not have the ability to create databases yourself).  
          <em>Choose the method of installation</em>.
          </p>
          <div class="row">
            <label for="install_root">Install Entire DB</label>
            <input type="radio" id="install_root" name="install_method" value="root" checked="true" />
          </div>
          <div class="row">
            <label for="install_tables">Install Tables in Existing DB</label>
            <input type="radio" id="install_tables" name="install_method" value="noroot" />
          </div>
          <h2>Install Entire Database</h2>
          <p class="instructions">
            In order to install the database properly, this installation script
            needs to be able to log in to your database server as a user who
            has permissions to create a new database and a new database user.
            Typically, this "super" user is called "root", which is what is entered
            in the field below by default.
          </p>
          <div class="row">
            <label for="db_root_user">Username with SUPER privileges</label>
            <input type="textbox" name="db_root_user" value="{$db_root_user}" size="30" maxlength="50" />
            {include file="form.error.tpl" Id="db_root_user"}
          </div>
          <div class="row">
            <label for="db_root_pass">Password for super user</label>
            <input type="password" name="db_root_pass" value="{$db_root_pass}" size="30" maxlength="50" />
            {include file="form.error.tpl" Id="db_root_pass"}
          </div>
          <h2>Database Information</h2>
           <div class="row">
            <label for="db_type">Database Type</label>
            <select name="db_type" value="{$db_type}">
             <option selected="true" value="mysql">MySQL</option>
             <option value="sqlite">SQLite</option>
             <option value="pgsql">PostgreSQL</option>
            </select>
            {include file="form.error.tpl" Id="db_type"}
           </div>
           <div class="row">
            <label for="db_host">Host (Server)</label>
            <input type="textbox" name="db_host" value="{$db_host}" size="30" maxlength="50" />
            {include file="form.error.tpl" Id="db_host"}
          </div>
          <div class="row">
            <label for="db_user">Database User</label>
            <input type="textbox" name="db_user" value="{$db_user}" size="30" maxlength="50" />
            {include file="form.error.tpl" Id="db_user"}
          </div>
          <div class="row">
            <label for="db_pass">Database Password</label>
            <input type="textbox" name="db_pass" value="{$db_pass}" size="30" maxlength="50" />
            {include file="form.error.tpl" Id="db_pass"}
          </div>
          <div class="row">
            <label for="db_schema">Name of Database</label>
            <input type="textbox" name="db_schema" value="{$db_schema}" size="30" maxlength="50" />
            {include file="form.error.tpl" Id="db_schema"}
          </div>
          <div class="row">
            <label for="table_prefix" class="optional">Prefix for Tables</label>
            <input type="textbox" name="table_prefix" value="{$table_prefix}" size="30" maxlength="50" />
            {include file="form.error.tpl" Id="table_prefix"}
          </div>
          <div style="clear: both;">
            <input type="submit" value="Install Habari" />
          </div>
        </form>
      </div>
    </div>
  </body>
</html>
