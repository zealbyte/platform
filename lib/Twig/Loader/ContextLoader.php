<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\Twig\Loader
{
	use Twig_LoaderInterface;
	use Twig_ExistsLoaderInterface;
	use Twig_SourceContextLoaderInterface;
	use ZealByte\Platform\Twig\Source;

	/**
	 * Context Loader
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class ContextLoader implements Twig_LoaderInterface, Twig_ExistsLoaderInterface, Twig_SourceContextLoaderInterface
	{
		public function getSource ($name)
		{
			return $this->getSourceContext($name)->getCode();
		}

		public function getCacheKey ($name)
		{
		}

		public function isFresh ($name, $time)
		{
		}

		public function exists ($name)
		{
			return true;
		}

		public function getSourceContext ($name)
		{
			$code = '';
			$name = '';
			$path = '';

			return new Source($code, $name, $path);
		}

	}
}
