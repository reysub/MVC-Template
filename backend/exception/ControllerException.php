<?php

	class ControllerException extends Exception
	{
		private $__additionalInfo;


		public function __construct( $message, $code, Exception $previous = null )
		{
			parent::__construct( $message, $code, $previous );
			$this->__additionalInfo = array();
		}


		public function getAdditionalInfo( $key )
		{
			if( !empty( $key ) && isset( $this->__additionalInfo[ $key ] ) ) {
				return $this->__additionalInfo[ $key ];
			}

			return $this->__additionalInfo;
		}


		public function addAdditionalInfo( $key, $value )
		{
			$this->__additionalInfo[ $key ] = $value;
		}


		public function setAdditionalInfo( $additionalInfo )
		{
			$this->__additionalInfo = $additionalInfo;
		}


		public function getLogMessage()
		{
			$additionalInfoString = "";
			foreach ( $this->__additionalInfo as $key => $value ) {
				$additionalInfoString .= is_array( $value ) ? "$key:\n" . print_r( $value, TRUE ) : "$key: $value";
			}

			return	$this->getCode() . " - " . $this->getMessage() . "\nAdditional info:\n$additionalInfoString";
		}
	}