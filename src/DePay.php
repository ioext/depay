<?php

namespace dekuan\depay;


/**
 *     DePay
 */
class DePay
{
	/**
	 *	Internal factory storage
	 *
	 *	@var GatewayFactory
	 */
	private static $g_cFactory;

	/**
	 *	Get the gateway factory
	 *
	 *	Creates a new empty GatewayFactory if none has been set previously.
	 *
	 *	@return GatewayFactory A GatewayFactory instance
	 */
	public static function getFactory()
	{
		if ( is_null( static::$g_cFactory ) )
		{
			static::$g_cFactory = new GatewayFactory;
		}
		
		return static::$g_cFactory;
	}

	/**
	 *	Set the gateway factory
	 *
	 *	@param GatewayFactory $cFactory A GatewayFactory instance
	 */
	public static function setFactory( GatewayFactory $cFactory = null )
	{
		static::$g_cFactory = $cFactory;
	}

	/**
	 *	Static function call router.
	 *
	 *	All other function calls to the Omnipay class are routed to the
	 *	factory.  e.g. Omnipay::getSupportedGateways(1, 2, 3, 4) is routed to the
	 *	factory's getSupportedGateways method and passed the parameters 1, 2, 3, 4.
	 *
	 *	Example:
	 *
	 *	<code>
	 *		//	Create a gateway for the PayPal ExpressGateway
	 *		$gateway = DePay::create( 'WeChatPay' );
	 *	</code>
	 *
	 *	@see GatewayFactory
	 *
	 *	@param string $sMethod		The factory method to invoke.
	 *	@param array  $arrParameters	Parameters passed to the factory method.
	 *
	 *	@return mixed
	 */
	public static function __callStatic( $sMethod, $arrParameters )
	{
		$cFactory = static::getFactory();
		return call_user_func_array( [ $cFactory, $sMethod ], $arrParameters );
	}
	

}