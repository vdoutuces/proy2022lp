<?php

/**
 * PHP MYSQLi Database Class
 *
 * This class helps developers make standardized calls across their entire
 * application. This class was found and forked (bennettstone/simple-mysqli)
 * as a necessity after mysql_connect was deprecated and my site crashed. I
 * was forced to go through each and every page of my site and edit each call
 * and thought there had to be a better way. Please feel free to use on your
 * site and submit issues where necessary. Thank you and happy coding.
 *
 *
 * @link              https://github.com/nowendwell/mysqli-class
 * @version           2.0.1
 *
 * Description:       A MYSQLi database wrapper for PHP
 * Last Update:       2018-04-05
 * Author:            Ben Miller
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 */

namespace App\Http\Models;


class DB
{
    // DB Connection Settings
    private $db_name    = DB_NOMBRE;
    private $db_user    = DB_USUARIO;
    private $db_pass    = DB_CLAVE;
    private $db_host    = DB_HOST;
    private $db_charset = 'utf8';

    // Debug Settings
    public $debug          = true;
    public $display_errors = true;
    public $send_mail      = true;
    public $send_to        = null;
    public $transactions   = false;
    public $log_path       = 'queries.log';

    // Class Settings
    private $link          = null;
    public $filter;
    public static $inst    = null;
    public static $counter = 0;
    public $queries        = array();


    public function __construct()
    {

        $args = func_get_args();

        if (sizeof($args) > 0) {
            $this->link = new \mysqli($args[0], $args[1], $args[2], $args[3]);
        } else {
            $this->link = new \mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);
        }
        if ($this->link->connect_errno) {
            exit("connect failed");
        }

