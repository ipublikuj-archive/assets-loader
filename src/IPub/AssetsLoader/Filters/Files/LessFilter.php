<?php
/**
 * LessFilter.php
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

class LessFilter extends FilesFilter
{
	/**
	 * Less compiler instance
	 *
	 * @var \lessc|NULL
	 */
	protected $lc = NULL;

	public function __construct(\lessc $lc = NULL)
	{
		$this->lc = $lc;
	}

	/**
	 * Invoke filter
	 *
	 * @param string $code
	 * @param \IPub\AssetsLoader\Compilers\Compiler $loader
	 * @param string $file
	 *
	 * @return string
	 */
	public function __invoke($code, \IPub\AssetsLoader\Compilers\Compiler $loader, $file)
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
	protected function getLessC()
	{
		// Lazy loading
		if (!$this->lc) {
			$this->lc = new \lessc();
		}

		return $this->lc;
	}
}
