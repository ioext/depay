<?php

namespace ioext\depay\Exception;

/**
 *	Invalid Response exception.
 *
 *	Thrown when a gateway responded with invalid or unexpected data (for example, a security hash did not match).
 */
class InvalidResponseException extends \Exception implements DePayException
{
    public function __construct($message = "Invalid response from payment gateway", $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
