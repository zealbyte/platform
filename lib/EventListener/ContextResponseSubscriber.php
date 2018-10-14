<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\EventListener
{
	use Symfony\Component\HttpKernel\KernelEvents;
	use Symfony\Component\EventDispatcher\EventSubscriberInterface;
	use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
	use Symfony\Component\DependencyInjection\ContainerInterface;
	use ZealByte\Platform\Context\ContextHandlerInterface;
	use ZealByte\Platform\Context\ContextInterface;

	/**
	 * Context Return Event Subscriber
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	final class ContextResponseSubscriber implements EventSubscriberInterface
	{
		const CONTEXT_HANDLER_PRIORITY = 1;

		private $contextHandler;

		public static function getSubscribedEvents ()
		{
			return [
				KernelEvents::VIEW => [
					['onKernelView', self::CONTEXT_HANDLER_PRIORITY],
				],
			];
		}

		public function __construct (ContextHandlerInterface $context_handler = null)
		{
			if ($context_handler)
				$this->setContextHandler($context_handler);
		}

		public function setContextHandler (ContextHandlerInterface $context_handler) : self
		{
			$this->contextHandler = $context_handler;

			return $this;
		}

		public function onKernelView (GetResponseForControllerResultEvent $event) : void
		{
			if ($this->contextHandler)
				$this->contextHandler->handleEvent($event);
		}

	}
}
