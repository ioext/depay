<?php

namespace dekuan\depay;


/**
 *	DePay class
 *
 *	Provides static access to the gateway factory methods.  This is the
 *	recommended route for creation and establishment of payment gateway
 *	objects via the standard GatewayFactory.
 *
 *	Example:
 *
 *	<code>
 *	//	Create a gateway for the PayPal ExpressGateway
 *	//	(routes to GatewayFactory::create)
 *	$gateway = DePay::create('ExpressGateway');
 *
 *	//	Initialise the gateway
 *	$gateway->initialize(...);
 *
 *	//	Get the gateway parameters.
 *	$parameters = $gateway->getParameters();
 *
 *	//	Create a credit card object
 *	$card = new CreditCard(...);
 *
 *	//	Do an authorisation transaction on the gateway
 *	if ( $gateway->supportsAuthorize() )
 *	{
 *		$gateway->authorize(...);
 *	}
 *	else
 *	{
 *		throw new \Exception('Gateway does not support authorize()');
 *	}
 *	</code>
 *
 *
 *	@method static array  all()
 *	@method static array  replace(array $gateways)
 *	@method static string register(string $className)
 *	@method static array  find()
 *	@method static array  getSupportedGateways()
 *	@codingStandardsIgnoreStart
 *	@method static \dekuan\depay\GatewayInterface create(string $class, \GuzzleHttp\ClientInterface $httpClient = null, \Symfony\Component\HttpFoundation\Request $httpRequest = null)
 *	@codingStandardsIgnoreEnd
 *
 *	@see \dekuan\depay\GatewayFactory
 */
class CDePay
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