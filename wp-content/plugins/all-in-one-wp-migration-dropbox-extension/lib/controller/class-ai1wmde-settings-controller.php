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

class Ai1wmde_Settings_Controller {

	public static function index() {
		$model = new Ai1wmde_Settings;

		$dropbox_backup_schedules  = get_option( 'ai1wmde_dropbox_cron', array() );
		$last_backup_timestamp = get_option( 'ai1wmde_dropbox_timestamp', false );

		$last_backup_date = $model->get_last_backup_date( $last_backup_timestamp );
		$next_backup_date = $model->get_next_backup_date( $dropbox_backup_schedules );

		Ai1wm_Template::render(
			'settings/index',
			array(
				'backups'                  => get_option( 'ai1wmde_dropbox_backups', false ),
				'dropbox_backup_schedules' => $dropbox_backup_schedules,
				'last_backup_date'         => $last_backup_date,
				'next_backup_date'         => $next_backup_date,
				'notify'                   => get_option( 'ai1wmde_dropbox_notify_toggle', false ),
				'email'                    => get_option( 'ai1wmde_dropbox_notify_email', get_option( 'admin_email', '' ) ),
				'ssl'                      => get_option( 'ai1wmde_dropbox_ssl', true ),
				'token'                    => get_option( 'ai1wmde_dropbox_token', false ),
				'total'                    => get_option( 'ai1wmde_dropbox_total', false ),
			),
			AI1WMDE_TEMPLATES_PATH
		);
	}

	public static function settings() {
		$model = new Ai1wmde_Settings;

		// Dropbox update
		if ( isset( $_POST['ai1wmde_dropbox_update'] ) ) {

			// Cron update
			if ( ! empty( $_POST['ai1wmde_dropbox_cron'] ) ) {
				$model->set_cron( (array) $_POST['ai1wmde_dropbox_cron'] );
			} else {
				$model->set_cron( array() );
			}

			// Set SSL mode
			if ( ! empty( $_POST['ai1wmde_dropbox_ssl'] ) ) {
				$model->set_ssl( 0 );
			} else {
				$model->set_ssl( 1 );
			}

			// Set number of backups
			if ( ! empty( $_POST['ai1wmde_dropbox_backups'] ) ) {
				$model->set_backups( (int) $_POST['ai1wmde_dropbox_backups'] );
			} else {
				$model->set_backups( 0 );
			}

			// Set size of backups
			if ( ! empty( $_POST['ai1wmde_dropbox_total'] ) && ! empty( $_POST['ai1wmde_dropbox_total_unit'] ) ) {
				$model->set_total( (int) $_POST['ai1wmde_dropbox_total'] . trim( $_POST['ai1wmde_dropbox_total_unit'] ) );
			} else {
				$model->set_total( 0 );
			}

			// Set notification toggle
			$model->set_toggle( isset( $_POST['ai1wmde_notification_toggle'] ) );

			// Set notification email
			$model->set_email( $_POST['ai1wmde_notification_email'] );
		}

		// Redirect to settings page
		wp_redirect( network_admin_url( 'admin.php?page=site-migration-dropbox-settings' ) );
		exit;
	}

	public static function revoke() {
		$model = new Ai1wmde_Settings;

		// Dropbox logout
		if ( isset( $_POST['ai1wmde_dropbox_logout'] ) ) {
			$model->revoke();
		}

		// Redirect to settings page
		wp_redirect( network_admin_url( 'admin.php?page=site-migration-dropbox-settings' ) );
		exit;
	}

	public static function account() {
		$model = new Ai1wmde_Settings;

		// Dropbox account
		if ( ( $account = $model->get_account() ) ) {
			echo json_encode( $account );
		}
		exit;
	}
}
