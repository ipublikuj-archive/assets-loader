<?php
/**
 * IFilesCollection.php
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

interface IFilesCollection
{
	/**
	 * @param array|\Traversable $files array list of files
	 *
	 * @return $this
	 */
	public function setFiles($files);

	/**
	 * @param array|\Traversable $files array list of files
	 *
	 * @return $this
	 */
	public function addFiles($files);

	/**
	 * @return array
	 */
	public function getFiles();

	/**
	 * @param $file string filename
	 *
	 * @return $this
	 */
	public function addFile($file);

	/**
	 * @param $file string filename
	 *
	 * @return $this
	 */
	public function removeFile($file);

	/**
	 * @param array $files list of files
	 *
	 * @return $this
	 */
	public function removeFiles(array $files);

	/**
	 * @param array|\Traversable $files array list of files
	 *
	 * @return $this
	 */
	public function setRemoteFiles($files);

	/**
	 * @return array
	 */
	public function getRemoteFiles();

	/**
	 * @param string $file URL address
	 *
	 * @return $this
	 */
	public function addRemoteFile($file);

	/**
	 * Remove all files
	 *
	 * @return $this
	 */
	public function clear();

	/**
	 * @return string
	 */
	public function getRoot();
}
