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
	 * Component Abstract
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	abstract class ComponentAbstract implements ComponentInterface
	{
		const VIEW = '@Platform/components.html.twig';

		const BLOCK_NAME = 'component';

		protected $parameters = [];

		private $block;

		private $view;

		/**
		 * {@inheritdoc}
		 */
		public function getBlockName () : string
		{
			return $this->block ?: static::BLOCK_NAME;
		}

		/**
		 * {@inheritdoc}
		 */
		public function getData ()
		{
			return $this->getParameters();
		}

		/**
		 * {@inheritdoc}
		 */
		public function getView () : string
		{
			return $this->view ?: static::VIEW;
		}

		/**
		 * {@inheritdoc}
		 */
		public function getParameters () : array
		{
			return $this->parameters;
		}

		/**
		 * {@inheritdoc}
		 */
		public function hasBlockName () : bool
		{
			return ($this->block || static::BLOCK_NAME) ? true : false;
		}

		/**
		 * {@inheritdoc}
		 */
		public function hasData () : bool
		{
			return $this->hasParameters();
		}

		/**
		 * {@inheritdoc}
		 */
		public function hasParameters () : bool
		{
			return !(array() === $this->parameters);
		}

		/**
		 * {@inheritdoc}
		 */
		public function hasView () : bool
		{
			return ($this->view) ? true : false;
		}

		/**
		 *
		 */
		public function setBlock ($block) : self
		{
			$this->block = $block;

			return $this;
		}

		/**
		 *
		 */
		public function setView ($view) : self
		{
			$this->view = $view;

			return $this;
		}

		/**
		 *
		 */
		protected function getParameter (string $parameter)
		{
			return $this->parameters[$parameter];
		}

		/**
		 *
		 */
		protected function hasParameter (string $parameter) : bool
		{
			return array_key_exists($parameter, $this->parameters);
		}

		/**
		 *
		 */
		protected function setParameter (string $parameter, $value) : ComponentInterface
		{
			$this->parameters[$parameter] = $value;

			return $this;
		}

	}
}
