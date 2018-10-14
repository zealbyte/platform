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
	 * Context Interface
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	interface ContextInterface
	{
		public function getComponent () : ComponentInterface;

		public function getStatus () : int;

		public function getView () : string;

		public function hasComponent () : bool;

		public function hasStatus () : bool;

		public function hasView () : bool;
	}
}
