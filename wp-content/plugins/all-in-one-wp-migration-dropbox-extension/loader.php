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

// include all the files that you want to load in here
require_once AI1WMDE_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-main-controller.php';

require_once AI1WMDE_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-export-controller.php';

require_once AI1WMDE_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-import-controller.php';

require_once AI1WMDE_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-settings-controller.php';

require_once AI1WMDE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-export-dropbox.php';

require_once AI1WMDE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-export-upload.php';

require_once AI1WMDE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-export-clean.php';

require_once AI1WMDE_IMPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-import-dropbox.php';

require_once AI1WMDE_IMPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-import-download.php';

require_once AI1WMDE_MODEL_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmde-settings.php';

require_once AI1WMDE_VENDOR_PATH .
			DIRECTORY_SEPARATOR .
			'dropbox-factory' .
			DIRECTORY_SEPARATOR .
			'dropbox-factory' .
			DIRECTORY_SEPARATOR .
			'lib' .
			DIRECTORY_SEPARATOR .
			'ServMaskDropboxClient.php';
