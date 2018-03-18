<?php
/**
 * CssCompiler.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     Compilers
 * @since          1.0.0
 *
 * @date           29.12.13
 */

declare(strict_types = 1);

namespace IPub\AssetsLoader\Compilers;

use IPub\AssetsLoader\Entities;

class CssCompiler extends Compiler
{
	/**
	 * @var string
	 */
	protected $type = self::TYPE_CSS;

	/**
	 * Compressed file name
	 *
	 * @var string
	 */
	protected $filename = 'css-%s.css';

	/**
	 * Load file content
	 *
	 * @param Entities\IFile $file
	 *
	 * @return string
	 */
	protected function loadFile(Entities\IFile $file) : string
	{
		return $this->loadFileContent($file->getPath());
	}

	/**
	 * @param string $file
	 *
	 * @return string
	 */
	private function loadFileContent(string $file) : string
	{
		$content = '';

		// Check if file exists & is readable
		if (file_exists($file) && is_readable($file)) {
			// Load the local CSS stylesheet
			$content = file_get_contents($file);

			// Change to the current stylesheet's directory
			$cwd = getcwd();
			chdir(dirname($file));

			// Replaces @import commands with the actual stylesheet content
			// this happens recursively but omits external files
			$content = preg_replace_callback('/@import\s*(?:url\()?[\'"]?(?![a-z]+:)([^\'"\()]+)[\'"]?\)?;/', [$this, 'loadFileRecursive'], $content);

			// Remove multiple charset declarations for standards compliance (and fixing Safari problems)
			$content = preg_replace('/^@charset\s+[\'"](\S*)\b[\'"];/i', '', $content);

			// Change back directory.
			chdir($cwd);
		}

		// Apply all files filters
		foreach ($this->fileFilters as $filter) {
			$content = call_user_func($filter, $content, $this, $file);
		}

		return $content;
	}

	/**
	 * @param $matches
	 *
	 * @return string
	 */
	private function loadFileRecursive(array $matches) : string
	{
		$filename = $matches[1];

		// Load the imported stylesheet and replace @import commands in there as well
		$file = $this->loadFileContent($filename);

		// If not current directory, alter all url() paths, but not external
		if (dirname($filename) != '.') {
			$file = preg_replace('/url\([\'"]?(?![a-z]+:|\/+)([^\'")]+)[\'"]?\)/i', 'url(' . dirname($filename) . '/\1)', $file);
		}

		return $file;
	}
}
