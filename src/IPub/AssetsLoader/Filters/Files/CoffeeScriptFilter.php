<?php
/**
 * CoffeeScriptFilter.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Filters
 * @since		5.0
 *
 * @date		29.12.13
 */

namespace IPub\AssetsLoader\Filters\Files;

use IPub;
use IPub\AssetsLoader;
use IPub\AssetsLoader\Compilers;

/**
 * Coffee script filter
 *
 * @author Patrik Votoček
 * @license MIT
 */
class CoffeeScriptFilter extends FilesFilter
{
	/**
	 * @var path to coffee bin
	 */
	protected $bin;

	/**
	 * @var bool
	 */
	protected $bare = FALSE;

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
	public function __invoke($code, Compilers\Compiler $compiler, $file)
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
	protected function compileCoffee($source, $bare = NULL)
	{
		if (is_NULL($bare)) {
			$bare = $this->bare;
		}

		$cmd = $this->bin . ' -p -s' . ($bare ? ' -b' : '');

		return $this->run($cmd, $source);
	}
}