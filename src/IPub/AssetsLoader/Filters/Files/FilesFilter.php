<?php
/**
 * FilesFilter.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Filters
 * @since		5.0
 *
 * @date		29.12.13
 */

namespace IPub\AssetsLoader\Filters\Files;

use Nette;

use IPub;
use IPub\AssetsLoader;
use IPub\AssetsLoader\Filters;

abstract class FilesFilter extends Nette\Object implements IFilesFilter, Filters\IFilter
{
	/**
	 * @param string
	 * @param string|NULL
	 *
	 * @return string
	 *
	 * @throws \RuntimeExeption
	 */
	protected function run($cmd, $stdin = NULL)
	{
		$descriptorspec = array(
			0 => array('pipe', 'r'), // stdin
			1 => array('pipe', 'w'), // stdout
			2 => array('pipe', 'w'), // stderr
		);

		$pipes = array();
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