<?php
/**
 * @version		$Id: title.php $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Migur Title System Plugin
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 		1.6
 */
class plgSystemMigurtitle extends JPlugin {

	/**
	 * Prepare new title for page
	 *
	 * @param	string		The context for the content passed to the plugin.
	 * @param	object		The content object.  Note $article->text is also available
	 * @param	object		The content params
	 * @param	int			The 'page' number
	 * @return	string
	 * @since	1.7
	 */
	public function onBeforeRender()
	{
		$app = JFactory::getApplication();

		if (!$app->isSite()) {
			return;
		}
		
		$menu = $app->getMenu();
		
		$pathway = $app->getPathway();
		
		$items = $pathway->getPathWay();

		// Do not change startpage by default
		if (count($items) == 0 && !$this->params->get('changeHome', 0)) {
			return;
		}

		$count = count($items);

		$titles = array();
		
		// Check if first item of pathway is the HOMEPAGE item.
		// Joomla 3 changed behavior a little bit.
		// If we have more than 1 element in pathway then 
		// J! adds a HOMEPAGE item as the first item in pathway.
		// If we are on home page then pathway is empty.
		// Prior J! never added HOMEPAGE item into pathway.
		// So let's remove HOMEPAGE if it exists here to remain usual behavior
		// of a plugin.
		$tree = $app->getMenu()->getActive()->tree;
		//var_dump($tree, $menu->getDefault()->id);
		$i = (!empty($tree[0]) && $menu->getDefault()->id == $tree[0])? 1:0;
		for (; $i < $count; $i ++) {
			$titles[$i] = $items[$i]->name;
		}

		if ($this->params->get('showHome', 1)) {
			$title = $this->params->get('homeText', JText::_('Home'));
			array_unshift($titles, $title);
			$count += 1;
		}

		$titles = array_reverse($titles);

		$title_row = '';
		$count = count($titles);
		for ($i = 0; $i < $count; $i ++) {
			// If not the last item in the breadcrumbs add the separator
			if ($i < $count -1) {
				$title_row .= $titles[$i];
				$title_row .= ' ' . $this->params->get('titleDivider', '|') . ' ';
			} else {
				$title_row .= $titles[$i];
			}
		}

		$doc = JFactory::getDocument();
		if (strlen($title_row) > 0) {
		    $doc->setTitle($title_row);
		}

		return '';
	}
}
