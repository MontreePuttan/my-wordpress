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

class Ai1wmde_Import_Download {

	public static function execute( $params, ServMaskDropboxClient $dropbox = null ) {

		// Set completed flag
		$params['completed'] = false;

		// Total bytes
		if ( ! isset( $params['totalBytes'] ) ) {
			throw new Ai1wm_Import_Exception(
				__( 'Unable to determine file size of Dropbox File.', AI1WMDE_PLUGIN_NAME )
			);
		}

		// Set startBytes
		if ( ! isset( $params['startBytes'] ) ) {
			$params['startBytes'] = 0;
		}

		// Set endBytes
		if ( ! isset( $params['endBytes'] ) ) {
			$params['endBytes'] = ServMaskDropboxClient::CHUNK_SIZE;
		}

		// Set retry
		if ( ! isset( $params['retry'] ) ) {
			$params['retry'] = 0;
		}

		// Set Dropbox client
		if ( is_null( $dropbox ) ) {
			$dropbox = new ServMaskDropboxClient(
				get_option( 'ai1wmde_dropbox_token' ),
				get_option( 'ai1wmde_dropbox_ssl', true )
			);
		}

		// Get archive file
		$archive = fopen( ai1wm_archive_path( $params ), 'ab' );

		try {

			// Increase number of retries
			$params['retry'] += 1;

			// Download file chunk
			$dropbox->getFile( $params['filePath'], $archive, $params );

		} catch ( Exception $e ) {
			// Retry 3 times
			if ( $params['retry'] <= 3 ) {
				return $params;
			}

			throw $e;
		}

		// Unset retry counter
		unset( $params['retry'] );

		// Calculate percent
		$percent = (int) ( ( $params['startBytes'] / $params['totalBytes'] ) * 100 );

		// Set progress
		Ai1wm_Status::progress( $percent );

		// Completed?
		if ( $params['totalBytes'] == $params['startBytes'] ) {

			// Unset total bytes
			unset( $params['totalBytes'] );

			// Unset start bytes
			unset( $params['startBytes'] );

			// Unset end bytes
			unset( $params['endBytes'] );

			// Unset completed flag
			unset( $params['completed'] );

		}

		// Close the archive file
		fclose( $archive );

		return $params;
	}
}
