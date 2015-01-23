<?php
/**
 * IFile.php
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

use IPub;
use IPub\AssetsLoader;
use IPub\AssetsLoader\Exceptions;

interface IFile
{
	/**
	 * @return string
	 */
	public function getFilename();

	/**
	 * @param string $path
	 *
	 * @return $this
	 */
	public function setPath($path);

	/**
	 * @return string
	 */
	public function getPath();

	/**
	 * @return string
	 */
	public function getMimetype();

	/**
	 * @param mixed $attribute
	 *
	 * @return $this
	 */
	public function setAttribute($attribute);

	/**
	 * @return mixed
	 */
	public function getAttribute();
}