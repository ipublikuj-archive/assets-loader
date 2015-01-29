<?php
/**
 * Test: IPub\AssetsLoader\Files
 * @testCase
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Tests
 * @since		5.0
 *
 * @date		25.01.15
 */

namespace IPubTests\AssetsLoader;

use Nette;

use Tester;
use Tester\Assert;

use IPub;
use IPub\AssetsLoader;

require __DIR__ . '/../bootstrap.php';

class FilesTest extends Tester\TestCase
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

	public function testFiles()
	{
		$dic = $this->createContainer();

		// Get default asset
		$defaultCssAsset = $dic->getService('assetsLoader.factory')->getAsset('default' .'.'. AssetsLoader\DI\AssetsLoaderExtension::TYPE_CSS);
		// Get collection
		$filesCollection = $defaultCssAsset->getFiles();

		Assert::true(isset($filesCollection[__DIR__ . DIRECTORY_SEPARATOR . 'assets/first.css']));
		Assert::false(isset($filesCollection[__DIR__ . DIRECTORY_SEPARATOR . 'assets/wrong.css']));

		Assert::true($filesCollection[__DIR__ . DIRECTORY_SEPARATOR . 'assets/first.css'] instanceof AssetsLoader\Entities\IFile);
	}

	public function testAddFile()
	{
		$dic = $this->createContainer();

		// Get default asset
		$defaultCssAsset = $dic->getService('assetsLoader.factory')->getAsset('default' .'.'. AssetsLoader\DI\AssetsLoaderExtension::TYPE_CSS);
		// Get collection
		$filesCollection = $defaultCssAsset->getFiles();
		// Add testing file
		$defaultCssAsset->addFile(__DIR__ . DIRECTORY_SEPARATOR . 'assets/third.css');

		Assert::true(isset($filesCollection[__DIR__ . DIRECTORY_SEPARATOR . 'assets/third.css']));
	}

	/**
	 * @throws \IPub\AssetsLoader\Exceptions\FileNotFoundException
	 */
	public function testAddInvalidFile()
	{
		$dic = $this->createContainer();

		// Get default asset
		$defaultCssAsset = $dic->getService('assetsLoader.factory')->getAsset('default' .'.'. AssetsLoader\DI\AssetsLoaderExtension::TYPE_CSS);
		// Add testing file
		$defaultCssAsset->addFile(__DIR__ . DIRECTORY_SEPARATOR . 'assets/invalid.css');
	}

	public function testClearFiles()
	{
		$dic = $this->createContainer();

		// Get default asset
		$defaultCssAsset = $dic->getService('assetsLoader.factory')->getAsset('default' .'.'. AssetsLoader\DI\AssetsLoaderExtension::TYPE_CSS);
		// Get collection
		$filesCollection = $defaultCssAsset->getFiles();
		// Clear all files in collection
		$filesCollection->clear();

		Assert::equal([], $filesCollection->getFiles());
		Assert::equal([], $filesCollection->getRemoteFiles());
	}

	public function testRemoveFile()
	{
		$dic = $this->createContainer();

		// Get default asset
		$defaultCssAsset = $dic->getService('assetsLoader.factory')->getAsset('default' .'.'. AssetsLoader\DI\AssetsLoaderExtension::TYPE_CSS);
		// Get collection
		$filesCollection = $defaultCssAsset->getFiles();
		// Add testing file
		$defaultCssAsset->addFile(__DIR__ . DIRECTORY_SEPARATOR . 'assets/third.css');
		$defaultCssAsset->addFile(__DIR__ . DIRECTORY_SEPARATOR . 'assets/fourth.css');

		Assert::true(isset($filesCollection[__DIR__ . DIRECTORY_SEPARATOR . 'assets/third.css']));
		Assert::true(isset($filesCollection[__DIR__ . DIRECTORY_SEPARATOR . 'assets/fourth.css']));

		// Remove one file from asset collection
		$filesCollection->removeFile(__DIR__ . DIRECTORY_SEPARATOR . 'assets/fourth.css');

		Assert::true(isset($filesCollection[__DIR__ . DIRECTORY_SEPARATOR . 'assets/third.css']));
		Assert::false(isset($filesCollection[__DIR__ . DIRECTORY_SEPARATOR . 'assets/fourth.css']));

		// Remove one file from asset collection
		$filesCollection->removeFiles([__DIR__ . DIRECTORY_SEPARATOR . 'assets/third.css']);

		Assert::false(isset($filesCollection[__DIR__ . DIRECTORY_SEPARATOR . 'assets/third.css']));
		Assert::false(isset($filesCollection[__DIR__ . DIRECTORY_SEPARATOR . 'assets/fourth.css']));
	}

	public function testRemoteFiles()
	{
		$dic = $this->createContainer();

		// Get default asset
		$defaultCssAsset = $dic->getService('assetsLoader.factory')->getAsset('default' .'.'. AssetsLoader\DI\AssetsLoaderExtension::TYPE_JS);
		// Get collection
		$filesCollection = $defaultCssAsset->getFiles();

		// Expected collection based on neon configuration
		$expected = array(
			'http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js',
			'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js',
		);

		Assert::equal($expected, $filesCollection->getRemoteFiles());
	}

	public function testTraversableFiles()
	{
		$dic = $this->createContainer();

		// Get default asset
		$defaultCssAsset = $dic->getService('assetsLoader.factory')->getAsset('default' .'.'. AssetsLoader\DI\AssetsLoaderExtension::TYPE_CSS);
		// Get collection
		$filesCollection = $defaultCssAsset->getFiles();
		// Set new files
		$filesCollection->setFiles(new \ArrayIterator([
			__DIR__ . DIRECTORY_SEPARATOR . 'assets/third.css',
			'http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js'
		]));

		Assert::equal(1, count($filesCollection->getFiles()));
		Assert::equal(1, count($filesCollection->getRemoteFiles()));
	}

	public function testSplFileInfo()
	{
		$dic = $this->createContainer();

		// Get default asset
		$defaultCssAsset = $dic->getService('assetsLoader.factory')->getAsset('default' .'.'. AssetsLoader\DI\AssetsLoaderExtension::TYPE_CSS);
		// Get collection
		$filesCollection = $defaultCssAsset->getFiles();
		// Add new file
		$filesCollection->addFile(new \SplFileInfo(__DIR__ . DIRECTORY_SEPARATOR . 'assets/third.css'));

		Assert::true(isset($filesCollection[__DIR__ . DIRECTORY_SEPARATOR . 'assets/third.css']));
	}
}

\run(new FilesTest());