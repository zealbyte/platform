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
	use Symfony\Component\Process\Process;
	use Symfony\Component\Process\Exception\ProcessFailedException;
	use Symfony\Component\PropertyAccess\PropertyAccess;
	use ZealByte\Platform\Exception\PlatformException;
	use ZealByte\Platform\Assets\PackageInterface;
	use ZealByte\Platform\Assets\Package;
	use ZealByte\Platform\Assets\PackageFile;

	/**
	 * Bower Asset Repository
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class BowerRepository extends RepositoryAbstract implements RepositoryInterface
	{
		protected $accessor;

		protected $cwd;

		protected $exec;

		protected $args;

		public function __construct (string $exec = null, string $args = null, string $working_directory = null)
		{
			if ($exec)
				$this->setExec($exec);

			if ($args)
				$this->setArgs($args);

			if ($working_directory)
				$this->setWorkingDirectory($working_directory);
		}

		public function getPackages () : array
		{
			$this->discoverPackages();

			return parent::getPackages();
		}

		public function setExec (string $exec)
		{
			$this->exec = $exec;

			return $this;
		}

		public function setArgs (string $args)
		{
			$this->args = $args;

			return $this;
		}

		public function setWorkingDirectory (string $directory)
		{
			$directory = realpath($directory);

			if (!$directory || !is_dir($directory))
				throw new \Exception("The directory $directory does not exist. Bower will not know where to look for the bower.json file.");

			$this->cwd = $directory;

			return $this;
		}

		protected function discoverPackages ()
		{
			$this->accessor = PropertyAccess::createPropertyAccessor();
			$json = $this->execBowerListJson();

			if ($json) {
				$list = json_decode($json, true);

				if (isset($list['dependencies']) && is_array($list['dependencies'])) {
					foreach ($list['dependencies'] as $package_data) {
						$this->yieldPackage($package_data);
					}
				}
			}

			return [];
		}

		private function execBowerListJson () : string
		{
			$json = '';
			$message = '';
			$currCwd = getcwd();

			try {
				if ($this->cwd)
					chdir($this->cwd);

				$exec = (string) $this->exec;
				$args = (string) $this->args;
				$command = trim(sprintf('%s %s', $exec, $args));

				$bower = new Process($command);
				$bower->mustRun();

				$message .= $bower->getErrorOutput();
				$json .= $bower->getOutput();
			} catch (ProcessFailedException $ex) {
				$message = $ex->getMessage();

				throw new \Exception($message);
			} finally {
				chdir($currCwd);
			}

			return $json;
		}

		private function yieldPackage (array $data)
		{
			$name = $this->findPackageName($data);

			if ($this->hasPackage($name))
				return $this->getPackage($name);

			$package = new Package();

			$this->yieldPackageName($package, $name);
			$this->yieldPackageData($package, $data);
			$this->addPackage($package);

			return $package;
		}

		private function findPackageName (array $data)
		{
			if (!$this->accessor->isReadable($data, '[endpoint][name]'))
				throw new \Exception('No package name supplied for package.');

			$name = $this->accessor->getValue($data, '[endpoint][name]');

			if (!is_string($name) || empty($name))
				throw new \Exception ('Package name must be a nonempty string.');

			return $name;
		}

		private function yieldPackageData (Package $package, array $data)
		{
			try {
				$this->yieldPackageBaseUrl($package, $data);
				$this->yieldPackageBasedir($package, $data);
				$this->yieldPackageAuthors($package, $data);
				$this->yieldPackageMeta($package, $data);
				$this->yieldPackageTags($package, $data);
				$this->yieldPackageFiles($package, $data);
				$this->yieldPackageDependentPackages($data);
				$this->yieldPackageDependencies($package, $data);
			} catch (\Exception $e) {
				$this->handlePackageException($package, $e);
			}
		}

		private function yieldPackageName (Package $package, string $name)
		{
			$this->accessor->setValue($package, 'name', $name);
		}

		private function yieldPackageBaseUrl (Package $package, array $data)
		{
			$this->accessor->setValue($package, 'base_url', '/vendor');
		}

		private function yieldPackageBasedir (Package $package, array $data)
		{
			if (!$this->accessor->isReadable($data, '[endpoint][name]'))
				throw new \Exception('No package endpoint name supplied for package.');

			$basedir = $this->accessor->getValue($data, '[endpoint][name]');

			if (!is_string($basedir) || empty($basedir))
				throw new \Exception ('Package endpoint name must be a nonempty string.');

			$this->accessor->setValue($package, 'basedir', $basedir);
		}

		private function yieldPackageAuthors (Package $package, array $data)
		{
			if ($this->accessor->isReadable($data, '[pkgMeta][authors]')) {
				$authors = $this->accessor->getValue($data, '[pkgMeta][authors]');

				if (!is_array($authors))
					$authors = [$authors];

				$this->accessor->setValue($package, 'authors', $authors);
			}
		}

		private function yieldPackageTags (Package $package, array $data)
		{
			if ($this->accessor->isReadable($data, '[pkgMeta][keywords]')) {
				$keywords = $this->accessor->getValue($data, '[pkgMeta][keywords]');

				if (!is_array($keywords))
					$keywords = [$keywords];

				//foreach ($keywords as $keyword)
					//$this->yieldPackageTagsAddTag($package, $keyword);
			}
		}

		private function yieldPackageFiles (Package $package, array $data)
		{
			if ($this->accessor->isReadable($data, '[pkgMeta][main]')) {
				$paths = $this->accessor->getValue($data, '[pkgMeta][main]');

				if (!is_array($paths))
					$paths = [$paths];

				$this->yieldPackageFilesAdd($package, $paths);
			}
		}

		private function yieldPackageFilesAdd (Package $package, array $paths)
		{
			foreach ($paths as $path)
				if ($path)
					$package->addFile(new PackageFile($path));
		}

		private function yieldPackageDependentPackages (array $data)
		{
			if ($this->accessor->isReadable($data, '[dependencies]')) {
				$deps = $this->accessor->getValue($data, '[dependencies]');

				if (!is_array($deps))
					$deps = [$deps];

				foreach ($deps as $package_data) {
					if (!empty($package_data))
						$dependency = $this->yieldPackage($package_data);
				}
			}
		}

		private function yieldPackageDependencies (Package $package, array $data)
		{
			if ($this->accessor->isReadable($data, '[pkgMeta][dependencies]')) {
				$dep_data = $this->accessor->getValue($data, '[pkgMeta][dependencies]');

				if (is_array($dep_data))
					foreach ($dep_data as $dependencyName => $dependency_target_version)
						$package->addDependency($dependencyName);
			}
		}

		private function yieldPackageMeta (Package $package, array $data)
		{
			$mapping = [
				'version' => '[pkgMeta][version]',
				'description' => '[pkgMeta][description]',
				'license' => '[pkgMeta][license]',
				'homepage' => '[pkgMeta][homepage]',
			];

			foreach ($mapping as $prop => $map) {
				if ($this->accessor->isReadable($data, $map)) {
					$value = $this->accessor->getValue($data, $map) ?: '';

					if (is_array($value))
						$value = implode(', ', $value);

					$this->accessor->setValue($package, $prop, $value);
				}
			}
		}

		private function handlePackageException (Package $package, \Exception $exception)
		{
			$name = $package->getName() ?: 'Unknown';
			$message = $exception->getMessage();

			echo sprintf("Package %s: %s\n", $name, $message);
		}

	}
}
