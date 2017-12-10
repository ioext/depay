<?php

namespace dekuan\depay;

use dekuan\delib\CLib;
use Guzzle\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

use dekuan\depay\Exception\RuntimeException;


/**
 *	DePay Gateway Factory class
 *
 *	This class abstracts a set of gateways that can be independently
 *	registered, accessed, and used.
 *
 *	Note that static calls to the DePay class are routed to this class by
 *	the static call router (__callStatic) in DePay.
 *
 *	Example:
 *
 *	<code>
 *		//	Create a gateway for the PayPal ExpressGateway
 *		//	(routes to GatewayFactory::create)
 *		$gateway = DePay::create( 'ExpressGateway' );
 *	</code>
 *
 *	@see \dekuan\depay\DePay
 */
class GatewayFactory
{
	/**
	 *	Internal storage for all available gateways
	 *
	 *	@var array
	 */
	private $m_arrGateways = array();

	/**
	 *	All available gateways
	 *
	 *	@return array An array of gateway names
	 */
	public function all()
	{
		return $this->m_arrGateways;
	}

	/**
	 *	Replace the list of available gateways
	 *
	 *	@param array $arrGateways An array of gateway names
	 */
	public function replace( array $arrGateways )
	{
		$this->m_arrGateways = $arrGateways;
	}

	/**
	 *	Register a new gateway
	 *
	 *	@param string $sClassName Gateway name
	 */
	public function register( $sClassName )
	{
		if ( CLib::IsExistingString( $sClassName ) )
		{
			if ( ! in_array( $sClassName, $this->m_arrGateways ) )
			{
				$this->m_arrGateways[] = $sClassName;
			}
		}
	}

	/**
	 *	Automatically find and register all officially supported gateways
	 *
	 *	@return array An array of gateway names
	 */
	public function find()
	{
        	foreach ( $this->getSupportedGateways() as $gateway )
        	{
			$class = Helper::getGatewayClassName( $gateway );
			if ( class_exists( $class ) )
			{
				$this->register( $gateway );
			}
		}

		ksort( $this->m_arrGateways );
		return $this->all();
	}

	/**
	 *	Create a new gateway instance
	 *
	 *	@param string			$sGatewayName	Gateway name
	 *	@param ClientInterface|null	$httpClient	A Guzzle HTTP Client implementation
	 *	@param HttpRequest|null		$httpRequest	A Symfony HTTP Request implementation
	 *	@throws RuntimeException			If no such gateway is found
	 *	@return GatewayInterface			An object of class $class is created and returned
	 */
	public function create( $sGatewayName, ClientInterface $httpClient = null, HttpRequest $httpRequest = null )
	{
		$sGatewayName = Helper::getGatewayClassName( $sGatewayName );

		if ( ! class_exists( $sGatewayName ) )
		{
			throw new RuntimeException( "Class '$sGatewayName' not found" );
		}

		return new $sGatewayName( $httpClient, $httpRequest );
	}

	/**
	 *	Get a list of supported gateways which may be available
	 *
	 *	@return array
	 */
	public function getSupportedGateways()
	{
		$package = json_decode( file_get_contents(__DIR__.'/../../../composer.json' ), true );
		return $package[ 'extra' ][ 'gateways' ];
	}
}
