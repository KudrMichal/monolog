<?php

namespace KudrMichal\Monolog\Tests\DI;

require_once __DIR__ . '/../bootstrap.php';

/**
 *
 * @author Michal Kudr kudrmichal@gmail.com
 * @package KudrMichal\Monolog\Tests\Unit
 * @testCase
 */
class MonologExtensionTest extends \Tester\TestCase
{

	/**
	 * @dataProvider createContainer
	 */
	public function testContainer(\Nette\DI\Container $container) {
		\Tester\Assert::true(
			$container->getService('kudrmichal.monolog.container') instanceof \KudrMichal\Monolog\Container
		);
	}


	/**
	 * @dataProvider createContainer
	 */
	public function testLoggers(\Nette\DI\Container $container)
	{
		\Tester\Assert::true(
			$container->getService('kudrmichal.monolog.logger.default') instanceof \KudrMichal\Monolog\Logger
		);

		\Tester\Assert::true(
			$container->getService('kudrmichal.monolog.logger.test') instanceof \KudrMichal\Monolog\Logger
		);
	}


	/**
	 * @dataProvider createContainer
	 */
	public function testHandlers(\Nette\DI\Container $container)
	{
		\Tester\Assert::true(
			$container->getService('kudrmichal.monolog.handler.error') instanceof \Monolog\Handler\HandlerInterface
		);
	}


	protected function createContainer(): array
	{
		$config = new \Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);
		$config->addConfig(__DIR__ . '/config/tests.neon');
		$config->addConfig(__DIR__ . '/config/monolog.neon');
		\KudrMichal\Monolog\DI\MonologExtension::register($config);

		return [[$config->createContainer()]];
	}

}

(new MonologExtensionTest())->run();
