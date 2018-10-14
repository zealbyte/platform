<?php

/*
 * This file is part of the ZealByte Platform Package.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Platform\Command
{
	use Symfony\Component\Console\Style\SymfonyStyle;

	/**
	 * ZealByte Command Console Styling
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	final class ConsoleStyle extends SymfonyStyle
	{
		public function fail ($message)
		{
			$this->writeln(' <options=bold>[<fg=red;options=underscore>FAIL</>]</> '.$message);
		}

		public function success ($message)
		{
			$this->writeln(' <options=bold>[ <fg=green;options=underscore>OK</> ]</> '.$message);
		}

		public function comment ($message, ?array $highlights = null)
		{
			if ($highlights) {
				$args = array_map(function ($arg) {
					return sprintf('<fg=green>%s</>', $arg);
				}, $highlights);

				array_unshift($args, $message);

				$message = call_user_func_array('sprintf', $args);
			}

			$this->text(" <fg=blue>//</> $message");
		}

	}
}
