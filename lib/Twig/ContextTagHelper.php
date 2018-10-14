<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\Twig
{
	use ZealByte\Platform\Context\ContextTagManagerInterface;

	/**
	 * Context Tag Helper
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class ContextTagHelper
	{
		private $contextTagContainer;

		public function __construct (ContextTagManagerInterface $contextTagContainer)
		{
			$this->contextTagContainer = $contextTagContainer;
		}

		public function __get (string $name)
		{
			return $this->contextTagContainer->get($name);
		}

		public function __set (string $name, $value)
		{
			$this->contextTagContainer->set($name, $value);
		}

		public function __isset (string $name)
		{
			return $this->contextTagContainer->has($name);
		}

	}
}
