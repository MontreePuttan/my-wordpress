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

class Ai1wmde_Settings_Controller {

	public static function index() {

		$model = new Ai1wmde_Settings;

		// Dropbox update
		if ( isset( $_POST['ai1wmde-dropbox-update'] ) ) {

			// Cron update
			if ( ! empty( $_POST['ai1wmde-dropbox-cron'] ) ) {
				$model->cron( (array) $_POST['ai1wmde-dropbox-cron'] );
			} else {
				$model->cron( array() );
			}

			// Set SSL mode
			if ( ! empty( $_POST['ai1wmde-dropbox-ssl'] ) ) {
				$model->ssl( 0 );
			} else {
				$model->ssl( 1 );
			}

			// Set number of backups
			if ( ! empty( $_POST['ai1wmde-dropbox-backups'] ) ) {
				$model->backups( (int) $_POST['ai1wmde-dropbox-backups'] );
			} else {
				$model->backups( 0 );
			}

			// Set size of backups
			if ( ! empty( $_POST['ai1wmde-dropbox-total'] ) && ! empty( $_POST['ai1wmde-dropbox-total-unit'] ) ) {
				$model->total( (int) $_POST['ai1wmde-dropbox-total'] . trim( $_POST['ai1wmde-dropbox-total-unit'] ) );
			} else {
				$model->total( 0 );
			}

			$model->email( $_POST['ai1wmde-notification-email'] );

			$model->toggle( isset( $_POST['ai1wmde-notification-toggle'] ) );

		}

		// Dropbox logout
		if ( isset( $_POST['ai1wmde-dropbox-logout'] ) ) {
			$model->revoke();
		}

		Ai1wm_Template::render(
			'settings/index',
			array(
				'backups'   => get_option( 'ai1wmde_dropbox_backups', false ),
				'cron'      => get_option( 'ai1wmde_dropbox_cron', array() ),
				'notify'    => get_option( 'ai1wmde_dropbox_notify_toggle', false ),
				'email'     => get_option( 'ai1wmde_dropbox_notify_email', get_option( 'admin_email', '' ) ),
				'ssl'       => get_option( 'ai1wmde_dropbox_ssl', true ),
				'timestamp' => get_option( 'ai1wmde_dropbox_timestamp', false ),
				'token'     => get_option( 'ai1wmde_dropbox_token', false ),
				'total'     => get_option( 'ai1wmde_dropbox_total', false ),
			),
			AI1WMDE_TEMPLATES_PATH
		);
	}

	public static function account() {
		$model = new Ai1wmde_Settings;
		if ( ( $account = $model->account() ) ) {
			echo json_encode( $account );
		}
		exit;
	}
}
