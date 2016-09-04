<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock;

use Psr\Log\LoggerInterface;

class LoggerMock implements LoggerInterface
{
	/**
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function emergency($message, array $context = array())
	{
	}

	/**
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function alert($message, array $context = array())
	{
	}

	/**
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function critical($message, array $context = array())
	{
	}

	/**
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function error($message, array $context = array())
	{
	}

	/**
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function warning($message, array $context = array())
	{
	}

	/**
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function notice($message, array $context = array())
	{
	}

	/**
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function info($message, array $context = array())
	{
	}

	/**
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function debug($message, array $context = array())
	{
	}

	/**
	 * @param mixed $level
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function log($level, $message, array $context = array())
	{
	}
}
