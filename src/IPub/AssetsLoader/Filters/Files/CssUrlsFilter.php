<?php
/**
 * CssUrlsFilter.php
 *
 * Absolutize urls in CSS
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

use IPub;
use IPub\AssetsLoader;
use IPub\AssetsLoader\Exceptions;
use IPub\AssetsLoader\Files;

class CssUrlsFilter extends FilesFilter
{
	/**
	 * Document root folder
	 *
	 * @var string
	 */
	protected $docRoot;

	/**
	 * Site base path
	 *
	 * @var string
	 */
	protected $basePath;

	/**
	 * Filter constructor
	 *
	 * @param string $docRoot web document root
	 * @param string $basePath base path
	 */
	public function __construct($docRoot, $basePath = NULL)
	{
		$this->docRoot = Files\Path::normalize($docRoot);

		if (!is_dir($this->docRoot)) {
			throw new Exceptions\InvalidArgumentException('Given document root is not directory.');
		}

		$this->basePath = $basePath;
	}

	/**
	 * Invoke filter
	 *
	 * @param string $code
	 * @param \IPub\AssetsLoader\Compilers\Compiler $loader
	 * @param string $file
	 *
	 * @return string
	 */
	public function __invoke($code, \IPub\AssetsLoader\Compilers\Compiler $loader, $file)
	{
		$self = $this;

		return preg_replace_callback('/url\([\'"]?(?![a-z]+:|\/+)([^\'")]+)[\'"]?\)/i', function ($matches) use ($self, $file)
		{
			return "url('" . $self->absolutizeUrl($matches[1], $file) . "')";
		}, $code);
	}

	/**
	 * Make relative url absolute
	 *
	 * @param string $url image url
	 * @param string $quote single or double quote
	 * @param string $cssFile absolute css file path
	 *
	 * @return string
	 */
	public function absolutizeUrl($url, $cssFile)
	{
		// Is already absolute
		if (preg_match('/^([a-z]+:\/)?\//', $url)) {
			return $url;
		}

		$cssFile = Files\Path::normalize($cssFile);

		// Inside document root
		if (strncmp($cssFile, $this->docRoot, strlen($this->docRoot)) === 0) {
			$path = $this->basePath . $this->cannonicalizePath(substr(dirname($cssFile), strlen($this->docRoot)) . DIRECTORY_SEPARATOR . $url);

		// Outside document root we don't know
		} else {
			return $url;
		}

		// Map image to file path
		if (preg_match('/\.(gif|png|jpg)$/i', $url)) {
			$imagePath = realpath(rtrim(dirname(realpath($cssFile)), '/') .'/'. $url);

			if (file_exists($imagePath) && filesize($imagePath) <= 10240 && preg_match('/\.(gif|png|jpg)$/i', $imagePath, $extension)) {
				$path = sprintf('data:image/%s;base64,%s', str_replace('jpg', 'jpeg', strtolower($extension[1])), base64_encode(file_get_contents($imagePath)));
			}
		}

		return $path;
	}

	/**
	 * Cannonicalize path
	 *
	 * @param string $path
	 *
	 * @return string path
	 */
	protected function cannonicalizePath($path)
	{
		$path = strtr($path, DIRECTORY_SEPARATOR, '/');

		$pathArr = array();

		foreach (explode('/', $path) as $i => $name) {
			if ($name === '.' || ($name === '' && $i > 0) )
				continue;

			if ($name === '..') {
				array_pop($pathArr);
				continue;
			}

			$pathArr[] = $name;
		}

		return implode('/', $pathArr);
	}
}