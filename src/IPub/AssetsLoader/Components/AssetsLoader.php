<?php
/**
 * AssetsLoader.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     Components
 * @since          1.0.0
 *
 * @date           29.12.13
 */

declare(strict_types = 1);

namespace IPub\AssetsLoader\Components;

use Nette\Utils;
use Nette\Application;

use IPub\AssetsLoader\Compilers;
use IPub\AssetsLoader\Entities;
use IPub\AssetsLoader\Files;

abstract class AssetsLoader extends Application\UI\Control
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
	 * @var string
	 */
	protected $type;

	/**
	 * @param Compilers\Compiler $compiler
	 * @param Entities\IAsset $set
	 */
	public function __construct(Compilers\Compiler $compiler, Entities\IAsset $set)
	{
		parent::__construct();

		$this->compiler = $compiler;
		$this->asset = $set;

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
	abstract public function getElement(string $source) : Utils\Html;

	/**
	 * Generates link
	 *
	 * @return string
	 */
	abstract public function getLink() : string;

	/**
	 * Process files and render elements including generated content
	 *
	 * @return void
	 */
	abstract public function renderFiles() : void;

	/**
	 * Generate compiled file(s) and render link(s)
	 *
	 * @return void
	 */
	public function render() : void
	{
		$hasArgs = func_num_args() > 0;

		$backup = NULL;

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

		if ($hasArgs && $backup !== NULL) {
			$this->setFiles($backup);
		}
	}

	/**
	 * Generates and render link
	 *
	 * @return void
	 */
	public function renderLink() : void
	{
		$hasArgs = func_num_args() > 0;

		$backup = NULL;

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

		if ($hasArgs && $backup !== NULL) {
			$this->setFiles($backup);
		}
	}

	/**
	 * @param Files\IFilesCollection $files
	 *
	 * @return void
	 */
	public function setFiles(Files\IFilesCollection $files) : void
	{
		$this->files = $files;
	}

	/**
	 * @return Files\IFilesCollection
	 */
	public function getFiles() : Files\IFilesCollection
	{
		return $this->files;
	}

	/**
	 * Remove all files
	 *
	 * @return void
	 */
	public function clear() : void
	{
		$this->files = NULL;
	}

	/**
	 * Set files compiler
	 *
	 * @param Compilers\Compiler $compiler
	 *
	 * @return void
	 */
	public function setCompiler(Compilers\Compiler $compiler) : void
	{
		$this->compiler = $compiler;
	}

	/**
	 * Get files compiler
	 *
	 * @return Compilers\Compiler
	 */
	public function getCompiler() : Compilers\Compiler
	{
		return $this->compiler;
	}

	/**
	 * @param Entities\IAsset $asset
	 *
	 * @return void
	 */
	public function setAsset(Entities\IAsset $asset) : void
	{
		$this->asset = $asset;
	}

	/**
	 * @return Entities\IAsset
	 */
	public function getAsset() : Entities\IAsset
	{
		return $this->asset;
	}

	/**
	 * Set type
	 *
	 * @param string $type
	 *
	 * @return void
	 */
	public function setType(string $type) : void
	{
		$this->type = $type;
	}

	/**
	 * Get type
	 *
	 * @return string
	 */
	public function getType() : string
	{
		return $this->type;
	}
}
