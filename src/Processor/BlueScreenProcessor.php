<?php


namespace KudrMichal\Monolog\Processor;

/**
 * @author Michal Kudr kudrmichal@gmail.com
 * @package KudrMichal\Monolog\Processor
 */
class BlueScreenProcessor
{
	public function __invoke(array $record): array {
		if (isset($record['context']['exception']) && $record['context']['exception'] instanceof \Throwable) {
			ob_start();
			(new \Tracy\BlueScreen())->render($record['context']['exception']);
			$record['message'] = ob_get_clean();
		}
		return $record;
	}
}
