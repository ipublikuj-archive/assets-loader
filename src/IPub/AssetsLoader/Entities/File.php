<?php
/**
 * File.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Entities
 * @since		5.0
 *
 * @date		23.01.15
 */

namespace IPub\AssetsLoader\Entities;

use Nette;

use IPub;
use IPub\AssetsLoader;
use IPub\AssetsLoader\Exceptions;
use IPub\AssetsLoader\Files;
use IPub\AssetsLoader\Filters;

class File extends Nette\Object implements IFile
{
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
	 * @param string|null $attribute
	 */
	public function __construct($path, $attribute = NULL)
	{
		// Parse file path into info pats
		$this->setPath($path);

		// File attributes
		$this->attribute	= $attribute;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFilename()
	{
		return $this->filename;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPath($path)
	{
		// Create full path
		$path = $this->cannonicalizePath($path);

		$this->path		= $path;
		$this->filename	= basename($path);
		$this->mimetype	= Files\MimeMapper::getMimeFromFilename($this->filename);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMimetype()
	{
		return $this->mimetype;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setAttribute($attribute)
	{
		$this->attribute = $attribute;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAttribute()
	{
		return $this->attribute;
	}

	/**
	 * @return int
	 */
	public function getFileSize()
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
	 * @param $path string
	 *
	 * @return string
	 *
	 * @throws Exceptions\FileNotFoundException
	 */
	protected function cannonicalizePath($path)
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