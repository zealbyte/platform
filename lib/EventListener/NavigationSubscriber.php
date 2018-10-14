<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\EventListener
{
	use Symfony\Component\HttpKernel\KernelEvents;
	use Symfony\Component\EventDispatcher\EventSubscriberInterface;
	use Symfony\Component\HttpKernel\Event\GetResponseEvent;
	use Knp\Menu\Provider\MenuProviderInterface;
	use Knp\Menu\Matcher\MatcherInterface;
	use Knp\Menu\Util\MenuManipulator;
	use Knp\Menu\Renderer\RendererProviderInterface;
	use ZealByte\Platform\Context\Tag\TitleContextTag;
	use ZealByte\Platform\Context\ContextInterface;
	use ZealByte\Bundle\MenuBundle\Twig\MenuHelper;

	/**
	 * Get current title from navigation menu
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	final class NavigationSubscriber implements EventSubscriberInterface
	{
		const TITLE_TAG_PRIORITY = 1;

		private $titleTag;

		private $menuHelper;

		public static function getSubscribedEvents ()
		{
			return [
				KernelEvents::REQUEST => [
					['onKernelRequest', self::TITLE_TAG_PRIORITY]
				],
			];
		}

		public function __construct (?TitleContextTag $title_tag = null, ?MenuHelper $menu_helper = null)
		{
			if ($title_tag)
				$this->setTitleContextTag($title_tag);

			if ($menu_helper)
				$this->setMenuHelper($menu_helper);
		}

		public function setTitleContextTag (TitleContextTag $title_tag) : self
		{
			$this->titleTag = $title_tag;

			return $this;
		}

		public function setMenuHelper (MenuHelper $menu_helper) : self
		{
			$this->menuHelper = $menu_helper;

			return $this;
		}

		public function onKernelRequest (GetResponseEvent $event) : void
		{
			if (!$this->titleTag || !$this->menuHelper || !$event->isMasterRequest())
				return;

			$menuItem = $this->menuHelper->getCurrentItem('navigation');
			$label = $menuItem ? $menuItem->getLabel() : null;

			if ($label)
				$this->titleTag->set($label);
		}

	}
}

