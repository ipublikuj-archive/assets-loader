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
use IPub\AssetsLoader\Files;
use IPub\AssetsLoader\Filters;

interface IAsset
{
	/**
	 * @param string $name
	 *
	 * @return $this
	 */
	public function setName($name);

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @param string $sourceDir
	 *
	 * @return $this
	 *
	 * @throw Exceptions\DirectoryNotFoundException
	 */
	public function setSourceDir($sourceDir);

	/**
	 * @return string
	 */
	public function getSourceDir();

	/**
	 * @param array $files
	 *
	 * @return $this
	 *
	 * @throw Exceptions\FileNotFoundException
	 */
	public function setFiles(array $files);

	/**
	 * @param string $file
	 *
	 * @return $this
	 *
	 * @throw Exceptions\FileNotFoundException
	 */
	public function addFile($file);

	/**
	 * @return Files\IFilesCollection
	 */
	public function getFiles();

	/**
	 * @param bool $joinFiles
	 *
	 * @return $this
	 */
	public function setJoinFiles($joinFiles);

	/**
	 * @return bool
	 */
	public function getJoinFiles();
}