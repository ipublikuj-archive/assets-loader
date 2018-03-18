<?php
/**
 * CoffeeScriptFilter.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     Filters
 * @since          1.0.0
 *
 * @date           29.12.13
 */

declare(strict_types = 1);

namespace IPub\AssetsLoader\Filters\Files;

use IPub;
use IPub\AssetsLoader;
use IPub\AssetsLoader\Compilers;

/**
 * Coffee script filter
 *
 * @author  Patrik Votoček
 * @license MIT
 */
class CoffeeScriptFilter extends FilesFilter
{
	/**
	 * @var path to coffee bin
	 */
	private $bin;

	/**
	 * @var bool
	 */
	private $bare = FALSE;

	/**
	 * @param string
	 */
	public function __construct($bin = 'coffee')
	{
		$this->bin = $bin;
	}

	/**
	 * Invoke filter
	 *
	 * @param string $code
	 * @param Compilers\Compiler $compiler
	 * @param string $file
	 *
	 * @return string
	 */
	public function __invoke(string $code, Compilers\Compiler $compiler, string $file) : string
	{
		if (pathinfo($file, PATHINFO_EXTENSION) === 'coffee') {
			$code = $this->compileCoffee($code);
		}

		return $code;
	}

	/**
	 * Compile coffe script
	 *
	 * @param string
	 * @param bool|NULL
	 *
	 * @return string
	 */
	private function compileCoffee(string $source, ?bool $bare = NULL) : string
	{
		if ($bare === NULL) {
			$bare = $this->bare;
		}

		$cmd = $this->bin . ' -p -s' . ($bare ? ' -b' : '');

		return $this->run($cmd, $source);
	}
}
