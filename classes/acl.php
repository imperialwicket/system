<?php

/**
 * Access Control List class
 *
 * The default Habari ACL class implements groups, and group permissions
 * Users are assigned to one or more groups.
 * Groups are assigned one or more permissions.
 * Membership in any group that grants a permission
 * means you have that permission.  Membership in any group that denies
 * that permission denies the user that permission, even if another group
 * grants that permission.
 *
 * @package Habari
 **/

class ACL {
	/**
	 * How to handle a permission request for a permission that is not in the permission list.
	 * For example, if you request $user->can('some non-existant permission') then this value is returned.
	 * It's true at the moment because that allows access to all features for upgrading users.
	 * @todo Decide if this is a setting we need or want to change, or perhaps it should be an option.
	 **/
	const ACCESS_NONEXISTANT_PERMISSION = true;

	/**
	 * Create a new permission, and save it to the Permissions table
	 * @param string The name of the permission
	 * @param string The description of the permission
	 * @return mixed the ID of the newly created permission, or boolean FALSE
	**/
	public static function create_permission( $name, $description )
	{
		$name= self::normalize_permission( $name );
		// first, make sure this isn't a duplicate
		if ( ACL::permission_exists( $name ) ) {
			return false;
		}
		$allow= true;
		// Plugins have the opportunity to prevent adding this permission
		$allow= Plugins::filter('permission_create_allow', $allow, $name, $description );
		if ( ! $allow ) {
			return false;
		}
		Plugins::act('permission_create_before', $name, $description);
		$result= DB::query('INSERT INTO {tokens} (name, description) VALUES (?, ?)', array( $name, $description) );

		if ( ! $result ) {
			// if it didn't work, don't bother trying to log it
			return false;
		}
		EventLog::log('New permission created: ' . $name, 'info', 'default', 'habari');
		Plugins::act('permission_create_after', $name, $description );
		return $result;
	}

	/**
	 * Remove a permission, and any assignments of it
	 * @param mixed a permission ID or name
	 * @return bool whether the permission was deleted or not
	**/
	public static function destroy_permission( $permission )
	{
		// make sure the permission exists, first
		if ( ! ACL::permission_exists( $permission ) ) {
			return false;
		}

		// grab permission ID
		$permission = ACL::permission_id( $permission );

		$allow = true;
		// plugins have the opportunity to prevent deletion
		$allow = Plugins::filter('permission_destroy_allow', $allow, $permission);
		if ( ! $allow ) {
			return false;
		}
		Plugins::act('permission_destroy_before', $permission );
		// capture the permission name
		$name = DB::get_value( 'SELECT name FROM {tokens} WHERE id=?', array( $permission ) );
		// remove all references to this permissions
		$result = DB::query( 'DELETE FROM {group_token_permissions} WHERE permission_id=?', array( $permission ) );
		$result = DB::query( 'DELETE FROM {user_token_permissions} WHERE permission_id=?', array( $permission ) );
		// remove this permission
		$result = DB::query( 'DELETE FROM {tokens} WHERE id=?', array( $permission ) );
		if ( ! $result ) {
			// if it didn't work, don't bother trying to log it
			return false;
		}
		EventLog::log( sprintf(_t('Permission deleted: %s'), $name), 'info', 'default', 'habari');
		Plugins::act('permission_destroy_after', $permission );
		return $result;
	}

	/**
	 * Get an array of QueryRecord objects containing all permissions
	 * @param string the order in which to sort the returning array
	 * @return array an array of QueryRecord objects containing all permissions
	**/
	public static function all_permissions( $order= 'id' )
	{
		$order= strtolower( $order );
		if ( ( 'id' != $order ) && ( 'name' != $order ) && ( 'description' != $order ) ) {
			$order= 'id';
		}
		$permissions= DB::get_results( 'SELECT id, name, description FROM {tokens} ORDER BY ' . $order );
		return $permissions ? $permissions : array();
	}

	/**
	 * Get a permission's name by its ID
	 * @param int a permission ID
	 * @return string the name of the permission, or boolean FALSE
	**/
	public static function permission_name( $id )
	{
		if ( ! is_int( $id ) ) {
			return false;
		} else {
			return DB::get_value( 'SELECT name FROM {tokens} WHERE id=?', array( $id ) );
		}
	}

	/**
	 * Get a permission's ID by its name
	 * @param string the name of the permission
	 * @return int the permission's ID
	**/
	public static function permission_id( $name )
	{
		if( is_integer($name) ) {
			return $name;
		}
		$name= self::normalize_permission( $name );
		return DB::get_value( 'SELECT id FROM {tokens} WHERE name=?', array( $name ) );
	}

	/**
	 * Fetch a permission description from the DB
	 * @param mixed a permission name or ID
	 * @return string the description of the permission
	**/
	public static function permission_description( $permission )
	{
		if ( is_int( $permission) ) {
			$query= 'id';
		} else {
			$query= 'name';
			$permission= self::normalize_permission( $permission );
		}
		return DB::get_value( "SELECT description FROM {tokens} WHERE $query=?", array( $permission ) );
	}

