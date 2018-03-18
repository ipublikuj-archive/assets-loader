<?php
/**
 * Route.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     Application
 * @since          1.0.0
 *
 * @date           15.01.15
 */

declare(strict_types = 1);

namespace IPub\AssetsLoader\Application;

use Nette\Application;
use Nette\Utils;

class Route extends Application\Routers\Route
{
	/**
	 * @param Application\IRouter $router
	 * @param Route $extensionRoute
	 *
	 * @return void
	 *
	 * @throws Utils\AssertionException
	 */
	public static function prependTo(Application\IRouter &$router, self $extensionRoute) : void
	{
		if (!$router instanceof Application\Routers\RouteList) {
			throw new Utils\AssertionException(
				'If you want to use IPub\AssetsLoader then your main router ' .
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

			$router[$i + 1] = $route;
		}

		$router[0] = $extensionRoute;
	}
}
