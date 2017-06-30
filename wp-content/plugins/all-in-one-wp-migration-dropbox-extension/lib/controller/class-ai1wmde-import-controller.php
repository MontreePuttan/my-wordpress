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

class Ai1wmde_Import_Controller {

	public static function button() {
		return Ai1wm_Template::get_content(
			'import/button',
			array( 'token' => get_option( 'ai1wmde_dropbox_token' ) ),
			AI1WMDE_TEMPLATES_PATH
		);
	}

	public static function picker() {
		Ai1wm_Template::render(
			'import/picker',
			array(),
			AI1WMDE_TEMPLATES_PATH
		);
	}

	public static function metadata() {
		// Set path
		$path = null;
		if ( isset( $_GET['path'] ) ) {
			$path = trim( $_GET['path'], '/' );
		}

		// Set Dropbox client
		$dropbox = new ServMaskDropboxClient(
			get_option( 'ai1wmde_dropbox_token' ),
			get_option( 'ai1wmde_dropbox_ssl', true )
		);

		// List folder
		$metadata = $dropbox->metadata( $path );

		// Set folder structure
		$response = array( 'path' => null, 'items' => array() );

		// Set folder path
		if ( isset( $metadata['path'] ) ) {
			$response['path'] = $metadata['path'];
		}

		// Set folder items
		if ( isset( $metadata['contents'] ) && ( $items = $metadata['contents'] ) ) {
			foreach ( $items as $item ) {
				$response['items'][] = array(
					'size'   => isset( $item['size'] ) ? $item['size'] : null,
					'path'	 => isset( $item['path'] ) ? $item['path'] : null,
					'icon'   => isset( $item['icon'] ) ? $item['icon'] : null,
					'bytes'  => isset( $item['bytes'] ) ? $item['bytes'] : null,
					'is_dir' => isset( $item['is_dir']) ? $item['is_dir'] : null,
				);
			}
		}

		echo json_encode( $response );
		exit;
	}
}
