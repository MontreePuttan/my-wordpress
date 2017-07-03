<?php
/**
 * Copyright (C) 2014-2017 ServMask Inc.
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

class ServMaskDropboxClient {

	const API_URL              = 'https://api.dropboxapi.com/2';

	const API_CONTENT_URL      = 'https://content.dropboxapi.com/2';

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
	 * @param  string $path   Dropbox file path.
	 * @param  string $file   File data.
	 * @param  array  $params Dropbox query params.
	 * @param  object $adapter Dropbox HTTP client.
	 * @return mixed
	 */
	public function uploadFile($path, $file, &$params = array(), $adapter = null) {
        if (is_null($adapter)) {
			$adapter = new ServMaskDropboxCurl;
		}

		$adapter->setAccessToken($this->accessToken);
		$adapter->setSSL($this->ssl);
		$adapter->setBaseURL(self::API_CONTENT_URL);
		$adapter->setOption(CURLOPT_POST, true);
		$adapter->setOption(CURLOPT_POSTFIELDS, $file);
		$adapter->setPath('/files/upload');
		$adapter->setHeader('Content-Type', 'application/octet-stream');
		$adapter->setHeader('Dropbox-API-Arg', json_encode(array(
			'path'       =>  $path,
			'mode'       =>  'add',
			'autorename' =>  true,
			'mute'       =>  false,
		)));

		return $adapter->makeRequest();
	}

	/**
	 * Upload first file chunk
	 *
	 * @param  string $chunk  File chunk data.
	 * @param  array  $params Dropbox query params.
	 * @param  object $adapter Dropbox HTTP client.
	 * @return array
	 */
	public function uploadFirstFileChunk($chunk, &$params = array(), $adapter = null) {
		if (is_null($adapter)) {
			$adapter = new ServMaskDropboxCurl;
		}

		$adapter->setAccessToken($this->accessToken);
		$adapter->setSSL($this->ssl);
		$adapter->setBaseURL(self::API_CONTENT_URL);
		$adapter->setOption(CURLOPT_POST, true);
		$adapter->setOption(CURLOPT_POSTFIELDS, $chunk);
		$adapter->setPath('/files/upload_session/start');
		$adapter->setHeader('Content-Type', 'application/octet-stream');
		$adapter->setHeader('Dropbox-API-Arg', json_encode(array(
			'close' => false,
		)));

		// Make request
		$response = $adapter->makeRequest();

		// Set upload ID
		if (isset($response['session_id'])) {
			$params['upload_id'] = $response['session_id'];
		}

		return $response;
	}

	/**
	 * Upload next file chunk
	 *
	 * @param  string $chunk  File chunk data.
	 * @param  array  $params Dropbox query params.
	 * @param  object $adapter Dropbox HTTP client.
	 * @return array
	 */
	public function uploadNextFileChunk($chunk, &$params = array(), $adapter = null) {
		if (is_null($adapter)) {
			$adapter = new ServMaskDropboxCurl;
		}

		$adapter->setAccessToken($this->accessToken);
		$adapter->setSSL($this->ssl);
		$adapter->setBaseURL(self::API_CONTENT_URL);
		$adapter->setOption(CURLOPT_POST, true);
		$adapter->setOption(CURLOPT_POSTFIELDS, $chunk);
		$adapter->setPath('/files/upload_session/append_v2');
		$adapter->setHeader('Content-Type', 'application/octet-stream');
		$adapter->setHeader('Dropbox-API-Arg', json_encode(array(
			'cursor' =>  array(
				'session_id' => $params['upload_id'],
				'offset'     => (int) $params['offset'],
			),
			'close'  =>  false,
		)));

		// Make request
		$response = $adapter->makeRequest();

		// Set upload ID
		if (isset($response['upload_id'])) {
			$params['upload_id'] = $response['upload_id'];
		}

		return $response;
	}

	/**
	 * Commit upload file chunk
	 *
	 * @param  string $path   Dropbox file path.
	 * @param  string $chunk  File chunk data.
	 * @param  array  $params Dropbox query params.
	 * @param  object $adapter Dropbox HTTP client.
	 * @return mixed
	 */
	public function uploadFileChunkCommit($path, $chunk, &$params = array(), $adapter = null) {
		if (is_null($adapter)) {
			$adapter = new ServMaskDropboxCurl;
		}

		$adapter->setAccessToken($this->accessToken);
		$adapter->setSSL($this->ssl);
		$adapter->setBaseURL(self::API_CONTENT_URL);
		$adapter->setOption(CURLOPT_POST, true);
		$adapter->setOption(CURLOPT_POSTFIELDS, $chunk);
		$adapter->setPath('/files/upload_session/finish');
		$adapter->setHeader('Content-Type', 'application/octet-stream');
		$adapter->setHeader('Dropbox-API-Arg', json_encode(array(
			'cursor' =>  array(
				'session_id' => $params['upload_id'],
				'offset'     => (int) $params['offset'],
			),
			'commit' =>  array(
				'path'       => $path,
				'mode'       => 'add',
				'autorename' => true,
				'mute'       => false,
			),
		)));

		return $adapter->makeRequest();
	}

	/**
	 * Download file from Dropbox
	 *
	 * @param  string   $path       The path to the file on Dropbox (UTF-8).
	 * @param  resource $fileStream File stream.
	 * @param  array    $params     Dropbox query params.
	 * @param  object   $adapter     Dropbox HTTP client.
	 * @return mixed
	 */
	public function getFile($path, $fileStream, &$params = array(), $adapter = null) {
		$this->chunkStream = fopen('php://temp', 'wb+');

		// Set client
		if (is_null($adapter)) {
			$adapter = new ServMaskDropboxCurl;
		}

		$adapter->setAccessToken($this->accessToken);
		$adapter->setSSL($this->ssl);
		$adapter->setBaseURL(self::API_CONTENT_URL);
		$adapter->setPath('/files/download');
		$adapter->setOption(CURLOPT_WRITEFUNCTION, array($this, 'curlWriteFunction'));
		$adapter->setHeader('Range', "bytes={$params['startBytes']}-{$params['endBytes']}");
		$adapter->setHeader('Dropbox-API-Arg', json_encode(array('path' => $path)));

		// Make request
		$adapter->makeRequest();

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
	 * @param  string $path   Dropbox path at which to create the folder (UTF-8).
	 * @param  object $adapter Dropbox HTTP client.
	 * @return mixed
	 */
	public function createFolder($path, $adapter = null) {
		if (is_null($adapter)) {
			$adapter = new ServMaskDropboxCurl;
		}

		$adapter->setAccessToken($this->accessToken);
		$adapter->setSSL($this->ssl);
		$adapter->setBaseURL(self::API_URL);
		$adapter->setPath('/fileops/create_folder');
		$adapter->setOption(CURLOPT_POST, true);
		$adapter->setOption(CURLOPT_POSTFIELDS, array(
			'root' => 'auto',
			'path' => $path,
		));

		return $adapter->makeRequest();
	}

	/**
	 * Retrieves file and folder metadata
	 *
	 * @param  string $path   Dropbox path at which to create the folder (UTF-8).
	 * @param  object $adapter Dropbox HTTP client.
	 * @return mixed
	 */
	public function metadata($path, $adapter = null) {
		if (is_null($adapter)) {
			$adapter = new ServMaskDropboxCurl;
		}

		$adapter->setAccessToken($this->accessToken);
		$adapter->setSSL($this->ssl);
		$adapter->setBaseURL(self::API_URL);
		$adapter->setPath('/files/get_metadata');
		$adapter->setHeader('Content-Type', 'application/json; charset=utf-8');
		$adapter->setOption(CURLOPT_POST, true);
		$adapter->setOption(CURLOPT_POSTFIELDS, json_encode(array(
			'path'                                => $path,
			'include_media_info'                  => false,
			'include_deleted'                     => false,
			'include_has_explicit_shared_members' => false,
		)));

		return $adapter->makeRequest();
	}

	/**
	 * Retrives the contents of a folder.
	 *
	 * @param  string $path   Dropbox path (UTF-8).
	 * @param  object $adapter Dropbox HTTP client.
	 * @return mixed
	 */
	public function listFolder($path, $adapter = null) {
		if (is_null($adapter)) {
			$adapter = new ServMaskDropboxCurl;
		}

		$adapter->setAccessToken($this->accessToken);
		$adapter->setSSL($this->ssl);
		$adapter->setBaseURL(self::API_URL);
		$adapter->setPath('/files/list_folder');
		$adapter->setHeader('Content-Type', 'application/json; charset=utf-8');
		$adapter->setOption(CURLOPT_POST, true);
		$adapter->setOption(CURLOPT_POSTFIELDS, json_encode(array(
			'path'                                => $path,
			'include_media_info'                  => false,
			'include_deleted'                     => false,
			'include_has_explicit_shared_members' => false,
		)));

		return $adapter->makeRequest();
	}


	/**
	 * Deletes a file or folder
	 *
	 * @param  string $path   Dropbox path of the file or folder to delete (UTF-8).
	 * @param  object $adapter Dropbox HTTP client.
	 * @return mixed
	 */
	public function delete($path, $adapter = null) {
		if (is_null($adapter)) {
			$adapter = new ServMaskDropboxCurl;
		}

		$adapter->setAccessToken($this->accessToken);
		$adapter->setSSL($this->ssl);
		$adapter->setBaseURL(self::API_URL);
		$adapter->setPath('/files/delete');
		$adapter->setHeader('Content-Type', 'application/json; charset=utf-8');
		$adapter->setOption(CURLOPT_POST, true);
		$adapter->setOption(CURLOPT_POSTFIELDS, json_encode(array(
			'path' => $path,
		)));

		return $adapter->makeRequest();
	}

	/**
	 * Get account info
	 *
	 * @param  object $adapter Dropbox HTTP client.
	 * @return mixed
	 */
	public function getAccountInfo($adapter = null) {
		if (is_null($adapter)) {
			$adapter = new ServMaskDropboxCurl;
		}

		$adapter->setAccessToken($this->accessToken);
		$adapter->setSSL($this->ssl);
		$adapter->setBaseURL(self::API_URL);
		$adapter->setPath('/users/get_current_account');
		$adapter->setOption(CURLOPT_POST, true);
		$adapter->setOption(CURLOPT_POSTFIELDS, array());
		$adapter->setHeader('Content-Type', 'application/json; charset=utf-8');

		return $adapter->makeRequest();
	}

	/**
	 * Get space usage info
	 *
	 * @param  object $adapter Dropbox HTTP client.
	 * @return mixed
	 */
	public function getUsageInfo($adapter = null) {
		if (is_null($adapter)) {
			$adapter = new ServMaskDropboxCurl;
		}

		$adapter->setAccessToken($this->accessToken);
		$adapter->setSSL($this->ssl);
		$adapter->setBaseURL(self::API_URL);
		$adapter->setPath('/users/get_space_usage');
		$adapter->setOption(CURLOPT_POST, true);
		$adapter->setOption(CURLOPT_POSTFIELDS, array());
		$adapter->setHeader('Content-Type', 'application/json; charset=utf-8');

		return $adapter->makeRequest();
	}

	/**
	 * Revoke token
	 *
	 * @param  object $adapter Dropbox HTTP client.
	 * @return mixed
	 */
	public function revoke($adapter = null) {
		if (is_null($adapter)) {
			$adapter = new ServMaskDropboxCurl;
		}

		$adapter->setAccessToken($this->accessToken);
		$adapter->setSSL($this->ssl);
		$adapter->setBaseURL(self::API_URL);
		$adapter->setPath('/disable_access_token');
		$adapter->setOption(CURLOPT_POST, true);

		return $adapter->makeRequest();
	}
}
