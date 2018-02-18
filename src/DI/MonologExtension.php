<?php

namespace KudrMichal\Monolog\DI;


/**
 * Monolog extension for nette. Creates a registers container and loggers
 *
 * @author Michal Kudr kudrmichal@gmail.com
 * @package kudrmichal\monolog\DI
 */
class MonologExtension extends \Nette\DI\CompilerExtension
{

	private const TAG_LOGGER = "kudrmichal.monolog.logger";
	private const TAG_HANDLER = "kudrmichal.monolog.handler";
	private const TAG_PROCESSOR = "kudrmichal.monolog.processor";

	private $defaults = [
		'debug' => '%debugMode%',
		'loggers' => [],
		'handlers' => [],
		'processors' => [],
	];

	private $loggerDefaults = [
		'logAware' => FALSE,
		'autowired' => FALSE,
		'handlers' => [],
		'processors => []'
	];


	public static function register(\Nette\Configurator $configurator): void
	{
		$configurator->onCompile[] = function (\Nette\Configurator $config, \Nette\DI\Compiler $compiler) {
			$compiler->addExtension('monolog', new MonologExtension());
		};
	}


	public function getConfig(): array
	{
		static $config;
		if (is_null($config)) {
			$config = parent::getConfig($this->defaults);
			foreach($config['loggers'] as &$loggerConfig) {
				$loggerConfig = array_merge($this->loggerDefaults, $loggerConfig);
			}
		}
		return $config;
	}


	public function prefix($id): string
	{
		return 'kudrmichal.'.parent::prefix($id);
	}


	public function loadConfiguration(): void
	{
		$config = $this->getConfig();

		$this->adjustLogDirectory($config);
		$this->registerContainer();
		$this->registerProcessors($config);
		$this->registerHandlers($config);
		$this->registerLoggers($config);

	}

	/**
	 * Resolves log directory for filesystem handlers
	 */
	protected function adjustLogDirectory(): void
	{
		$builder = $this->getContainerBuilder();
		if (!isset($builder->parameters["logDir"])) {
			if ( ! empty(\Tracy\Debugger::$logDirectory)) {
				$builder->parameters["logDir"] = \Tracy\Debugger::$logDirectory;

			} else {
				$builder->parameters["logDir"] = \Nette\DI\Helpers::expand("%appDir%/../log", $builder->parameters);
			}
		}

		if (!file_exists($builder->parameters['logDir'])) {
			@mkdir($builder->parameters['logDir'], 0777, TRUE);
		}

	}


	protected function registerContainer(): \Nette\DI\ServiceDefinition
	{
		return $this->getContainerBuilder()
			->addDefinition($this->prefix('container'))
			->setClass(\KudrMichal\Monolog\Container::class)
		;
	}


	protected function registerProcessors(array $config): void
	{
		foreach ($config['processors'] as $processorName => $implementation) {
			$this->validateName($processorName);
			$this->compiler->parseServices(
				$this->getContainerBuilder(),
				[
					'services' => [
						$serviceName = $this->prefix('processor.' . $processorName) => $implementation
					],
				]
			);
			$this->getContainerBuilder()->getDefinition($serviceName)->addTag(self::TAG_PROCESSOR);
		}
	}

	protected function registerHandlers(array $config): void
	{
		foreach ($config['handlers'] as $handlerName => $implementation) {
			$this->validateName($handlerName);
			$this->getContainerBuilder()
				->addDefinition($this->prefix('handler.' . $handlerName))
				->setClass($implementation['class'], $implementation['arguments'])
				->addTag(self::TAG_HANDLER)
			;
		}
	}

	protected function registerLoggers(array $config): void
	{
		foreach ($config['loggers'] as $loggerName => $loggerConfig) {
			$this->validateName($loggerName);
			$this->getContainerBuilder()
				->addDefinition($this->prefix('logger.' . $loggerName))
				->setClass(\KudrMichal\Monolog\Logger::class, [$loggerName])
				->setAutowired(isset($loggerConfig['autowired']) && (bool) $loggerConfig['autowired'])
				->addTag(self::TAG_LOGGER)
			;
		}
	}

	/**
	 * Push loggers into container, handlers and processors into loggers
	 */
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig();

		$container = current($builder->findByType(\KudrMichal\Monolog\Container::class));
		
		foreach($config['loggers'] as $loggerName => $loggerConfig) {
			$logger = $builder->getDefinition($this->prefix('logger.' . $loggerName));
			if (isset($loggerConfig['handlers'])) {
				foreach($loggerConfig['handlers'] as $handlerName) {
					$logger->addSetup('pushHandler', ['@'.$this->prefix('handler.' . $handlerName)]);
				}
			}

			if (isset($loggerConfig['processors'])) {
				foreach($loggerConfig['processors'] as $processorName) {
					$logger->addSetup('pushProcessor', ['@'.$this->prefix('processor.' . $processorName)]);
				}
			}

			$container->addSetup('add', [$logger]);

			if ($loggerConfig['logAware']) {
				foreach ($builder->findByType(\Psr\Log\LoggerAwareInterface::class) as $service) {
					$service->addSetup('setLogger', ['@' . $this->prefix('logger.' . $loggerName)]);
				}
			}
		}

		foreach($config['handlers'] as $handlerName => $handlerConfig) {
			$handler = $builder->getDefinition($this->prefix('handler.'.$handlerName));
			if (isset($handlerConfig['processors'])) {
				foreach($handlerConfig['processors'] as $processorName) {
					$handler->addSetup('pushProcessor', ['@'.$this->prefix('processor.'.$processorName)]);
				}
			}
		}

	}


	/**
	 * Logger/handler/processor name must contains only alphanumeric characters
	 * @throws \KudrMichal\Monolog\Exception\IllegalCharacterException
	 */
	private function validateName(string $name): void
	{
		if (preg_match("/.*[^\\w].*/", $name)) {
			throw \KudrMichal\Monolog\Exception\IllegalCharacterException::create();
		}
	}




}
