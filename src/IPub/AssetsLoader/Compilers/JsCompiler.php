<?php
/**
 * JsCompiler.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:AssetsLoader!
 * @subpackage     Compilers
 * @since          1.0.0
 *
 * @date           29.12.13
 */

declare(strict_types = 1);

namespace IPub\AssetsLoader\Compilers;

class JsCompiler extends Compiler
{
	/**
	 * @var string
	 */
	protected $type = self::TYPE_JS;

	/**
	 * Compressed file name
	 *
	 * @var string
	 */
	protected $filename = 'js-%s.js';
}
