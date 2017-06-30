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

class Ai1wmde_Export_Clean {

	public static function execute( $params ) {

		// Set Dropbox client
		$dropbox = new ServMaskDropboxClient(
			get_option( 'ai1wmde_dropbox_token' ),
			get_option( 'ai1wmde_dropbox_ssl', true )
		);

		// Get metadata
		$metadata = $dropbox->metadata( ai1wm_archive_folder() );

		// Number of backups
		if ( ( $backups = get_option( 'ai1wmde_dropbox_backups' ) ) ) {
			if ( ( $backups = count( $metadata['contents'] ) - $backups ) ) {
				for ( $i = 0; $i < $backups; $i++ ) {
					if ( empty( $metadata['contents'][$i]['is_dir'] ) ) {
						$dropbox->delete( $metadata['contents'][$i]['path'] );
					}
				}
			}
		}

		// Size of backups
		if ( ( $total = ai1wm_parse_size( get_option( 'ai1wmde_dropbox_total' ) ) ) ) {
			$bytes = 0;
			if ( isset( $metadata['contents'] ) && ( $contents = $metadata['contents'] ) ) {
				foreach ( $contents as $content ) {
					$bytes += $content['bytes'];
				}

				// Delete backups
				foreach ( $contents as $content ) {
					if ( $bytes > $total ) {
						if ( empty( $content['is_dir'] ) ) {
							$dropbox->delete( $content['path'] );

							// Decrease bytes
							$bytes -= $content['bytes'];
						}
					}
				}
			}
		}

		return $params;
	}
}
