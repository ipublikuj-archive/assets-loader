<?php
/**
 * Asset.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     Entities
 * @since          1.0.0
 *
 * @date           16.01.15
 */

declare(strict_types = 1);

namespace IPub\AssetsLoader\Entities;

use Nette;
use Nette\Utils;

use IPub\AssetsLoader\Exceptions;
use IPub\AssetsLoader\Files;
use IPub\AssetsLoader\Filters;

class Asset implements IAsset
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var Files\IFilesCollection
	 */
	protected $files;

	/**
	 * @var bool
	 */
	protected $joinFiles = TRUE;

	/**
	 * @var bool
	 */
	protected $gzip = FALSE;

	public function __construct()
	{
		$this->files = new Files\FilesCollection;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setName(string $name) : void
	{
		$this->name = $name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setFiles(array $files) : void
	{
		// Add files into collection
		$this->files->addFiles($files);
	}

	/**
	 * {@inheritdoc}
	 */
	public function addFile($file) : void
	{
		// Add file into collection
		$this->files->addFile($file);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFiles() : Files\IFilesCollection
	{
		return $this->files;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setJoinFiles(bool $joinFiles) : void
	{
		$this->joinFiles = $joinFiles;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getJoinFiles() : bool
	{
		return $this->joinFiles;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setGzip(bool $gzip) : void
	{
		$this->gzip = $gzip;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getGzip() : bool
	{
		return $this->gzip;
	}
}
