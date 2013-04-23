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

	protected $_pageTitle = null;
	
	/**
	 * Is intented to launch the setting of a document title earlier 
	 * to cover some third party extensions that do rendering of a 
	 * template EARLIER that onBeforeRended event.
	 * 
	 * @return void
	 * @since	1.0.4
	 */
	public function onAfterDispatch()
	{
		if (!JFactory::getApplication()->isSite() || $this->params->get('alternateEvent') != '1') {
			return;
		}
		
		$title = $this->_getPageTitle();
		if (strlen($title) > 0) {
			JFactory::getDocument()->setTitle($title);
		}
	}
	
	/**
	 * Regular handler that is intented to set a title.
	 * 
	 * @return void
	 * @since	1.0.0
	 */
	public function onBeforeRender()
	{
		if (!JFactory::getApplication()->isSite()) {
			return;
		}
		
		$title = $this->_getPageTitle();
		if (strlen($title) > 0) {
			JFactory::getDocument()->setTitle($title);
		}
	}
	
	
	/**
	 * Prepare new title for page
	 *
	 * @return	string
	 * @since	1.0.4
	 */
	protected function _getPageTitle()
	{
		if ($this->_pageTitle !== null) {
			return $this->_pageTitle;
		}
		
		$app = JFactory::getApplication();

		$menu = $app->getMenu();
		
		$pathway = $app->getPathway();
		
		$items = $pathway->getPathWay();

		$title_row = '';
		
		// Do not change startpage by default
		if (count($items) > 0 || $this->params->get('changeHome', 0) > 0) {

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
		}	

		$this->_pageTitle = $title_row;
		
		return $title_row;
	}
}
