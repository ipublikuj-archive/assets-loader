<?php
/**
 * IFilesCollection.php
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

use IPub\AssetsLoader\Entities;

interface IFilesCollection
{
	/**
	 * @param array|\Traversable $files array list of files
	 *
	 * @return void
	 */
	public function setFiles($files) : void;

	/**
	 * @param array|\Traversable $files array list of files
	 *
	 * @return void
	 */
	public function addFiles($files) : void;

	/**
	 * @return array
	 */
	public function getFiles() : array;

	/**
	 * @param string|Entities\IFile $file
	 *
	 * @return void
	 */
	public function addFile($file) : void;

	/**
	 * @param string $file
	 *
	 * @return void
	 */
	public function removeFile(string $file) : void;

	/**
	 * @param array $files list of files
	 *
	 * @return void
	 */
	public function removeFiles(array $files) : void;

	/**
	 * @param array|\Traversable $files array list of files
	 *
	 * @return void
	 */
	public function setRemoteFiles($files) : void;

	/**
	 * @return array
	 */
	public function getRemoteFiles() : array;

	/**
	 * @param string $file URL address
	 *
	 * @return void
	 */
	public function addRemoteFile(string $file) : void;

	/**
	 * Remove all files
	 *
	 * @return void
	 */
	public function clear() : void;

	/**
	 * @return string
	 */
	public function getRoot() : string;
}
