<?php
/**
 * Test: IPub\AssetsLoader\Compiler
 * @testCase
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Tests
 * @since		5.0
 *
 * @date		27.01.15
 */

namespace IPubTests\AssetsLoader;

use Nette;
use Nette\Application;
use Nette\Application\UI;

use Tester;
use Tester\Assert;

use IPub;
use IPub\AssetsLoader;

require __DIR__ . '/../bootstrap.php';

class ComponentTest extends Tester\TestCase
{
	/**
	 * @var Nette\Application\IPresenterFactory
	 */
	private $presenterFactory;

	/**
	 * @var \SystemContainer|\Nette\DI\Container
	 */
	private $container;

	/**
	 * Set up
	 */
	public function setUp()
	{
		parent::setUp();

		$this->container = $this->createContainer();

		// Get presenter factory from container
		$this->presenterFactory = $this->container->getByType('Nette\Application\IPresenterFactory');
	}

	public function testCssDefaultComponent()
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Create GET request
		$request = new Application\Request('Test', 'GET', array('action' => 'default'));
		// & fire presenter & catch response
		$response = $presenter->run($request);

		Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Assert::true($response->getSource() instanceof Nette\Application\UI\ITemplate);

		$dq = Tester\DomQuery::fromHtml((string) $response->getSource());

		Assert::true($dq->has('link[rel="stylesheet"][media="all"]'));

		// Get all styles element
		$styleElements = $dq->find('link[rel="stylesheet"][media="all"]');

		Assert::match('#^\/assets\/[0-9a-z]+\-t[0-9]+\.css$#i', (string) $styleElements[0]->attributes()->{'href'});
	}

	public function testJsDefaultComponent()
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Create GET request
		$request = new Application\Request('Test', 'GET', array('action' => 'default'));
		// & fire presenter & catch response
		$response = $presenter->run($request);

		Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Assert::true($response->getSource() instanceof Nette\Application\UI\ITemplate);

		$dq = Tester\DomQuery::fromHtml((string) $response->getSource());

		Assert::true($dq->has('script[type="text/javascript"]'));

		// Get all styles element
		$scriptElements = $dq->find('script[type="text/javascript"]');

		Assert::match('#^\/assets\/[0-9a-z]+\-t[0-9]+\.js$#i', (string) $scriptElements[2]->attributes()->{'src'});
	}

	public function testCssGetLinkComponent()
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Create GET request
		$request = new Application\Request('Test', 'GET', array('action' => 'cssLink'));
		// & fire presenter & catch response
		$response = $presenter->run($request);

		Assert::match('#^\/assets\/[0-9a-z]+\-t[0-9]+\.css$#i', (string) $response->getSource());
	}

	public function testJsGetLinkComponent()
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Create GET request
		$request = new Application\Request('Test', 'GET', array('action' => 'jsLink'));
		// & fire presenter & catch response
		$response = $presenter->run($request);

		Assert::match('#^\/assets\/[0-9a-z]+\-t[0-9]+\.js$#i', (string) $response->getSource());
	}

	/**
	 * @return Application\IPresenter
	 */
	protected function createPresenter()
	{
		// Create test presenter
		$presenter = $this->presenterFactory->createPresenter('Test');
		// Disable auto canonicalize to prevent redirection
		$presenter->autoCanonicalize = FALSE;

		return $presenter;
	}

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
		$config->addConfig(__DIR__ . '/files/presenters.neon', $config::NONE);

		return $config->createContainer();
	}
}

class TestPresenter extends UI\Presenter
{
	use AssetsLoader\TAssetsLoader;

	/**
	 * JS static files component
	 *
	 * @return \IPub\AssetsLoader\Components\CssLoader
	 */
	protected function createComponentCss()
	{
		return $this->assetsLoader->createCssLoader('default');
	}

	/**
	 * JS static files component
	 *
	 * @return \IPub\AssetsLoader\Components\JsLoader
	 */
	protected function createComponentJs()
	{
		return $this->assetsLoader->createJsLoader('default');
	}

	public function actionCssLink()
	{
		$this->sendResponse(new Application\Responses\TextResponse($this['css']->getLink()));
	}

	public function actionJsLink()
	{
		$this->sendResponse(new Application\Responses\TextResponse($this['js']->getLink()));
	}

	public function renderDefault()
	{
		// Set template for component testing
		$this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR .'templates'. DIRECTORY_SEPARATOR .'default.latte');
	}
}

\run(new ComponentTest());