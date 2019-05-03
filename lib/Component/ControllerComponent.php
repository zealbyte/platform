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
	/**
	 * Controller Component
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class ControllerComponent extends ComponentAbstract implements ControllerComponentInterface
	{
		const BLOCK_NAME = 'controller_component';

		public function __construct (string $route, array $route_parameters = null)
		{
			$this->parameters = [
				'_route' => null,
				'_route_parameters' => [],
			];

			if ($route)
				$this->setRoute($route);

			if ($route_parameters)
				foreach ($route_parameters as $parameter => $value)
					$this->addRouteParameter($parameter, $value);
		}

		/**
		 * {@inheritdoc}
		 */
		public function getRoute () : string
		{
			return $this->parameters['_route'];
		}

		/**
		 * {@inheritdoc}
		 */
		public function getRouteParameters () : array
		{
			return $this->parameters['_route_parameters'];
		}

		/**
		 * {@inheritdoc}
		 */
		public function hasRoute () : bool
		{
			return !([] === $this->parameters['_route']);
		}

		/**
		 * {@inheritdoc}
		 */
		public function hasRouteParameters () : bool
		{
			return !([] === $this->parameters['_route_parameters']);
		}

		/**
		 *
		 */
		public function addRouteParameter (string $parameter, $value) : self
		{
			$this->parameters['_route_parameters'][$parameter] = $value;

			return $this;
		}

		/**
		 *
		 */
		public function setRoute (string $route) : self
		{
			$this->parameters['_route'] = $route;

			return $this;
		}

	}
}
