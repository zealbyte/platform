<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\Cache
{
	use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
	use ZealByte\Platform\Assets\PackageManagerInterface;

	/**
	 * Asset Packages Cache Warmer
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class PackageCacheWarmer implements CacheWarmerInterface
	{
		private $package_managers = [];

		public function __construct (?PackageManagerInterface $package_manager = null)
		{
			if ($package_manager)
				$this->addPackageManager($package_manager);
		}

		public function addPackageManager (PackageManagerInterface $package_manager) : self
		{
			$pm_class = get_class($package_manager);

			if (array_key_exists($pm_class, $this->package_managers))
				throw new \Exception("The package manager $pm_class is already added to this formatter.");

			$this->package_managers[] = $package_manager;

			return $this;
		}

		public function warmUp ($cacheDirectory)
		{
			foreach ($this->package_managers as $packageManager)
				$packageManager->buildPackageCache();
		}

		public function isOptional ()
		{
			return true;
		}
	}
}
