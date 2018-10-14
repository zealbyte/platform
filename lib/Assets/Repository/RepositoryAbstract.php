<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\Assets\Repository
{
	use Exception;
	use ZealByte\Platform\Assets\PackageInterface;
	use ZealByte\Util;

	/**
	 * Asset Repository Abstract
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	abstract class RepositoryAbstract implements RepositoryInterface
	{
		protected $packages = [];

		public function getPackages () : array
		{
			return $this->packages;
		}

		public function getPackage (string $name) : PackageInterface
		{
			if (!$this->hasPackage($name))
				throw new Exception("Package $name was not found.");

			return $this->packages[Util\Canonical::name($name)];
		}

		public function addPackage (PackageInterface $package) : self
		{
			$packageName = Util\Canonical::name($package->getName());

			if (!isset($this->packages[$packageName]))
				$this->packages[$packageName] = $package;

			return $this;
		}

		public function hasPackage (string $name) : bool
		{
			return array_key_exists(Util\Canonical::name($name), $this->packages);
		}

	}
}
