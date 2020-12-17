<?php
/**
* @version		2.0
* @package		DJ Classifieds
* @subpackage 	DJ Classifieds Component
* @copyright 	Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license 		http://www.gnu.org/licenses GNU/GPL
* @author 		url: http://design-joomla.eu
* @author 		email contact@design-joomla.eu
* @developer 	Łukasz Ciastek - lukasz.ciastek@design-joomla.eu
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
defined ('_JEXEC') or die('Restricted access');

/*Items Model*/

//jimport('joomla.application.component.model');
jimport('joomla.application.component.modellist');

class DjClassifiedsModelProfiles extends JModelList{
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'name', 'u.name',
				'username', 'u.id',
				'email', 'u.email',
				'up.u_points', 'i.u_items',
				'p.verified', 'f.attachment'		
			);
		}

		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		// List state information.
		parent::populateState('u.id', 'desc');
		$app = JFactory::getApplication();

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$verified = $this->getUserStateFromRequest($this->context.'.filter.verified', 'filter_verified');
		$this->setState('filter.verified', $verified);

		$attachment = $this->getUserStateFromRequest($this->context.'.filter.attachment', 'filter_attachment');
		$this->setState('filter.attachment', $attachment);		

		$active = $this->getUserStateFromRequest($this->context.'.filter.active', 'filter_active');
		$this->setState('filter.active', $active);		
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');		
		$id	.= ':'.$this->getState('filter.verified');	
		$id	.= ':'.$this->getState('filter.attachment');
		$id	.= ':'.$this->getState('filter.active');
		
		return parent::getStoreId($id);
	}
	
	public function _buildWhere(){		
		$app = JFactory::getApplication();
		$where= '';				

		$search = $this->getState('filter.search');		
		if (!empty($search)) {
			$db= JFactory::getDBO();
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			$where .= " AND (u.name LIKE ".$search." OR u.username LIKE ".$search." OR u.email LIKE ".$search." )";
		}				
		
		$verified = $this->getState('filter.verified');		
		if (is_numeric($verified) && $verified!='-1') {
			$where .= " AND p.verified=".$verified." ";
		}

		$attachment = $this->getState('filter.attachment');		
		if (is_numeric($attachment) && $attachment!='-1') {
			if($attachment == '1'){
				$where .= " AND f.attachment > 0 ";
			}else{
				$where .= " AND f.attachment IS NULL ";
			}
		}

		$active = $this->getState('filter.active');		
		if (is_numeric($active) && $active!='-1') {
			if($active == '1'){
				$where .= " AND u.block=0 AND (u.activation = '0' OR u.activation = '') ";
			}else{
				$where .= " AND (u.block=1 OR (u.activation != '0' AND u.activation != '')) ";
			}
		}

		return $where;
	}
	
	function getItems(){
		
			$limit = $this->getState('list.limit');
			$limitstart = $this->getState('list.start');			
			
			$orderCol	= $this->state->get('list.ordering');
			$orderDirn	= $this->state->get('list.direction');									
			
			$db= JFactory::getDBO();	
			$query = "SELECT u.*, up.u_points, i.u_items, p.verified, f.attachment FROM #__users u "
			 		/*."LEFT JOIN ( SELECT img.id, img.item_id, img.name, img.path, img.ext, img.ordering 
			 					  FROM (SELECT * FROM #__djcf_images WHERE type='profile' ORDER BY ordering) img GROUP BY img.item_id ) AS img ON img.item_id=u.id "*/
			 		."LEFT JOIN (SELECT SUM(points) as u_points, user_id 
			 					  FROM #__djcf_users_points GROUP BY user_id) up ON up.user_id=u.id "
			 		."LEFT JOIN (SELECT COUNT(i.id) as u_items, i.user_id 
								   FROM #__djcf_items i GROUP BY i.user_id) i ON i.user_id=u.id "	
					."LEFT JOIN #__djcf_profiles p ON p.user_id=u.id "		
					."LEFT JOIN (SELECT MAX(id) attachment, item_id FROM #__djcf_files WHERE type='profile' GROUP BY item_id) f ON f.item_id=u.id"				
					."  WHERE 1  ".$this->_buildWhere()." order by ".$orderCol." ".$orderDirn." ";
			$profiles = $this->_getList($query, $limitstart, $limit);
			
			
			if(count($profiles)){
				$id_list= '';
				foreach($profiles as $profile){
					$id_list .= ($id_list) ? ','.$profile->id : $profile->id;
				}

				$profiles_img = DJClassifiedsImage::getAdsImages($id_list,'profile');
			
				for($i=0;$i<count($profiles);$i++){
					$profiles[$i]->img_path='';
					$profiles[$i]->img_name='';
					$profiles[$i]->img_ext='';
					$profiles[$i]->img_ord='';
					$profiles[$i]->images = array();
					$main_img = 0;
					foreach($profiles_img as $img){
						$profiles[$i]->images[] = $img;
						if($profiles[$i]->id==$img->item_id && $main_img==0){
							$profiles[$i]->img_path=$img->path;
							$profiles[$i]->img_name=$img->name;
							$profiles[$i]->img_ext=$img->ext;
							$profiles[$i]->img_ord=$img->ordering;
							$main_img=1;
						}
					}
				}
			}
			
			//$db->setQuery($query);$items=$db->loadObjectList();echo '<pre>';print_r($db);print_r($items);die();
		
		return $profiles;
	}
	
	public function getCountItems(){
		if(empty($this->_countProfiles)) {
			$db= JFactory::getDBO();
			$query = "SELECT count(u.id) "
					."FROM #__users u "
					."LEFT JOIN #__djcf_profiles p ON u.id=p.user_id "
					."LEFT JOIN (SELECT COUNT(*) attachment, item_id FROM #__djcf_files WHERE type='profile' GROUP BY item_id) f ON f.item_id=u.id "
					."WHERE 1 ".$this->_buildWhere();
			$db->setQuery($query);
			$this->_countProfiles=$db->loadResult();
		}
		return $this->_countProfiles;
	}
	
	public function _getListQuery(){
		$query = "SELECT u.* "
				."FROM #__users u "
				."LEFT JOIN #__djcf_profiles p ON u.id=p.user_id "
				."LEFT JOIN (SELECT COUNT(*) attachment, item_id FROM #__djcf_files WHERE type='profile' GROUP BY item_id) f ON f.item_id=u.id "
				."WHERE 1 ".$this->_buildWhere();
		return $query;
	}

	function getUsergroupsSelect(){
		$db =  JFactory::getDBO();
		$query = "SELECT id as value, title as text "
		."FROM #__usergroups "
		."ORDER BY id";
		$db->setQuery($query);
		$usergroups = $db->loadObjectList();

		return $usergroups;
	}
}
?>