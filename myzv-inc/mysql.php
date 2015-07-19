<?

// mysql.php -  mysql class
// by Bryan Pass (2002ish)

class database  // MySQL Database class Start
{
	var $connected = false; 	// Initialize connected as false
	var $database_user;			// MySQL database username
	var $database_server;		// MySQL database servername
	var $database_name;			// MySQL database name
	var $database_pers;			// MySQL persistent connection ?

	var $connection_id; 		// MySQL Connection resID
	var $last_query;			// Last Query resID
	var $last_qs;				// Last Query string
	var $queries;				// Query Counter

	function database( $server, $username, $password, $dbname, $persistent = true )
	{
		$this->connect( $server, $username, $password, $dbname, $persistent );
	}

# --------------------------------------------------------------------------------------------------------
# function connect():  this function is used to connect to the database and takes the following arguments
#                      server/username/password/name/persistent
#---------------------------------------------------------------------------------------------------------
	function connect( $server, $username, $password, $dbname, $persistent = true )
	{
		$this->database_user = $username;
		$this->database_pass = $password;
		$this->database_server = $server;
		$this->database_name = $dbname;
		$this->database_pers = $persistent;

		if ( $this->connected )
			$this->close();
		$this->connected = false;

		if ( $this->database_pers )
			$this->connection_id = new mysqli( "p:$this->database_server", $this->database_user, $this->database_pass );
		else
			$this->connection_id = new mysqli( $this->database_server, $this->database_user, $this->database_pass );

		if ( !$this->connection_id )
			return 0;

		if ( $this->database_name != "" )
		{
			$dbsel = @mysqli_select_db( $this->connection_id, $this->database_name );
			if ( !$dbsel )
			{
				$this->close();
				$this->connection_id = 0;
				return 0;
			}
		}

		$this->queries = 0;
		$this->connected = true;
		return $this->connection_id;
	}

# --------------------------------------------------------------------------------------------------------
# function close():  this function is used to close the current connection to the database, which it uses
#                    connection_id for.
#---------------------------------------------------------------------------------------------------------
	function close()
	{
		if ( !$this->connected || !$this->connection_id )
			return 0;

		$this->connected = false;
		return @mysqli_close( $this->connection_id );
	}

# --------------------------------------------------------------------------------------------------------
# function query():  this function is used to query the database after there is a connection made.  It 
#                    takes the argument query_string which is a mysql standard query.
#---------------------------------------------------------------------------------------------------------
	function query( $query_string )
	{
		if ( $query_string == "" || !$this->connected )
			return FALSE;

		$last_qs = $query_string;
		if ( !$this->last_query = @mysqli_query($this->connection_id, $query_string) )
			die( $this->get_error() );
		else
			$this->queries++;

		return $this->last_query;
	}

# --------------------------------------------------------------------------------------------------------
# function num_rows():  returns the number of rows in a query
#---------------------------------------------------------------------------------------------------------
	function num_rows( $query = FALSE )
	{
		$result = FALSE;

		if ( !$query )
			$query = $this->last_query;

		if ( $query && $this->connected )
			$result = @mysqli_num_rows( $query );

		return $result;
	}

# --------------------------------------------------------------------------------------------------------
# function affected_rows():  returns the number of rows in a query
#---------------------------------------------------------------------------------------------------------
	function affected_rows( $query = FALSE )
	{
		$result = FALSE;

		if ( !$query )
			$query = $this->last_query;

		if ( $query && $this->connected )
			$result = @mysqli_affected_rows( $this->connection_id );

		return $result;
	}

# --------------------------------------------------------------------------------------------------------
# function num_fields():  returns the number of fields in a query
#---------------------------------------------------------------------------------------------------------
	function num_fields( $query = FALSE )
	{
		$result = FALSE;

		if ( !$query )
			$query = $this->last_query;

		if ( $query && $this->connected )
			$result = @mysqli_num_fields( $query );

		return $result;

	}

# --------------------------------------------------------------------------------------------------------
# function field_name():  returns the name of a field in a query
#---------------------------------------------------------------------------------------------------------
	function field_name( $field, $query = FALSE )
	{
		$result = FALSE;

		if ( !$query )
			$query = $this->last_query;

		if ( $query && $this->connected ) {
			$result = @mysqli_fetch_field_direct($query, $field);
			$result = $result->name;
		}

		return $result;

	}

# --------------------------------------------------------------------------------------------------------
# function field_type():  returns the type of field in a query
#---------------------------------------------------------------------------------------------------------
	function field_type( $field, $query = FALSE )
	{
		$result = FALSE;

		if ( !$query )
			$query = $this->last_query;

		if ( $query && $this->connected ) {
			$result = @mysqli_fetch_field_direct($query, $field);
			$result = $result->type;
		}

		return $result;

	}

# --------------------------------------------------------------------------------------------------------
# function fetchrow():  subsitute for mysql get array
#---------------------------------------------------------------------------------------------------------
	function fetchrow( $query = FALSE )
	{
		if ( !$query )
			$query = $this->last_query;

		if ( $query && $this->connected )
		{
			$temp_array = array();
			$temp_array = @mysqli_fetch_assoc( $query );
			return $temp_array;
		} else {
			return FALSE;
		}
	}

# --------------------------------------------------------------------------------------------------------
# function fetchobject():  dual array matrix
#---------------------------------------------------------------------------------------------------------
	function fetchobject( $query = FALSE )
	{
		if ( !$query )
			$query = $this->last_query;

		if ( $query && $this->connected )
			return @mysqli_fetch_object( $query );
		else
			return FALSE;
	}


# --------------------------------------------------------------------------------------------------------
# function fetchrowset():  dual array matrix
#---------------------------------------------------------------------------------------------------------
	function fetchrowset( $query = 0 )
	{
		if ( !$query )
			$query = $this->last_query;

		if ( $query && $this->connected )
		{
			$temp_matrix = array();
			$temp_array = array();
			while ( $temp_array = $this->get_array( $query ) )
				$temp_matrix[] = $temp_array;
			return $temp_matrix;
		} else {
			return FALSE;
		}
	}


# --------------------------------------------------------------------------------------------------------
# function fetchfield():  gets a specific field value
#---------------------------------------------------------------------------------------------------------
	function fetchfield( $field, $row = 0, $query = FALSE )
	{
		if ( !$query )
			$query = $this->last_query;

		if ( $query && $this->connected ) {
			$result = @mysqli_fetch_assoc( $query );
			return $result[$field];
		} else
			return FALSE;
	}

# --------------------------------------------------------------------------------------------------------
# function seek_row():  gets a specific rown number
#---------------------------------------------------------------------------------------------------------
	function seek_row( $row, $query = FALSE )
	{
		$result = FALSE;

		if ( !$query )
			$query = $this->last_query;

		if ( $query && $this->connected )
			$result = @mysqli_data_seek( $query, $row );

		return $result;
	}

# --------------------------------------------------------------------------------------------------------
# function next_id():  gets us the next id for insert strings
#---------------------------------------------------------------------------------------------------------
	function insert_id()
	{
		$result = FALSE;

		if ( $this->connected )
			$result = @mysqli_insert_id( $this->connection_id );

		return $result;
	}

# --------------------------------------------------------------------------------------------------------
# function free_result(): free resources associated with a mysql query
#---------------------------------------------------------------------------------------------------------
	function free_result( $query = 0 )
	{
		$result = FALSE;

		if ( !$query )
			$query = $this->last_query;

		if ( $query && $this->connected )
			$result = @mysqli_free_result( $query );

		return $result;
	}

# --------------------------------------------------------------------------------------------------------
# function get_errror():  this function prints any mysql error to the current browser
#---------------------------------------------------------------------------------------------------------
	function get_error( $format = "SQL Error #{NUM}: '{MSG}'" )
	{
		if ( $this->connection_id )
		{
			$en = @mysqli_errno($this->connection_id);
			$es = @mysqli_error($this->connection_id);
		}
		else
		{
			$en = -1;
			$es = "Not connected to the database! (No connection id)";
		}

		$format = str_replace( $format, "{NUM}", $en );
		$format = str_replace( $format, "{MSG}", $es );
		return $format;
	}

# --------------------------------------------------------------------------------------------------------
# function num_queries():  this function returns the number of queries exec'd on this connection
#---------------------------------------------------------------------------------------------------------
	function num_queries()
	{
		return $this->queries;
	}

} // MySQL database class end
?>
