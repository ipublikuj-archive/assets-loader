<?php
/**
 * Response.php
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
use Nette\Http;
use Nette\Utils;

use IPub;
use IPub\AssetsLoader;

class Response extends Nette\Object implements Application\IResponse
{
	/**
	 * @var string
	 */
	private $content;

	/**
	 * @var string
	 */
	private $contentType;

	/**
	 * @var string
	 */
	private $etag;

	/**
	 * @param string $content
	 * @param string $contentType
	 * @param string $eTag
	 */
	public function __construct($content, $contentType, $eTag = NULL)
	{
		$this->content		= $content;
		$this->contentType	= $contentType;
		$this->etag			= $eTag;
	}

	/**
	 * @return string
	 */
	final public function getContent()
	{
		return $this->content;
	}

	/**
	 * @return string
	 */
	final public function getContentType()
	{
		return $this->contentType;
	}

	/**
	 * @return string
	 */
	final public function getEtag()
	{
		return $this->etag;
	}

	/**
	 * Sends response to output.
	 *
	 * @param Http\IRequest $httpRequest
	 * @param Http\IResponse $httpResponse
	 */
	public function send(Http\IRequest $httpRequest, Http\IResponse $httpResponse)
	{
		if (strlen($this->etag)) {
			$httpResponse->setHeader('Etag', $this->etag);
		}

		$httpResponse->setExpiration(Http\IResponse::PERMANENT);

		if (($inm = $httpRequest->getHeader('if-none-match')) && $inm == $this->etag) {
			$httpResponse->setCode(Http\IResponse::S304_NOT_MODIFIED);

			return;
		}

		$httpResponse->setContentType($this->contentType);

		echo $this->content;
	}
}