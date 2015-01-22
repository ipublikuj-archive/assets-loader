<?php
/**
 * AssetsLoaderExtension.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	DI
 * @since		5.0
 *
 * @date		30.12.13
 */

namespace IPub\AssetsLoader\DI;

use Nette;
use Nette\DI;
use Nette\PhpGenerator as Code;

use Tracy;

use IPub;
use IPub\AssetsLoader;
use IPub\AssetsLoader\Entities;

class AssetsLoaderExtension extends DI\CompilerExtension
{
	// Define tag string for filters
	const TAG_FILTER = 'ipub.assetsloader.filter';

	const TYPE_CSS	= 'css';
	const TYPE_JS	= 'js';

	/**
	 * Extension default configuration
	 *
	 * @var array
	 */
	protected $defaults = [
		'routes'			=> [
			'assets'	=> '/assets-loader/<id>',
			'files'		=> '/assets-loader/files-<id>'
		],
		self::TYPE_CSS		=> [
			'sourceDir'	=> '%wwwDir%/css/',
			'gzip'		=> FALSE,
			'files'		=> [],
			'filters'	=> [
				'files'		=> [],
				'content'	=> []
			],
			'joinFiles'	=> TRUE,
		],
		self::TYPE_JS		=> [
			'sourceDir'	=> '%wwwDir%/js/',
			'gzip'		=> FALSE,
			'files'		=> [],
			'filters'	=> [
				'files'		=> [],
				'content'	=> []
			],
			'joinFiles'	=> TRUE,
		],
		'assets'			=> [],
		'debugger'			=> '%debugMode%',
	];

	/**
	 * Default set structure
	 *
	 * @var array
	 */
	protected $defaultAsset = array(
		self::TYPE_CSS => array(
			'sourceDir'		=> '%wwwDir%/css',
			'files'			=> [],
			'filters'		=> [
				'files'		=> [],
				'content'	=> [],
			],
			'joinFiles'		=> TRUE,
		),
		self::TYPE_JS => array(
			'sourceDir'		=> '%wwwDir%/js',
			'files'			=> [],
			'filters'		=> [
				'files'		=> [],
				'content'	=> [],
			],
			'joinFiles'		=> TRUE
		),
		'packages' => []
	);

	/**
	 * @var array
	 */
	protected $assets = [];

	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		$this->loadConfig('assetsloader');

		// Create web loader factory
		$factory = $builder->addDefinition($this->prefix('factory'))
			->setClass('IPub\AssetsLoader\LoaderFactory');

		// Create extensions route for assets
		$builder->addDefinition($this->prefix('route.assets'))
			->setClass('IPub\AssetsLoader\Application\Route', [$config['routes']['assets'], ['presenter' => 'IPub:AssetsLoader', 'action' => 'assets']])
			->setAutowired(FALSE)
			->setInject(FALSE);

		// Add route to router
		$builder->getDefinition('router')
			->addSetup('IPub\AssetsLoader\Application\Route::prependTo($service, ?)', [$this->prefix('@route.assets')]);

		// Create extensions route for images
		$builder->addDefinition($this->prefix('route.files'))
			->setClass('IPub\AssetsLoader\Application\Route', [$config['routes']['files'], ['presenter' => 'IPub:AssetsLoader', 'action' => 'files']])
			->setAutowired(FALSE)
			->setInject(FALSE);

		// Add route to router
		$builder->getDefinition('router')
			->addSetup('IPub\AssetsLoader\Application\Route::prependTo($service, ?)', [$this->prefix('@route.files')]);

		// Update presenters mapping
		$builder->getDefinition('nette.presenterFactory')
			->addSetup('if (method_exists($service, ?)) { $service->setMapping([? => ?]); } '
				.'elseif (property_exists($service, ?)) { $service->mapping[?] = ?; }',
				['setMapping', 'IPub', 'IPub\IPubModule\*\*Presenter', 'mapping', 'IPub', 'IPub\IPubModule\*\*Presenter']
			);

		// Create cache services
		$builder->addDefinition($this->prefix('cache.assets'))
			->setClass('IPub\AssetsLoader\Caching\AssetCache', ['@cacheStorage', 'IPub.AssetsLoader.Assets'])
			->setInject(FALSE);

		$builder->addDefinition($this->prefix('cache.files'))
			->setClass('IPub\AssetsLoader\Caching\FileCache', ['@cacheStorage', 'IPub.AssetsLoader.Files'])
			->setInject(FALSE);

		// Collect all assets
		foreach ($config['assets'] as $name => $assetConfig) {
			// Merge set config with default structure
			$assetConfig = DI\Config\Helpers::merge($assetConfig, $this->defaultAsset);

			// Check for packages
			foreach ($assetConfig['packages'] as $package) {
				foreach ([self::TYPE_CSS, self::TYPE_JS] as $type) {
					if (isset($package[$type])) {
						$assetConfig = DI\Config\Helpers::merge([
							$type => [
								'files' => $package[$type]
							]
						], $assetConfig);
					}
				}
			}

			// Remove packages definition
			unset($assetConfig['packages']);

			// Update set configuration
			$this->assets[$name] = $assetConfig;
		}

