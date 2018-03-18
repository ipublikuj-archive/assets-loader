<?php
/**
 * LessFilter.php
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

use IPub\AssetsLoader\Compilers;

/**
 * Less CSS filter
 *
 * @author  Jan Marek
 * @license MIT
 */
class LessFilter extends FilesFilter
{
	/**
	 * Less compiler instance
	 *
	 * @var \lessc|NULL
	 */
	private $lc = NULL;

	/**
	 * @param \lessc|NULL $lc
	 */
	public function __construct(\lessc $lc = NULL)
	{
		$this->lc = $lc;
	}

	/**
	 * Invoke filter
	 *
	 * @param string $code
	 * @param Compilers\Compiler $compiler
	 * @param string $file
	 *
	 * @return string
	 *
	 * @throws \exception
	 */
	public function __invoke(string $code, Compilers\Compiler $compiler, string $file) : string
	{
		if (pathinfo($file, PATHINFO_EXTENSION) === 'less') {
			$this->getLessC()->importDir = pathinfo($file, PATHINFO_DIRNAME) . '/';

			return $this->getLessC()->parse($code);
		}

		return $code;
	}

	/**
	 * Get less compiler
	 *
	 * @return \lessc
	 */
	private function getLessC() : \lessc
	{
		// Lazy loading
		if (!$this->lc) {
			$this->lc = new \lessc();
		}

		return $this->lc;
	}
}
