<?php
/**
 * File defining PDOBinding
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    Base
 * @subpackage Bindings
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
namespace Backend\Base\Bindings;
/**
 * PDO Connection Binding
 *
 * @category   Backend
 * @package    Base
 * @subpackage Bindings
 * @author     J Jurgens du Toit <jrgns@jrgns.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 * @todo This is a rudimentary implementation of the PDOBinding. It can be improved a lot.
 */
class PDOBinding extends DatabaseBinding
{
    /**
     * @var PDO The PDO connection for this binding
     */
    protected $connection;

    /**
     * @var string The name of the table this binding operates on
     */
    protected $table;

    protected $className;

    /**
     * The constructor for the object.
     *
     * The settings array should contain at least the name of the table to use.
     *
     * @param array $settings The settings for the PDO Connection
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        if (empty($settings['table'])) {
            throw new \Exception('Missing Table for Binding ' . get_class($this));
        }
        $this->table     = $settings['table'];
        $this->className = $settings['class'];
    }

    /**
     * Initialize the connection
     *
     * @param array $connection The connection information for the binding
     *
     * @return Object The current object 
     */
    protected function init(array $connection)
    {
        if (empty($connection['driver'])) {
            throw new \Exception('Missing Driver for Connection ' . $this->_name);
        }
        $driver = $connection['driver'];
        unset($connection['driver']);
        if (array_key_exists('username', $connection)) {
            $username = $connection['username'];
            unset($connection['username']);
        } else {
            $username = '';
        }
        if (array_key_exists('password', $connection)) {
            $password = $connection['password'];
            unset($connection['password']);
        } else {
            $password = '';
        }

        //TODO It will be wise to extend the PDOBinding class into driver specific classes at some point
        switch ($driver) {
        case 'sqlite':
            $dsn = $driver . ':' . $connection['path'];
            break;
        default:
            $dsn = $driver . ':' . urldecode(http_build_query($connection, '', ';'));
            break;
        }
        $this->connection = new \PDO($dsn, $username, $password);

        return $this;
    }

    /**
     * Execute a statement on the current connection
     *
     * @param PDOStatement $statement  The statement to execute
     * @param array        $parameters The parameters to use when executing the statement
     *
     * @return PDOStatement The statement after executing it
     */
    protected function executeStatement($statement, array $parameters = array())
    {
        if ($statement && $statement->execute($parameters)) {
            return $statement;
        } else {
            $info = $statement->errorInfo();
            throw new \Exception('PDO Error: ' . $info[2] . ' (' . $info[0] . ')');
        }
    }

    /**
     * Execute a query on the current connection
     *
     * @param string $query      The query to execute
     * @param array  $parameters The parameters to use when executing the query
     *
     * @return PDOStatement The statement of the query that was executed
     */
    public function executeQuery($query, array $parameters = array())
    {
        return $this->executeStatement($this->connection->prepare($query), $parameters);
    }

    /**
     * Find a specified instances of the resource
     *
     * Don't specify any criteria to retrieve a full list of instances.
     *
     * @param array $conditions An array of conditions on which to filter the list
     * @param array $options    An array of options
     *
     * @todo Implement conditions
     * @return array An array of representations of the resource
     */
    public function find(array $conditions = array(), array $options = array())
    {
        $query = 'SELECT * FROM ' . $this->table;
        return $this->executeQuery($query)->fetchAll(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, $this->className);
    }

    /**
     * Create an instance of the source, and return the instance
     *
     * @param mixed $data A respresentation of the data with which to create the instance
     *
     * @return mixed A respresentation of the created instance of the resource if succesful.
     */
    public function create($data)
    {
        $query  = 'INSERT INTO ' . $this->table;
        $params = array();
        $values = array();
        $names  = array();
        foreach ($data as $name => $value) {
            $params[':' . $name] = $value;
            $names[]  = $name;
            $values[] = ':' . $name;
        }
        $query .= ' (' . implode(', ', $names) . ') VALUES (' . implode(', ', $values) . ')';
        if ($this->executeQuery($query, $params)) {
            return $this->read($this->connection->lastInsertId());
        }
        return false;
    }

    /**
     * Read a specified instance of the source, and return the instance
     *
     * @param mixed $identifier The unique identifier for the instance.
     *
     * @return mixed A respresentation of the specified instance of the resource.
     */
    public function read($identifier)
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE `id` = :id';
        $stmt  = $this->connection->prepare($query);
        if ($stmt->execute(array(':id' => $identifier))) {
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return false;
    }

    /**
     * Update the specified instance of the resource
     *
     * @param mixed $identifier The unique identifier for the instance.
     * @param mixed $data       A respresentation of the data with which to update the instance
     *
     * @todo Implement this
     * @return mixed A respresentation of the updated instance of the resource if succesful.
     */
    public function update($identifier, $data)
    {
        $query  = 'UPDATE ' . $this->table . ' SET ';
        $params = array();
        $values = array();
        foreach ($data as $name => $value) {
            $params[':' . $name] = $value;
            $values[] = $name . ' = :' . $name;
        }
        $query .= implode(', ', $values) . ' WHERE id = :identifier';
        $params[':identifier'] = $identifier;
        if ($this->executeQuery($query, $params)) {
            return $this->read($identifier);
        }
        return false;
    }

     /**
     * Delete the specified instance of the resource
     *
     * @param mixed $identifier The unique identifier for the instance.
     *
     * @return boolean If the deletion was succesful or not.
     */
    public function delete($identifier)
    {
        $query = 'DELETE FROM ' . $this->table . ' WHERE `id` = :id';
        $stmt  = $this->connection->prepare($query);
        return (bool)$stmt->execute(array(':id' => $identifier));
    }
}
