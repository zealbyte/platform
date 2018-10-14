<?php

/*
 * This file is part of the ZealByte Platform package
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\Form\Extension\ValidationMessage\Type
{
	use Symfony\Component\Translation\TranslatorInterface;
	use Symfony\Component\Form\Extension\Core\Type\FormType;
	use Symfony\Component\Form\AbstractTypeExtension;
	use Symfony\Component\Form\FormBuilderInterface;
	use Symfony\Component\Form\FormInterface;
	use Symfony\Component\Form\FormView;
	use Symfony\Component\OptionsResolver\OptionsResolver;
	use ZealByte\Message\Provider\MessageProvider;
	use ZealByte\Platform\Form\Extension\ValidationMessage\EventListener\ValidationMessageSubscriber;

	/**
	 * Validation Message Type Extension
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class ValidationMessageTypeExtension extends AbstractTypeExtension
	{
		private $messageProvider;

		private $translator;

		public function __construct (MessageProvider $message_provider, ?TranslatorInterface $translator = null)
		{
			$this->messageProvider = $message_provider;

			if ($translator)
				$this->setTranslator($translator);
		}

		/**
		 * Adds a CSRF field to the form when the CSRF protection is enabled.
		 *
		 * @param FormBuilderInterface $builder The form builder
		 * @param array                $options The options
		 */
		public function buildForm (FormBuilderInterface $builder, array $options)
		{
			if (!$options['show_invalid'])
				return;

			$builder
				->addEventSubscriber(new ValidationMessageSubscriber($this->messageProvider, $this->translator));
		}

		/**
		 * {@inheritdoc}
		 */
		public function finishView (FormView $view, FormInterface $form, array $options)
		{
			if (!$options['show_success'] || !$form->isSubmitted() || !$form->isValid())
				return;

			if (0 < $form->getErrors(true)->count())
				return;

			$successMessage = $form->getConfig()->getOption('success_message');

			$this->messageProvider->addSuccess(
				$this->translator ? $this->translator->trans($successMessage) : $successMessage);
		}

		/**
		 * {@inheritdoc}
		 */
		public function configureOptions (OptionsResolver $resolver)
		{
			$resolver->setDefaults([
				'show_success' => false,
				'show_invalid' => false,
				'success_message' => "success",
				'invalid_message' => "invalid",
			]);

			$resolver->setAllowedTypes('show_success', 'bool');
			$resolver->setAllowedTypes('show_invalid', 'bool');
			$resolver->setAllowedTypes('success_message', 'string');
			$resolver->setAllowedTypes('invalid_message', 'string');
		}

		/**
		 * {@inheritdoc}
		 */
		public function getExtendedType ()
		{
			return FormType::class;
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
