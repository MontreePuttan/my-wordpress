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
?>

<div class="ai1wm-container">
	<div class="ai1wm-row">
		<div class="ai1wm-left">
			<div class="ai1wm-holder">
				<h1><i class="ai1wm-icon-gear"></i> <?php _e( 'Dropbox Settings', AI1WMDE_PLUGIN_NAME ); ?></h1>
				<br />
				<br />

				<div class="ai1wm-field">
					<?php if ( $token ): ?>
						<p id="ai1wmde-dropbox-details">
							<?php _e( 'Retrieving Dropbox account details..', AI1WMDE_PLUGIN_NAME ); ?>
						</p>

						<div id="ai1wmde-dropbox-progress">
							<div id="ai1wmde-dropbox-progress-bar"></div>
						</div>

						<p id="ai1wmde-dropbox-space"></p>

						<form method="POST" action="">
							<button type="submit" class="ai1wm-button-red" name="ai1wmde-dropbox-logout" id="ai1wmde-dropbox-logout">
								<i class="ai1wm-icon-dropbox"></i>
								<?php _e( 'Sign Out from your dropbox account', AI1WMDE_PLUGIN_NAME ); ?>
							</button>
						</form>

					<?php else: ?>

						<form method="POST" action="<?php echo AI1WMDE_DROPBOX_URL; ?>">
							<input type="hidden" name="ai1wmde_client" id="ai1wmde_client" value="<?php echo network_admin_url( 'admin.php?page=site-migration-dropbox-settings' ); ?>" />
							<button type="submit" class="ai1wm-button-blue" name="ai1wmde-dropbox-link" id="ai1wmde-dropbox-link">
								<i class="ai1wm-icon-dropbox"></i>
								<?php _e( 'Link your dropbox account', AI1WMDE_PLUGIN_NAME ); ?>
							</button>
						</form>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $token ): ?>
				<div class="ai1wm-holder" style="margin-top:22px;">
					<form method="POST" action="">
						<div>
							<h1><i class="ai1wm-icon-gear"></i> <?php _e( 'Dropbox Backups', AI1WMDE_PLUGIN_NAME ); ?></h1>
							<br />
							<br />
							<p><?php _e( 'Configure your backup plan', AI1WMDE_PLUGIN_NAME ); ?></p>
							<ul id="ai1wmde-dropbox-cron">
								<li>
									<input type="checkbox" name="ai1wmde-dropbox-cron[]" id="ai1wmde-dropbox-cron-hourly" value="hourly" <?php echo in_array( 'hourly', $cron ) ? 'checked' : null; ?> />
									<label for="ai1wmde-dropbox-cron-hourly"><?php _e( 'Every hour', AI1WMDE_PLUGIN_NAME ); ?></label>
								</li>
								<li>
									<input type="checkbox" name="ai1wmde-dropbox-cron[]" id="ai1wmde-dropbox-cron-daily" value="daily" <?php echo in_array( 'daily', $cron ) ? 'checked' : null; ?> />
									<label for="ai1wmde-dropbox-cron-daily"><?php _e( 'Every day', AI1WMDE_PLUGIN_NAME ); ?></label>
								</li>
								<li>
									<input type="checkbox" name="ai1wmde-dropbox-cron[]" id="ai1wmde-dropbox-cron-weekly" value="weekly" <?php echo in_array( 'weekly', $cron ) ? 'checked' : null; ?> />
									<label for="ai1wmde-dropbox-cron-weekly"><?php _e( 'Every week', AI1WMDE_PLUGIN_NAME ); ?></label>
								</li>
								<li>
									<input type="checkbox" name="ai1wmde-dropbox-cron[]" id="ai1wmde-dropbox-cron-monthly" value="monthly" <?php echo in_array( 'monthly', $cron ) ? 'checked' : null; ?> />
									<label for="ai1wmde-dropbox-cron-monthly"><?php _e( 'Every month', AI1WMDE_PLUGIN_NAME ); ?></label>
								</li>
							</ul>
						</div>

						<p>
							<?php _e( 'Last backup date:', AI1WMDE_PLUGIN_NAME ); ?>
							<?php if ( $timestamp ): ?>
								<strong>
									<?php echo date_i18n( sprintf( '%s %s', get_option( 'date_format' ), get_option( 'time_format' ) ), $timestamp ); ?>
								</strong>
							<?php else: ?>
								<strong><?php _e( 'None', AI1WMDE_PLUGIN_NAME ); ?></strong>
							<?php endif; ?>
						</p>

						<p>
							<input type="checkbox" name="ai1wmde-dropbox-ssl" id="ai1wmde-dropbox-ssl" value="1" <?php echo empty( $ssl ) ? 'checked' : null; ?> />
							<label for="ai1wmde-dropbox-ssl"><?php _e( 'Disable connecting to Dropbox via SSL (only if export is failing)', AI1WMDE_PLUGIN_NAME ); ?></label>
						</p>

						<article class="ai1wmde-article">
							<h3><?php _e( 'Notification settings', AI1WMDE_PLUGIN_NAME ); ?></h3>
							<p>
								<label for="ai1wmde-notification-toggle">
								<input type="checkbox" id="ai1wmde-notification-toggle" name="ai1wmde-notification-toggle" <?php echo $notify ? 'checked' : ''; ?> />
									<?php _e( 'Send an email when a backup is complete.', AI1WMDE_PLUGIN_NAME ); ?>
								</label>
							</p>

							<p>
								<label><?php _e( 'Email address', AI1WMDE_PLUGIN_NAME ); ?></label>
								<br />
								<input class="ai1wmde-email" style="width: 15rem;" type="email" id="ai1wmde-notification-email" name="ai1wmde-notification-email" value="<?php echo $email; ?>" />
							</p>
						</article>

						<article class="ai1wmde-article">
							<h3><?php _e( 'Retention settings', AI1WMDE_PLUGIN_NAME ); ?></h3>
							<p>
								<div class="ai1wm-field">
									<label for="ai1wmde-dropbox-backups"><?php _e( 'Keep the most recent', AI1WMDE_PLUGIN_NAME ); ?></label>
									<input style="width: 3em" type="number" name="ai1wmde-dropbox-backups" id="ai1wmde-dropbox-backups" value="<?php echo intval( $backups ); ?>" />
									<?php _e( 'backups. <small>Default: <strong>0</strong> unlimited.</small>', AI1WMDE_PLUGIN_NAME ); ?>
								</div>

								<div class="ai1wm-field">
									<label for="ai1wmde-dropbox-total"><?php _e( 'Limit the total size of backups to', AI1WMDE_PLUGIN_NAME ); ?></label>
									<input style="width: 4em" type="number" name="ai1wmde-dropbox-total" id="ai1wmde-dropbox-total" value="<?php echo intval( $total ); ?>" />
									<select style="margin-top: -2px" name="ai1wmde-dropbox-total-unit" id="ai1wmde-dropbox-total-unit">
										<option value="MB" <?php echo strpos( $total, 'MB' ) !== false ? 'selected="selected"' : null; ?>><?php _e( 'MB', AI1WMDE_PLUGIN_NAME ); ?></option>
										<option value="GB" <?php echo strpos( $total, 'GB' ) !== false ? 'selected="selected"' : null; ?>><?php _e( 'GB', AI1WMDE_PLUGIN_NAME ); ?></option>
									</select>
									<?php _e( '<small>Default: <strong>0</strong> unlimited.</small>', AI1WMDE_PLUGIN_NAME ); ?>
								</div>
							</p>
						</article>

						<p>
							<button type="submit" class="ai1wm-button-blue" name="ai1wmde-dropbox-update" id="ai1wmde-dropbox-update">
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