	/**
	 * Determine whether a permission exists
	 * @param mixed a permission name or ID
	 * @return bool whether the permission exists or not
	**/
	public static function permission_exists( $permission )
	{
		if ( is_int( $permission ) ) {
			$query= 'id';
		}
		else {
			$query= 'name';
			$permission= self::normalize_permission( $permission );
		}
		return ( DB::get_value( "SELECT COUNT(id) FROM {tokens} WHERE $query=?", array( $permission ) ) > 0 );
	}

	/**
	 * Determine whether the specified user is a member of the specified group
	 * @param mixed A user  ID or name
	 * @param mixed A group ID or name
	 * @return bool True if the user is in the group, otherwise false
	**/
	public static function user_in_group( $user_id, $group_id )
	{
		if ( ! is_int( $user_id ) ) {
			$user= User::get( $user_id );
			$user_id= $user->id;
		}
		if ( ! is_int( $group_id ) ) {
			$group_id= UserGroup::id( $group_id );
		}
		$group= DB::get_value( 'SELECT id FROM {users_groups} WHERE user_id=? AND group_id=?', array( $user_id, $group_id ) );
		if ( $group ) {
			return true;
		}
		return false;
	}

	/**
	 * Determine whether a group can perform a specific action
	 * @param mixed $group A group ID or name
	 * @param mixed $permission An action ID or name
	 * @param string $access Check for 'read', 'write', or 'full' access
	 * @return bool Whether the group can perform the action
	**/
	public static function group_can( $group, $permission, $access = 'full' )
	{
		// Use only numeric ids internally
		$group = UserGroup::id( $group );
		$permission = ACL::permission_id( $permission );
		$sql = <<<SQL
SELECT p.name FROM {group_token_permissions} gp, {permissions} p WHERE
gp.group_id=? AND gp.token_id=? AND gp.permission_id=p.id;
SQL;
		$result = DB::get_values( $sql );
		if ( $result == $access ) {
			// the permission has been granted to this group
			return true;
		}
		// either the permission hasn't been granted, or it's been
		// explicitly denied.
		return false;
	}

	/**
	 * Determine whether a user can perform a specific action
	 * @param mixed $user A user object, user ID or a username
	 * @param mixed $permission A permission ID or name
	 * @param string $access Check for 'read', 'write', or 'full' access
	 * @return bool Whether the user can perform the action
	**/
	public static function user_can( $user, $permission, $access = 'full' )
	{
		// Use only numeric ids internally
		$permission= ACL::permission_id( $permission );
		// if we were given a user ID, use that to fetch the group membership from the DB
		if ( is_int( $user) ) {
			$user_id= $user;
		} else {
			// otherwise, make sure we have a User object, and get
			// the groups from that
			if ( ! $user instanceof User ) {
				$user= User::get( $user );
			}
			$user_id= $user->id;
		}

		/**
		 * Jay Pipe's explanation of the following SQL
		 * 1) Look into user_permissions for the user and the token.  
		 * If exists, use that permission flag for the check. If not, 
		 * go to 2)
		 *
		 * 2) Look into the group_permissions joined to 
		 * users_groups for the user and the token.  Order the results 
		 * by the permission_id flag. The lower the flag value, the 
		 * fewest permissions that group has. Use the first record's 
		 * permission flag to check the ACL.
		 *
		 * This gives the system very fine grained control and grabbing 
		 * the permission flag and can be accomplished in a single SQL 
		 * call.
		 */ 
		$sql = <<<SQL
SELECT COALESCE(permission_id, 0) as permission_id
FROM (
(
  SELECT permission_id
  FROM {user_token_permissions}
  WHERE user_id = :user_id
  AND token_id = :token_id
) AS up
UNION ALL
(
  SELECT gp.permission_id
  FROM {users_groups} ug
  INNER JOIN {group_token_permissions} gp
  ON ug.group_id = gp.group_id
  AND ug.user_id = :user_id
  AND gp.token_id = :token_id
  ORDER BY permission_id ASC
  LIMIT 1
)
)
LIMIT 1; 
SQL;
		$result = DB::get_value( $sql, array( ':user_id' => $user_id, ':token_id' => $permission );

		// TODO: modify above call to return the permission name rather than the ID
		// For now, I'll just look for a result > 0
		if ( $result !== FALSE && intval($result) > 0 ) {
			return true;
		}

		// if the permission is neither denied nor granted, they're not
		// allowed to do it.
		return self::ACCESS_NONEXISTANT_PERMISSION;
		return false;
	}

	/**
	 * Convert a permission name into a valid format
	 *
	 * @param string $name The name of a permission
	 * @return string The permission with spaces converted to underscores and all lowercase
	 */
	public static function normalize_permission( $name )
	{
		return strtolower( preg_replace( '/\s+/', '_', trim($name) ) );
	}
}
?>
