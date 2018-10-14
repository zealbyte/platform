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

	/**
	 * Context Handler Interfac
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	interface ContextHandlerInterface
	{
		public function handleContext (Request $request, ContextInterface $context) : Response;
	}
}
