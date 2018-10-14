<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\Form\Extension\HelpLink
{

	use Symfony\Component\Form\AbstractExtension;
	use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
	use Symfony\Component\Translation\TranslatorInterface;

	/**
	 * This extension adds a help link option to form fields
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class HelpLinkExtension extends AbstractExtension
	{
		/**
		 * @var Symfony\Component\Routing\Generator\UrlGeneratorInterface
		 */
		private $urlGenerator;


		/**
		 * @param UrlGeneratorInterface $url_generator The URL Generator
		 * @param TranslatorInterface $translator The translator for translating error messages
		 * @param null|string $translation_domain The translation domain for translating
		 */
		public function __construct (UrlGeneratorInterface $url_generator)
		{
			$this->urlGenerator = $url_generator;
		}

		/**
		 * {@inheritdoc}
		 */
		protected function loadTypeExtensions ()
		{
			return [
				new Type\FormHelpLinkTypeExtension($this->urlGenerator),
			];
		}

	}
}
