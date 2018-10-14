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
	 * Context Tag Abstract
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	abstract class ContextTagAbstract implements ContextTagInterface
	{
		protected $value;

		public function __construct ($value = null)
		{
			if ($value)
				$this->set($value);
		}

		public function get ()
		{
			return (string) $this->value;
		}

		public function set ($value)
		{
			$this->value = $value;
		}

	}
}
