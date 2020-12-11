<?php
/**
 * J!Blank Template for Joomla by JBlank.pro (JBZoo.com)
 *
 * @package    JBlank
 * @author     SmetDenis <admin@jbzoo.com>
 * @copyright  Copyright (c) JBlank.pro
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 * @link       http://jblank.pro/ JBlank project page
 */

defined('_JEXEC') or die;


/**
 * Renders the pagination footer
 * @param  array $list Array containing pagination footer
 * @return string
 */
function pagination_list_footer($list)
{
    $html = array();

    $html[] = '<div class="list-footer">';
    $html[] = '<div class="limit">' . JText::_('JGLOBAL_DISPLAY_NUM') . $list['limitfield'] . '</div>';
    $html[] = $list['pageslinks'];
    $html[] = '<div class="counter">' . $list['pagescounter'] . '</div>';
    $html[] = '<input type="hidden" name="' . $list['prefix'] . 'limitstart" value="' . $list['limitstart'] . '" />';
    $html[] = '</div>';

    return implode("\n ", $html);
}

function pagination_list_render($list)
{

	$doc = JFactory::getDocument();

	// Calculate to display range of pages
	$currentPage = 1;
	$range = 1;
	$step = 5;
	foreach ($list['pages'] as $k => $page)
	{
		if (!$page['active'])
		{
			$currentPage = $k;
		}
	}
	if ($currentPage >= $step)
	{
		if ($currentPage % $step == 0)
		{
			$range = ceil($currentPage / $step) + 1;
		}
		else
		{
			$range = ceil($currentPage / $step);
		}
	}

	$html .= '<ul class="pagination-list">';
    $html .= '<li class="pagination-start">' . str_replace('?limitstart=0', '', $list['start']['data']) . '</li>';
    $html .= '<li class="pagination-prev">' . str_replace('?limitstart=0', '', $list['previous']['data']) . '</li>';

	$prev_href = null;

	preg_match('#(href=").*?(")#', $list['previous']['data'], $prev_a);

	if (count($prev_a) > 0) {
		$prev_href = str_replace(array('href="/','"'), "", $prev_a[0]);
	}
	//var_dump(JURI::base().$prev_href);


	if(isset($prev_href)) {
		$doc->addCustomTag('<link rel="prev" href="'.JURI::base().$prev_href.'">');
	}

	foreach ($list['pages'] as $k => $page)
	{
		if (in_array($k, range($range * $step - ($step + 1), $range * $step)))
		{
			if (($k % $step == 0 || $k == $range * $step - ($step + 1)) && $k != $currentPage && $k != $range * $step - $step)
			{
				$page['data'] = preg_replace('#(<a.*?>).*?(</a>)#', '$1...$2', $page['data']);
			}
		}
		
		$html .= '<li class="number">' . str_replace('?limitstart=0', '', $page['data']) . '</li>';
		
	}

	$html .= '<li class="pagination-next">' . $list['next']['data'] . '</li>';

	$next_href = null;

	preg_match('#(href=").*?(")#', $list['next']['data'], $next_a);

	if (count($next_a) > 0) {
		$next_href = str_replace(array('href="/','"'), "", $next_a[0]);
	}
	//var_dump(JURI::base().$prev_href);


	if(isset($next_href)) {
		$doc->addCustomTag('<link rel="next" href="'.JURI::base().$next_href.'">');
	}

    $html .= '<li class="pagination-end">' . $list['end']['data'] . '</li>';
    $html .= '</ul>';
	return $html;
}

/**
 * Renders an active item in the pagination block
 * @param  JPaginationObject $item The current pagination object
 * @return string
 */
function pagination_item_active(JPaginationObject $item)
{
    return '<a title="' . $item->text . '" href="' . $item->link . '" class="pagenav">' . $item->text . '</a>';
}

/**
 * Renders an inactive item in the pagination block
 * @param  JPaginationObject $item The current pagination object
 * @return string
 */
function pagination_item_inactive(JPaginationObject $item)
{
    //var_export($item);
    //die;
    return '<span class="pagenav active">' . $item->text . '</span>';
}
