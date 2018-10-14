<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\Assets
{
	/**
	 * Asset Package Manager Interface
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	interface PackageManagerInterface
	{
		public function getPackage (string $name) : PackageInterface;
	}
}
