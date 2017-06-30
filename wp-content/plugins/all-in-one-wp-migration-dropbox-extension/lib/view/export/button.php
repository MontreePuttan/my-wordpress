<?php if ( $token ): ?>
	<a href="#" id="ai1wm-export-dropbox"><?php _e( 'Dropbox', AI1WMDE_PLUGIN_NAME ); ?></a>
<?php else: ?>
	<a href="<?php echo network_admin_url( 'admin.php?page=site-migration-dropbox-settings' ); ?>"><?php _e( 'Dropbox', AI1WMDE_PLUGIN_NAME ); ?></a>
<?php endif; ?>
