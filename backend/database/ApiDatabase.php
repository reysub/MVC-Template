<?php

	class ApiDatabase
	{
		/** @var PDO */
		private static	$__connection;


		public static function openConnection()
		{
			try {
				self::$__connection = new PDO( "mysql:host=" . Config::$DB_HOST . ";dbname=" . Config::$DB_NAME . ";charset=utf8", Config::$DB_USER, Config::$DB_PASSWORD );
			} catch ( PDOException $e ) {
				$exception = new ApiException( "Failed to open database connection", Config::$LOG_LEVEL_ERROR_KEY );
				$exception->setAdditionalInfo( "source", "ApiDatabase::openConnection" );
				$exception->setAdditionalInfo( "host", Config::$DB_HOST );
				$exception->setAdditionalInfo( "user", Config::$DB_USER );
				$exception->setAdditionalInfo( "password", Config::$DB_PASSWORD );
				$exception->setAdditionalInfo( "name", Config::$DB_NAME );
				$exception->setAdditionalInfo( "code", $e->getCode() );
				$exception->setAdditionalInfo( "message", $e->getMessage() );
				throw $exception;
			}
		}


		public static function select( $what, $table, $where, $group, $having, $order, $limit, $bindings )
		{
			$query = "SELECT " . self::__parseWhat( $what ) . " FROM " . self::__parseTable( $table );

			if( !empty( $where ) ) {
				$query .= " WHERE " . self::__parseWhere( $where );
			}

			if( !empty( $group ) ) {
				$query .= " GROUP BY " . self::__parseGroup( $group );
			}

			if( !empty( $having ) ) {
				$query .= " HAVING " . self::__parseHaving( $having );
			}

			if( !empty( $order ) ) {
				$query .= " ORDER BY " . self::__parseOrder( $order );
			}

			if( !empty( $limit ) ) {
				$query .= " LIMIT " . self::__parseLimit( $limit );
			}

			return self::retrieve( $query, $bindings );
		}


		public static function insert( $table, $bindings )
		{
			$keys = implode( ", " , str_replace( ":", "", array_keys( $bindings ) ) );
			$values = implode( ", ", array_keys( $bindings ) );

			$query = "INSERT INTO " . self::__parseTable( $table ) . " ( $keys ) VALUES ( $values )";
			return self::execute( $query, $bindings );
		}


		public static function update( $table, $where, $limit, $bindings )
		{
			$set = array();
			foreach ( $bindings as $key => $value ) {
				$set[] = str_replace( ":", "", $key ) . " = " . $key;
			}

			$query = "UPDATE " . self::__parseTable( $table ) . " SET " . implode( ", ", $set );

			if( !empty( $where ) ) {
				$query .= " WHERE " . self::__parseWhere( $where );
			}

			if( !empty( $limit ) ) {
				$query .= " LIMIT " . self::__parseLimit( $limit );
			}

			return self::execute( $query, $bindings );
		}


		public static function delete( $table, $where, $limit, $bindings )
		{
			$query = "DELETE FROM " . self::__parseTable( $table );

			if( !empty( $where ) ) {
				$query .= " WHERE " . self::__parseWhere( $where );
			}

			if( !empty( $limit ) ) {
				$query .= " LIMIT " . self::__parseLimit( $limit );
			}

			return self::execute( $query, $bindings );
		}


		public static function retrieve( $query, $bindings )
		{
			return self::__query( $query, $bindings )->fetchAll( PDO::FETCH_ASSOC );
		}


		public static function execute( $query, $bindings )
		{
			$statement = self::__query( $query, $bindings );
			return array( "affectedRows" => $statement->rowCount(), "lastInsertId" => statement->lastInsertId() );
		}


		private static function __query( $query, $bindings )
		{
			$statement = self::$__connection->prepare( $query );
			$statement->execute( $bindings );

			if( $statement->errorCode() != "00000" ) {
				$exception = new ApiException( "Failed to execute query $query", Config::$LOG_LEVEL_ERROR_KEY );
				$exception->setAdditionalInfo( "source", "ApiDatabase::__query" );
				$exception->setAdditionalInfo( "query", $query );
				$exception->setAdditionalInfo( "bindings", $bindings );
				$exception->setAdditionalInfo( "code", $statement->errorCode() );
				$exception->setAdditionalInfo( "message", $statement->errorMessage() );
				throw $exception;
			}

			return $statement;
		}


		private static function __parseWhat( $what )
		{
			if( !is_array( $what ) ) {
				return $what;
			}

			$parsedWhat = array();
			foreach ( $what as $key => $value ) {
				$parsedWhat[] = is_numeric( $key ) ? $value : "$key AS $value";
			}

			return implode( ", ", $parsedWhat );
		}

		
		private static function __parseTable( $table )
		{
			if( !is_array( $table ) ) {
				return $table;
			}
			
			$pasedTable = "";
			foreach ( $table as $key => $value ) {
				$pasedTable .= is_numeric( $key ) ? "$value " : $value[ "joinType" ] . " JOIN $key ON " . $value[ "joinCondition" ] . " ";
			}

			return rtrim( $pasedTable, " " );
		}


		private static function __parseWhere( $where )
		{
			return is_array( $where ) ? implode( ", ", $where ) : $where;
		}


		private static function __parseGroup( $group )
		{
			return is_array( $group ) ? implode( ", ", $group ) : $group;
		}


		private static function __parseHaving( $having )
		{
			return is_array( $having ) ? implode( ", ", $having ) : $having;
		}


		private static function __parseOrder( $order )
		{
			if( !is_array( $order ) ) {
				return $order;
			}

			$parsedOrder = array();
			foreach ( $order as $key => $value ) {
				$parsedOrder[] = is_numeric( $key ) ? $value : "$key $value";
			}

			return implode( ", ", $parsedOrder );			
		}


		private static function __parseLimit( $limit )
		{
			return is_array( $limit ) ? implode( ", ", $limit ) : $limit;
		}

	}