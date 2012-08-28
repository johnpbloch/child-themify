<?php

class CTF_Exit_Overload {

	protected static $messages = array( );

	public static function handler( $message = null ) {
		self::$messages[] = $message;
		return false;
	}

	public static function message() {
		return end( self::$messages );
	}

	public static function count() {
		return count( self::$messages );
	}

}