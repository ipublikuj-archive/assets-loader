<?php
/**
 * Test: IPub\AssetsLoader\Compiler
 * @testCase
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     Tests
 * @since          1.0.0
 *
 * @date           27.01.15
 */

declare(strict_types = 1);

namespace IPubTests\AssetsLoader;

use Nette;
use Nette\Application;
use Nette\Application\UI;

use Tester;
use Tester\Assert;

use IPub\AssetsLoader;

require __DIR__ . '/../bootstrap.php';

class ComponentTest extends Tester\TestCase
{
	/**
	 * @var Application\IPresenterFactory
	 */
	private $presenterFactory;

	/**
	 * @var Nette\DI\Container
	 */
	private $container;

	/**
	 * Set up
	 */
	public function setUp() : void
	{
		parent::setUp();

		$this->container = $this->createContainer();

		// Get presenter factory from container
		$this->presenterFactory = $this->container->getByType('Nette\Application\IPresenterFactory');
	}

	public function testCssDefaultComponent() : void
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Create GET request
		$request = new Application\Request('Test', 'GET', ['action' => 'default']);
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

	public function testJsDefaultComponent() : void
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Create GET request
		$request = new Application\Request('Test', 'GET', ['action' => 'default']);
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

	public function testCssGetLinkComponent() : void
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Create GET request
		$request = new Application\Request('Test', 'GET', ['action' => 'cssLink']);
		// & fire presenter & catch response
		$response = $presenter->run($request);

		Assert::match('#^\/assets\/[0-9a-z]+\-t[0-9]+\.css$#i', (string) $response->getSource());
	}

	public function testJsGetLinkComponent() : void
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Create GET request
		$request = new Application\Request('Test', 'GET', ['action' => 'jsLink']);
		// & fire presenter & catch response
		$response = $presenter->run($request);

		Assert::match('#^\/assets\/[0-9a-z]+\-t[0-9]+\.js$#i', (string) $response->getSource());
	}

	/**
	 * @return Application\IPresenter
	 */
	protected function createPresenter() : Application\IPresenter
	{
		// Create test presenter
		$presenter = $this->presenterFactory->createPresenter('Test');
		// Disable auto canonicalize to prevent redirection
		$presenter->autoCanonicalize = FALSE;

		return $presenter;
	}

	/**
	 * @return Nette\DI\Container
	 */
	protected function createContainer() : Nette\DI\Container
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		$config->addParameters([
			"staticFilesDir" => realpath(__DIR__ . DIRECTORY_SEPARATOR . 'assets'),
		]);

		AssetsLoader\DI\AssetsLoaderExtension::register($config);

		$config->addConfig(__DIR__ . '/files/config.neon');
		$config->addConfig(__DIR__ . '/files/presenters.neon');

		return $config->createContainer();
	}
}

class TestPresenter extends UI\Presenter
{
	use AssetsLoader\TAssetsLoader;

	/**
	 * JS static files component
	 *
	 * @return AssetsLoader\Components\CssLoader
	 */
	protected function createComponentCss() : AssetsLoader\Components\CssLoader
	{
		return $this->assetsLoader->createCssLoader('default');
	}

	/**
	 * JS static files component
	 *
	 * @return AssetsLoader\Components\JsLoader
	 */
	protected function createComponentJs() : AssetsLoader\Components\JsLoader
	{
		return $this->assetsLoader->createJsLoader('default');
	}

	public function actionCssLink() : void
	{
		$this->sendResponse(new Application\Responses\TextResponse($this['css']->getLink()));
	}

	public function actionJsLink() : void
	{
		$this->sendResponse(new Application\Responses\TextResponse($this['js']->getLink()));
	}

	public function renderDefault() : void
	{
		// Set template for component testing
		$this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'default.latte');
	}
}

\run(new ComponentTest());