        $this->link->set_charset($this->db_charset);
    }

    public function __destruct()
    {
        if ($this->link) {
            $this->disconnect();
        }
    }

    /**
    * Sanitize user data
    *
    * Example usage:
    * $user_name = $db->filter( $_POST['user_name'] );
    *
    * Or to filter an entire array:
    * $data = array( 'name' => $_POST['name'], 'email' => 'email@address.com' );
    * $data = $db->filter( $data );
    *
    * @access public
    * @param mixed $data
    * @return mixed $data
    */
    public function filter($data)
    {
        if (!is_array($data)) {
            $data = $this->link->real_escape_string($data);
            $data = trim(htmlentities($data, ENT_QUOTES, 'UTF-8', false));
        } else {
            //Self call function to sanitize array data
            $data = array_map(array( $this, 'filter' ), $data);
        }
        return $data;
    }

    /**
    * Extra function to filter when only mysqli_real_escape_string is needed
    * @access public
    * @param mixed $data
    * @return mixed $data
    */
    public function escape($data)
    {
        if (!is_array($data)) {
            $data = $this->link->real_escape_string($data);
        } else {
            //Self call function to sanitize array data
            $data = array_map(array( $this, 'escape' ), $data);
        }
        return $data;
    }

    /**
    * Normalize sanitized data for display (reverse $db->filter cleaning)
    *
    * Example usage:
    * echo $db->clean( $data_from_database );
    *
    * @access public
    * @param string $data
    * @return string $data
    */
    public function clean($data)
    {
        $data = stripslashes($data);
        $data = html_entity_decode($data, ENT_QUOTES, $this->db_charset);
        $data = nl2br($data);
        $data = urldecode($data);
        return $data;
    }


    /**
    * Perform queries
    * All following functions run through this function
    *
    * @access public
    * @param string
    * @return string
    * @return array
    * @return bool
    *
    */
    public function query($query)
    {
        self::$counter++;


        $full_query = $this->link->query($query);

        if ($this->link->error) {

            return false;
        } else {
            return $full_query;
        }
    }

    /**
    * Determine if database table exists
    * Example usage:
    * if( !$db->tableExists( 'checkingfortable' ) )
    * {
    *      //Install your table or throw error
    * }
    *
    * @access public
    * @param string
    * @return bool
    *
    */
    public function tableExists($table)
    {
        $check = $this->query("SELECT * FROM information_schema.tables WHERE table_schema = '{$this->db_name}' AND table_name = '{$table}' LIMIT 1;");

        if ($check !== false) {
            if ($check->num_rows > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
    * Count number of rows found matching a specific query
    *
    * Example usage:
    * $rows = $db->numRows( "SELECT id FROM users WHERE user_id = 44" );
    *
    * @access public
    * @param string
    * @return int
    *
    */
    public function numRows($query)
    {
        $query = $this->query($query);
        return $query->num_rows;
    }

    /**
    * Run check to see if value exists, returns true or false
    *
    * Example Usage:
    * $check_user = array(
    *    'user_email' => 'someuser@gmail.com',
    *    'user_id' => 48
    * );
    * $exists = $db->exists( 'your_table', 'user_id', $check_user );
    *
    * @access public
    * @param string database table name
    * @param string field to check (i.e. 'user_id' or COUNT(user_id))
    * @param array column name => column value to match
    * @return bool
    *
    */
    public function exists($table = '', $check_val = '', $params = array())
    {
        if (empty($table) || empty($check_val) || empty($params)) {
            return false;
        }

        $check = array();

        foreach ($params as $field => $value) {
            if (!empty($field) && !empty($value)) {
                //Check for frequently used mysql commands and prevent encapsulation of them
                if ($this->dbCommon($value)) {
                    $check[] = "`{$field}` = {$value}";
                } else {
                    $check[] = "`{$field}` = '{$value}'";
                }
            }
        }

        $check = implode(' AND ', $check);

        $rs_check = "SELECT {$check_val} FROM `{$table}` WHERE {$check}";
        $number = $this->numRows($rs_check);

        if ($number === 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
    * Return specific row based on db query
    *
    * Example usage:
    * list( $name, $email ) = $db->get_array( "SELECT name, email FROM users WHERE user_id = 44" );
    *
    * @access public
    * @param string
    * @param bool $object (true returns results as objects)
    * @return array
    *
    */
    public function getArray($query, $type = MYSQLI_ASSOC)
    {
        $row = $this->query($query);
        $r = [];
        
        while ($q = $row->fetch_array($type)) {
            $r[] = $q;
        }

        return $r;
    }

    /**
    * Return specific row based on db query
    *
    * Example usage:
    * list( $name, $email ) = $db->getRow( "SELECT name, email FROM users WHERE user_id = 44" );
    *
    * @access public
    * @param string
    * @param bool $object (true returns results as objects)
    * @return array
    *
    */
    public function getRow($query, $object = false)
    {
        $row = $this->query($query);
        $r = (!$object) ? $row->fetch_assoc() : $row->fetch_object();
        return $r;
    }

    /**
    * Perform query to retrieve single result
    *
    * Example usage:
    * echo $db->getResult( "SELECT name, email FROM users ORDER BY name ASC" );
    *
    * @access public
    * @param string
    * @param int|string    (Can be either position in the array or the name of the returned field)
    * @return string
    *
    */
    public function getResult($query, $pos = 0)
    {
        $results = $this->query($query);
        $result = $results->fetch_array();
        return $result[$pos];
    }

    /**
    * Perform query to retrieve array of associated results
    *
    * Example usage:
    * $users = $db->getResults( "SELECT name, email FROM users ORDER BY name ASC" );
    * foreach( $users as $user )
    * {
    *      echo $user['name'] . ': '. $user['email'] .'<br />';
    * }
    *
    * @access public
    * @param string
    * @param bool $object (true returns object)
    * @return array
    *
    */
    public function getResults($query, $object = false)
    {
        $results = $this->query($query);

        $row = array();
        while ($r = (!$object) ? $results->fetch_assoc() : $results->fetch_object()) {
            $row[] = $r;
        }

        return $row;
    }

    /**
    * Insert data into database table
    *
    * Example usage:
    * $user_data = array(
    *      'name' => 'Bennett',
    *      'email' => 'email@address.com',
    *      'active' => 1
    * );
    * $db->insert( 'users_table', $user_data );
    *
    * @access public
    * @param string table name
    * @param array table column => column value
    * @return bool
    *
    */
    public function insert($table, $variables = array())
    {
        //Make sure the array isn't empty
        if (empty($variables)) {
            return false;
        }

        $variables = $this->filter($variables);

        $sql = "INSERT INTO {$table}";

        $fields = array();
        $values = array();

        foreach ($variables as $field => $value) {
            $fields[] = $field;
            if ($value === null) {
                $values[] = "NULL";
            } else {
                $values[] = "'{$value}'";
            }
        }

        $fields = " (`" . implode("`, `", $fields) . "`)";
        $values = "(". implode(", ", $values) .")";

        $sql .= $fields ." VALUES {$values};";
       
        $query = $this->query($sql);


        return $query;
    }

    /**
    * Search data in database table
    *
    * Example usage:
    * $where = array( 'user_id' => 44, 'name' => 'Bennett' );
    * $db->search( 'users', $where );
    *
    * @access public
    * @param string table name
    * @param array where parameters table column => column value
    * @param int limit
    * @return mixed
    *
    */
    public function search($table, $where = array(), $limit = null)
    {
        if (empty($where)) {

        //  return false;
            if ( $limit == null )
            {
                $limit = 100;
            }
        } else{

            $where = $this->filter($where);
        }
        
        $sql = "SELECT * FROM `{$table}`";

        //Add the $where clauses as needed
        if (!empty($where)) {
            foreach ($where as $field => $value) {
                $clause[] = "`{$field}` = '{$value}'";
            }
            $sql .= ' WHERE '. implode(' AND ', $clause);
        }

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
        }

        return $this->getResults($sql);
    }

    /**
    * Update data in database table
    *
    * Example usage:
    * $update = array( 'name' => 'Not bennett', 'email' => 'someotheremail@email.com' );
    * $where = array( 'user_id' => 44, 'name' => 'Bennett' );
    * $db->update( 'users_table', $update, $where, 1 );
    *
    * @access public
    * @param string table name
    * @param array values to update table column => column value
    * @param array where parameters table column => column value
    * @param int limit
    * @return bool
    *
    */
    public function update($table, $variables = array(), $where = array(), $limit = null)
    {
        if (empty($variables)) {
            return false;
        }

        $variables = $this->filter($variables);

        $sql = "UPDATE {$table} SET ";
        foreach ($variables as $field => $value) {
            if ($value === null) {
                $updates[] = "`{$field}` = NULL";
            } else {
                $updates[] = "`{$field}` = '{$value}'";
            }
        }
        $sql .= implode(', ', $updates);

        //Add the $where clauses as needed
        if (!empty($where)) {
            foreach ($where as $field => $value) {
                $value = $value;

                $clause[] = "`{$field}` = '{$value}'";
            }
            $sql .= ' WHERE '. implode(' AND ', $clause);
        }

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
        }

        $query = $this->query($sql);

        return $query;
    }

    /**
    * Upserts data into database table
    *
    * Example usage:
    * $data = array(
    *      'name' => 'Jon'
    * );
    * $where = array(
    *      'name' => 'Bennett',
    *      'email' => 'email@address.com',
    *      'active' => 1
    * );
    * $db->upsert( 'users_table', $data, $where);
    *
    * @access public
    * @param string table name
    * @param array table column => column value
    * @return bool
    *
    */
    public function upsert($table, $data = array(), $where = array())
    {
        //Make sure the args aren't empty
        if (empty($table) || empty($data) || empty($where)) {
            return false;
        }

        // Find if the row exists
        $find = $this->search($table, $where);

        // if the row exists, update, if not, insert
        if (empty($find)) {
            return $this->insert($table, $data);
        } else {
            return $this->update($table, $data, $where);
        }
    }

    /**
    * Delete data from table
    *
    * Example usage:
    * $where = array( 'user_id' => 44, 'email' => 'someotheremail@email.com' );
    * $db->delete( 'users_table', $where, 1 );
    *
    * @access public
    * @param string table name
    * @param array where parameters table column => column value
    * @param int max number of rows to remove.
    * @return bool
    *
    */
    public function delete($table, $where = array(), $limit = null)
    {
        //Delete clauses require a where param, otherwise use "truncate"
        if (empty($where)) {
            return false;
        }

        $sql = "DELETE FROM `{$table}`";

        foreach ($where as $field => $value) {
            $value = $value;
            $clause[] = "`{$field}` = '{$value}'";
        }

        $sql .= " WHERE ". implode(' AND ', $clause);

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
        }

        $query = $this->query($sql);

        return $query;
    }


    /**
    * Get last auto-incrementing ID associated with an insertion
    *
    * Example usage:
    * $db->insert( 'users_table', $user );
    * $last = $db->lastid();
    *
    * @access public
    * @return int
    *
    */
    public function lastId()
    {
        return $this->link->insert_id;
    }


    /**
    * Return the number of rows affected by a given query
    *
    * Example usage:
    * $db->insert( 'users_table', $user );
    * $db->affected();
    *
    * @access public
    * @param none
    * @return int
    */
    public function affected()
    {
        return $this->link->affected_rows;
    }


    /**
    * Get number of fields
    *
    * Example usage:
    * echo $db->numFields( "SELECT * FROM users_table" );
    *
    * @access public
    * @param query
    * @return int
    */
    public function numFields($query)
    {
        $query = $this->query($query);
        $fields = $query->field_count;
        return $fields;
    }

    /**
    * Get columns from associated table
    *
    * Example usage:
    * $fields = $db->showColumns( "users_table" );
    * echo '<pre>';
    * print_r( $fields );
    * echo '</pre>';
    *
    * @access public
    * @param string
    * @return array
    */
    public function showColumns($table)
    {
        $query = $this->getResults("SHOW COLUMNS FROM `{$table}`;");
        return $query;
    }




    /**
    * Singleton function
    *
    * Example usage:
    * $db = DB::getInstance();
    *
    * @access private
    * @return self
    */
    public static function getInstance()
    {
        if (self::$inst == null) {
            self::$inst = new DB();
        }
        return self::$inst;
    }


    /**
    * Disconnect from db server
    * Called automatically from __destruct function
    */
    public function disconnect()
    {
        $this->link->close();
    }
} //end class DB
