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
	use DateTime;
	use DateTimeInterface;
	use Doctrine\DBAL\Types\DateTimeType;
	use Doctrine\DBAL\Platforms\AbstractPlatform;
	use Doctrine\DBAL\Types\ConversionException;

	/**
	 * Zulu (UTC) DateTime Type
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class ZuluDateTimeType extends DateTimeType
	{
		static private $utc = null;

		public function convertToDatabaseValue ($value, AbstractPlatform $platform)
		{
			if ($value === null)
				return null;

			if (!($value instanceof DateTimeInterface))
				$value = new DateTime($value);

			if (is_null(self::$utc))
				self::$utc = new \DateTimeZone('UTC');

			$value->setTimeZone(self::$utc);

			return $value->format($platform->getDateTimeFormatString());
		}

		public function convertToPHPValue ($value, AbstractPlatform $platform)
		{
			if ($value === null)
				return null;

			if (is_null(self::$utc))
				self::$utc = new \DateTimeZone('UTC');

			$val = \DateTime::createFromFormat($platform->getDateTimeFormatString(), $value, self::$utc);

			if (!$val)
				throw ConversionException::conversionFailed($value, $this->getName());

			return $val;
		}

	}
}
