<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\Command
{
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Flex\Recipe;
	use ZealByte\Platform\Command\ConsoleStyle;
	use ZealByte\Platform\Assets\PackageManagerInterface;

	/**
	 * Build Asset Package Cache Command
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class BuildPackageCacheCommand extends Command
	{
		/** @var ConsoleStyle */
		private $io;

		private $package_managers = [];

		public function __construct (PackageManagerInterface $package_manager = null)
		{
			if ($package_manager)
				$this->addPackageManager($package_manager);

			parent::__construct();
		}

		public function addPackageManager (PackageManagerInterface $package_manager) : self
		{
			$pm_class = get_class($package_manager);

			if (array_key_exists($pm_class, $this->package_managers))
				throw new \Exception("The package manager $pm_class is already added to this formatter.");

			$this->package_managers[] = $package_manager;

			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		protected function configure ()
		{
			$this
				->setName('zealbyte:packages:build-cache')
				->setDescription('Rebuild asset package cache.')
				->setHelp('This command will pull all packages form the configured asset package repos and build a cache file with all asset info and paths.');
		}

		protected function initialize (InputInterface $input, OutputInterface $output)
		{
			$this->io = new ConsoleStyle($input, $output);
		}

		protected function execute (InputInterface $input, OutputInterface $output)
		{
			$packageNames = [];

			$this->io->comment('Creating package cache for the %s environment with debug %s',
				['dev', 'true']);
			$this->io->newLine();

			foreach ($this->package_managers as $packageManager) {
				$packageManager->buildPackageCache();

				foreach ($packageManager->getPackageNames() as $packageName) {
					array_push($packageNames, $packageName);

					//$this->io->success("cached $packageName");
				}
			}

			$packageCount = count($packageNames);

			$this->io->success("Package cache built with $packageCount packages found.");
		}

	}
}
