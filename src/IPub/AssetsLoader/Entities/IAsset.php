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

use IPub\AssetsLoader\Files;

interface IAsset
{
	/**
	 * @param string $name
	 *
	 * @return void
	 */
	public function setName(string $name) : void;

	/**
	 * @return string
	 */
	public function getName() : string;

	/**
	 * @param array $files
	 *
	 * @return void
	 */
	public function setFiles(array $files) : void;

	/**
	 * @param string $file
	 *
	 * @return void
	 */
	public function addFile($file) : void;

	/**
	 * @return Files\IFilesCollection
	 */
	public function getFiles() : Files\IFilesCollection;

	/**
	 * @param bool $joinFiles
	 *
	 * @return void
	 */
	public function setJoinFiles(bool $joinFiles) : void;

	/**
	 * @return bool
	 */
	public function getJoinFiles() : bool;

	/**
	 * @param bool $gzip
	 *
	 * @return void
	 */
	public function setGzip(bool $gzip) : void;

	/**
	 * @return bool
	 */
	public function getGzip() : bool;
}
