<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\Form\Extension\ValidationMessage
{
	use Symfony\Component\Form\AbstractExtension;
	use ZealByte\Message\Provider\MessageProvider;

	/**
	 * This extension adds a help link option to form fields
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class ValidationMessageExtension extends AbstractExtension
	{
		/**
		 * @var Symfony\Component\Routing\Generator\UrlGeneratorInterface
		 */
		private $messages;

		/**
		 * @param UrlGeneratorInterface $url_generator The URL Generator
		 * @param TranslatorInterface $translator The translator for translating error messages
		 * @param null|string $translation_domain The translation domain for translating
		 */
		public function __construct (MessageProvider $message_provider)
		{
			$this->messages = $message_provider;
		}

		/**
		 * {@inheritdoc}
		 */
		protected function loadTypeExtensions ()
		{
			return [
				new Type\ValidationMessageTypeExtension($this->messages),
			];
		}

	}
}
