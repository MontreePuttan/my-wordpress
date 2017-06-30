<?php
/**
 * Copyright (C) 2014-2015 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ServMaskDropboxCurl.php';

class ServMaskDropboxClient {

	const API_URL              = 'https://api.dropbox.com/1';

	const API_CONTENT_URL      = 'https://api-content.dropbox.com/1';

	const CHUNK_THRESHOLD_SIZE = 9863168; // 8 MB

	const CHUNK_SIZE           = 4194304; // 4 MB

	/**
	 * OAuth Access Token
	 *
	 * @var string
	 */
	protected $accessToken = null;

	/**
	 * SSL Mode
	 *
	 * @var boolean
	 */
	protected $ssl = null;

	/**
	 * Chunk stream
	 *
	 * @var resource
	 */
	protected $chunkStream = null;

	public function __construct($accessToken, $ssl = true) {
		$this->accessToken = $accessToken;
		$this->ssl = $ssl;
	}

	/**
	 * Upload file
	 *
	 * @param  string   $path       Dropbox file path.
	 * @param  resource $fileStream File stream.
	 * @param  int      $fileSize   File size.
	 * @return mixed
	 */
	public function uploadFile($path, $fileStream, $fileSize) {
		$api = new ServMaskDropboxCurl;
		$api->setAccessToken($this->accessToken);
		$api->setSSL($this->ssl);
		$api->setBaseURL(self::API_CONTENT_URL);
		$api->setPath("/files_put/auto/$path");
		$api->setOption(CURLOPT_PUT, true);
		$api->setOption(CURLOPT_INFILE, $fileStream);
		$api->setOption(CURLOPT_INFILESIZE, $fileSize);

		return $api->makeRequest();
	}

	/**
	 * Upload file chunk
	 *
	 * @param  string $chunk  File chunk data.
	 * @param  array  $params Dropbox query params.
	 * @return array
	 */
	public function uploadFileChunk($chunk, &$params = array()) {
		// Upload ID
		if (!isset($params['upload_id'])) {
			$params['upload_id'] = null;
		}

		$api = new ServMaskDropboxCurl;
		$api->setAccessToken($this->accessToken);
		$api->setSSL($this->ssl);
		$api->setBaseURL(self::API_CONTENT_URL);
		$api->setOption(CURLOPT_CUSTOMREQUEST, 'PUT');
		$api->setHeader('Content-Type', 'application/octet-stream');

		// Upload chunk
		$api->setOption(CURLOPT_POSTFIELDS, $chunk);
		$api->setPath('/chunked_upload?' . http_build_query(array(
			'upload_id' => $params['upload_id'],
			'offset'    => $params['offset'],
		)));

		// Make request
		$response = $api->makeRequest();

		// Set upload ID
		if (isset($response['upload_id'])) {
			$params['upload_id'] = $response['upload_id'];
		}

		// Set offset
		if (isset($response['offset'])) {
			$params['offset'] = $response['offset'];
		}

		return $response;
	}

	/**
	 * Commit upload file chunk
	 *
	 * @param  string $path   Dropbox file path.
	 * @param  array  $params Dropbox query params.
	 * @return mixed
	 */
	public function uploadFileChunkCommit($path, &$params = array()) {
		$api = new ServMaskDropboxCurl;
		$api->setAccessToken($this->accessToken);
		$api->setSSL($this->ssl);
		$api->setBaseURL(self::API_CONTENT_URL);
		$api->setPath("/commit_chunked_upload/auto/$path");
		$api->setOption(CURLOPT_POST, true);
		$api->setOption(CURLOPT_POSTFIELDS, array(
			'upload_id' => $params['upload_id'],
		));

		return $api->makeRequest();
	}

	/**
	 * Download file from Dropbox
	 *
	 * @param  string   $path       The path to the file on Dropbox (UTF-8).
	 * @param  resource $fileStream File stream.
	 * @param  array    $params     Dropbox query params.
	 * @return mixed
	 */
	public function getFile($path, $fileStream, &$params = array()) {
		$this->chunkStream = fopen('php://temp', 'wb+');

		$api = new ServMaskDropboxCurl;
		$api->setAccessToken($this->accessToken);
		$api->setSSL($this->ssl);
		$api->setBaseURL(self::API_CONTENT_URL);
		$api->setPath("/files/auto/$path");
		$api->setOption(CURLOPT_WRITEFUNCTION, array($this, 'curlWriteFunction'));
		$api->setHeader('Range', "bytes={$params['startBytes']}-{$params['endBytes']}");

		// Make request
		$api->makeRequest();

		// Copy chunk data into file stream
		if (fwrite($fileStream, stream_get_contents($this->chunkStream, -1, 0)) === false) {
			throw new Exception('Unable to save the file from Dropbox');
		}

		// Close chunk stream
		fclose($this->chunkStream);

		// Next startBytes
		if ($params['totalBytes'] < ($params['startBytes'] + self::CHUNK_SIZE)) {
			$params['startBytes'] = $params['totalBytes'];
		} else {
			$params['startBytes'] = $params['endBytes'] + 1;
		}

		// Next endBytes
		if ($params['totalBytes'] < ($params['endBytes'] + self::CHUNK_SIZE)) {
			$params['endBytes'] = $params['totalBytes'];
		} else {
			$params['endBytes'] += self::CHUNK_SIZE;
		}

		return $params;
	}

	/**
	 * Curl write function callback
	 *
	 * @param  resource $ch   Curl handler
	 * @param  string   $data Curl data
	 * @return integer
	 */
	public function curlWriteFunction($ch, $data) {
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($code !== 200 && $code !== 206) {
			throw new Exception(sprintf('Unable to connect to Dropbox. Error code: %d', $code), $code);
		}

		// Write data to stream
		fwrite($this->chunkStream, $data);

		return strlen($data);
	}

	/**
	 * Creates a folder
	 *
	 * @param  string $path The Dropbox path at which to create the folder (UTF-8).
	 * @return mixed
	 */
	public function createFolder($path) {
		$api = new ServMaskDropboxCurl;
		$api->setAccessToken($this->accessToken);
		$api->setSSL($this->ssl);
		$api->setBaseURL(self::API_URL);
		$api->setPath('/fileops/create_folder');
		$api->setOption(CURLOPT_POST, true);
		$api->setOption(CURLOPT_POSTFIELDS, array(
			'root' => 'auto',
			'path' => $path,
		));

		return $api->makeRequest();
	}

	/**
	 * Retrieves file and folder metadata
	 *
	 * @param  string $path The Dropbox path at which to create the folder (UTF-8).
	 * @return mixed
	 */
	public function metadata($path) {
		$api = new ServMaskDropboxCurl;
		$api->setAccessToken($this->accessToken);
		$api->setSSL($this->ssl);
		$api->setBaseURL(self::API_URL);
		$api->setPath("/metadata/auto/$path");

		return $api->makeRequest();
	}

	/**
	 * Deletes a file or folder
	 *
	 * @param  string $path The Dropbox path of the file or folder to delete (UTF-8).
	 * @return mixed
	 */
	public function delete($path) {
		$api = new ServMaskDropboxCurl;
		$api->setAccessToken($this->accessToken);
		$api->setSSL($this->ssl);
		$api->setBaseURL(self::API_URL);
		$api->setPath('/fileops/delete');
		$api->setOption(CURLOPT_POST, true);
		$api->setOption(CURLOPT_POSTFIELDS, array(
			'root' => 'auto',
			'path' => $path,
		));

		return $api->makeRequest();
	}

	/**
	 * Get account info
	 *
	 * @return mixed
	 */
	public function getAccountInfo() {
		$api = new ServMaskDropboxCurl;
		$api->setAccessToken($this->accessToken);
		$api->setSSL($this->ssl);
		$api->setBaseURL(self::API_URL);
		$api->setPath('/account/info');

		return $api->makeRequest();
	}

	/**
	 * Revoke token
	 *
	 * @return mixed
	 */
	public function revoke() {
		$api = new ServMaskDropboxCurl;
		$api->setAccessToken($this->accessToken);
		$api->setSSL($this->ssl);
		$api->setBaseURL(self::API_URL);
		$api->setPath('/disable_access_token');
		$api->setOption(CURLOPT_POST, true);

		return $api->makeRequest();
	}
}
