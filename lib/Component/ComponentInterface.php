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
	 * Component Interface
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	interface ComponentInterface
	{
		/**
		 *
		 */
		public function getBlockName () : string;

		/**
		 *
		 */
		public function getData ();

		/**
		 *
		 */
		public function getParameters () : array;

		/**
		 *
		 */
		public function getView () : string;

		/**
		 *
		 */
		public function hasBlockName () : bool;

		/**
		 *
		 */
		public function hasData () : bool;

		/**
		 *
		 */
		public function hasParameters () : bool;

		/**
		 *
		 */
		public function hasView () : bool;
	}
}
