<?php
/**
 * AssetResponse.php
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

use Nette;
use Nette\Application;
use Nette\Http;

class AssetResponse implements Application\IResponse
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

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
	public function __construct(string $content, string $contentType, ?string $eTag = NULL)
	{
		$this->content = $content;
		$this->contentType = $contentType;
		$this->etag = $eTag;
	}

	/**
	 * @return string
	 */
	final public function getContent() : string
	{
		return $this->content;
	}

	/**
	 * @return string
	 */
	final public function getContentType() : string
	{
		return $this->contentType;
	}

	/**
	 * @return string|NULL
	 */
	final public function getEtag() : ?string
	{
		return $this->etag;
	}

	/**
	 * Sends response to output.
	 *
	 * @param Http\IRequest $httpRequest
	 * @param Http\IResponse $httpResponse
	 *
	 * @return void
	 */
	public function send(Http\IRequest $httpRequest, Http\IResponse $httpResponse) : void
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
