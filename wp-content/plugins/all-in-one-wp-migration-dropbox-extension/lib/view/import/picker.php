<div id="ai1wmde-import-modal" class="ai1wmde-modal ai1wm-not-visible">
	<div class="ai1wm-modal-content-middle">
		<span class="ai1wm-loader" v-if="files === false"></span>
		<div class="ai1wmde-file-browser" v-if="files !== false">
			<span class="ai1wmde-path">
				<i class="ai1wm-icon-folder"></i>
				<span id="ai1wmde-download-path">{{path}}</span>
			</span>
			<ul class="ai1wmde-file-list">
				<li v-repeat="files" v-on="click: browse(this)" class="ai1wmde-file-item">
					<span class="ai1wmde-filename">
						<i class="{{type | icon}}"></i>
						{{name}}
					</span>
					<span class="ai1wmde-filedate" v-if="type !== 'folder'">{{date}}</span>
					<span class="ai1wmde-filesize" v-if="type !== 'folder'">{{size}}</span>
				</li>
			</ul>
			<p v-if="files.length === 0"><?php _e( 'No files or directories', AI1WMDE_PLUGIN_NAME ); ?></p>
		</div>
	</div>

	<div class="ai1wm-modal-action">
		<p>
			<span class="ai1wmde-contact-dropbox" v-if="files === false">
				<?php _e( 'Connecting to Dropbox ...', AI1WMDE_PLUGIN_NAME ); ?>
			</span>
		</p>
		<p>
			<span id="ai1wmde-download-file" class="ai1wmde-selected-file" v-if="selected_filename" v-animation>
				<i class="ai1wm-icon-file-zip"></i>
				{{selected_filename}}
			</span>
		</p>
		<p>
			<button type="button" class="ai1wm-button-red" id="ai1wmde-import-file-cancel" v-on="click: cancel">
				<i class="ai1wm-icon-notification"></i>
				<?php _e( 'Cancel', AI1WMDE_PLUGIN_NAME ); ?>
			</button>
			<button type="button" class="ai1wm-button-green" id="ai1wmde-import-file" v-if="selected_filename" v-on="click: import">
				<i class="ai1wm-icon-publish"></i>
				<?php _e( 'Import', AI1WMDE_PLUGIN_NAME ); ?>
			</button>
		</p>
	</div>
</div>
