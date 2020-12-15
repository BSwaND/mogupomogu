<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2016 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
 
class PlgLogmanDJClassifieds extends ComLogmanPluginJoomla
{
	
	public function onAfterRoute() {
		
		$app = JFactory::getApplication();
		if(!$app->isAdmin()) return;
		
		// first check if there is an action to log in the user session
		$log = $app->getUserState('djcf_logman_object');
		
		if($log) {
			
			if($log->view == 'payment')
			
			// adding new row 
			$data = $this->_getCFObjectData($log->view);
			
			if($data) $this->log(array(
				'context' => 'com_djclassifieds.'.$log->view,
		        'data'    => $data,
		        'verb'    => $log->verb,
		        'event'   => $log->event
			));
			
			$app->setUserState('djcf_logman_object', null);
		}
		
		$option = $app->input->get('option');
		$id = $app->input->get('id', 0);
		$cid = $app->input->get('cid', array());
		
		if($option != 'com_djclassifieds') return;
		
		// check if there was action taken
		$tmp = explode('.', $app->input->get('task'));
		$view = $tmp[0];
		$task = @$tmp[1];
		
		switch($view) {
			case 'categories': $view = 'category'; break;
			case 'items': $view = 'item'; break;
			case 'fields': $view = 'field'; break;
			case 'regions': $view = 'region'; break;
			case 'promotions': $view = 'promotion'; break;
			case 'types': $view = 'type'; break;
			case 'points': $view = 'point'; break;
			case 'userspoints': $view = 'userspoint'; break;
			case 'profiles': $view = 'profile'; break;
			case 'payments': $view = 'payment'; break;
			case 'emails': $view = 'email'; break;
			case 'itemsunits': $view = 'itemsunit'; break;
		}
		
		$context = $option.'.'.$view;
		
		$saveEvents = array('apply', 'save', 'save2new', 'saveItem', 'save2copy');
		$stateEvents = array('publish', 'unpublish', 'archive');
		
		// handle save and edit event
		if(in_array($task, $saveEvents)) {
			
			if($id) {
				
				$data = $this->_getCFObjectData($view, $id);
				
				if($data) $this->log(array(
					'context' => $context,
		            'data'    => $data,
		            'verb'    => 'edit',
		            'event'   => 'onContentAfterSave'
				));
				
			} else {
				// in case the row is added we have to fetch it after save
				
				$log = (object) array(
		            'view' => $view,
		            'verb' => 'add',
		            'event'   => 'onContentAfterSave'
		        );
				
				$app->setUserState('djcf_logman_object', $log);
			}
			
		} else if($task == 'delete') {
			
			if($cid) {
				
				$items = $this->_getCFObjectData($view, $cid);
				
				foreach($items as $item) {
					$this->log(array(
						'context' => $context,
			            'data'    => $item,
			            'verb'    => 'delete',
			            'event'   => 'onContentAfterDelete'
					));
				}
			}

		} else if(in_array($task, $stateEvents)) {
			
			if($cid) {
				
				$items = $this->_getCFObjectData($view, $cid);
				
				foreach($items as $item) {
					$this->log(array(
						'context' => $context,
			            'data'    => $item,
			            'result'  => $task == 'archive' ? 'archived' : $task.'ed',
			            'verb'    => $task,
			            'event'   => 'onContentChangeState'
					));
				}
			}
			
		}
	}

	protected function _getCFObjectData($view, $ids = 0) {
		
		$db = JFactory::getDBO();
		$data = null;
		$name = 'name';
		$prefix = 'djcf_';
		
		// more than one item
		if(is_array($ids)) $where = ' WHERE id IN ('.implode(',',$ids).')';
		else {
			// one known item
			if($ids) $where = ' WHERE id='.(int)$ids;
			// one unknown item (after add action)
			else $where = ' ORDER BY id DESC LIMIT 1';
		}
		
		switch($view) {
			case 'category': 
				$table = 'categories';	
				break;
			case 'item': 
				$table = 'items'; 
				break;
			case 'field': 
				$table = 'fields'; 
				break;
			case 'region': 
				$table = 'regions'; 
				break;
			case 'promotion': 
				$table = 'promotions'; 
				break;
			case 'day': 
				$table = 'days';
				$name = 'days'; 
				break;
			case 'type': 
				$table = 'types'; 
				break;
			case 'point': 
				$table = 'points'; 
				break;
			case 'userspoint':
			case 'userspoints': 
				$table = 'users_points';
				$name = 'concat(user_id, " (points: ", points, ")")'; 
				break;
			case 'profile':
				$table = 'users'; 
				$prefix = '';
				break;
			case 'email':
				$table = 'emails';
				$name = 'title'; 
				break;
			case 'itemsunit':
				$table = 'items_units'; 
				break;
			default: 
				$table = ''; //$table = $view; 
				break;
		}
		
		if($table){
			$db->setQuery('SELECT id, '.$name.' as title FROM #__'.$prefix.$table.$where);
		
			if(is_array($ids)) {
				$data = $db->loadObjectList();
			} else {
				$data = $db->loadObject();
			}
		}
		
		return $data;
	}
	
	public function onAfterPaymentStatusChange($payment) {
		
		$app = JFactory::getApplication();
		if(!$app->isAdmin()) return;
		
		$this->log(array(
			'context' => 'com_djclassifieds.payment',
			'data' => array(
				'id' => $payment->id,
				'title' => $payment->id,
			),
			'verb'    => 'change',
			'result'  => $payment->status
		));
	}
	
	public function onContentAfterSave($context, $article, $isNew) {
		// will be override logging default action we need to clean this trigger
		return true;
	}
	
	function onContentAfterDelete($context, $pks) {
		// will be override logging default action we need to clean this trigger
		return true;
	}
	
	public function onContentChangeState($context, $pks, $value) {
		// will be override logging default action we need to clean this trigger
		return true;
	}
}
