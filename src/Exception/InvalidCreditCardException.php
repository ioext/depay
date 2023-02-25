<?php

namespace ioext\depay\Exception;

/**
 *	Invalid Credit Card Exception
 *
 *	Thrown when a credit card is invalid or missing required fields.
 */
class InvalidCreditCardException extends \Exception implements DePayException
{
}
