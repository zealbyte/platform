<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\Twig\Extension
{
	use Twig_Extension;
	use Twig_SimpleFunction;
	use Twig_ExtensionInterface;
	use Symfony\Component\Asset\Packages;
	use ZealByte\Platform\Context\Tag\ContextTagManagerInterface;
	use ZealByte\Platform\Twig\TokenParser\ContextTagTokenParser;

	/**
	 * Platform Twig Extension
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class ZealBytePlatformExtension extends Twig_Extension implements Twig_ExtensionInterface
	{
		private $contextTagContainer;

    /**
     * Construct the environment needed to communicate to Wordpress before
     * any output (styles / scripts etc...)
		 */
    public function __construct (ContextTagManagerInterface $contextTagContainer)
    {
			$this->contextTagContainer = $contextTagContainer;
    }

		/**
		 * Returns the token parser instances to add Ti wigdet tags
		 *
		 * @return array An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
		 */
		public function getTokenParsers ()
		{
			$parsers = [];

			foreach ($this->contextTagContainer->getTags() as $name) {
				$parsers[] = new ContextTagTokenParser($name);
			}

			return $parsers;
		}

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
		public function getFunctions ()
		{
			$iconRendorMethod = $this->findIconRendorMethod();

			return [
				new Twig_SimpleFunction('icon', [$this, $iconRendorMethod], ['is_safe' => ['html']])
			];
		}

		public function getContextTagContainer ()
		{
			return $this->contextTagContainer;
		}

		public function renderSvgIcon (string $icon_name = null, int $size = 24) : string
		{
			$f_size = $size;
			$f_name = "ic_{$icon_name}_{$f_size}px";
			$basepath = "icons/{$f_name}.svg";

			$r = '&times;';

			//$path = $this->contextTagContainer->getAssetPath($basepath);
			$path = '/nowhere';

			if (is_file($path))
				$r = "<span class=\"svg-ic svg-{$f_name}-dims\">\n"
				. file_get_contents($path)
				. "\n</span>";

			return $r;
		}

		private function findIconRendorMethod ()
		{
			return 'renderSvgIcon';
		}

  }
}
