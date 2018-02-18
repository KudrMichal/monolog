<?php

namespace KudrMichal\Monolog\Exception;

/**
 * Logger with specified name does not exist
 *
 * @author Michal Kudr kudrmichal@gmail.com
 * @package KudrMichal\Monolog\Exceptions
 */
class LoggerNotExistsException extends MonologException
{

	public static function create(string $name): LoggerNotExistsException
	{
		return new static(sprintf('Logger \'%s\' does not exist!', $name));
	}

}

