<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\Form\Extension\HelpLink\Type
{
	use Symfony\Component\Form\AbstractTypeExtension;
	use Symfony\Component\Form\Extension\Csrf\EventListener\CsrfValidationListener;
	use Symfony\Component\Form\FormBuilderInterface;
	use Symfony\Component\Form\FormInterface;
	use Symfony\Component\Form\FormView;
	use Symfony\Component\OptionsResolver\OptionsResolver;
	use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

	/**
	 * Form Help Link Type Extension
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class FormHelpLinkTypeExtension extends AbstractTypeExtension
	{
		private $urlGenerator;

		public function __construct (UrlGeneratorInterface $url_generator)
		{
			$this->urlGenerator = $url_generator;
		}

		/**
		 * Adds a CSRF field to the form when the CSRF protection is enabled.
		 *
		 * @param FormBuilderInterface $builder The form builder
		 * @param array                $options The options
		 */
		public function buildForm (FormBuilderInterface $builder, array $options)
		{
			if (!($options['help_link_url'] && $options['help_link_route'] && $options['help_link_text']))
				return;
		}

		/**
		 * {@inheritdoc}
		 */
		public function finishView (FormView $view, FormInterface $form, array $options)
		{
			$vars = [
				'url' => $options['help_link_url'],
				'route' => $options['help_link_route'],
				'text' => $options['help_link_text'],
			];

			if (empty($vars['url']) && !empty($vars['route']))
				$vars['url'] = $this->urlGenerator->generate($vars['route']);

			if (empty($vars['text']))
				$vars['text'] = $vars['url'];

			$view->vars['help_link_text'] = $vars['text'];
			$view->vars['help_link_url'] = $vars['url'];
		}

		/**
		 * {@inheritdoc}
		 */
		public function configureOptions (OptionsResolver $resolver)
		{
			$resolver->setDefaults([
				'help_link_url' => null,
				'help_link_route' => null,
				'help_link_text' => null,
			]);

			$resolver->setAllowedTypes('help_link_url', ['null', 'string']);
			$resolver->setAllowedTypes('help_link_route', ['null', 'string']);
			$resolver->setAllowedTypes('help_link_text', ['null', 'string']);
		}

		/**
		 * {@inheritdoc}
		 */
		public function getExtendedType()
		{
			return 'Symfony\Component\Form\Extension\Core\Type\FormType';
		}
	}
}
