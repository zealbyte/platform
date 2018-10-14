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
	 * Comma Separated Value Data Type
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class CsvType extends Type
	{
		protected $delimiter = ',';

		protected $enclosure = '"';

		protected $escape = '\\';

		public function getSQLDeclaration (array $fieldDeclaration, AbstractPlatform $platform)
		{
			return "BLOB COMMENT '(DC2Type:csv)'";
		}

		public function convertToPHPValue ($value, AbstractPlatform $platform)
		{
			return str_getcsv($value, $this->delimiter, $this->enclosure, $this->escape);
		}

		public function convertToDatabaseValue ($value, AbstractPlatform $platform)
		{
			if (!is_array($value))
				$value = [$value];

			foreach ($value as $key => $datum)
			{
				if ($datum && !is_scalar($datum))
					throw new InvalidArgumentException("CSV data type can only accept an array of scalar values.");

				// check for presence of special char.
				if ((strpos($datum, ',')  !== false) || (strpos($datum, '"')  !== false) ||
					(strpos($datum, ' ')  !== false) || (strpos($datum, "\t") !== false) ||
					(strpos($datum, "\n") !== false) || (strpos($datum, "\r") !== false))
				{
					$value[$key] = $enclosure.str_replace($enclosure, $enclosure.$enclosure, $datum).$enclosure;
				}
			}

			// now create the CSV line by joining with a comma, also put a \n at the end.
			return implode(',', $value) . "\n";
		}

		public function getName ()
		{
			return 'csv';
		}

	}
}
