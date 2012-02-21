<?php
/**
 * File defining PDOBinding
 *
 * Copyright (c) 2011 JadeIT cc
 * @license http://www.opensource.org/licenses/mit-license.php
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in the
 * Software without restriction, including without limitation the rights to use, copy,
 * modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the
 * following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR
 * A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package BindingFiles
 */
/**
 * PDO Connection Binding
 *
 * @package Binding
 */
namespace Backend\Base\Bindings;

class PDOBinding extends DatabaseBinding
{
    /**
     * @var PDO The PDO connection for this binding
     */
    protected $_connection;

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
        if (array_key_exists('username', $connection)) {
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
        $this->_connection = new \PDO($dsn, $username, $password);
    }

    protected function executeStatement($statement)
    {
        if ($statement && $statement->execute()) {
            return $statement;
        } else {
            $info = $this->_connection->errorInfo();
            throw new \Exception('PDO Error: ' . $info[2] . ' (' . $info[0] . ')');
        }
    }

    public function executeQuery($query)
    {
        return $this->executeStatement($this->_connection->prepare($query));
    }

    public function find(array $conditions = array(), array $options = array())
    {
        $query = 'SELECT * FROM ' . $this->_table;
        //TODO: Return the statement as it's an iterator?
        return $this->executeQuery($query)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
    }

    public function read($identifier)
    {
        $query = 'SELECT * FROM ' . $this->_table . ' WHERE `id` = :id';
        $stmt  = $this->_connection->prepare($query);
        if ($stmt->execute(array(':id' => $identifier))) {
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function update($identifier, $data)
    {
    }

    public function delete($identifier)
    {
        $query = 'DELETE FROM ' . $this->_table . ' WHERE `id` = :id';
        $stmt  = $this->_connection->prepare($query);
        return (bool)$stmt->execute(array(':id' => $identifier));
    }
}
