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
	use ZealByte\Platform\Context\Tag\ContextTagInterface;
	use ZealByte\Platform\Exception\PlatformException;
	use ZealByte\Platform\Exception\FormatterNotFoundException;
	use ZealByte\Util;

	/**
	 * Context Tag Manager
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class ContextTagManager implements ContextTagManagerInterface
	{
		private $tags = [];

		public function get (string $name)
		{
			return $this->pullValue($name);
		}

		public function getTags () : array
		{
			return array_keys($this->tags);
		}

		public function has (string $name) : bool
		{
			if (array_key_exists($name, $this->tags))
					return true;

			return false;
		}

		public function registerTag (ContextTagInterface $tag, string $name) : ContextTagManagerInterface
		{
			$name = Util\Canonical::name($name);

			if (isset($this->tags[$name]))
				throw new PlatformException("Context tag alias \"$name\" already registered.");

			$this->tags[$name] = $tag;

			return $this;
		}

		public function set (string $name, $value) : ContextTagManagerInterface
		{
			$this->putValue($name, $value);

			return $this;
		}

		private function pullValue (string $name)
		{
			if (!array_key_exists($name, $this->tags))
				throw new TagFormatterNotFoundException("There is no $name formatter registered.");

			return $this->tags[$name]->get();
		}

		private function putValue (string $name, $value)
		{
			if (!array_key_exists($name, $this->tags))
				throw new TagFormatterNotFoundException("There is no $name formatter registered.");

			$this->tags[$name]->set($value);
		}

	}
}
