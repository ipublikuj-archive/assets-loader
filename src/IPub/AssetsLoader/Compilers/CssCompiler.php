<?php
/**
 * CssCompiler.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Compilers
 * @since		5.0
 *
 * @date		29.12.13
 */

namespace IPub\AssetsLoader\Compilers;

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
	 * @param string $file path
	 *
	 * @return string
	 */
	protected function loadFile($file)
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
			$content = preg_replace_callback('/@import\s*(?:url\()?[\'"]?(?![a-z]+:)([^\'"\()]+)[\'"]?\)?;/', array($this, 'loadFileRecursive'), $content);

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
	protected function loadFileRecursive($matches)
	{
		$filename = $matches[1];

		// Load the imported stylesheet and replace @import commands in there as well
		$file = $this->loadFile($filename);

		// If not current directory, alter all url() paths, but not external
		if (dirname($filename) != '.') {
			$file = preg_replace('/url\([\'"]?(?![a-z]+:|\/+)([^\'")]+)[\'"]?\)/i', 'url('. dirname($filename) .'/\1)', $file);
		}

		return $file;
	}
}