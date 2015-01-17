<?php
/**
 * JsCompiler.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Compilers
 * @since		5.0
 *
 * @date		29.12.13
 */

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