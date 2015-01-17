<?php
/**
 * Route.php
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

namespace IPub\AssetsLoader\Application;

use Nette;
use Nette\Application;
use Nette\Utils;

use IPub;
use IPub\AssetsLoader;

class Route extends Application\Routers\Route
{
	/**
	 * @param Application\IRouter $router
	 * @param Route $extensionRoute
	 *
	 * @throws Utils\AssertionException
	 */
	public static function prependTo(Application\IRouter &$router, self $extensionRoute)
	{
		if (!$router instanceof Application\Routers\RouteList) {
			throw new Utils\AssertionException(
				'If you want to use IPub\AssetsLoader then your main router '.
				'must be an instance of Nette\Application\Routers\RouteList'
			);
		}

		// Add extension route to router
		$router[] = $extensionRoute;

		$lastKey = count($router) - 1;

		foreach ($router as $i => $route) {
			if ($i === $lastKey) {
				break;
			}

			$router[$i+1] = $route;
		}

		$router[0] = $extensionRoute;
	}
}