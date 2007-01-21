<?php

/**
 * Habari QueryRecord Class
 *
 * Requires PHP 5.0.4 or later
 * @package Habari
 */

class QueryRecord
{
	protected $fields = array();  // Holds field values from db
	protected $newfields = array(); // Holds updated field values to commit to db
	protected $field_map = array(); // Holds an array of fields from each table that are to be written to that table
	private $loaded = false;  // Set to true after the constructor executes, is false when PDO fills data fields
	
	/**
	 * constructor __construct
	 * Constructor for the QueryRecord class.
	 * @param array an associative array of initial field values.
	 **/	 	 	 	
	public function __construct($paramarray = array())
	{
		$this->loaded = true;

		// Defaults
		$this->fields = array_merge(
			$this->fields,
			Utils::get_params($paramarray)
		);
	}
	
	/**
	 * function __get
	 * Handles getting virtual properties for this class
	 * @param string Name of the property
	 * @return mixed The set value
	 **/	 
	public function __get($name)
	{
		return isset($this->newfields[$name]) ? $this->newfields[$name] : $this->fields[$name];
	}
	
	/**
	 * function __set
	 * Handles setting virtual properties for this class
	 * @param string Name of the property
	 * @param mixed Value to set it to	 
	 * @return mixed The set value 
	 **/	 
	public function __set($name, $value)
	{
		if($this->loaded) {
			$this->newfields[$name] = $value;
		}
		else {
			$this->fields[$name] = $value;
		}
		return $this->__get($name);
	}

	/**
	 * Sets the mapping of fields to specific tables during inserts and updates
	 * 
	 * @param array An array indexed by table of fields
	 * <code>$record->map(array('post'=>array('content', 'title')));</code>
	 */
	public function map($map)
	{
		$this->field_map = $map;
	} 	 	 	 	 	

	/**
	 * function insert
	 * Inserts this record's fields as a new row
	 * @param string Table to update
	 * @return boolean True on success, false if not 
	 **/	 
	public function insert()
	{
		$insertid = false;
		foreach($this->field_map as $table => $fields) {
			$merge = array_merge($this->fields, $this->newfields);
			$merge = array_intersect_key($merge, array_flip($fields));
			if($insertid != false) {
				$merge[$fields[0]] = $insertid;  // This is kind of kludgey
			}
			$result = DB::insert($table, $merge);
			$insertid = DB::last_insert_id();
		}
		return $result;
	}
	
	/**
	 * function to_array
	 * Returns an array with the current field settings
	 * @return array The field settings as they would be saved
	 **/
	public function to_array()
	{
		return array_merge($this->fields, $this->newfields);
	}	 

	/**
	 * function update
	 * Updates this record's fields using the new data
	 * @param string Table to update
	 * @param array An associative array of field data to match	 	 	 		
	 * @return boolean True on success, false if not 
	 **/	 
	public function update($table, $updatekeyfields = array() )
	{
		$merge = array_merge($this->fields, $this->newfields);
		return DB::update($table, array_diff_key($merge, $this->unsetfields), $updatekeyfields);
	}
	
	/**
	 * function delete
	 * Deletes a record based on the match array
	 * @param string Table to delete from
	 * @param array An associative array of field data to match	 	 	 		
	 * @return boolean True on success, false if not 
	 **/	 
	public function delete($table, $updatekeyfields)
	{
		global $db;
		
		return DB::delete($table, $updatekeyfields);
	}

}

?>
