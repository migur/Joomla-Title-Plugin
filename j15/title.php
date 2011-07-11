<?php
/**
 * @version		$Id: title.php $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Migur Title Content Plugin
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 		1.5
 */
class plgContentTitle extends JPlugin
{

	private $prams = array();

	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param object $params  The object that holds the plugin parameters
	 * @since 1.5
	 */
	function plgContentTitle( &$subject, $params )
	{
		$this->params = $params;
		parent::__construct( $subject, $params );
	}


	/**
	 * Prepare new title for page
	 *
	 * Method is called by the view
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 */
	function onAfterDisplayTitle( &$article, &$params, $limitstart )
	{
		$app = JFactory::getApplication();

		$pathway =& $app->getPathway();

		$items   = $pathway->getPathWay();

		// Do not change startpage by default
		if (count($items) == 0 && !$this->params->get('changeHome', 0)) {
			return;
		}

		$count = count($items);
		for ($i = 0; $i < $count; $i ++) {
			$items[$i]->name = stripslashes(htmlspecialchars($items[$i]->name));
		}

		if ($this->params->get('showHome', 1)) {
			$item = new stdClass();
			$item->name = $this->params->get('homeText', JText::_('Home'));
			array_unshift($items, $item);
			$count += 1;
		}

		$items = array_reverse($items);

		$title_row = '';
		for ($i = 0; $i < $count; $i ++) {
			// If not the last item in the breadcrumbs add the separator
			if ($i < $count -1) {
				$title_row .= $items[$i]->name;
				$title_row .= ' ' . $this->params->get('titleDivider', '|') . ' ';
			} else {
				$title_row .= $items[$i]->name;
			}
		}

		$doc =& JFactory::getDocument();
		if (strlen($title_row) > 0) {
		    $doc->setTitle($title_row);
		}

		return '';
	}
}
