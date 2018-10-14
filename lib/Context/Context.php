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
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use ZealByte\Platform\Component\ComponentInterface;

	/**
	 * Context
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class Context implements ContextInterface
	{
		private $view;

		private $component;

		private $status;

		private $response;

		public function __construct (?ComponentInterface $component = null, ?string $view = null, ?int $status = null)
		{
			if ($component)
				$this->setComponent($component);

			if ($view)
				$this->setView($view);

			if ($status)
				$this->setStatus($status);
		}

		/**
		 *
		 */
		public function getComponent () : ComponentInterface
		{
			return $this->component;
		}

		/**
		 *
		 */
		public function getStatus () : int
		{
			return $this->status;
		}

		/**
		 *
		 */
		public function getResponse () : Response
		{
			return $this->response;
		}

		/**
		 *
		 */
		public function getView () : string
		{
			return $this->view;
		}

		/**
		 *
		 */
		public function hasResponse () : bool
		{
			return ($this->response) ? true : false;
		}

		/**
		 *
		 */
		public function hasComponent () : bool
		{
			return ($this->component) ? true : false;
		}

		/**
		 *
		 */
		public function hasStatus () : bool
		{
			return ($this->status) ? true : false;
		}

		/**
		 *
		 */
		public function hasView () : bool
		{
			return ($this->view) ? true : false;
		}

		/**
		 *
		 */
		public function setComponent (ComponentInterface $component) : ContextInterface
		{
			$this->component = $component;

			return $this;
		}

		/**
		 *
		 */
		public function setResponse (Response $response) : ContextInterface
		{
			$this->response = $response;

			return $this;
		}

		/**
		 *
		 */
		public function setStatus (int $status) : ContextInterface
		{
			$this->status = $status;

			return $this;
		}

		/**
		 *
		 */
		public function setView (string $view) : ContextInterface
		{
			$this->view = $view;

			return $this;
		}


	}
}
