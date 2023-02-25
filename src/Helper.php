<?php
namespace ioext\depay;

use InvalidArgumentException;

/**
 * Helper class
 *
 * This class defines various static utility functions that are in use
 * throughout the Omnipay system.
 */
class Helper
{
	/**
	 *	Convert a string to camelCase. Strings already in camelCase will not be harmed.
	 *
	 *	@param  string  $str The input string
	 *	@return string camelCased output string
	 */
	public static function camelCase( $str )
	{
        	$str = self::convertToLowercase( $str );
        	return preg_replace_callback(
			'/_([a-z])/',
			function( $match )
			{
				return strtoupper( $match[ 1 ] );
			},
			$str
		);
	}

	/**
	 *	Convert strings with underscores to be all lowercase before camelCase is preformed.
	 *
	 *	@param  string $sString		The input string
	 *	@return string The output string
	 */
	protected static function convertToLowercase( $sString )
	{
		return ( is_string( $sString ) && strchr( $sString, '_' ) )
			?
			trim( strtolower( $sString ) ) : trim( $sString );
	}

	/**
	 *	Validate a card number according to the Luhn algorithm.
	 *
	 *	@param  string  $number The card number to validate
	 *	@return boolean True if the supplied card number is valid
	 */
	public static function validateLuhn( $number )
	{
		$str = '';
		foreach ( array_reverse( str_split( $number ) ) as $i => $c )
		{
			$str .= ( $i % 2 ? $c * 2 : $c );
		}
	
		return 0 === array_sum( str_split( $str ) ) % 10;
	}

	/**
	 *	Initialize an object with a given array of parameters
	 *
	 *	Parameters are automatically converted to camelCase. Any parameters which do
	 *	not match a setter on the target object are ignored.
	 *
	 *	@param mixed $target		The object to set parameters on
	 *	@param array $parameters An array of parameters to set
	 */
	public static function initialize( $target, $parameters )
	{
		if ( is_array( $parameters ) )
		{
			foreach ( $parameters as $key => $value )
			{
				$method = 'set' . ucfirst( static::camelCase( $key ) );
				if ( method_exists( $target, $method ) )
				{
					$target->$method( $value );
				}
			}
		}
	}

	/**
	 *	Resolve a gateway class to a short name.
	 *
	 *	The short name can be used with GatewayFactory as an alias of the gateway class,
	 *	to create new instances of a gateway.
	 *
	 *	@param $sClassName
	 *	@return string
	 */
	public static function getGatewayShortName( $sClassName )
	{
		$sSearch	= "\\ioext\\depay\\";
		$nSearchLen	= strlen( $sSearch );
		if ( 0 === strpos( $sClassName, $sSearch ) )
		{
			return trim( str_replace(
				"\\",
				"_",
				substr( $sClassName, $nSearchLen, -7 ) ), '_'
			);
		}

		return "\\" . $sClassName;
	}

	/**
	*	Resolve a short gateway name to a full namespaced gateway class.
	*
	*	Class names beginning with a namespace marker (\) are left intact.
	*	Non-namespaced classes are expected to be in the \ioext\depay\ namespace, e.g.:
	*
	*	\Custom\Gateway		=> \Custom\Gateway
	*	\Custom_Gateway		=> \Custom\Gateway
	*	WeChatPay		=> \ioext\depay\WeChatPay\Gateway
	*	PayPal\Express		=> \ioext\depay\PayPal\Express\Gateway
	*	PayPal_Express		=> \ioext\depay\PayPal\Express\Gateway
	*
	*	@param  string	$sShortName	The short gateway name
	*	@return string		The fully gateway class name with namespace
	*/
	public static function getGatewayClassName( $sShortName )
	{
		if ( 0 === strpos( $sShortName, '\\' ) )
		{
			//	class name begin with '\'
			return $sShortName;
		}

		//	replace underscores with namespace marker, PSR-0 style
		$sShortName	= str_replace( '_', '\\', $sShortName );
		$sShortName	= rtrim( $sShortName, "\\" );

		return "\\ioext\\depay\\" . $sShortName . "\\Gateway";
	}


	/**
	 *	Convert an amount into a float.
	 *	The float datatype can then be converted into the string
	 *	format that the remote gateway requies.
	 *
	 *	@var string|int|float $value The value to convert.
	 *	@throws InvalidArgumentException on a validation failure.
	 *	@return float The amount converted to a float.
	 */
	public static function toFloat( $vValue )
	{
        	if ( ! is_string( $vValue ) && ! is_int( $vValue ) && ! is_float( $vValue ) )
        	{
			throw new InvalidArgumentException('Data type is not a valid decimal number.');
		}

		if ( is_string( $vValue ) )
		{
			//	Validate generic number, with optional sign and decimals.
			if ( ! preg_match( '/^[-]?[0-9]+(\.[0-9]*)?$/', $vValue ) )
			{
				throw new InvalidArgumentException( 'String is not a valid decimal number.' );
			}
		}

		return (float)$vValue;
	}

}