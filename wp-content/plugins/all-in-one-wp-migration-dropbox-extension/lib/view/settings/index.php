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
?>

<div class="ai1wm-container">
	<div class="ai1wm-row">
		<div class="ai1wm-left">
			<div class="ai1wm-holder">
				<h1><i class="ai1wm-icon-gear"></i> <?php _e( 'Dropbox Settings', AI1WMDE_PLUGIN_NAME ); ?></h1>
				<br />
				<br />

				<div class="ai1wm-field">
					<?php if ( $token ) :  ?>
						<p id="ai1wmde-dropbox-details">
							<?php _e( 'Retrieving Dropbox account details..', AI1WMDE_PLUGIN_NAME ); ?>
						</p>

						<div id="ai1wmde-dropbox-progress">
							<div id="ai1wmde-dropbox-progress-bar"></div>
						</div>

						<p id="ai1wmde-dropbox-space"></p>

						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php?action=ai1wmde_dropbox_revoke' ) ); ?>">
							<button type="submit" class="ai1wm-button-red" name="ai1wmde_dropbox_logout" id="ai1wmde-dropbox-logout">
								<i class="ai1wm-icon-dropbox"></i>
								<?php _e( 'Sign Out from your dropbox account', AI1WMDE_PLUGIN_NAME ); ?>
							</button>
						</form>

					<?php else : ?>

						<form method="post" action="<?php echo esc_url( AI1WMDE_DROPBOX_URL ); ?>">
							<input type="hidden" name="ai1wmde_client" id="ai1wmde-client" value="<?php echo network_admin_url( 'admin.php?page=site-migration-dropbox-settings' ); ?>" />
							<button type="submit" class="ai1wm-button-blue" name="ai1wmde_dropbox_link" id="ai1wmde-dropbox-link">
								<i class="ai1wm-icon-dropbox"></i>
								<?php _e( 'Link your dropbox account', AI1WMDE_PLUGIN_NAME ); ?>
							</button>
						</form>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $token ) :  ?>
				<div class="ai1wm-holder" style="margin-top:22px;">
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php?action=ai1wmde_dropbox_settings' ) ); ?>">
						<h1><i class="ai1wm-icon-gear"></i> <?php _e( 'Dropbox Backups', AI1WMDE_PLUGIN_NAME ); ?></h1>
						<br />
						<br />

						<article class="ai1wmde-article">
							<h3><?php _e( 'Configure your backup plan', AI1WMDE_PLUGIN_NAME ); ?></h3>
							<ul id="ai1wmde-dropbox-cron">
								<li>
									<label for="ai1wmde-dropbox-cron-hourly">
										<input type="checkbox" name="ai1wmde_dropbox_cron[]" id="ai1wmde-dropbox-cron-hourly" value="hourly" <?php echo in_array( 'hourly', $dropbox_backup_schedules ) ? 'checked' : null; ?> />
										<?php _e( 'Every hour', AI1WMDE_PLUGIN_NAME ); ?>
									</label>
								</li>
								<li>
									<label for="ai1wmde-dropbox-cron-daily">
										<input type="checkbox" name="ai1wmde_dropbox_cron[]" id="ai1wmde-dropbox-cron-daily" value="daily" <?php echo in_array( 'daily', $dropbox_backup_schedules ) ? 'checked' : null; ?> />
										<?php _e( 'Every day', AI1WMDE_PLUGIN_NAME ); ?>
									</label>
								</li>
								<li>
									<label for="ai1wmde-dropbox-cron-weekly">
										<input type="checkbox" name="ai1wmde_dropbox_cron[]" id="ai1wmde-dropbox-cron-weekly" value="weekly" <?php echo in_array( 'weekly', $dropbox_backup_schedules ) ? 'checked' : null; ?> />
										<?php _e( 'Every week', AI1WMDE_PLUGIN_NAME ); ?>
									</label>
								</li>
								<li>
									<label for="ai1wmde-dropbox-cron-monthly">
										<input type="checkbox" name="ai1wmde_dropbox_cron[]" id="ai1wmde-dropbox-cron-monthly" value="monthly" <?php echo in_array( 'monthly', $dropbox_backup_schedules ) ? 'checked' : null; ?> />
										<?php _e( 'Every month', AI1WMDE_PLUGIN_NAME ); ?>
									</label>
								</li>
							</ul>

							<p>
								<?php _e( 'Last backup date:', AI1WMDE_PLUGIN_NAME ); ?>
								<strong>
									<?php echo $last_backup_date; ?>
								</strong>
							</p>

							<p>
								<?php _e( 'Next backup date:', AI1WMDE_PLUGIN_NAME ); ?>
								<strong>
									<?php echo $next_backup_date; ?>
								</strong>
							</p>

							<p>
								<label for="ai1wmde-dropbox-ssl">
									<input type="checkbox" name="ai1wmde_dropbox_ssl" id="ai1wmde-dropbox-ssl" value="1" <?php echo empty( $ssl ) ? 'checked' : null; ?> />
									<?php _e( 'Disable connecting to Dropbox via SSL (only if export is failing)', AI1WMDE_PLUGIN_NAME ); ?>
								</label>
							</p>
						</article>

						<article class="ai1wmde-article">
							<h3><?php _e( 'Notification settings', AI1WMDE_PLUGIN_NAME ); ?></h3>
							<p>
								<label for="ai1wmde-notification-toggle">
									<input type="checkbox" id="ai1wmde-notification-toggle" name="ai1wmde_notification_toggle" <?php echo $notify ? 'checked' : null; ?> />
									<?php _e( 'Send an email when a backup is complete.', AI1WMDE_PLUGIN_NAME ); ?>
								</label>
							</p>

							<p>
								<label for="ai1wmde-notification-email">
									<?php _e( 'Email address', AI1WMDE_PLUGIN_NAME ); ?>
									<br />
									<input class="ai1wmde-email" style="width: 15rem;" type="email" id="ai1wmde-notification-email" name="ai1wmde_notification_email" value="<?php echo $email; ?>" />
								</label>
							</p>
						</article>

						<article class="ai1wmde-article">
							<h3><?php _e( 'Retention settings', AI1WMDE_PLUGIN_NAME ); ?></h3>
							<p>
								<div class="ai1wm-field">
									<label for="ai1wmde-dropbox-backups">
										<?php _e( 'Keep the most recent', AI1WMDE_PLUGIN_NAME ); ?>
										<input style="width: 3em" type="number" min="0" name="ai1wmde_dropbox_backups" id="ai1wmde-dropbox-backups" value="<?php echo intval( $backups ); ?>" />
									</label>
									<?php _e( 'backups. <small>Default: <strong>0</strong> unlimited.</small>', AI1WMDE_PLUGIN_NAME ); ?>
								</div>

								<div class="ai1wm-field">
									<label for="ai1wmde-dropbox-total">
										<?php _e( 'Limit the total size of backups to', AI1WMDE_PLUGIN_NAME ); ?>
										<input style="width: 4em" type="number" min="0" name="ai1wmde_dropbox_total" id="ai1wmde-dropbox-total" value="<?php echo intval( $total ); ?>" />
									</label>
									<select style="margin-top: -2px" name="ai1wmde_dropbox_total_unit" id="ai1wmde-dropbox-total-unit">
										<option value="MB" <?php echo strpos( $total, 'MB' ) !== false ? 'selected="selected"' : null; ?>><?php _e( 'MB', AI1WMDE_PLUGIN_NAME ); ?></option>
										<option value="GB" <?php echo strpos( $total, 'GB' ) !== false ? 'selected="selected"' : null; ?>><?php _e( 'GB', AI1WMDE_PLUGIN_NAME ); ?></option>
									</select>
									<?php _e( '<small>Default: <strong>0</strong> unlimited.</small>', AI1WMDE_PLUGIN_NAME ); ?>
								</div>
							</p>
						</article>

						<p>
							<button type="submit" class="ai1wm-button-blue" name="ai1wmde_dropbox_update" id="ai1wmde-dropbox-update">
								<i class="ai1wm-icon-dropbox"></i>
								<?php _e( 'Update', AI1WMDE_PLUGIN_NAME ); ?>
							</button>
						</p>
					</form>
				</div>
			<?php endif; ?>
		</div>
		<div class="ai1wm-right">
			<div class="ai1wm-sidebar">
				<div class="ai1wm-segment">
					<?php if ( ! AI1WM_DEBUG ) : ?>
						<?php include AI1WM_TEMPLATES_PATH . '/common/share-buttons.php'; ?>
					<?php endif; ?>

					<h2><?php _e( 'Leave Feedback', AI1WMDE_PLUGIN_NAME ); ?></h2>

					<?php include AI1WM_TEMPLATES_PATH . '/common/leave-feedback.php'; ?>
				</div>
			</div>
		</div>
	</div>
</div>