		// Create default asset
		$defaultAsset = [
			self::TYPE_CSS	=> $config[self::TYPE_CSS],
			self::TYPE_JS	=> $config[self::TYPE_JS],
		];

		// Check for packages
		if (isset($config['packages']) === TRUE) {
			foreach ($config['packages'] as $package) {
				foreach ([self::TYPE_CSS, self::TYPE_JS] as $type) {
					if (isset($package[$type])) {
						$defaultAsset = DI\Config\Helpers::merge([
							$type => [
								'files' => $package[$type]
							]
						], $assetConfig);
					}
				}
			}
		}

		$this->assets['default'] = $defaultAsset;

		// Register diagnostic panel
		if ($config['debugger'] && interface_exists('Tracy\IBarPanel')) {
			$builder->addDefinition($this->prefix('panel'))
				->setClass('IPub\AssetsLoader\Diagnostics\Panel');

			$factory->addSetup('?->register(?)', array($this->prefix('@panel'), '@self'));
		}
	}

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		// Get web loader factory
		$factory = $builder->getDefinition($this->prefix('factory'));

		// Get all registered filters
		foreach (array_keys($builder->findByTag(self::TAG_FILTER)) as $serviceName) {
			// Register filter to factory
			$factory->addSetup('registerFilter', ['@' .$serviceName, $serviceName]);
		}

		foreach ($this->compiler->getExtensions() as $extension) {
			if (!$extension instanceof IStaticFilesProvider) {
				continue;
			}

			// Get files from extensions
			$files = $extension->getStaticFiles();

			foreach ([self::TYPE_CSS, self::TYPE_JS] as $type) {
				if (isset($files[$type])) {
					$this->assets['default'] = DI\Config\Helpers::merge([
						$type => [
							'files' => $files[$type]
						]
					], $this->assets['default']);
				}
			}

			foreach($files as $assetName => $assetFiles) {
				if (isset($this->assets[$assetName])) {
					foreach ([self::TYPE_CSS, self::TYPE_JS] as $type) {
						if (isset($assetFiles[$type])) {
							$this->assets[$assetName] = DI\Config\Helpers::merge([
								$type => [
									'files' => $assetFiles[$type]
								]
							], $this->assets[$assetName]);
						}
					}
				}
			}
		}

		// Create compilers
		foreach ($this->assets as $name => $assetConfig) {
			// Assets are splitted into types CSS/JS
			foreach ([self::TYPE_CSS, self::TYPE_JS] as $type) {
				$compiler = $builder->addDefinition($this->prefix($type . ucfirst($name) . 'Compiler'))
					->setClass('IPub\AssetsLoader\Compilers\\' . ucfirst($type) . 'Compiler')
					->setArguments([$this->prefix('@cache.assets')]);

				// Add content filters
				foreach ($assetConfig[$type]['filters']['content'] as $filter) {
					// Check if filter is defined as service name
					if (substr($filter, 0, 1) != '@') {
						$filter = $builder->getDefinition($this->prefix('assetsloader.filters.content.'. $filter));
					}

					// Add filter to compiler
					$compiler->addSetup('addFilter', array($filter));
				}

				// Add files filters
				foreach ($assetConfig[$type]['filters']['files'] as $filter) {
					// Check if filter is defined as service name
					if (substr($filter, 0, 1) != '@') {
						$filter = $builder->getDefinition($this->prefix('assetsloader.filters.files.'. $filter));
					}

					// Add filter to compiler
					$compiler->addSetup('addFilter', array($filter));
				}
			}
		}

		// Register all assets
		foreach ($this->assets as $name => $assetConfig) {
			// Assets are splitted into types CSS/JS
			foreach ([self::TYPE_CSS, self::TYPE_JS] as $type) {
				// Create set name
				$assetConfig[$type]['name'] = $name .'.'. $type;

				// Register set to factory
				$factory->addSetup('registerAsset', [$assetConfig[$type], $this->prefix('@'. $type . ucfirst($name) . 'Compiler')]);
			}
		}
	}

	/**
	 * @param string $name
	 */
	private function loadConfig($name)
	{
		$this->compiler->parseServices(
			$this->getContainerBuilder(),
			$this->loadFromFile(__DIR__ . '/config/' . $name . '.neon'),
			$this->prefix($name)
		);
	}

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 */
	public static function register(Nette\Configurator $config, $extensionName = 'assetsLoader')
	{
		$config->onCompile[] = function (Nette\Configurator $config, Nette\DI\Compiler $compiler) use ($extensionName) {
			$compiler->addExtension($extensionName, new AssetsLoaderExtension());
		};
	}
}