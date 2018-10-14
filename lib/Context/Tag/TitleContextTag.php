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
	/**
	 * Title Context Tag
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class TitleContextTag implements ContextTagInterface
	{
		protected $name;

		protected $title;

		protected $pattern;

		public function __construct (string $name = null, string $pattern = null)
		{
			if ($name)
				$this->setName($name);

			if ($pattern)
				$this->setPattern($pattern);
		}

		public function get ()
		{
			return $this->title;
		}

		public function getFullTitle ()
		{
			$pattern = $this->pattern ?: '%s - %s';

			return sprintf($pattern, $this->title, $this->name);
		}

		public function set ($value)
		{
			$this->title = $value;
		}

		public function setName (string $name) : self
		{
			$this->name = $name;

			return $this;
		}

		private function setPattern (string $pattern)
		{
			$this->pattern = $pattern;
		}

	}
}
