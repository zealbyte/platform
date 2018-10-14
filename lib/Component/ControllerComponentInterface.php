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
	 * Controller Component Interface
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	interface ControllerComponentInterface extends ComponentInterface
	{
		/**
		 *
		 */
		public function getRoute () : string;

		/**
		 *
		 */
		public function getRouteParameters () : array;

		/**
		 *
		 */
		public function hasRoute () : bool;

		/**
		 *
		 */
		public function hasRouteParameters () : bool;

	}
}
