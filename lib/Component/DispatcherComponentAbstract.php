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
	 * Event Dispatcher Aware Component Abstract
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	abstract class DispatcherComponentAbstract extends ComponentAbstract implements DispatcherComponentInterface
	{
		private $eventDispatcher;

		/**
		 * {@inheritdoc}
		 */
		public function setEventDispatcher (EventDispatcherInterface $event_dispatcher) : DispatcherComponentInterface
		{
			$this->eventDispatcher = $event_dispatcher;

			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public function hasEventDispatcher () : bool
		{
			return ($this->eventDispatcher) ? true : false;
		}

		/**
		 *
		 */
		protected function getEventDispatcher () : EventDispatcherInterface
		{
			return $this->eventDispatcher;
		}

	}
}
