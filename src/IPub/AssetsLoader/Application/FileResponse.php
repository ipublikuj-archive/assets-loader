<?php
/**
 * FileResponse.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:AssetsLoader!
 * @subpackage	Application
 * @since		5.0
 *
 * @date		22.01.15
 */

namespace IPub\AssetsLoader\Application;

use Nette;
use Nette\Application;
use Nette\Http;
use Nette\Utils;

use IPub;
use IPub\AssetsLoader;

class FileResponse extends Nette\Object implements Application\IResponse
{
	/**
	 * @var string
	 */
	private $filePath;

	/**
	 * @param string $filePath
	 */
	public function __construct($filePath)
	{
		$this->filePath = $filePath;
	}

	/**
	 * @return string
	 */
	final public function getFilePath()
	{
		return $this->filePath;
	}

	/**
	 * Sends response to output.
	 *
	 * @param Http\IRequest $httpRequest
	 * @param Http\IResponse $httpResponse
	 */
	public function send(Http\IRequest $httpRequest, Http\IResponse $httpResponse)
	{
		$httpResponse->setExpiration(Http\IResponse::PERMANENT);

		if (($inm = $httpRequest->getHeader('if-none-match')) && $inm == $this->etag) {
			$httpResponse->setCode(Http\IResponse::S304_NOT_MODIFIED);

			return;
		}

		$httpResponse->setContentType(AssetsLoader\Files\MimeMapper::getMimeFromFilename($this->filePath));
		$httpResponse->setHeader('Content-Transfer-Encoding', 'binary');
		$httpResponse->setHeader('Content-Length', filesize($this->filePath));
		$httpResponse->setHeader('Content-Disposition', 'attachment; filename="'. basename($this->filePath) .'"');

		$httpResponse->setHeader('Access-Control-Allow-Origin', '*');

		// Read the file
		readfile($this->filePath);
	}
}