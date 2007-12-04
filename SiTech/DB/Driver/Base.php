<?php
/**
 * Base file for database drivers.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @package SiTech_DB
 */

/**
 * @see SiTech_DB_Driver_Interface
 */
require_once ('SiTech/DB/Driver/Interface.php');

/**
 * Base driver for database backend.
 *
 * @author Eric Gach <eric.gach@gmail.com>
 * @name SiTech_DB_Driver_Base
 * @package SiTech_DB
 */
abstract class SiTech_DB_Driver_Base extends SiTech_DB_Driver_Interface
{
	/**
	 * Array holder for all attributes
	 *
	 * @var array
	 */
	protected $_attributes = array();
	
	/**
	 * Array holder for current configuration
	 *
	 * @var array
	 */
	protected $_config = array();
	
	/**
	 * Database connection holder.
	 *
	 * @var resource
	 */
	protected $_conn;
	
	public function __construct(array $config, array $options = array())
	{
		$this->_config = $config;
		foreach ($options as $attribute => $value) {
			$this->setAttribute($attribute, $value);
		}
	}
	
	/**
	 * Start a transactional state with the database. Will return false on
	 * failure or if feature is unsupported.
	 *
	 * @return bool
	 */
	abstract public function beginTransaction();
	
	/**
	 * Commit all outstanding transactions to the database. This finalizes the
	 * changes made.
	 *
	 * @return bool
	 */
	abstract public function commit();
	
	/**
	 * Execute a SQL query on the database and return the number of rows
	 * affected.
	 *
	 * @param string $sql
	 * @param array $params
	 * @return int
	 */
	public function exec($sql, $params = array())
	{
		$stmnt = $this->prepare($sql);
		$stmnt->execute($params);
		return($stmnt->rowCount());
	}
	
	/**
	 * Get the value of the specified attribute. An unsuccessful call to
	 * this 
	 *
	 * @param int $attributes
	 * @return mixed
	 */
	public function getAttribute($attributes)
	{
		if (isset($this->_attributes[$attributes])) {
			return($this->_attributes[$attributes]);
		} else {
			return(null);
		}
	}
	
	/**
	 * Return the error number generated by the last statement executed. Will
	 * return 0 if no previous error is found.
	 * 
	 * @return int
	 */
	abstract public function getErrno();
	
	/**
	 * Get the last error returned byt he db server.
	 *
	 * @return array
	 */
	abstract public function getError();
	
	/**
	 * Get the ID from the last insert statement. The column name to get the ID from
	 * can also be specified.
	 *
	 * @param string $column
	 * @return mixed
	 */
	abstract public function getLastInserId($column = null);
	
	/**
	 * Prepare a SQL statement for execution.
	 *
	 * @param string $sql
	 * @return SiTech_DB_Statement_Interface
	 */
	abstract public function prepare($sql);
	
	/**
	 * Execute a SQL query on the database and return a new instance of
	 * SiTech_DB_Statement_Interface.
	 *
	 * @param string $sql
	 * @param int $fetchMode
	 * @param mixed $arg1
	 * @param mixed $arg2
	 * @return SiTech_DB_Statement_Interface
	 */
	public function query($sql, $fetchMode = null, $arg1 = null, $arg2 = null)
	{
		$stmnt = $this->prepare($sql);
		$stmnt->setFetchMode($fetchMode, $arg1, $arg2);
		$stmnt->execute();
		return($stmnt);
	}
	
	/**
	 * Quote a string for use with the database based on the type specified.
	 *
	 * @param mixed $string Value to be quoted
	 * @param int $paramType SiTech_DB::TYPE_* constant
	 */
	abstract public function quote($string, $paramType=SiTech_DB::TYPE_STRING);
	
	/**
	 * Roll back all changes made by the current transaction.
	 *
	 * @return bool
	 */
	abstract public function rollBack();
	
	/**
	 * Set an attribute for the current connection.
	 *
	 * @param int $attribute
	 * @param mixed $value
	 * @return bool
	 */
	public function setAttribute($attribute, $value)
	{
		switch ($attribute) {
			case SiTech_DB::ATTR_AUTOCOMMIT:
			case SiTech_DB::ATTR_PREFETCH:
			case SiTech_DB::ATTR_TIMEOUT:
			case SiTech_DB::ATTR_ERRMODE:
			case SiTech_DB::ATTR_SERVER_VERSION:
			case SiTech_DB::ATTR_CLIENT_VERSION:
			case SiTech_DB::ATTR_SERVER_INFO:
			case SiTech_DB::ATTR_CONNECTION_STATUS:
			case SiTech_DB::ATTR_CASE:
			case SiTech_DB::ATTR_CURSOR_NAME:
			case SiTech_DB::ATTR_CURSOR:
			case SiTech_DB::ATTR_ORACLE_NULLS:
			case SiTech_DB::ATTR_PERSISTENT:
			case SiTech_DB::ATTR_STATEMENT_CLASS:
			case SiTech_DB::ATTR_FETCH_TABLE_NAMES:
			case SiTech_DB::ATTR_FETCH_CATALOG_NAMES:
			case SiTech_DB::ATTR_DRIVER_NAME:
			case SiTech_DB::ATTR_STRINGIFY_FETCHES:
			case SiTech_DB::ATTR_MAX_COLUMN_LEN:
			case SiTech_DB::ATTR_EMULATE_PREPARES:
			case SiTech_DB::ATTR_DEFAULT_FETCH_MODE:
				$this->_attributes[$attribute] = $value;
				break;
			
			default:
				return(false);
				break;
		}
		
		return(true);
	}
	
	/**
	 * Enter description here...
	 */
	abstract protected function __connect();
	
	/**
	 * Enter description here...
	 */
	abstract protected function __disconnect();
}