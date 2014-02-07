<?php
namespace York\Database;
use York\Exception;
use York\Exception\Database;
/**
 * connection to a mysqli database
 * see it as an improved mysql wrapper class
 *
 * @author wolxXx
 * @version 3.0
 * @package York\Database
 */
class Connection{
	/**
	 * host name
	 *
	 * @var string
	 */
	private $host;

	/**
	 * database schema
	 *
	 * @var string
	 */
	private $schema;

	/**
	 * user name
	 *
	 * @var string
	 */
	private $user;

	/**
	 * password
	 *
	 * @var string
	 */
	private $password;

	/**
	 * the last occurred error
	 *
	 * @var string
	 */
	private $error;

	/**
	 * real database connection
	 *
	 * @var \mysqli
	 */
	private $connection;

	/**
	 * the last executed query
	 *
	 * @var string
	 */
	private $lastQuery;

	/**
	 * set credentials
	 * you can set them later via setters!
	 *
	 * @param string | null $host
	 * @param string | null $schema
	 * @param string | null $user
	 * @param string | null $password
	 */
	public function __construct($host = null, $schema = null, $user = null, $password = null){
		$this
			->setHost($host)
			->setSchema($schema)
			->setUser($user)
			->setPassword($password);
	}

	/**
	 * on destruction close the database connection
	 */
	public function __destruct(){
		$this->disconnect();
	}

	/**
	 * create a database connection
	 *
	 * @throws Database
	 * @return Connection
	 */
	public function connect(){
		$this->connection = new \mysqli($this->host, $this->user, $this->password);
		$this->connection->select_db($this->schema);

		if(null !== $this->connection->connect_error){
			throw new Database('db-error: '.$this->connection->connect_error);
		}

		return $this;
	}

	/**
	 * disconnect from database
	 *
	 * @return \York\Database\Connection
	 */
	public function disconnect(){
		if(
			null !== $this->connection &&
			null !== $this->connection->info &&
			0 !== $this->connection->connect_errno
		){
			$this->connection->close();
		}
		return $this;
	}

	/**
	 * returns the last occurred error
	 *
	 * @return string
	 */
	public function getError(){
		return $this->connection->error;
	}

	/**
	 * returns the last occurred error number
	 *
	 * @return integer
	 */
	public function getErrno(){
		return $this->connection->errno;
	}

	/**
	 * returns the amount of affected rows in the last query
	 *
	 * @return integer
	 */
	public function getAffectedRows(){
		return $this->connection->affected_rows;
	}

	/**
	 * fires a query
	 * saves the query as the last query
	 *
	 * @param string $query
	 * @return \mysqli_result
	 */
	public function query($query){
		$this->lastQuery = $query;
		/**
		 * @var \York\Logger\Manager $logger
		 */
		$logger = \York\Dependency\Manager::get('logger');
		$logger->log($query, $logger::LEVEL_DATABASE_DEBUG);

		return $this->connection->query($query);
	}

	/**
	 * escapes a string for inserting data
	 *
	 * @param string $string
	 * @return string
	 */
	public function escape($string){
		if(false === is_string($string)){
			return $string;
		}

		return $this->connection->real_escape_string($string);
	}

	/**
	 * returns the last insert id
	 *
	 * @return integer
	 */
	public function getLastInsertId(){
		return $this->connection->insert_id;
	}

	/**
	 * clears a table
	 *
	 * @param string $tableName
	 * @return \York\Database\QueryResult
	 */
	public function clearTable($tableName){
		$query = 'truncate '.$tableName;
		$result = $this->query($query);
		return new QueryResult($result, $query, $this->connection->error);
	}

	/**
	 * provides the last executed query
	 *
	 * @return string
	 */
	public function getLastQuery(){
		return $this->lastQuery;
	}

	/**
	 * sets the host name
	 * returns this object in sense of fluent interfaces
	 *
	 * @param string $host
	 * @return \York\Database\Connection
	 */
	public function setHost($host){
		$this->host = $host;

		return $this;
	}

	/**
	 * sets the database name
	 * returns this object in sense of fluent interfaces
	 *
	 * @param string $schema
	 * @return \York\Database\Connection
	 */
	public function setSchema($schema){
		$this->schema = $schema;

		return $this;
	}

	/**
	 * getter for the database name
	 *
	 * @return string
	 */
	public function getSchema(){
		return $this->schema;
	}

	/**
	 * sets the user name
	 * returns this object in sense of fluent interfaces
	 *
	 * @param string $user
	 * @return \York\Database\Connection
	 */
	public function setUser($user){
		$this->user = $user;

		return $this;
	}

	/**
	 * sets the password
	 * returns this object in sense of fluent interfaces
	 *
	 * @param string $password
	 * @return \York\Database\Connection
	 */
	public function setPassword($password){
		$this->password = $password;

		return $this;
	}
}
