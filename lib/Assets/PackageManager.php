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
	use Exception;
	use Generator;
	use Symfony\Component\Asset\Packages as AssetPackages;
	use Symfony\Component\Asset\PathPackage;
	use Symfony\Component\Asset\UrlPackage;
	use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;
	use Symfony\Component\Asset\Context\RequestStackContext;
	use ZealByte\Platform\Assets\Repository\RepositoryInterface;
	use ZealByte\Platform\Assets\PackageInterface;
	use ZealByte\Util;

	/**
	 * Asset Package Manager
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class PackageManager implements PackageManagerInterface
	{
		private $assetPackages;

		private $assetContext;

		private $cachePath;

		private $cacheFileName;

		private $ignorePackageNames = [];

		private $packages = [];

		private $packageMods = [];

		private $priorities = [];

		private $repositories = [];

		public function __construct (AssetPackages $asset_packages = null, RequestStackContext $asset_context = null)
		{
			$this->cacheFileName = 'packages.cache';

			if ($asset_packages)
				$this->setAssetPackages($asset_packages);

			if ($asset_context)
				$this->setAssetContext($asset_context);
		}

		public function setAssetPackages (AssetPackages $asset_packages) : self
		{
			$this->assetPackages = $asset_packages;

			return $this;
		}

		public function setAssetContext (RequestStackContext $asset_context) : self
		{
			$this->assetContext = $asset_context;

			return $this;
		}

		public function setCachePath (string $path) : self
		{
			$this->cachePath = $path;

			return $this;
		}

		public function addIgnore (string $ignore_package_name) : self
		{
			$ignorePackageId = Util\Canonical::name($ignore_package_name);

			$this->ignorePackageNames[$ignorePackageId] = true;

			return $this;
		}

		public function addRepository (RepositoryInterface $repository, int $priority = 10) : PackageManagerInterface
		{
			$repositoryId = Util\Canonical::name(get_class($repository));

			if (array_key_exists($repositoryId, $this->repositories))
				throw new InvalidArgumentException("Asset package manager already has a $repositoryId repository.");

			$this->repositories[$repositoryId] = $repository;
			$this->priorities[$repositoryId] = $priority;

			return $this;
		}

		public function getRepositories () : array
		{
			return $this->repositories;
		}

		public function getPriorities () : array
		{
			return $this->priorities;
		}

		public function getPackage (string $name) : PackageInterface
		{
			$this->statPackages();

			$packageId = Util\Canonical::name($name);

			if (!array_key_exists($packageId, $this->packages))
				throw new Exception("Asset package $name is not found.");

			if (array_key_exists($packageId, $this->ignorePackageNames))
				throw new Exception("Asset package $name is in the ignore package list.");

			return $this->packages[$packageId];
		}

		public function getPackages () : array
		{
			$this->statPackages();

			$packages = [];

			foreach ($this->packages as $packageId => $package)
				if (!$this->isPackageIgnored($packageId))
					$packages[$packageId] = $package;

			return $packages;
		}

		public function getPackageNames () : array
		{
			$this->statPackages();

			$packageNames = [];

			foreach ($this->packages as $packageId => $package)
				if (!array_key_exists($packageId, $this->ignorePackageNames))
					$packageNames[$packageId] = $package->getName();

			return $packageNames;
		}

		public function hasPackage (string $name) : bool
		{
			$this->statPackages();

			if (array_key_exists(Util\Canonical::name($name), $this->packages)
				&& !array_key_exists(Util\Canonical::name($name), $this->ignorePackageNames))
				return true;

			return false;
		}

		public function isPackageIgnored (string $name)
		{
			$this->statPackages();

			return ;
		}

		public function modPackage (string $package_name, callable $mod_callback)
		{
			$packageId = Util\Canonical::name($package_name);

			if (!array_key_exists($packageId, $this->packageMods))
				$this->packageMods[$packageId] = [];

			$this->packageMods[$packageId][] = $mod_callback;
		}

		public function buildPackageCache () : void
		{
			if ($this->findRemoveFilePath($this->cacheFileName))
				$this->discoverPackages(true);
		}

		private function statPackages () : void
		{
			if (empty($this->packages))
				$this->discoverPackages();
		}

		private function discoverPackages (?bool $force = false) : void
		{
			$this->readPackagesFromCache();

			if (empty($this->packages) || $force) {
				$this->discoverPackagesFromRepositories();
				$this->writePackagesToCache();
			}

			$this->applyPackageMods();
			$this->createAssetPackages();
		}

		private function createAssetPackages () : void
		{
			if (!$this->assetPackages)
				return;

			foreach ($this->packages as $package) {
				$baseurl = $package->getBaseUrl();
				$version = new StaticVersionStrategy($package->getVersion(), '%s?version=%s');
				$pathPackage = new PathPackage($baseurl.'/'.$package->getBasedir(), $version, $this->assetContext);

				$this->assetPackages->addPackage($package->getName(), $pathPackage);
			}
		}

		private function readPackagesFromCache () : void
		{
			$path = $this->findCacheFilePath($this->cacheFileName);
			$packages = unserialize(file_get_contents($path));

			if ($packages)
				$this->packages = $packages;
		}

		private function writePackagesToCache () : void
		{
			if (!empty($this->packages)) {
				$path = $this->findCacheFilePath($this->cacheFileName);
				$data = serialize($this->packages);

				if ($path && $data)
					file_put_contents($path, $data);
			}
		}

		private function discoverPackagesFromRepositories () : void
		{
			foreach ($this->prioritizeRepositories() as $repository) {
				foreach ($repository->getPackages() as $package) {
					$packageId = Util\Canonical::name($package->getName());

					if (!array_key_exists($packageId, $this->packages))
						$this->packages[$packageId] = $package;
				}
			}
		}

		private function prioritizeRepositories () : Generator
		{
			$priorities = $this->priorities;

			asort($priorities, SORT_NUMERIC);

			foreach (array_keys($priorities) as $repositoryId)
				yield $this->repositories[$repositoryId];
		}

		private function findCacheFilePath (string $cache_file) : string
		{
			if (!is_dir($this->cachePath))
				throw new Exception("$this->cachePath is not a valid path.");

			$cacheFilePath = $this->cachePath . DIRECTORY_SEPARATOR . $cache_file;

			if (!touch($cacheFilePath))
				throw new Exception("Cannot write to $this->cachePath.");

			return $cacheFilePath;
		}

		private function findRemoveFilePath (string $cache_file) : bool
		{
			if (!is_dir($this->cachePath))
				throw new Exception("$this->cachePath is not a valid path.");

			$cacheFilePath = $this->cachePath . DIRECTORY_SEPARATOR . $cache_file;

			if (file_exists($cacheFilePath))
				return unlink($cacheFilePath);

			return true;
		}

		private function applyPackageMods ()
		{
			foreach ($this->packages as $packageId => $package) {
				if (empty($this->packageMods[$packageId]))
					continue;

				foreach ($this->packageMods[$packageId] as $modCallback)
					call_user_func($modCallback, $package);
			}
		}

	}
}
