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
	 * Event Dispatcher Aware Component Interface
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	interface DispatcherComponentInterface extends ComponentInterface
	{
		/**
		 *
		 */
		public function setEventDispatcher (EventDispatcherInterface $event_dispatcher) : DispatcherComponentInterface;

		/**
		 *
		 */
		public function hasEventDispatcher () : bool;
	}
}
