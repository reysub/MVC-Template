<?php

	class FacadeException extends ControllerException
	{

		public function __construct( $message, $code, Exception $previous = null )
		{
			parent::__construct( $message, $code, $previous );
		}

	}