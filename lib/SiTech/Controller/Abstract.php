<?php
/**
 * SiTech/Controller/Abstract.php
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008-2009
 * @filesource
 * @package SiTech_Controller
 * @version $Id$
 */

/**
 * Description of Abstract
 *
 * @package SiTech_Controller
 */
abstract class SiTech_Controller_Abstract
{
	/**
	 * Internal use for the action of the controller.
	 *
	 * @var string Name of action for this controller.
	 */
	protected $_action;

	/**
	 * Internal argument map so the controller knows what arguments to map to
	 * what variables.
	 *
	 * @var array
	 */
	protected $_argMap = array();

	/**
	 * Arguments received by the controller. If they don't have a named key
	 * in the $_argMap array, they will only be available through the numerical
	 * index.
	 *
	 * @var array
	 */
	protected $_args;

	/**
	 * This tells if _display() has been called yet. If it hasn't then we call
	 * it automatically.
	 *
	 * @var boolean
	 */
	private $_display = false;

	/**
	 * If $_GET['xhr'] or $_POST['xhr'] is set, the request is a XHR request
	 * and this will be set to true.
	 *
	 * @var boolean
	 */
	protected $_isXHR = false;

	/**
	 * The layout to use for the current page. If it is empty, the layout will
	 * remain unused.
	 *
	 * @var string
	 */
	protected $_layout;

	/**
	 * This is a SiTech_Uri object used in the controller itself.
	 *
	 * @var SiTech_Uri
	 */
	protected $_uri;

	/**
	 *
	 * @var SiTech_Template
	 */
	protected $_view;

	public function __construct(SiTech_Uri $uri)
	{
		$this->_uri = $uri;
		
		$this->_action = $this->_uri->getAction();

		$this->_args = explode('/', $this->_uri->getPath(SiTech_Uri::FLAG_REWRITE | SiTech_Uri::FLAG_LTRIM | SiTech_Uri::FLAG_CONTROLLER | SiTech_Uri::FLAG_ACTION));

		if (isset($this->_argMap[$this->_action]) && is_array($this->_argMap[$this->_action])) {
			foreach ($this->_argMap[$this->_action] as $k => $arg) {
				if (!isset($this->_args[$k])) $this->_args[$arg] = null;
				else $this->_args[$arg] = $this->_args[$k];
			}
		}

		// TODO: How reliable is this method?
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') { // || isset($_POST['xhr']) || isset($_GET['xhr'])) {
			$this->_isXHR = true;
		}

		/**
		 * If the init() doesn't define its own view, set a generic view.
		 */
		if (empty($this->_view)) {
			$this->_view = new SiTech_Template(SITECH_APP_PATH.DIRECTORY_SEPARATOR.'views');
		}

		// Initalize the controller
		$this->init();

		/**
		 * If the action does not exist, this is the same as a 404 error (page
		 * not found) so we want to relay this to our application for a chance
		 * to handle the error.
		 */
		if (!method_exists($this, $this->_action)) {
			throw new SiTech_Exception('Method '.get_class($this).'::'.$this->_action.'() not found', null, 404);
		}

		// Call the action for the controller.
		$ret = $this->{$this->_action}();

		/**
		 * If the display has not been initated, we need to call it. It will
		 * default to using $controller/$action.tpl
		 */
		if ($this->_display !== true && $ret !== false) {
			$this->_display($this->_uri->getController().DIRECTORY_SEPARATOR.$this->_action.'.tpl');
		}
	}

	/**
	 * Initalization needed for the controller. We should never override the
	 * constructor, so this is how we initalize our controller in the application.
	 */
	protected function init()
	{
	}

	/**
	 * Built in display method to call the view's display method. This is called
	 * automatically or by our application. Either way, every action gets the
	 * view called automatically.
	 *
	 * @param string $page Template page to display.
	 */
	protected function _display($page, $type = 'text/html')
	{
		if (!empty($this->_layout)) {
			$this->_view->setLayout($this->_layout);
		}

		$this->_display = true;
		$this->_view->display($page, $type);
	}

	protected function _paging($totalRecords, $limit, $link, $current = 1)
	{
		$current = (int)$current;
		$pages = array();

		if ($current < 1) $current = 1;

		$i = 1;
		do {
			$pages[] = array(
				'current' => ($current === $i),
				'link'    => str_replace('[page]', $i, $link),
				'next'    => ((($i * $limit) >= $totalRecords)? false : $i + 1),
				'number'  => $i,
				'prev'    => (($i > 1)? true : false)
			);

		} while (($i++ * $limit) <= $totalRecords);

		return($pages);
	}
}
