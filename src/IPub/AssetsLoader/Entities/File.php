<?php
/**
 * File.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     Entities
 * @since          1.0.0
 *
 * @date           23.01.15
 */

declare(strict_types = 1);

namespace IPub\AssetsLoader\Entities;

use Nette;

use IPub\AssetsLoader\Exceptions;
use IPub\AssetsLoader\Files;

class File implements IFile
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var string
	 */
	protected $filename;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var mixed
	 */
	protected $attribute;

	/**
	 * @var string
	 */
	protected $mimetype;

	/**
	 * @param string $path
	 * @param string|NULL $attribute
	 */
	public function __construct(string $path, ?string $attribute = NULL)
	{
		// Parse file path into info pats
		$this->setPath($path);

		// File attributes
		$this->attribute = $attribute;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFilename() : string
	{
		return $this->filename;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPath(string $path) : void
	{
		// Create full path
		$path = $this->cannonicalizePath($path);

		$this->path = $path;
		$this->filename = basename($path);
		$this->mimetype = Files\MimeMapper::getMimeFromFilename($this->filename);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPath() : string
	{
		return $this->path;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMimetype() : string
	{
		return $this->mimetype;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setAttribute(string $attribute) : void
	{
		$this->attribute = $attribute;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAttribute() : ?string
	{
		return $this->attribute;
	}

	/**
	 * @return int
	 */
	public function getFileSize() : int
	{
		return filesize($this->path);
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->path;
	}

	/**
	 * Make path absolute
	 *
	 * @param string $path
	 *
	 * @return string
	 *
	 * @throws Exceptions\FileNotFoundException
	 */
	private function cannonicalizePath(string $path) : string
	{
		if (file_exists($path)) {
			return $path;
		}

		$abs = Files\Path::normalize($path);

		if (file_exists($abs)) {
			return $abs;
		}

		throw new Exceptions\FileNotFoundException("File '$path' does not exist.");
	}
}
