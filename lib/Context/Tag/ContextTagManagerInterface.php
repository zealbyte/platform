<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\Context\Tag
{
	/**
	 * Context Tag Manager Interface
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	interface ContextTagManagerInterface
	{
		public function get (string $name);

		public function getTags () : array;

		public function has (string $name) : bool;

		public function registerTag (ContextTagInterface $tag, string $name) : ContextTagManagerInterface;

		public function set (string $name, $value) : ContextTagManagerInterface;
	}
}
