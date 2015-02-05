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

use Nette;
use Nette\Application;

use IPub;
use IPub\AssetsLoader;
use IPub\AssetsLoader\Caching;
use IPub\AssetsLoader\Compilers;
use IPub\AssetsLoader\Files;

class CssUrlsFilter extends FilesFilter
{
	/**
	 * Site base path
	 *
	 * @var string
	 */
	protected $basePath;

	/**
	 * @var Caching\FileCache
	 */
	protected $cache;

	/**
	 * @var Application\Application
	 */
	protected $application;

	/**
	 * Filter constructor
	 *
	 * @param Caching\FileCache $cache
	 * @param Application\Application $application
	 */
	public function __construct(Caching\FileCache $cache, Application\Application $application)
	{
		$this->cache = $cache;
		$this->application = $application;
	}

	/**
	 * Invoke filter
	 *
	 * @param string $code
	 * @param Compilers\Compiler $compiler
	 * @param string $file
	 *
	 * @return string
	 */
	public function __invoke($code, Compilers\Compiler $compiler, $file)
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

		// Get css file real path
		$cssFile = Files\Path::normalize($cssFile);

		// Remove query string
		$url = preg_replace('/\?.*/', '', $url);

		// Create full file path
		$filePath = realpath(rtrim(dirname(realpath($cssFile)), '/') .'/'. $url);

		// Check if file exists
		if (!$filePath || !file_exists($filePath)) {
			return $url;
		}

		// Check for images which can be encoded
		if (preg_match('/\.(gif|png|jpg)$/i', $url) && filesize($filePath) <= 10240 && preg_match('/\.(gif|png|jpg)$/i', $filePath, $extension)) {
			$path = sprintf('data:image/%s;base64,%s', str_replace('jpg', 'jpeg', strtolower($extension[1])), base64_encode(file_get_contents($filePath)));

		// Other files
		} else {
			// Create file hash
			$fileHash = $this->getHash($filePath);

			// Save file into cache
			$this->cache->save(
				$fileHash,
				[
					Caching\FileCache::CONTENT	=> $filePath
				],
				[
					Caching\FileCache::TAGS		=> ['ipub.assetsloader', 'ipub.assetsloader.images'],
					Caching\FileCache::FILES	=> [$filePath]
				]
			);

			// Create route for specific file
			$path = $this->getPresenter()->link(':IPub:AssetsLoader:files', ['id' => $fileHash]);
		}

		return $path;
	}

	/**
	 * @return Application\IPresenter
	 */
	protected function getPresenter()
	{
		return $this->application->getPresenter();
	}

	/**
	 * @param $file
	 *
	 * @return string
	 */
	protected function getHash($file)
	{
		$tmp = $file . filesize($file);

		return substr(md5($tmp), 0, 12);
	}
}