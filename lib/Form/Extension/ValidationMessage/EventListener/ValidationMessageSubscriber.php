<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\Form\Extension\ValidationMessage\EventListener
{
	use Symfony\Component\EventDispatcher\EventSubscriberInterface;
	use Symfony\Component\Translation\TranslatorInterface;
	use Symfony\Component\Form\FormEvents;
	use Symfony\Component\Form\FormEvent;
	use Symfony\Component\Form\FormError;
	use ZealByte\Platform\ZealBytePlatform;
	use ZealByte\Message\Provider\MessageProvider;

	/**
	 * Add validation message to form
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	final class ValidationMessageSubscriber implements EventSubscriberInterface
	{
		const FORM_POST_SUBMIT_PRIORITY = ZealBytePlatform::LOW_PRIORITY;

		private $messages;

		private $translator;

		public static function getSubscribedEvents ()
		{
			return [
				FormEvents::POST_SUBMIT => [
					['onPostSubmit', self::FORM_POST_SUBMIT_PRIORITY],
				],
			];
		}

		public function __construct (MessageProvider $message_provider, ?TranslatorInterface $translator = null)
		{
			$this->messages = $message_provider;

			if ($translator)
				$this->setTranslator($translator);
		}

		public function hasTranslator () : bool
		{
			return ($this->translator) ? true : false;
		}

		/**
		 *
		 */
		public function onPostSubmit (FormEvent $event) : void
		{
			$form = $event->getForm();

			$showInvalid = $form->getConfig()->getOption('show_invalid');
			$invalidMessage = $form->getConfig()->getOption('invalid_message');

			if ($showInvalid && !$form->isValid())
				$form->addError(new FormError(
					$this->hasTranslator() ? $this->translator->trans($invalidMessage) : $invalidMessage));
		}

		/**
		 *
		 */
		public function setTranslator (TranslatorInterface $translator) : self
		{
			$this->translator = $translator;

			return $this;
		}

	}
}
