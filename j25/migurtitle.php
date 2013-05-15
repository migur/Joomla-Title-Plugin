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

		// We don't want to do something in admin area 
		if ($app->isAdmin()) {
			return;
		}
		
		$menu = $app->getMenu();
		
		$pathway = $app->getPathway();
		
		$items = $pathway->getPathWay();

		$title_row = '';
		
		$count = count($items);

		$changeHome = $this->params->get('changeHome', 0);
		
		// Do not change startpage by default
		if ($count == 0 && !$changeHome) {
			return;
		}
		
		$titles = array();
		for ($i = 0; $i < $count; $i ++) {
			$titles[$i] = $items[$i]->name;
		}

		// If we need to show home text always or this is a homepage and we have to change it
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

		$this->_pageTitle = $title_row;
		
		return $title_row;
	}
}
