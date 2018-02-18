<?php


namespace KudrMichal\Monolog;

/**
 * @author Michal Kudr kudrmichal@gmail.com
 * @package kudrmichal\monolog
 */
class Container
{
	/**
	 * @var Logger[]
	 */
	private $loggers = [];


	public function __construct()
	{

	}


	public function add(Logger $logger): void
	{
		$this->loggers[$logger->getName()] = $logger;
	}


	/**
	 * @throws Exceptions\LoggerNotExistsException
	 */
	public function get(string $name): Logger
	{
		if ( ! isset($this->loggers[$name])) {
			throw \KudrMichal\Monolog\Exceptions\LoggerNotExistsException::create($name);
		}
	    return $this->loggers[$name];
	}

}
