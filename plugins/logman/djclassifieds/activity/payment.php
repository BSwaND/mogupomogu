<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2016 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Item/Menu Activity Entity
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Joomlatools\Plugin\LOGman
 */
class PlgLogmanDJClassifiedsActivityPayment extends ComLogmanModelEntityActivity
{
    protected function _initialize(KObjectConfig $config)
    {
    	//print_r($config);
        $config->append(array(
            'format'        => '<em>{actor}</em> has changed the <em>{object.type}</em> to <strong>{action}</strong> for payment with ID <strong>{object}</strong>'
        ));

        parent::_initialize($config);
    }

    protected function _objectConfig(KObjectConfig $config)
    {
    	$config->append(array(
            'type' => array('objectName' => 'Payment Status', 'object' => true)
        ));

        parent::_objectConfig($config);
    }
	
	public function getPropertyImage()
	{
	    return 'icon-cart';
	}
}