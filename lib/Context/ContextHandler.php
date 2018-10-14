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
	use Twig_Environment;
	use Twig_Template;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\PropertyAccess\PropertyAccess;
	use Symfony\Component\HttpKernel\Event\GetResponseEvent;
	use ZealByte\Platform\Component\ComponentInterface;
	use ZealByte\Platform\Component\ContainerComponentInterface;
	use ZealByte\Platform\Context\ContextFactoryInterface;
	use ZealByte\Platform\Context\Tag\ContextTagManagerInterface;
	use ZealByte\Platform\Context\ContextInterface;
	use ZealByte\Platform\Context\Context;

	/**
	 * Context Handler
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class ContextHandler implements ContextHandlerInterface
	{
		private $contextTags;

		private $twig;

		private $defaultView;

		private $options = [];

		public function __construct (ContextFactoryInterface $context_factory, ?Twig_Environment $twig = null, ?array $context_options = null, ?string $default_view = null)
		{
			$this->contextFactory = $context_factory;
			$this->setContextTagManager($context_factory->getContextTagManager());

			if ($twig)
				$this->setTwigEnvironment($twig);

			if ($context_options)
				foreach ($context_options as $context => $options)
					$this->setContextOptions($context, $options);

			if ($default_view)
				$this->setDefaultView($default_view);
		}

		public function handleEvent (GetResponseEvent $event) : void
		{
			$isMasterRequest = $event->isMasterRequest();
			$request = $event->getRequest();
			$result = $event->getControllerResult();

			if ($request && $result && ($result instanceof ContextInterface)) {
				$response = ($result->hasResponse()) ?
					$result->getResponse() : $this->handleContext($request, $result, $isMasterRequest);

				$event->setResponse($response);
			}
		}

		public function handleContext (Request $request, ContextInterface $context, ?bool $wrap_response = true) : Response
		{
			$status = ($context->hasStatus()) ? $context->getStatus() : Response::HTTP_OK;

			if ($request->isXmlHttpRequest())
				$response = $this->handleXhrRequest($context, $request, $status);
			else
				$response = $this->handleRequest($context, $request, $status, $wrap_response);

			$this->filterResponse($response, false);

			return $response;
		}

		public function setContextTagManager (ContextTagManagerInterface $context_tags) : self
		{
			$this->contextTags = $context_tags;

			return $this;
		}

		public function setTwigEnvironment (Twig_Environment $twig) : self
		{
			$this->twig = $twig;

			return $this;
		}

		public function setContextOptions (string $context, array $options) : self
		{
			$this->options[$context] = $options;

			return $this;
		}

		public function setDefaultView (string $default_view) : self
		{
			$this->defaultView = $default_view;

			return $this;
		}

		public function getView (string $context) : string
		{
			if (array_key_exists($context, $this->options))
				if (array_key_exists('view', $this->options[$context]))
					return $this->options[$context]['view'];

			return $this->defaultView;
		}

		protected function filterResponse (Response $response, bool $is_data_request)
		{
			$noCacheHeaders = [
					'no-cache' => true,
					'max-age' => 0,
					'must-revalidate' => true,
					'no-store' => true
				];

			$response->setVary("X-Requested-With");

			if ($is_data_request)
				foreach ($noCacheHeaders as $header => $value)
					$response->headers->addCacheControlDirective($header, $value);
		}

		protected function handleXhrRequest (ContextInterface $context, Request $request, int $status) : Response
		{
			$type = ($request->attributes->has('_context')) ? $request->attributes->get('_context') : get_class($context);
			$content = trim($this->renderContextComponent($context));

			$contextData = [
				'context' => $this->compileWebApp([
					'content' => $content,
					'type' => $type,
				]),
				'data' => $this->getComponentData($context),
			];

			$response = $context->hasResponse() ?
				$context->getResponse() : new JsonResponse($contextData, $status);

			return $response;
		}

		protected function handleRequest (ContextInterface $context, Request $request, int $status, bool $wrap_response) : Response
		{
			if ($wrap_response) {
				$content = $this->wrapResponse($context, $request);
			}
			else {
				$content = $this->renderContextComponent($context);
			}

			$response = $context->hasResponse() ?
				$context->getResponse() : new Response($content, $status, ['Content-Type' => 'text/html']);

			return $response;
		}

		private function wrapResponse (ContextInterface $context, Request $request) : string
		{
			$type = ($request->attributes->has('_context')) ? $request->attributes->get('_context') : get_class($context);

			$view = $context->hasView() ? $context->getView() : $this->getView($type);

			if (array_key_exists($type, $this->options))
				foreach ($this->options[$type] as $option => $value)
					if ($this->contextTags->has($option))
						$this->contextTags->set($option, $value);

			$content = $this->renderContextComponent($context);

			if ($context->hasResponse())
				return '';

			$parameters = $this->compileWebApp([
				'content' => $content,
			]);

			return $this->twig->render($view, $parameters);
		}

		private function compileWebApp (array $parameters = []) : array
		{
			if ($this->contextTags)
				foreach ($this->contextTags->getTags() as $tag)
					$parameters[$tag] = $this->contextTags->get($tag);

			return $parameters;
		}

		private function renderContextComponent (ContextInterface $context)
		{
			if (!$context->hasComponent())
				return '';

			if (!$this->twig)
				throw new \LogicException('The Templating Component or the Twig Bundle are not available. Try running "composer require symfony/twig-bundle".');

			$component = $context->getComponent();

			return $this->renderComponent($component);
		}

		private function renderComponent (ComponentInterface $component, ?int $depth = 0) : string
		{
			$view = $component->getView();
			$block = $component->getBlockName();
			$parameters = $component->getParameters();
			$template = $this->twig->loadTemplate($view);

			if ($component instanceof ContainerComponentInterface)
				$parameters['components'] = $this->renderContainerComponents($component, $depth);

			return $this->renderComponentTemplate($template, $parameters, $block);
		}

		private function renderContainerComponents (ContainerComponentInterface $container, int $depth) : array
		{
			if (10 < $depth)
				throw new \Exception("$depth is max depth of component containers!");

			$components = [];

			foreach ($container->getComponents() as $component) {
				$content = $this->renderComponent($component, ($depth + 1));

				$parameters = $this->compileWebApp([
					'content' => $content,
				]);

				array_push($components, $parameters);
			}

			return $components;
		}

		public function renderComponentTemplate (Twig_Template $template, ?array $parameters = [], ?string $block = null) : string
		{
			if ($block && $template->hasBlock($block, $parameters))
				return $template->renderBlock($block, $parameters);

			return $template->render($parameters);
		}

		private function getComponentData (ContextInterface $context)
		{
			if ($context->hasComponent())
				return $context->getComponent()->getData();

			return [];
		}

	}
}
