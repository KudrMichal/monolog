<?php


namespace KudrMichal\Monolog\Exception;

/**
 * @author Michal Kudr kudrmichal@gmail.com
 * @package KudrMichal\Monolog\Exceptions
 */
class IllegalCharacterException extends MonologException
{

	public static function create(): IllegalCharacterException
	{
		return new static('Only alphanumeric characters are allowed in logger/handler/processor aliases.');
	}

}
