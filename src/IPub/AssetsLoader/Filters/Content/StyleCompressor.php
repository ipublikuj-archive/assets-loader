<?php
/**
 * StyleCompressor.php
 *
 * Stylesheet compressor helper class, minifies css
 * Based on Minify_CSS_Compressor (http://code.google.com/p/minify/, Stephen Clay <steve@mrclay.org>, New BSD License)
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Filters
 * @since		5.0
 *
 * @date		08.06.13
 */

namespace IPub\AssetsLoader\Filters\Content;

use IPub;
use IPub\AssetsLoader;
use IPub\AssetsLoader\Filters;

class StyleCompressor implements IContentFilter, Filters\IFilter
{
	/**
	 * @var bool Are we "in" a hack?
	 *
	 * I.e. are some browsers targetted until the next comment?
	 */
	protected $_inHack = FALSE;

	/**
	 * Minify a CSS string
	 *
	 * @param string $code
	 * @param \IPub\AssetsLoader\Compilers\Compiler $loader
	 *
	 * @return string
	 */
	public function __invoke($code, \IPub\AssetsLoader\Compilers\Compiler $loader)
	{
		$code = str_replace("\r\n", "\n", $code);

		// preserve empty comment after '>'
		// http://www.webdevout.net/css-hacks#in_css-selectors
		$code = preg_replace('@>/\\*\\s*\\*/@', '>/*keep*/', $code);

		// preserve empty comment between property and value
		// http://css-discuss.incutio.com/?page=BoxModelHack
		$code = preg_replace('@/\\*\\s*\\*/\\s*:@', '/*keep*/:', $code);
		$code = preg_replace('@:\\s*/\\*\\s*\\*/@', ':/*keep*/', $code);

		// apply callback to all valid comments (and strip out surrounding ws
		$code = preg_replace_callback('@\\s*/\\*([\\s\\S]*?)\\*/\\s*@', array($this, '_commentCB'), $code);

		// remove ws around { } and last semicolon in declaration block
		$code = preg_replace('/\\s*{\\s*/', '{', $code);
		$code = preg_replace('/;?\\s*}\\s*/', '}', $code);

		// remove ws surrounding semicolons
		$code = preg_replace('/\\s*;\\s*/', ';', $code);

		// remove ws around urls
		$code = preg_replace('/
				url\\(      # url(
				\\s*
				([^\\)]+?)  # 1 = the URL (really just a bunch of non right parenthesis)
				\\s*
				\\)         # )
			/x', 'url($1)', $code);

		// remove ws between rules and colons
		$code = preg_replace('/
				\\s*
				([{;])              # 1 = beginning of block or rule separator
				\\s*
				([\\*_]?[\\w\\-]+)  # 2 = property (and maybe IE filter)
				\\s*
				:
				\\s*
				(\\b|[#\'"-])       # 3 = first character of a value
			/x', '$1$2:$3', $code);

		// remove ws in selectors
		$code = preg_replace_callback('/
				(?:              # non-capture
					\\s*
					[^~>+,\\s]+  # selector part
					\\s*
					[,>+~]       # combinators
				)+
				\\s*
				[^~>+,\\s]+      # selector part
				{                # open declaration block
			/x'
			,array($this, '_selectorsCB'), $code);

		// minimize hex colors
		$code = preg_replace('/([^=])#([a-f\\d])\\2([a-f\\d])\\3([a-f\\d])\\4([\\s;\\}])/i', '$1#$2$3$4$5', $code);

		// remove spaces between font families
		$code = preg_replace_callback('/font-family:([^;}]+)([;}])/', array($this, '_fontFamilyCB'), $code);

		$code = preg_replace('/@import\\s+url/', '@import url', $code);

		// replace any ws involving newlines with a single newline
		$code = preg_replace('/[ \\t]*\\n+\\s*/', "\n", $code);

		// separate common descendent selectors w/ newlines (to limit line lengths)
		$code = preg_replace('/([\\w#\\.\\*]+)\\s+([\\w#\\.\\*]+){/', "$1\n$2{", $code);

		// Use newline after 1st numeric value (to limit line lengths).
		$code = preg_replace('/
			((?:padding|margin|border|outline):\\d+(?:px|em)?) # 1 = prop : 1st numeric value
			\\s+
			/x'
			,"$1\n", $code);

		// prevent triggering IE6 bug: http://www.crankygeek.com/ie6pebug/
		$code = preg_replace('/:first-l(etter|ine)\\{/', ':first-l$1 {', $code);

		return trim($code);
	}

	/**
	 * Replace what looks like a set of selectors
	 *
	 * @param array $m regex matches
	 *
	 * @return string
	 */
	protected function _selectorsCB($m)
	{
		// remove ws around the combinators
		return preg_replace('/\\s*([,>+~])\\s*/', '$1', $m[0]);
	}

	/**
	 * Process a comment and return a replacement
	 *
	 * @param array $m regex matches
	 *
	 * @return string
	 */
	protected function _commentCB($m)
	{
		$hasSurroundingWs = (trim($m[0]) !== $m[1]);
		$m = $m[1];

		// $m is the comment content w/o the surrounding tokens,
		// but the return value will replace the entire comment.
		if ($m === 'keep') {
			return '/**/';
		}

		if ($m === '" "') {
			// component of http://tantek.com/CSS/Examples/midpass.html
			return '/*" "*/';
		}

		if (preg_match('@";\\}\\s*\\}/\\*\\s+@', $m)) {
			// component of http://tantek.com/CSS/Examples/midpass.html
			return '/*";}}/* */';
		}

		if ($this->_inHack) {
			// inversion: feeding only to one browser
			if (preg_match('@
					^/               # comment started like /*/
					\\s*
					(\\S[\\s\\S]+?)  # has at least some non-ws content
					\\s*
					/\\*             # ends like /*/ or /**/
				@x', $m, $n)) {
				// end hack mode after this comment, but preserve the hack and comment content
				$this->_inHack = FALSE;
				return "/*/{$n[1]}/**/";
			}
		}

		if (substr($m, -1) === '\\') { // comment ends like \*/
			// begin hack mode and preserve hack
			$this->_inHack = TRUE;
			return '/*\\*/';
		}

		if ($m !== '' && $m[0] === '/') { // comment looks like /*/ foo */
			// begin hack mode and preserve hack
			$this->_inHack = TRUE;
			return '/*/*/';
		}

		if ($this->_inHack) {
			// a regular comment ends hack mode but should be preserved
			$this->_inHack = FALSE;
			return '/**/';
		}

		// Issue 107: if there's any surrounding whitespace, it may be important, so
		// replace the comment with a single space
		return $hasSurroundingWs // remove all other comments
			? ' '
			: '';
	}

	/**
	 * Process a font-family listing and return a replacement
	 *
	 * @param array $m regex matches
	 *
	 * @return string
	 */
	protected function _fontFamilyCB($m)
	{
		$m[1] = preg_replace('/
				\\s*
				(
					"[^"]+"      # 1 = family in double qutoes
					|\'[^\']+\'  # or 1 = family in single quotes
					|[\\w\\-]+   # or 1 = unquoted family
				)
				\\s*
			/x', '$1', $m[1]);

		return 'font-family:' . $m[1] . $m[2];
	}
}