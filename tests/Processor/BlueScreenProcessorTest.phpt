<?php

namespace KudrMichal\Monolog\Tests\Processor;

require_once __DIR__ . '/../bootstrap.php';

/**
 * @author Michal Kudr kudrmichal@gmail.com
 * @package KudrMichal\Monolog\Tests\Processor
 */
class BlueScreenProcessorTest extends \Tester\TestCase
{
	public function testRender()
	{
		$processor = new \KudrMichal\Monolog\Processor\BlueScreenProcessor();

		$data = [
			'context' => [
				'exception' => new \Exception('test exception'),
			],
			'message' => 'test message',

		];

		$updatedData = $processor($data);

		\Tester\Assert::true($data['message'] !== $updatedData['message']);
		\Tester\Assert::true($updatedData['message'] !== strip_tags($updatedData['message']));
		\Tester\Assert::true( (bool) preg_match('/<html>/', $updatedData['message']));
	}

	public function testNoRender()
	{
		$processor = new \KudrMichal\Monolog\Processor\BlueScreenProcessor();

		$data = [
			'message' => 'test message',

		];

		$updatedData = $processor($data);

		\Tester\Assert::true($data['message'] === $updatedData['message']);
		\Tester\Assert::true($updatedData['message'] === strip_tags($updatedData['message']));
		\Tester\Assert::false( (bool) preg_match('/<html>/', $updatedData['message']));
	}




}

(new BlueScreenProcessorTest())->run();
