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

class Ai1wmde_Export_Upload {

	public static function execute( $params, ServMaskDropboxClient $dropbox = null, ServMaskDropboxCurl $adapter = null ) {

		// Set completed flag
		$params['completed'] = false;

		// Set offset
		if ( ! isset( $params['offset'] ) ) {
			$params['offset'] = 0;
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

		// Set archive file
		$archive  = fopen( ai1wm_archive_path( $params ), 'rb' );

		// Set archive details
		$folder = ai1wm_archive_folder();
		$name   = ai1wm_archive_name( $params );
		$bytes  = ai1wm_archive_bytes( $params );
		$size   = ai1wm_archive_size( $params );

		// Read file chunk
		if ( ( fseek( $archive, $params['offset'] ) !== -1 )
				&& ( $chunk = fread( $archive, ServMaskDropboxClient::CHUNK_SIZE ) ) ) {

			// Upload file in one chunk if <= 4MB
			if ( $bytes <= ServMaskDropboxClient::CHUNK_SIZE ) {

				try {

					// Increase number of retries
					$params['retry'] += 1;

					// Upload file
					$dropbox->uploadFile( sprintf( '/%s/%s', $folder, $name ), $chunk, $params, $adapter );

				} catch ( Exception $e ) {
					// Retry 3 times
					if ( $params['retry'] <= 3 ) {
						return $params;
					}

					throw $e;
				}

				// Set completed flag
				$params['completed'] = true;

			} else if ( $params['offset'] === 0 ) {

				try {

					// Increase number of retries
					$params['retry'] += 1;

					// Upload first file chunk
					$dropbox->uploadFirstFileChunk( $chunk, $params, $adapter );

				} catch ( Exception $e ) {
					// Retry 3 times
					if ( $params['retry'] <= 3 ) {
						return $params;
					}

					throw $e;
				}

			} else if ( ( $bytes > $params['offset'] + ServMaskDropboxClient::CHUNK_SIZE ) ) {

				try {

					// Increase number of retries
					$params['retry'] += 1;

					// Upload next file chunk
					$dropbox->uploadNextFileChunk( $chunk, $params, $adapter );

				} catch ( Exception $e ) {
					// Retry 3 times
					if ( $params['retry'] <= 3 ) {
						return $params;
					}

					throw $e;
				}

			} else {

				try {

					// Increase number of retries
					$params['retry'] += 1;

					// Upload file chunk commit
					$dropbox->uploadFileChunkCommit( sprintf( '/%s/%s', $folder, $name ), $chunk, $params, $adapter );

				} catch ( Exception $e ) {
					// Retry 3 times
					if ( $params['retry'] <= 3 ) {
						return $params;
					}

					throw $e;
				}

				// Set completed flag
				$params['completed'] = true;
			}

			// Set next offset
			$params['offset'] = ftell( $archive );
		}

		// Unset retry counter
		unset( $params['retry'] );

		// Set progress
		if ( empty( $params['completed'] ) ) {

			// Get progress
			if ( isset( $params['offset'] ) ) {
				$progress = (int) ( ( $params['offset'] / $bytes ) * 100 );
			} else {
				$progress = 100;
			}

			// Set progress
			Ai1wm_Status::info(
				sprintf(
					__(
						'<i class="ai1wm-icon-dropbox"></i> ' .
						'Uploading <strong>%s</strong> (%s)<br />%d%% complete',
						AI1WMDE_PLUGIN_NAME
					),
					$name,
					$size,
					$progress
				)
			);

		} else {

			// Set last backup date
			update_option( 'ai1wmde_dropbox_timestamp', time() );

			// Set progress
			Ai1wm_Status::done(
				__( 'Your WordPress archive has been uploaded to Dropbox.', AI1WMDE_PLUGIN_NAME ),
				__( 'Dropbox', AI1WMDE_PLUGIN_NAME )
			);

			self::notify_admin_of_new_backup( $params );

			// Unset upload ID
			unset( $params['upload_id'] );

			// Unset offset
			unset( $params['offset'] );

			// Unset completed flag
			unset( $params['completed'] );

		}

		// Close the archive file
		fclose( $archive );

		return $params;
	}

	public static function is_notification_enabled( $recipient ) {
		return (
			get_option( 'ai1wmde_dropbox_notify_toggle', false ) &&
			! empty( $recipient ) &&
			function_exists( 'wp_mail' )
		);
	}

	public static function notify_admin_of_new_backup( $params ) {
		$recipient = get_option( 'ai1wmde_dropbox_notify_email', get_option( 'admin_email', '' ) );

		if ( self::is_notification_enabled( $recipient ) ) {
			$subject = __( 'Dropbox Backup report', AI1WMDE_PLUGIN_NAME );
			$message = sprintf( __( "Your site %s was successfully exported to Dropbox.\n", AI1WMDE_PLUGIN_NAME ), site_url() );
			$message .= "\n\n";
			$message .= sprintf( __( "Date: %s\n", AI1WMDE_PLUGIN_NAME ), date( 'r' ) );
			$message .= sprintf( __( "Backup file: %s\n", AI1WMDE_PLUGIN_NAME ), ai1wm_archive_name( $params ) );
			$message .= sprintf( __( "Size: %s\n", AI1WMDE_PLUGIN_NAME ), ai1wm_archive_size( $params ) );
			wp_mail( $recipient, $subject, $message );
		}
	}
}
