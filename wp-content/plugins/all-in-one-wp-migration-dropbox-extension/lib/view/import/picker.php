<div id="ai1wmde-import-modal" class="ai1wmde-modal ai1wm-not-visible">
	<div class="ai1wm-modal-content-middle">
		<span class="ai1wm-loader {{files !== false ? 'ai1wmde-hide' : ''}}"></span>
		<div class="ai1wmde-file-browser {{files !== false ? '' : 'ai1wmde-hide'}}">
			<span class="ai1wmde-path">
				<i class="ai1wm-icon-folder"></i>
				<span id="ai1wmde-download-path">{{path}}</span>
			</span>
			<ul class="ai1wmde-file-list">
				<li v-repeat="files" v-on="click: browse(this)" class="ai1wmde-file-item {{icon | icon}}">
					<span class="ai1wmde-filename" v-text="path | path"></span>
					<span class="ai1wmde-filesize {{is_dir ? 'ai1wmde-hide' : ''}}">{{size}}</span>
				</li>
			</ul>
			<p class="{{files.length > 0 ? 'ai1wmde-hide' : ''}}">No files or directories</p>
		</div>
	</div>

	<div class="ai1wm-modal-action">
		<p>
			<span class="ai1wmde-contact-dropbox {{files !== false ? 'ai1wmde-hide' : ''}}">
				<?php _e( 'Connecting to Dropbox ...', AI1WMDE_PLUGIN_NAME ); ?>
			</span>
			<br />
			<br />
			<span class="{{selected_file ? '' : 'ai1wmde-hide'}}">
				<span id="ai1wmde-download-file" class="ai1wmde-selected-file ai1wm-icon-file-zip" v-if="selected_file_name" v-animation>{{selected_file_name}}</span>
				<br />
				<br />
			</span>
			<button class="ai1wm-button-red" id="ai1wmde-import-file-cancel" v-on="click: cancel">
				<i class="ai1wm-icon-notification"></i>
				<?php _e( 'Cancel', AI1WMDE_PLUGIN_NAME ); ?>
			</button>
			<button class="ai1wm-button-green" id="ai1wmde-import-file" v-if="selected_file" v-on="click: import">
				<i class="ai1wm-icon-publish"></i>
				<?php _e( 'Import', AI1WMDE_PLUGIN_NAME ); ?>
			</button>
		</p>
	</div>
</div>
