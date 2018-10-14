<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\Logger\Handler
{
	use Monolog\Logger;
	use Monolog\Handler\AbstractProcessingHandler;
	use Fluent\Logger\FluentLogger;
	use Fluent\Logger\Entity;

	/**
	 * Fluent Logger Handler
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class FluentHandler extends AbstractProcessingHandler
	{
		private $fluent;

		private $address;

		private $port;

		private $options;

		/**
		 * Init
		 *
		 * @param string $address The fluentd URI.
		 * @param int $port The fluentd port.
		 * @param int $level The log levels to output to fluentd
		 * @param bool $bubble To bubble log messages
		 * @param array $options
		 */
		public function __construct (?string $address = null, ?int $port = null, ?int $level = Logger::ERROR, ?bool $bubble = true, ?array $options = null)
		{
			parent::__construct($level, $bubble);

			if ($address)
				$this->setAddress($address);

			if ($port)
				$this->setPort($port);

			if ($options)
				$this->setOptions($options);
		}

		/**
		 *
		 *
		 */
		public function getAddress () : ?string
		{
			return $this->address ?: FluentLogger::DEFAULT_ADDRESS;
		}

		/**
		 *
		 *
		 */
		public function getPort () : ?int
		{
			return $this->port ?: FluentLogger::DEFAULT_LISTEN_PORT;
		}

		/**
		 *
		 *
		 */
		public function getOptions () : array
		{
			return $this->options ?: [];
		}

		/**
		 *
		 *
		 */
		public function setAddress ( string $address ) : void
		{
			$this->address = $address;
		}

		/**
		 *
		 *
		 */
		public function setPort ( int $port ) : void
		{
			$this->port = $port;
		}

		/**
		 *
		 *
		 */
		public function setOptions ( array $options ) : void
		{
			$this->options = $options;
		}

		/**
		 * {@inheritdoc}
		 */
		public function close()
		{
			$this->fluent->close();
		}

		/**
		 *
		 *
		 */
		protected function statFluent () : void
		{
			if (!$this->fluent)
				$this->fluent = FluentLogger::open($this->getAddress(), $this->getPort());
		}

		/**
		 * {@inheritdoc}
		 */
		protected function write (array $record)
		{
			$this->statFluent();

			$tag = $this->buildTag($record);
			$timestamp = $record['datetime']->getTimestamp();

			unset($record['formatted'], $record['datetime']);

			try {
				$this->fluent->post2(new Entity($tag, $record, $timestamp));
			} catch (\Exception $e) {
				throw $e;
			}
		}

		/**
		 *
		 */
		private function buildTag (array $record) : string
		{
			$tag = 'app.';
			$indices = ['channel'];

			foreach ($indices as $index) {
				if (array_key_exists($index, $record))
					$tag .= "{$record[$index]}.";
			}

			$tag = trim($tag, '.');

			return $tag;
		}
	}
}
