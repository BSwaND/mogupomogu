<?php
	/**
	 * @package     Joomla.Site
	 * @subpackage  Templates.protostar
	 *
	 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
	 * @license     GNU General Public License version 2 or later; see LICENSE.txt
	 */


	defined( '_JEXEC' ) or die( 'Restricted access' );

	if (($this->error->getCode()) == '404') {
		header("HTTP/1.1 404 Not Found");
		echo file_get_contents(JURI::root() . 'page-404');
		exit;
	}
