<?php
/**
* @version 2.0
* @package DJ Classifieds
* @subpackage DJ Classifieds Component
* @copyright Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license http://www.gnu.org/licenses GNU/GPL
* @author url: http://design-joomla.eu
* @author email contact@design-joomla.eu
* @developer Łukasz Ciastek - lukasz.ciastek@design-joomla.eu
*
*
* DJ Classifieds is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* DJ Classifieds is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with DJ Classifieds. If not, see <http://www.gnu.org/licenses/>.
*
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.plugin.plugin');
if(!defined("DS")){ define('DS',DIRECTORY_SEPARATOR);}
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_djclassifieds'.DS.'lib'.DS.'djnotify.php');

class plgUserdjcfpoints extends JPlugin
{
	private $points_received = false;
	
	public function onUserAfterSave($user, $isnew, $success, $msg){

		$user_groups = $this->params->get('user_groups', '');

		if ($isnew){
			if($user_groups){
				$user_in_groups = JUserHelper::getUserGroups($user['id']);
				foreach ($user_groups as $group) {
					if (in_array($group, $user_in_groups)) {
						$this->_savePoints($user['id']);
						$this->points_received = true;
						break;
					}
				}
			} else {
				$this->_savePoints($user['id']);
			}
		}

		return true;

	}

	public function onAfterDJClassifiedsSaveUser($data, $user_id){

		$user_groups = $this->params->get('user_groups', '');

		if($user_groups && !$this->points_received){
			$user_in_groups = JUserHelper::getUserGroups($user_id);
			foreach ($user_groups as $group) {
				if (in_array($group, $user_in_groups)) {
					$this->_savePoints($user_id);
					break;
				}
			}
		}

		return true;

	}

	protected function _savePoints($user_id) {

		$points = $this->params->get('points', 1);
		$points_desc = $this->params->get('points_desc', '');

		if($points > 0) {
			$points_info = array();
			$points_info['value'] = $points;
			$points_info['description'] = $points_desc;
			DJClassifiedsNotify::notifyUserPoints($user_id,$points_info);
			$db = JFactory::getDBO();
			$query = "INSERT INTO #__djcf_users_points(`user_id`,`points`,`description`) VALUES ";
			$query .= "('".$user_id."','".$points."','".$points_desc."'); ";
			$db->setQuery($query);
			$db->query();
		}

	}
}