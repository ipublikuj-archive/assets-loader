<?php
/**
 * FilesCollection.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Files
 * @since		5.0
 *
 * @date		29.12.13
 */

namespace IPub\AssetsLoader\Files;

use Nette;
use Nette\Utils;

use IPub;
use IPub\AssetsLoader;
use IPub\AssetsLoader\Exceptions;

class FilesCollection implements IFilesCollection, \IteratorAggregate
{
	/**
	 * @var string
	 */
	private $root;

	/**
	 * @var array
	 */
	private $files = [];

	/**
	 * @var array
	 */
	private $remoteFiles = [];

	/**
	 * @param string|NULL $root files root for relative paths
	 */
	public function __construct($root = NULL)
	{
		$this->root = $root;
	}

	/**
	 * Make path absolute
	 *
	 * @param $path string
	 *
	 * @return string
	 *
	 * @throws Exceptions\FileNotFoundException
	 */
	public function cannonicalizePath($path)
	{

		$rel = Path::normalize($this->root . DIRECTORY_SEPARATOR . $path);

		if (file_exists($rel)) {
			return $rel;
		}

		$abs = Path::normalize($path);

		if (file_exists($abs)) {
			return $abs;
		}

		throw new Exceptions\FileNotFoundException("File '$path' does not exist.");
	}

	/**
	 * {@inheritdoc}
	 */
	public function setFiles($files)
	{
		foreach ($files as $file) {
			// Finder support
			if (is_array($file) && isset($file['files']) && (isset($file['in']) || isset($file['from']))) {
				$finder = Utils\Finder::findFiles($file['files']);

				if (isset($file['exclude'])) {
					$finder->exclude($file['exclude']);
				}

				if (isset($file['in'])) {
					$finder->in($file['in']);

				} else {
					$finder->from($file['from']);
				}

				foreach ($finder as $foundFile) {
					$this->addFile($foundFile);
				}

			// Normal files
			} else {
				// Check if file is remote file
				if (Utils\Strings::startsWith($file, 'http://') || Utils\Strings::startsWith($file, 'https://')) {
					$this->addRemoteFile($file);

				// Local file detected
				} else {
					$this->addFile($file);
				}
			}
		}

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFiles()
	{
		return array_values($this->files);
	}

	/**
	 * {@inheritdoc}
	 */
	public function addFile($file)
	{
		$file = $this->cannonicalizePath((string) $file);

		if (in_array($file, $this->files)) {
			return $this;
		}

		$this->files[] = $file;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function removeFile($file)
	{
		$this->removeFiles(array($file));

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function removeFiles(array $files)
	{
		$files = array_map([$this, 'cannonicalizePath'], $files);

		$this->files = array_diff($this->files, $files);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setRemoteFiles($files)
	{
		foreach ($files as $file) {
			$this->addRemoteFile($file);
		}

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRemoteFiles()
	{
		return $this->remoteFiles;
	}

	/**
	 * {@inheritdoc}
	 */
	public function addRemoteFile($file)
	{
		if (in_array($file, $this->remoteFiles)) {
			return;
		}

		$this->remoteFiles[] = $file;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function clear()
	{
		$this->files		= [];
		$this->remoteFiles	= [];

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRoot()
	{
		return $this->root;
	}

	/**
	 * Implements the IteratorAggregate
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->files);
	}
}