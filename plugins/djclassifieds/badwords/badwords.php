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
defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.plugin.plugin' );
jimport ( 'joomla.utilities.utility' );
if(!defined("DS")){ define('DS',DIRECTORY_SEPARATOR);}

class plgDJClassifiedsBadwords extends JPlugin {
	
	public function __construct(& $subject, $config) {
		parent::__construct ( $subject, $config );
		$this->loadLanguage();
	}
	
	function onAfterDJClassifiedsSaveAdvert($row,$is_new){
		
		if($this->params->get('word_blacklist','')){
			
			$word_blacklist = explode(',', $this->params->get('word_blacklist',''));
		
			$title_splitted = explode(' ', $row->name);
			$intro_desc_splitted = explode(' ', $row->intro_desc);
			$desc_splitted = explode(' ', $row->description);
			
			$bad_word_match = self::matchBadWords($title_splitted, $word_blacklist);

			if(!$bad_word_match){
				$bad_word_match = self::matchBadWords($intro_desc_splitted, $word_blacklist);
			}			
			if(!$bad_word_match){
				$bad_word_match = self::matchBadWords($desc_splitted, $word_blacklist);
			}
			
			if($bad_word_match){
				if($row->published){
					JFactory::getApplication()->enqueueMessage(JText::_('PLG_DJCLASSIFIEDS_BADWORDS_USER_MESSAGE'), 'warning');
					$row->published = 0;
					$row->store();
				}
				DJClassifiedsNotify::notifyAdminBadWords($row);
			}
		}
	}
	
	private static function matchBadWords($text_splitted, $word_blacklist){

		foreach($text_splitted as $word){
			foreach($word_blacklist as $bl){
				$bl = trim($bl);
				if (!$bl){
					continue;
				}
				if (stripos($word, $bl) !== false){
					return true;
				}
			}
		}
		
		return false;
	}
}