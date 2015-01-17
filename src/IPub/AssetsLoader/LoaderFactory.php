<?php
/**
 * LoaderFactory.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	AssetsLoader
 * @since		5.0
 *
 * @date		30.12.13
 */

namespace IPub\AssetsLoader;

use Nette;
use Nette\Utils;

use IPub;
use IPub\AssetsLoader;
use IPub\AssetsLoader\Compilers;
use IPub\AssetsLoader\Components;
use IPub\AssetsLoader\DI;
use IPub\AssetsLoader\Entities;
use IPub\AssetsLoader\Filters;

class LoaderFactory extends Nette\Object
{
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
	 * @return bool|Entities\IAsset
	 */
	public function getAsset($name)
	{
		if (isset($this->assets[$name])) {
			return $this->assets[$name]->asset;
		}

		return FALSE;
	}

	/**
	 * @param string $name
	 *
	 * @return bool|Compilers\Compiler
	 */
	public function getAssetCompiler($name)
	{
		if (isset($this->assets[$name])) {
			return $this->assets[$name]->compiler;
		}

		return FALSE;
	}

	/**
	 * @param string $name
	 * @param Filters\IFilter $filter
	 *
	 * @return $this
	 */
	public function addFilter($name, Filters\IFilter $filter)
	{
		$this->filters[$name] = $filter;

		return $this;
	}

	/**
	 * @param string $type
	 * @param string $name
	 *
	 * @return Filters\IFilter|null
	 */
	public function getFilter($type, $name)
	{
		foreach($this->filters as $serviceName => $filter) {
			if (Utils\Strings::contains($serviceName, $type .'.'. $name)) {
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
	public function createCssLoader($name = 'default')
	{
		// Get asset entity by name
		if (!$asset = $this->getAsset($name .'.'. DI\AssetsLoaderExtension::TYPE_CSS)) {
			return NULL;
		}

		// Get asset compiler
		$compiler = $this->getAssetCompiler($name .'.'. DI\AssetsLoaderExtension::TYPE_CSS);

		// Create component
		$control = new Components\CssLoader($compiler, $asset);

		return $control;
	}

	/**
	 * @param string $name
	 *
	 * @return Components\JsLoader
	 */
	public function createJsLoader($name = 'default')
	{
		// Get asset entity by name
		if (!$asset = $this->getAsset($name .'.'. DI\AssetsLoaderExtension::TYPE_JS)) {
			return NULL;
		}

		// Get asset compiler
		$compiler = $this->getAssetCompiler($name .'.'. DI\AssetsLoaderExtension::TYPE_JS);

		$control = new Components\JsLoader($compiler, $asset);

		return $control;
	}

	/**
	 * @param Filters\IFilter $filter
	 * @param string $name
	 *
	 * @return $this
	 */
	public function registerFilter(Filters\IFilter $filter, $name)
	{
		$this->filters[$name] = $filter;

		return $this;
	}

	/**
	 * @param array $configuration
	 * @param Compilers\Compiler $compiler
	 *
	 * @return $this
	 */
	public function registerAsset(array $configuration, Compilers\Compiler $compiler)
	{
		// Create set entity
		$asset = (new Entities\Asset)
			->setName($configuration['name'])
			->setSourceDir($configuration['sourceDir'])
			->setFiles($configuration['files'])
			->setJoinFiles($configuration['joinFiles']);

		// Add set into collection
		$this->assets[$asset->getName()] = new Utils\ArrayHash;
		$this->assets[$asset->getName()]->asset		= $asset;
		$this->assets[$asset->getName()]->compiler	= $compiler;

		return $this;
	}
}