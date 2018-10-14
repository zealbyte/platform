<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\Twig\Node
{
	use Twig_Compiler;
	use Twig_Token;
	use Twig_TokenParser;
	use Twig_Node;
	use ZealByte\Platform\Twig\Extension\ZealBytePlatformExtension;

	/**
	 * Context Tag Node
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class ContextTagNode extends Twig_Node
	{
		public function __construct (array $nodes, array $attributes, $lineno, $tag = null)
		{
			parent::__construct($nodes, $attributes, $lineno, $tag);
		}

		public function compile (Twig_Compiler $compiler)
		{
			$name = $this->getNodeTag();

			if ($this->hasAttribute('name'))
				$name = $this->getAttribute('name');

			if ($this->hasNode('value'))
				$compiler
					->addDebugInfo($this)
					->write("\$this->env->getExtension(\ZealByte\Platform\Twig\Extension\ZealBytePlatformExtension::class)->getContextTagContainer()->set('$name',")
					->subcompile($this->getNode('value'))
					->raw(");\n");
		}

	}
}
