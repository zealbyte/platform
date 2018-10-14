<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform
{
	use ArrayAccess;
	use Iterator;
	use JsonSerializable;
	use Exception;

	/**
	 * @todo Get rid of this
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class ListVar implements ArrayAccess, Iterator, JsonSerializable
	{
		private $position;

		private $keys = [];

		private $positions = [];

		private $container = [];

		public function __construct ()
		{
			$this->erase();
		}

		public function jsonSerialize ()
		{
			$vars = [];

			foreach ($this->keys as $key => $num)
				$vars[$key] = $this->container[$num];

			return $vars;
		}

		public function erase ()
		{
			$this->position = 0;
			$this->keys = [];
			$this->container = [];
		}

		public function isEmpty ()
		{
			return (bool) (count($this->keys) == 0);
		}

		/**
		 * {@inheritdoc}
		 */
		public function offsetSet ($offset, $value)
		{
			$num = count($this->keys);

			if (is_null($offset))
				$offset = $num;

			if (array_key_exists($offset, $this->keys))
				$num = $this->keys[$offset];

			$this->keys[$offset] = $num;
			$this->container[$num] = $value;
			$this->positions[$num] = $offset;
		}

		/**
		 * {@inheritdoc}
		 */
		public function offsetExists ($offset)
		{
			return array_key_exists($offset, $this->keys);
		}

		/**
		 * {@inheritdoc}
		 */
		public function offsetUnset ($offset)
		{
			$itr = 0;

			if (!array_key_exists($offset, $this->keys))
				return;

			foreach ($this->keys as $key => $num) {
				if ($offset == $key) {
					unset($this->keys[$offset]);
					unset($this->container[$num]);
				} else {
					$this->keys[$offset] = $itr;
					$this->container[$itr] = $this->container[$num];
					$this->positions[$itr] = $offset;
					++$itr;
				}
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function offsetGet ($offset)
		{
			$num = isset($this->keys[$offset]) ? $this->keys[$offset] : null;

			return !is_null($num) ? $this->container[$num] : null;
		}

		/**
		 * {@inheritdoc}
		 */
		public function rewind ()
		{
			$this->position = 0;
		}

		/**
		 * {@inheritdoc}
		 */
		public function current ()
		{
			return $this->container[$this->position];
		}

		/**
		 * {@inheritdoc}
		 */
		public function key ()
		{
			return $this->positions[$this->position];
		}

		/**
		 * {@inheritdoc}
		 */
		public function next ()
		{
			++$this->position;
		}

		/**
		 * {@inheritdoc}
		 */
		public function valid ()
		{
			return isset($this->container[$this->position]);
		}

	}
}
