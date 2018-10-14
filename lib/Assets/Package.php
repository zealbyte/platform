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
	use ZealByte\Util;

	/**
	 * Asset Package
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class Package implements PackageInterface
	{
		/* meta name */
		private $name;

		/* meta description */
		private $description;

		/* meta version */
		private $version;

		/* meta homepage */
		private $homepage;

		/* meta license */
		private $license;

		/* meta authors */
		private $authors;

		/* url path */
		private $baseurl;

		/* endpoint source */
		private $basedir;

		/* meta keywords */
		private $tags = [];

		/* meta main */
		private $files = [];

		/* meta dependencies */
		private $dependencies = [];

		public function getName ()
		{
			return $this->name;
		}
		public function getDescription ()
		{
			return $this->description;
		}

		public function getVersion ()
		{
			return $this->version;
		}

		public function getHomepage ()
		{
			return $this->homepage;
		}

		public function getLicense ()
		{
			return $this->license;
		}

		public function getAuthors ()
		{
			return $this->authors;
		}

		public function getBaseUrl ()
		{
			return $this->baseurl;
		}

		public function getBasedir ()
		{
			return $this->basedir;
		}

		public function getTags ()
		{
			return $this->tags;
		}

		public function getFiles ()
		{
			return $this->files;
		}

		public function getDependencies ()
		{
			return $this->dependencies;
		}

		public function setName (string $name)
		{
			$this->name = $name;

			return $this;
		}
		public function setDescription (string $description)
		{
			$this->description = $description;

			return $this;
		}

		public function setVersion (string $version)
		{
			$this->version = $version;

			return $this;
		}

		public function setHomepage (string $homepage)
		{
			$this->homepage = $homepage;

			return $this;
		}

		public function setLicense (string $license)
		{
			$this->license = $license;

			return $this;
		}

		public function setAuthors (array $authors)
		{
			$this->authors = $authors;

			return $this;
		}

		public function setBaseUrl (string $baseUrl) : self
		{
			$this->baseurl = trim($baseUrl, '/\\');

			return $this;
		}

		public function setBasedir (string $basedir)
		{
			$this->basedir = trim($basedir, '/\\');

			return $this;
		}

		public function addTag (PackageTag $tag)
		{
			$this->tags[$tag->getId()] = $tag;

			return $this;
		}

		public function addFile (PackageFile $file)
		{
			$this->files[$file->getPathCanonical()] = $file;

			return $this;
		}

		public function addDependency (string $dependency_name)
		{
			$this->dependencies[Util\Canonical::name($dependency_name)] = $dependency_name;

			return $this;
		}

		public function delTag (PackageTag $tag)
		{
			unset($this->tag[$tag->getId()]);

			return $this;
		}

		public function delFile (PackageFile $file = null)
		{
			if ($file)
				foreach ($this->files as $curr_file_id => $curr_file)
					if ($file == $curr_file)
						unset($this->files[$curr_file_id]);

			return $this;
		}

		public function delDependency (string $dependency_name)
		{
			unset($this->dependencies[Util\Canonical::name($dependency_name)]);

			return $this;
		}

	}
}
