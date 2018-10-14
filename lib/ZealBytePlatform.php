<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform
{
	/**
	 * The ZealByte Platfom
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class ZealBytePlatform
	{
		const LOW_PRIORITY = 0;

		const REGULAR_PRIORITY = 5;

		const HIGH_PRIORITY = 10;

		const CONTEXT_FACTORY = 'ZealByte\Platform\Context\ContextFactory';

		const CONTEXT_TAGS = 'ZealByte\Platform\Context\Tag\ContextTagManager';
	}
}
