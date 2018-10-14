<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\Twig\TokenParser
{
	use Twig_TokenParser;
	use Twig_Token;
	use ZealByte\Platform\Twig\Node\ContextTagNode;

	/**
	 * Context Tag Token Parser
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class ContextTagTokenParser extends Twig_TokenParser
	{
		private $name;

		public function __construct (string $name)
		{
			$this->name = $name;
		}

		/**
		 * Parses a token and returns a node.
		 *
		 * @param Twig_Token $token A Twig_Token instance
		 *
		 * @return Twig_NodeInterface A Twig_NodeInterface instance
		 */
		public function parse (Twig_Token $token)
		{
			$stream = $this->parser->getStream();

			$value = $this->parser->getExpressionParser()->parseExpression();

			$stream->expect(Twig_Token::BLOCK_END_TYPE);

			return new ContextTagNode(
				['value' => $value],
				['name' => $this->name],
				$token->getLine(), $this->getTag());
		}

		/**
		 * Gets the tag name associated with this token parser.
		 *
		 * @param string The tag name
		 *
		 * @return string
		 */
		public function getTag ()
		{
			return $this->name;
		}

	}
}
