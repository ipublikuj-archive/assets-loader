<?php
/**
 * FilesFilter.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     Filters
 * @since          1.0.0
 *
 * @date           29.12.13
 */

declare(strict_types = 1);

namespace IPub\AssetsLoader\Filters\Files;

use Nette;

use IPub\AssetsLoader\Filters;

abstract class FilesFilter implements IFilesFilter, Filters\IFilter
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @param string $cmd
	 * @param string|NULL $stdin
	 *
	 * @return string
	 */
	protected function run(string $cmd, ?string $stdin = NULL) : string
	{
		$descriptorspec = [
			0 => ['pipe', 'r'], // stdin
			1 => ['pipe', 'w'], // stdout
			2 => ['pipe', 'w'], // stderr
		];

		$pipes = [];
		$proc = proc_open($cmd, $descriptorspec, $pipes);

		if (!empty($stdin)) {
			fwrite($pipes[0], $stdin . PHP_EOL);
		}

		fclose($pipes[0]);

		$stdout = stream_get_contents($pipes[1]);
		$stderr = stream_get_contents($pipes[2]);

		$code = proc_close($proc);

		if ($code != 0) {
			throw new \RuntimeException($stderr, $code);
		}

		return $stdout;
	}
}
