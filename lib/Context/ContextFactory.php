<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\Context
{
	use SplObjectStorage;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\RequestStack;
	use Symfony\Component\EventDispatcher\EventDispatcherInterface;
	use ZealByte\Platform\Context\Tag\ContextTagManagerInterface;
	use ZealByte\Platform\Component\DispatcherComponentInterface;
	use ZealByte\Platform\Component\ComponentInterface;
	use ZealByte\Platform\Component\Component;

	// TODO :: Temporary
	use ZealByte\Catalog\Context\CatalogContext;

	/**
	 * Context Factory
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class ContextFactory implements ContextFactoryInterface
	{
		private $contexts;

		private $requestContexts;

		private $requests;

		private $eventDispatcher;

		private $requestStack;

		public function __construct (ContextTagManagerInterface $context_tag_manager, ?RequestStack $request_stack = null, ?EventDispatcherInterface $event_dispatcher = null)
		{
			$this->contexts = new SplObjectStorage();

			$this->requestContexts = new SplObjectStorage();

			$this->contextTagManager = $context_tag_manager;

			if ($request_stack)
				$this->requestStack = $request_stack;

			if ($event_dispatcher)
				$this->setEventDispatcher($event_dispatcher);
		}

		/**
		 *
		 */
		public function createContext (Request $request = null, ?ComponentInterface $component = null, ?string $view = null, ?int $status = null) : ContextInterface
		{
			if (!$request && $this->hasRequestStack())
				$request = $this->requestStack->getCurrentRequest();

			if (!$this->hasContext($request))
				$this->setContext($request, new Context($component, $view, $status));

			if ($component)
				$this->statComponent($component);

			return $this->getContext($request);
		}

		public function setContext (Request $request, ContextInterface $context)
		{
			$this->requestContexts[$request] = $context;
			//$this->contexts->attach($context, $this->discoverContextOptions($name, $context));

			return $this;
		}

		public function hasRequestStack ()
		{
			return ($this->requestStack) ? true : false;
		}

		public function hasContext (Request $request)
		{
			return $this->requestContexts->contains($request);
		}

		public function getContext (Request $request)
		{
			return $this->requestContexts[$request];
		}

		/**
		 *
		 */
		public function getContextTagManager () : ContextTagManagerInterface
		{
			return $this->contextTagManager;
		}

		/**
		 *
		 */
		public function getContextOptions (ContextInterface $context) : array
		{
			if ($this->contexts->contains($context))
				return $this->contexts[$context];
		}

		/**
		 *
		 */
		public function getEventDispatcher () : EventDispatcherInterface
		{
			if (!$this->hasEventDispatcher())
				throw new \Exception("No event dispatcher has been set!");

			return $this->eventDispatcher;
		}

		/**
		 *
		 */
		public function hasEventDispatcher () : bool
		{
			return ($this->eventDispatcher) ? true : false;
		}

		/**
		 *
		 */
		public function setEventDispatcher (EventDispatcherInterface $event_dispatcher) : self
		{
			$this->eventDispatcher = $event_dispatcher;

			return $this;
		}

		/**
		 *
		 */
		private function statComponent (ComponentInterface $component) : void
		{
			if ($component instanceof DispatcherComponentInterface && !$component->hasEventDispatcher()) {
				if (!$this->hasEventDispatcher())
					throw new \RuntimeException("The ContextFactory Service must have an Event Dispatcher!");

				$component->setEventDispatcher($this->getEventDispatcher());
			}
		}

		/**
		 *
		 */
		private function discoverContextOptions (string $name, Context $context) : array
		{
			// TODO :: Possibly use to determine to AJAX load controller components or render internally
			return [
				'name' => $name,
			];
		}

	}
}
