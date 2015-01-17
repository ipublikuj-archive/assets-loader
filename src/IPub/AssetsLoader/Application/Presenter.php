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
	 * @var Caching\Cache
	 */
	private $cache;

	/**
	 * @param Http\IRequest $httpRequest
	 * @param Application\IRouter $router
	 * @param Caching\Cache $cache
	 */
	public function __construct(
		Http\IRequest $httpRequest = NULL,
		Application\IRouter $router = NULL,
		Caching\Cache $cache
	) {
		$this->httpRequest	= $httpRequest;
		$this->router		= $router;
		$this->cache		= $cache;
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

		if (!isset($params['id'])) {
			throw new Application\BadRequestException('Parameter id is missing.');
		}

		if (NULL === ($item = $this->cache->getItem(Utils\Strings::webalize($params['id'])))) {
			return new Application\Responses\TextResponse('');
		}

		return new AssetsLoader\Application\Response($item[Caching\Cache::CONTENT], $item[Caching\Cache::CONTENT_TYPE], $item[Caching\Cache::ETAG]);
	}
}