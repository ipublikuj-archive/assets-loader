<?php
/**
 * Presenter.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Application
 * @since		5.0
 *
 * @date		15.01.15
 */

namespace IPub\IPubModule;

use Nette;
use Nette\Application;
use Nette\Http;
use Nette\Utils;

use IPub;
use IPub\AssetsLoader;
use IPub\AssetsLoader\Caching;

class AssetsLoaderPresenter extends Nette\Object implements Application\IPresenter
{
	/**
	 * @var Http\IRequest
	 */
	private $httpRequest;

	/**
	 * @var Application\IRouter
	 */
	private $router;

	/**
	 * @var Application\Request
	 */
	private $request;

	/**
	 * @var Caching\AssetCache
	 */
	private $assetCache;

	/**
	 * @var Caching\FileCache
	 */
	private $fileCache;

	/**
	 * @param Http\IRequest $httpRequest
	 * @param Application\IRouter $router
	 * @param Caching\AssetCache $assetCache
	 * @param Caching\FileCache $cache
	 */
	public function __construct(
		Http\IRequest $httpRequest = NULL,
		Application\IRouter $router = NULL,
		Caching\AssetCache $assetCache,
		Caching\FileCache $fileCache
	) {
		$this->httpRequest	= $httpRequest;
		$this->router		= $router;
		$this->assetCache	= $assetCache;
		$this->fileCache	= $fileCache;
	}

	/**
	 * @param $id
	 *
	 * @return AssetsLoader\Application\AssetResponse|Application\Responses\TextResponse
	 */
	public function actionAssets($id)
	{
		if (NULL === ($item = $this->assetCache->getItem(Utils\Strings::webalize($id)))) {
			return new Application\Responses\TextResponse('');
		}

		return new AssetsLoader\Application\AssetResponse($item[Caching\AssetCache::CONTENT], $item[Caching\AssetCache::CONTENT_TYPE], $item[Caching\AssetCache::ETAG]);
	}

	/**
	 * @param $id
	 *
	 * @return AssetsLoader\Application\FileResponse|Application\Responses\TextResponse
	 */
	public function actionFiles($id)
	{
		if (NULL === ($item = $this->fileCache->getItem(Utils\Strings::webalize($id)))) {
			return new Application\Responses\TextResponse('');
		}

		return new AssetsLoader\Application\FileResponse($item[Caching\FileCache::CONTENT]);
	}

	/**
	 * @param Application\Request $request
	 *
	 * @return Application\IResponse
	 *
	 * @throws Application\BadRequestException
	 */
	public function run(Application\Request $request)
	{
		$this->request = $request;

		if ($this->httpRequest && $this->router && !$this->httpRequest->isAjax() && ($request->isMethod('get') || $request->isMethod('head'))) {
			$refUrl = clone $this->httpRequest->getUrl();

			$url = $this->router->constructUrl($request, $refUrl->setPath($refUrl->getScriptPath()));

			if ($url !== NULL && !$this->httpRequest->getUrl()->isEqual($url)) {
				return new Application\Responses\RedirectResponse($url, Http\IResponse::S301_MOVED_PERMANENTLY);
			}
		}

		$params = $request->getParameters();

		if (!isset($params['action'])) {
			throw new Application\BadRequestException('Parameter action is missing.');
		}

		if (!isset($params['id'])) {
			throw new Application\BadRequestException('Parameter id is missing.');
		}

		// calls $this->action<Action>()
		if (!$response = $this->tryCall(Application\UI\Presenter::formatActionMethod(Utils\Strings::capitalize($params['action'])), $params)) {
			throw new Application\BadRequestException('Action not callable.');
		}

		return $response;
	}

	/**
	 * Calls public method if exists
	 *
	 * @param  string
	 * @param  array
	 *
	 * @return bool  does method exist?
	 */
	protected function tryCall($method, array $params)
	{
		$rc = $this->getReflection();

		if ($rc->hasMethod($method)) {
			$rm = $rc->getMethod($method);

			if ($rm->isPublic() && !$rm->isAbstract() && !$rm->isStatic()) {
				return $rm->invokeArgs($this, Application\UI\PresenterComponentReflection::combineArgs($rm, $params));
			}
		}

		return FALSE;
	}
}