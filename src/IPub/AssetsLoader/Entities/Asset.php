<?php
/**
 * Asset.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Entities
 * @since		5.0
 *
 * @date		16.01.15
 */

namespace IPub\AssetsLoader\Entities;

use Nette;
use Nette\Utils;

use IPub;
use IPub\AssetsLoader;
use IPub\AssetsLoader\Exceptions;
use IPub\AssetsLoader\Files;
use IPub\AssetsLoader\Filters;

class Asset extends Nette\Object implements IAsset
{
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $sourceDir;

	/**
	 * @var Files\IFilesCollection
	 */
	protected $files;

	/**
	 * @var bool
	 */
	protected $joinFiles = TRUE;

	public function __construct()
	{
		$this->files = new Files\FilesCollection;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setName($name)
	{
		$this->name = (string) $name;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setSourceDir($sourceDir)
	{
		if (!is_dir($sourceDir)) {
			throw new Exceptions\DirectoryNotFoundException('Invalid set source directory given.');
		}

		$this->sourceDir = (string) $sourceDir;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSourceDir()
	{
		return $this->sourceDir;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setFiles(array $files)
	{
		// Add files into collection
		$this->files->setFiles($files);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function addFile($file)
	{
		// Add file into collection
		$this->files->addFile($file);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFiles()
	{
		return $this->files;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setJoinFiles($joinFiles)
	{
		$this->joinFiles = (bool) $joinFiles;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getJoinFiles()
	{
		return $this->joinFiles;
	}
}