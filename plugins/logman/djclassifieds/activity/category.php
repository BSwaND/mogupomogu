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
class PlgLogmanDJClassifiedsActivityCategory extends ComLogmanModelEntityActivity
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'format'        => '<em>{actor}</em> has {action} the <em>{object.subtype} {object.type}</em> titled <strong>{object}</strong>',
            'object_table'  => 'djcf_categories',
            'object_column' => 'id')
        );

        parent::_initialize($config);
    }

    protected function _objectConfig(KObjectConfig $config)
    {
        $config->append(array(
            'subtype' => array('objectName' => 'Classifieds', 'object' => true)
        ));

        parent::_objectConfig($config);
    }
}