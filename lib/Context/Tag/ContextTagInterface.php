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
	 * Context Tag Interface
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	interface ContextTagInterface
	{
		public function get ();

		public function set ($value);
	}
}
