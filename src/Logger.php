<?php

namespace KudrMichal\Monolog;

use Monolog;

/**
 * @author Michal Kudr kudrmichal@gmail.com
 * @package kudrmichal\monolog
 */
class Logger extends Monolog\Logger
{

	public function addRecord(int $level, $message, array $context = []): bool
	{
		if ($message instanceof \Exception) {
			$context['exception'] = $message;
		}
		$context['level'] = $level;

		return parent::addRecord($level, $message, $context);
	}
}
