<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\DBAL\Types
{
	use InvalidArgumentException;
	use Doctrine\DBAL\Types\Type;
	use Doctrine\DBAL\Platforms\AbstractPlatform;

	/**
	 * Price Data Type
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class PriceType extends Type
	{
		protected $name = 'price';
		protected $decimal = '.';
		protected $thousands = ',';

		public function getSQLDeclaration (array $fieldDeclaration, AbstractPlatform $platform)
		{
			return "DECIMAL(20,4) COMMENT '(DC2Type:".$this->name.")'";
		}

		public function convertToPHPValue ($value, AbstractPlatform $platform)
		{
			return number_format($value, 2, $this->decimal, $this->thousands);
		}

		public function convertToDatabaseValue ($value, AbstractPlatform $platform)
		{
			if (!$value)
				$value = 0;

			if (is_string($value))
				$value = str_replace($this->thousands, '', $value);

			if (!is_numeric($value))
				throw new InvalidArgumentException("Invalid '".$this->name."' value.");

			return $value;
		}

		public function getName ()
		{
			return $this->name;
		}

	}
}
