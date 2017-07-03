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

class Ai1wmde_Export_Clean {

	public static function execute( $params ) {

		// Set Dropbox client
		$dropbox = new ServMaskDropboxClient(
			get_option( 'ai1wmde_dropbox_token' ),
			get_option( 'ai1wmde_dropbox_ssl', true )
		);

		// List folder
		$directory = $dropbox->listFolder( sprintf( '/%s', ai1wm_archive_folder() ) );

		// No backups, no need to apply backup retention
		if ( empty( $directory['entries'] ) ) {
			return $params;
		}

		$backups = array();
		foreach ( $directory['entries'] as $backup ) {
			if ( $backup['.tag'] === 'file' ) {
				$backups[] = $backup;
			}
		}

		// Skip calculations if there are no backups to delete
		if ( count( $backups ) === 0 ) {
			return $params;
		}

		usort( $backups, 'self::sort_by_date_asc' );

		// Number of backups
		if ( ( $backups_limit = get_option( 'ai1wmde_dropbox_backups', 0 ) ) ) {
			if ( ( $backups_to_remove = count( $backups ) - intval( $backups_limit ) ) > 0 ) {
				for ( $i = 0; $i < $backups_to_remove; $i++ ) {
					$dropbox->delete( $backups[ $i ]['path_lower'] );
				}
			}
		}

		// Sort backups by date desc
		$backups = array_reverse( $backups );

		// Get the size of the latest backup before we remove it
		$size_of_backups = $backups[0]['size'];

		// Remove the latest backup, the user should have at least one backup
		array_shift( $backups );

		// Size of backups
		if ( ( $retention_size = ai1wm_parse_size( get_option( 'ai1wmde_dropbox_total', 0 ) ) ) > 0 ) {
			foreach ( $backups as $backup ) {
				$size_of_backups += $backup['size'];

				// Remove file if retention size is exceeded
				if ( $size_of_backups > $retention_size ) {
					$dropbox->delete( $backup['path_lower'] );
				}
			}
		}

		return $params;
	}

	public static function sort_by_date_asc( $first_backup, $second_backup ) {
		return strtotime( $first_backup['server_modified'] ) - strtotime( $second_backup['server_modified'] );
	}
}
