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
	 * Container Component
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class ContainerComponent extends ComponentAbstract implements ContainerComponentInterface
	{
		const BLOCK_NAME = 'container_component';

		public function __construct (?string $view = null, ?array $components = null)
		{
			$this->parameters['_components'] = [];

			if ($view)
				$this->setView = ($view);

			if ($components)
				foreach ($components as $component)
					$this->addComponent($component);
		}

		/**
		 * {@inheritdoc}
		 */
		public function getComponents () : array
		{
			return $this->parameters['_components'];
		}

		/**
		 * {@inheritdoc}
		 */
		public function hasComponents () : bool
		{
			return !([] === $this->parameters['_components']);
		}

		/**
		 *
		 */
		public function addComponent (ComponentInterface $component) : self
		{
			$this->parameters['_components'][] =  $component;

			return $this;
		}

	}
}
