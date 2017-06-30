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

class Ai1wmde_Settings
{
	public function revoke() {
		// Set Dropbox client
		$dropbox = new ServMaskDropboxClient(
			get_option( 'ai1wmde_dropbox_token' ),
			get_option( 'ai1wmde_dropbox_ssl', true )
		);

		// Revoke token
		$dropbox->revoke();

		// Remove token option
		delete_option( 'ai1wmde_dropbox_token' );
	}

	public function cron( $schedules ) {
		// Reset cron schedules
		Ai1wm_Cron::clear( 'ai1wmde_dropbox_hourly_export' );
		Ai1wm_Cron::clear( 'ai1wmde_dropbox_daily_export' );
		Ai1wm_Cron::clear( 'ai1wmde_dropbox_weekly_export' );
		Ai1wm_Cron::clear( 'ai1wmde_dropbox_monthly_export' );

		// Update cron options
		update_option( 'ai1wmde_dropbox_cron', $schedules );

		// Update cron schedules
		foreach ( $schedules as $recurrence ) {
			Ai1wm_Cron::add( "ai1wmde_dropbox_{$recurrence}_export", $recurrence, array(
				'secret_key' => get_option( AI1WM_SECRET_KEY ),
				'dropbox'    => 1,
			) );
		}
	}

	public function ssl( $mode ) {
		update_option( 'ai1wmde_dropbox_ssl', $mode );
	}

	public function backups( $number ) {
		update_option( 'ai1wmde_dropbox_backups', $number );
	}

	public function total( $size ) {
		update_option( 'ai1wmde_dropbox_total', $size );
	}

	public function email( $email ) {
		update_option( 'ai1wmde_dropbox_notify_email', $email );
	}

	public function toggle( $toggle ) {
		update_option( 'ai1wmde_dropbox_notify_toggle', $toggle );
	}

	public function account() {
		// Set Dropbox client
		$dropbox = new ServMaskDropboxClient(
			get_option( 'ai1wmde_dropbox_token' ),
			get_option( 'ai1wmde_dropbox_ssl', true )
		);

		// Get account info
		$account = $dropbox->getAccountInfo();

		// Set account name
		$name = null;
		if ( isset( $account['display_name'] ) ) {
			$name = $account['display_name'];
		}

		// Set used quota
		$used = null;
		if ( isset( $account['quota_info']['normal'] ) ) {
			$used = $account['quota_info']['normal'];
		}

		// Set total quota
		$total = null;
		if ( isset( $account['quota_info']['quota'] ) ) {
			$total = $account['quota_info']['quota'];
		}

		return array(
			'name'     => $name,
			'used'     => size_format( $used ),
			'total'    => size_format( $total ),
			'progress' => ceil( ( $used / $total ) * 100 ),
		);
	}
}
