<?php
/**
 * JsLoader.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     Components
 * @since          1.0.0
 *
 * @date           08.06.13
 */

declare(strict_types = 1);

namespace IPub\AssetsLoader\Components;

use Nette;
use Nette\Utils;

use IPub\AssetsLoader\Exceptions;
use IPub\AssetsLoader\Files;

class JsLoader extends AssetsLoader
{
	/**
	 * @var string
	 */
	protected $contentType = 'text/javascript';

	/**
	 * Get script element
	 *
	 * @param string $source
	 *
	 * @return Utils\Html
	 */
	public function getElement(string $source) : Utils\Html
	{
		return Utils\Html::el('script')
			->appendAttribute('type', $this->contentType)
			->appendAttribute('src', $source);
	}

	/**
	 * @return void
	 *
	 * @throws Nette\Application\UI\InvalidLinkException
	 */
	public function renderFiles() : void
	{
		// Remote files
		foreach ($this->files->getRemoteFiles() as $file) {
			echo $this->getElement($file), PHP_EOL;
		}

		// Check if we should join all files into one
		if ($this->asset->getJoinFiles()) {
			// Compile files collection
			$result = $this->compiler->generate($this->files->getFiles(), $this->contentType);

			echo $this->getElement($this->getPresenter()->link(':IPub:AssetsLoader:assets', ['type' => 'js', 'id' => $result->hash, 'timestamp' => $result->lastModified])), PHP_EOL;

			// Leave files splitted
		} else {
			foreach ($this->files as $file) {
				// Compile single file
				$result = $this->compiler->generate([$file], $this->contentType);

				echo $this->getElement($this->getPresenter()->link(':IPub:AssetsLoader:assets', ['type' => 'js', 'id' => $result->hash, 'timestamp' => $result->lastModified])), PHP_EOL;
			}
		}
	}

	/**
	 * Generates link
	 *
	 * @return string
	 *
	 * @throws Exceptions\InvalidStateException
	 * @throws Nette\Application\UI\InvalidLinkException
	 */
	public function getLink() : string
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

		if (!$this->asset->getJoinFiles()) {
			throw new Exceptions\InvalidStateException("Can't generate link with disabled joinFiles.");
		}

		// Compile files collection
		$result = $this->compiler->generate($this->files->getFiles(), $this->contentType);

		$link = $this->getPresenter()->link(':IPub:AssetsLoader:assets', ['type' => 'js', 'id' => $result->hash, 'timestamp' => $result->lastModified]);

		if ($hasArgs && $backup !== NULL) {
			$this->setFiles($backup);
		}

		return $link;
	}
}
