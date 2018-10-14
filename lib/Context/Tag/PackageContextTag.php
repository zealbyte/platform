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
	use Symfony\Component\Asset\Packages as AssetPackages;
	use Symfony\Component\Asset\Context\RequestStackContext;
	use ZealByte\Platform\Context\Tag\ContextTagInterface;
	use ZealByte\Platform\Context\Tag\ContextTagAbstract;
	use ZealByte\Platform\Assets\PackageManagerInterface;
	use ZealByte\Platform\Assets\PackageInterface;

	use ZealByte\Platform\ListVar;

	/**
	 * Asset Package Context Tag
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class PackageContextTag implements ContextTagInterface
	{
		private $assetPackages;

		private $assetContext;

		private $package_managers = [];

		private $applied = [];

		private $requested = [];

		private $packages = [];

		private $packageVar;

		public function __construct (PackageManagerInterface $package_manager = null, AssetPackages $asset_packages = null, RequestStackContext $asset_context = null)
		{
			if ($package_manager)
				$this->setPackageManager($package_manager);

			if ($asset_packages)
				$this->setAssetPackages($asset_packages);

			if ($asset_context)
				$this->setAssetContext($asset_context);
		}

		public function get ()
		{
			$package = $this->getAppliedPackageData();

			return $package;
		}

		public function set ($value)
		{
			if (is_array($value))
				foreach ($value as $val)
					$this->applyPackage($val);
			else
				$this->applyPackage($value);

			$this->getAppliedPackageData();
		}

		public function setPackageManager (PackageManagerInterface $package_manager) : self
		{
			$pm_class = get_class($package_manager);

			if (array_key_exists($pm_class, $this->package_managers))
				throw new \Exception("The package manager $pm_class is already added to this formatter.");

			$this->package_managers[] = $package_manager;

			return $this;
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

		protected function applyPackage (string $package_name)
		{
			if (in_array($package_name, $this->requested))
				return;

			$this->addRequestedPackageName($package_name);

			foreach ($this->package_managers as $packageManager) {
				if ($packageManager->hasPackage($package_name)) {
					$package = $packageManager->getPackage($package_name);

					$dependencyNames = $this->resolveDependencies($package);
					$packageName = $this->resolvePackage($package);

					foreach ($dependencyNames as $dependencyName)
						$this->applied[] = $dependencyName;

					$this->applied[] = $packageName;

					break;
				}
			}
		}

		protected function getAppliedPackageData () : ListVar
		{
			if (!$this->packageVar)
				$this->packageVar = new ListVar();

			$pkgs = [];

			foreach ($this->packages as $package) {
				$pkg = [
					'name' => $package->getName(),
					'version' => $package->getVersion(),
					'dependencies' => [],
					'resources' => [],
				];

				foreach ($package->getDependencies() as $dependencyName) {
					if ($dependencyName)
						$pkg['dependencies'][] = $dependencyName;
				}

				foreach ($package->getFiles() as $file) {
					$name = $file->getPathCanonical();
					$type = $file->getMimeType();
					$path = $this->assetPackages ? $this->assetPackages->getUrl($name, $pkg['name']) : $name;

					$pkg['resources'][] = [
						'name' => $name,
						'type' => $type,
						'path' => $path,
					];
				}

				$pkgs[] = $pkg;
			}

			$this->packageVar['requested'] = $this->requested;
			$this->packageVar['applied'] = $this->applied;
			$this->packageVar['packages'] = $pkgs;

			return $this->packageVar;
		}

		protected function resolveDependencies (PackageInterface $package, int $depth = 0) : array
		{
			if (isset($this->packages[$package->getName()]) || 10 < $depth)
				return [];

			$dependencyNames = [];

			foreach ($package->getDependencies() as $dependencyName) {
				if ($dependencyName) {
					foreach ($this->package_managers as $packageManager) {
						if ($packageManager->hasPackage($dependencyName)) {
							$dependency = $packageManager->getPackage($dependencyName);
							$dependencyDependencyNames = $this->resolveDependencies($dependency, ($depth + 1));

							foreach ($dependencyDependencyNames as $dependencyDependencyName)
								$dependencyNames[] = $dependencyDependencyName;

							if (!isset($this->packages[$dependencyName])) {
								$this->packages[$dependencyName] = $dependency;
								$dependencyNames[] = $dependencyName;
							}

							break;
						}
					}
				}
			}

			return $dependencyNames;
		}

		protected function resolvePackage (PackageInterface $package)
		{
			if (isset($this->packages[$package->getName()]))
				return;

			$packageName = $package->getName();

			$this->packages[$packageName] = $package;

			return $packageName;
		}

		protected function addRequestedPackageName (string $package_name)
		{
			$requested[] = $package_name;
			$this->requested[] = $package_name;

			return;
		}

	}
}
