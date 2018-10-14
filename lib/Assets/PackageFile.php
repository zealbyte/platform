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
	 * Asset Package File
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class PackageFile
	{
		private $dirname;

		private $basename;

		private $extension;

		private $mime_type;

		public function __construct (string $path = null)
		{
			if ($path)
				$this->setPath($path);
		}

		public function getPathCanonical ()
		{
			return trim(sprintf('%s/%s', $this->dirname, $this->basename), '/');
		}

		public function getDirname ()
		{
			return $this->dirname;
		}

		public function getBasename ()
		{
			return $this->basename;
		}

		public function getExtension ()
		{
			return $this->extension;
		}

		public function getMimeType ()
		{
			return $this->mime_type;
		}

		public function setPath (string $path)
		{
			$this->name = $path;
			$pathinfo = pathinfo($path);

			$this->dirname = str_replace('\\', '/', trim($pathinfo['dirname'], './\\'));
			$this->basename = $pathinfo['basename'];
			$this->extension = $pathinfo['extension'];
			$this->mime_type = Util\MediaType::findMimeByExtension($pathinfo['extension']);

			return $this;
		}

	}
}
