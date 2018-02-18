<?php

namespace KudrMichal\Monolog;

use Monolog;

/**
 * @author Michal Kudr kudrmichal@gmail.com
 * @package kudrmichal\monolog
 */
class Logger extends Monolog\Logger
{
	public function __construct($name, $handlers = [], $processors = [])
	{
		parent::__construct($name, $handlers, $processors);
	}

	public function addRecord(int $level, $message, array $context = [])
	{
		if ($message instanceof \Exception) {
			$context['exception'] = $message;
		}
		$context['level'] = $level;

		return parent::addRecord($level, $message, $context);
	}
}
