<?php
/**
 * FilesCollection.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     Files
 * @since          1.0.0
 *
 * @date           29.12.13
 */

declare(strict_types = 1);

namespace IPub\AssetsLoader\Files;

use Nette;
use Nette\Utils;

use IPub;
use IPub\AssetsLoader;
use IPub\AssetsLoader\Entities;
use IPub\AssetsLoader\Exceptions;

class FilesCollection implements IFilesCollection, \IteratorAggregate, \ArrayAccess
{
	/**
	 * @var string
	 */
	private $root;

	/**
	 * @var Entities\IFile[]
	 */
	private $files = [];

	/**
	 * @var array
	 */
	private $remoteFiles = [];

	/**
	 * @param string|NULL $root files root for relative paths
	 */
	public function __construct(?string $root = NULL)
	{
		$this->root = $root;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setFiles($files) : void
	{
		// Clear files collection
		$this->clear();
		// Add new files
		$this->addFiles($files);
	}

	/**
	 * {@inheritdoc}
	 */
	public function addFiles($files) : void
	{
		foreach ($files as $key => $file) {
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
				$attribute = NULL;

				// Files with attributes support from neon
				if (is_object($file) && isset($file->value) && isset($file->attributes)) {
					$attribute = reset($file->attributes);
					$file = $file->value;
				}

				// Files with attributes support from template
				if (is_string($key) && !is_numeric($key)) {
					$attribute = $file;
					$file = $key;
				}

				// Check if file is remote file
				if (Utils\Strings::startsWith($file, 'http://') || Utils\Strings::startsWith($file, 'https://')) {
					$this->addRemoteFile($file, $attribute);

					// Local file detected
				} else {
					$this->addFile($file, $attribute);
				}
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFiles() : array
	{
		return $this->files;
	}

	/**
	 * {@inheritdoc}
	 */
	public function addFile($file, ?string $attribute = NULL) : void
	{
		// Check for entity
		if (!$file instanceof Entities\IFile) {
			// Create file entity
			$file = new Entities\File((string) $file, $attribute);
		}

		// Add entity to collection
		$this->files[(string) $file] = $file;
	}

	/**
	 * {@inheritdoc}
	 */
	public function removeFile(string $file) : void
	{
		$this->removeFiles([$file]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function removeFiles(array $files) : void
	{
		foreach ($files as $file) {
			unset($this->files[(string) $file]);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function setRemoteFiles($files) : void
	{
		foreach ($files as $file) {
			$this->addRemoteFile($file);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRemoteFiles() : array
	{
		return $this->remoteFiles;
	}

	/**
	 * {@inheritdoc}
	 */
	public function addRemoteFile(string $file) : void
	{
		if (in_array($file, $this->remoteFiles)) {
			return;
		}

		$this->remoteFiles[] = $file;
	}

	/**
	 * {@inheritdoc}
	 */
	public function clear() : void
	{
		$this->files = [];
		$this->remoteFiles = [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRoot() : string
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

	/**
	 * Whether an application parameter or an object exists
	 *
	 * @param  string $offset
	 *
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->files[$offset]);
	}

	/**
	 * Gets an application parameter or an object
	 *
	 * @param  string $offset
	 *
	 * @return Entities\IFile
	 */
	public function offsetGet($offset)
	{
		return $this->files[$offset];
	}

	/**
	 * Sets an application parameter or an object
	 *
	 * @param  string $offset
	 * @param Entities\IFile $value
	 */
	public function offsetSet($offset, $value)
	{
		$this->files[$offset] = $value;
	}

	/**
	 * Unsets an application parameter or an object
	 *
	 * @param  string $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->files[$offset]);
	}
}
