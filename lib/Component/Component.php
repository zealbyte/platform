<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\Component
{
	use Symfony\Component\EventDispatcher\EventDispatcherInterface;

	/**
	 * Component
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class Component extends ComponentAbstract implements ComponentInterface
	{
		public function __construct (?string $view = null, ?array $parameters = null)
		{
			if ($view)
				$this->setView($view);

			if ($parameters)
				$this->setParameters($parameters);
		}

		/**
		 *
		 */
		public function addParameter (string $parameter, $value) : self
		{
			$this->parameters[$parameter] = $value;

			return $this;
		}

		/**
		 *
		 */
		public function setParameters (array $parameters) : self
		{
			$this->parameters = $parameters;

			return $this;
		}

	}
}
