<?php
/**
 * AssetsLoader.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Components
 * @since		5.0
 *
 * @date		29.12.13
 */

namespace IPub\AssetsLoader\Components;

use Nette;
use Nette\Utils;

use IPub;
use IPub\AssetsLoader\Compilers;
use IPub\AssetsLoader\Entities;
use IPub\AssetsLoader\Files;

abstract class AssetsLoader extends \Nette\Application\UI\Control
{
	/**
	 * Files compiler
	 *
	 * @var Compilers\Compiler
	 */
	protected $compiler;

	/**
	 * Static files asset
	 *
	 * @var Entities\IAsset
	 */
	protected $asset;

	/**
	 * @var Files\IFilesCollection
	 */
	protected $files;

	/**
	 * @param Compilers\Compiler $compiler
	 * @param Entities\IAsset $set
	 */
	public function __construct(Compilers\Compiler $compiler, Entities\IAsset $set)
	{
		parent::__construct();

		$this->compiler	= $compiler;
		$this->asset	= $set;

		// Get files collection from asset
		$this->setFiles($set->getFiles());
	}

	/**
	 * Get html element including generated content
	 *
	 * @param string $source
	 *
	 * @return Utils\Html
	 */
	abstract public function getElement($source);

	/**
	 * Generates link
	 *
	 * @return string
	 */
	abstract public function getLink();

	/**
	 * Process files and render elements including generated content
	 *
	 * @return Utils\Html
	 */
	abstract public function renderFiles();

	/**
	 * Generate compiled file(s) and render link(s)
	 */
	public function render()
	{
		$hasArgs = func_num_args() > 0;

		if ($hasArgs) {
			// Backup files
			$backup = $this->files;
			// Clear files collection
			$this->clear();

			// Get all arguments which could be files
			$args = func_get_args();
			$args = reset($args);

			// Create new collection from arguments
			$newFiles = new Files\FilesCollection;
			$newFiles->addFiles($args);

			// Create new files collection
			$this->setFiles($newFiles);
		}

		// Process rendering
		$this->renderFiles();

		if ($hasArgs) {
			$this->setFiles($backup);
		}
	}

	/**
	 * Generates and render link
	 */
	public function renderLink()
	{
		$hasArgs = func_num_args() > 0;

		if ($hasArgs) {
			// Backup files
			$backup = $this->files;
			// Clear files collection
			$this->clear();

			// Get all arguments which could be files
			$args = func_get_args();
			$args = reset($args);

			// Create new collection from arguments
			$newFiles = new Files\FilesCollection;
			$newFiles->addFiles($args);

			// Create new files collection
			$this->setFiles($newFiles);
		}

		echo $this->getLink();

		if ($hasArgs) {
			$this->setFiles($backup);
		}
	}

	/**
	 * @param Files\IFilesCollection $files
	 *
	 * @return $this
	 */
	public function setFiles(Files\IFilesCollection $files)
	{
		$this->files = $files;

		return $this;
	}

	/**
	 * @return Files\IFilesCollection
	 */
	public function getFiles()
	{
		return $this->files;
	}

	/**
	 * Remove all files
	 */
	public function clear()
	{
		$this->files = NULL;
	}

	/**
	 * Set files compiler
	 *
	 * @param Compilers\Compiler $compiler
	 *
	 * @return $this
	 */
	public function setCompiler(Compilers\Compiler $compiler)
	{
		$this->compiler = $compiler;

		return $this;
	}

	/**
	 * Get files compiler
	 *
	 * @return Compilers\Compiler
	 */
	public function getCompiler()
	{
		return $this->compiler;
	}

	/**
	 * @param Entities\IAsset $asset
	 *
	 * @return $this
	 */
	public function setAsset(Entities\IAsset $asset)
	{
		$this->asset = $asset;

		return $this;
	}

	/**
	 * @return Entities\IAsset
	 */
	public function getAsset()
	{
		return $this->asset;
	}

	/**
	 * Set type
	 *
	 * @param string $type
	 *
	 * @return CssLoader
	 */
	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * Get type
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}
}