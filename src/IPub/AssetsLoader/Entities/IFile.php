<?php
/**
 * IFile.php
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

interface IFile
{
	/**
	 * @return string
	 */
	public function getFilename() : string;

	/**
	 * @param string $path
	 *
	 * @return void
	 */
	public function setPath(string $path) : void;

	/**
	 * @return string
	 */
	public function getPath() : string;

	/**
	 * @return string
	 */
	public function getMimetype() : string;

	/**
	 * @param mixed $attribute
	 *
	 * @return void
	 */
	public function setAttribute(string $attribute) : void;

	/**
	 * @return string|NULL
	 */
	public function getAttribute() : ?string;
}
