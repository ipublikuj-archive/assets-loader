<?php
/**
 * LoaderFactory.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     common
 * @since          1.0.0
 *
 * @date           30.12.13
 */

declare(strict_types = 1);

namespace IPub\AssetsLoader;

use Nette;
use Nette\Utils;

use IPub\AssetsLoader\Compilers;
use IPub\AssetsLoader\Components;
use IPub\AssetsLoader\DI;
use IPub\AssetsLoader\Entities;
use IPub\AssetsLoader\Filters;

class LoaderFactory
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var Utils\ArrayHash
	 */
	protected $assets = [];

	/**
	 * @var Filters\IFilter[]
	 */
	protected $filters = [];

	/**
	 * @param string $name
	 *
	 * @return Entities\IAsset|NULL
	 */
	public function getAsset(string $name) : ?Entities\IAsset
	{
		if (isset($this->assets[$name])) {
			return $this->assets[$name]->asset;
		}

		return NULL;
	}

	/**
	 * @param string $name
	 *
	 * @return Compilers\Compiler|NULL
	 */
	public function getAssetCompiler(string $name) : ?Compilers\Compiler
	{
		if (isset($this->assets[$name])) {
			return $this->assets[$name]->compiler;
		}

		return NULL;
	}

	/**
	 * @param string $name
	 * @param Filters\IFilter $filter
	 *
	 * @return void
	 */
	public function addFilter(string $name, Filters\IFilter $filter) : void
	{
		$this->filters[$name] = $filter;
	}

	/**
	 * @param string $type
	 * @param string $name
	 *
	 * @return Filters\IFilter|NULL
	 */
	public function getFilter(string $type, string $name) : ?Filters\IFilter
	{
		foreach ($this->filters as $serviceName => $filter) {
			if (Utils\Strings::contains($serviceName, $type . '.' . $name)) {
				return $filter;
			}
		}

		return NULL;
	}

	/**
	 * @param string $name
	 *
	 * @return Components\CssLoader
	 */
	public function createCssLoader(string $name = 'default') : ?Components\CssLoader
	{
		// Get asset entity by name
		$asset = $this->getAsset($name . '.' . DI\AssetsLoaderExtension::TYPE_CSS);

		if ($asset === NULL) {
			return NULL;
		}

		// Get asset compiler
		$compiler = $this->getAssetCompiler($name . '.' . DI\AssetsLoaderExtension::TYPE_CSS);

		// Create component
		$control = new Components\CssLoader($compiler, $asset);

		return $control;
	}

	/**
	 * @param string $name
	 *
	 * @return Components\JsLoader
	 */
	public function createJsLoader(string $name = 'default') : ?Components\JsLoader
	{
		$asset = $this->getAsset($name . '.' . DI\AssetsLoaderExtension::TYPE_JS);

		// Get asset entity by name
		if ($asset === NULL) {
			return NULL;
		}

		// Get asset compiler
		$compiler = $this->getAssetCompiler($name . '.' . DI\AssetsLoaderExtension::TYPE_JS);

		$control = new Components\JsLoader($compiler, $asset);

		return $control;
	}

	/**
	 * @param Filters\IFilter $filter
	 * @param string $name
	 *
	 * @return void
	 */
	public function registerFilter(Filters\IFilter $filter, string $name) : void
	{
		$this->filters[$name] = $filter;
	}

	/**
	 * @param array $configuration
	 * @param Compilers\Compiler $compiler
	 *
	 * @return void
	 */
	public function registerAsset(array $configuration, Compilers\Compiler $compiler) : void
	{
		// Create set entity
		$asset = new Entities\Asset;
		$asset->setName($configuration['name']);
		$asset->setFiles($configuration['files']);
		$asset->setJoinFiles($configuration['joinFiles']);
		$asset->setGzip($configuration['gzip']);

		// Add set into collection
		$this->assets[$asset->getName()] = new Utils\ArrayHash;
		$this->assets[$asset->getName()]->asset = $asset;
		$this->assets[$asset->getName()]->compiler = $compiler;
	}
}
