<?php
/**
 * Test: IPub\AssetsLoader\Extension
 * @testCase
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Tests
 * @since		5.0
 *
 * @date		22.01.15
 */

namespace IPubTests\AssetsLoader;

use Nette;

use Tester;
use Tester\Assert;

use IPub;
use IPub\AssetsLoader;

require __DIR__ . '/../bootstrap.php';

class ExtensionTest extends Tester\TestCase
{
	/**
	 * @return \SystemContainer|\Nette\DI\Container
	 */
	protected function createContainer()
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		$config->addParameters([
			"staticFilesDir" => realpath(__DIR__ . DIRECTORY_SEPARATOR .'assets'),
		]);

		AssetsLoader\DI\AssetsLoaderExtension::register($config);

		$config->addConfig(__DIR__ . '/files/config.neon', $config::NONE);

		return $config->createContainer();
	}

	public function testCompilersServices()
	{
		$dic = $this->createContainer();

		Assert::true($dic->getService('assetsLoader.cssDefaultCompiler') instanceof IPub\AssetsLoader\Compilers\CssCompiler);
		Assert::true($dic->getService('assetsLoader.jsDefaultCompiler') instanceof IPub\AssetsLoader\Compilers\JsCompiler);
	}

	public function testFactoryService()
	{
		$dic = $this->createContainer();

		Assert::true($dic->getService('assetsLoader.factory') instanceof IPub\AssetsLoader\LoaderFactory);
	}

	public function testConfiguration()
	{
		$dic = $this->createContainer();

		Assert::true($dic->getService('assetsLoader.factory')->getAsset('default' .'.'. AssetsLoader\DI\AssetsLoaderExtension::TYPE_CSS)->getJoinFiles());
		Assert::false($dic->getService('assetsLoader.factory')->getAsset('first' .'.'. AssetsLoader\DI\AssetsLoaderExtension::TYPE_CSS)->getJoinFiles());
	}
}

\run(new ExtensionTest());