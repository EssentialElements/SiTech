<?php
/**
 * Contains the file based session handler.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008
 * @filesource
 * @package SiTech
 * @subpackage SiTech_Session
 * @version $Id$
 */

/**
 * @see SiTech_Session_Handler_Interface
 */
require_once('SiTech/Session/Handler/Interface.php');

/**
 * SiTech session handler for file based session storage.
 *
 * @package SiTech_Session
 * @subpackage SiTech_Session_Handler
 */
class SiTech_Session_Handler_File implements SiTech_Session_Handler_Interface
{
	/**
	 * Folder path to save sessions in.
	 *
	 * @var string
	 */
	protected $_savePath;

	/**
	 * Close the session.
	 *
	 * @return bool
	 */
	public function close ()
	{
		return(true);
	}

	/**
	 * Delete the session entierly.
	 *
	 * @param string $id
	 * @return bool
	 */
	public function destroy ($id)
	{
		$file = 'sess_'.$id;
		$file = realpath($this->_savePath.DIRECTORY_SEPARATOR.$file);
		return(@unlink($file));
	}

	/**
	 * Do garbage cleanup.
	 *
	 * @return bool
	 */
	public function gc ($maxLife)
	{
		foreach (glob($this->_savePath.DIRECTORY_SEPARATOR.'sess_*') as $file) {
			if (filemtime($this->_savePath.DIRECTORY_SEPARATOR.$file) + $maxLife < time()) {
				if (($fp = @fopen($this->_savePath.DIRECTORY_SEPARATOR.$file)) !== false) {
					$r = trim(@fgets($fp, 4));
					@fclose($fp);
					if ($r == '0') {
						@unlink($this->_savePath.DIRECTORY_SEPARATOR.$file);
					}
				}
			}
		}

		return(true);
	}

	/**
	 * Open the session.
	 *
	 * @param string $path
	 * @param string $name
	 * @return bool
	 */
	public function open ($path, $name)
	{
		$this->_savePath = $path;
		$_SESSION->setAttribute(SiTech_Session::ATTR_NAME, $name);
		return(true);
	}

	/**
	 * Read the session information.
	 *
	 * @param string $id
	 * @return string
	 */
	public function read ($id)
	{
		$file = 'sess_'.$id;
		$file = realpath($this->_savePath.DIRECTORY_SEPARATOR.$file);

		if (file_exists($file)) {
			$data = @file_get_contents($file);
			list($r, $s, $data) = explode("\n", $data, 3);

			$_SESSION->setAttribute(SiTech_Session::ATTR_REMEMBER, (bool)$r);
			$_SESSION->setAttribute(SiTech_Session::ATTR_STRICT, (bool)$s);

			return((string)$data);
		} else {
			return('');
		}
	}

	/**
	 * Write the session information.
	 *
	 * @param string $id
	 * @param string $data
	 * @return bool
	 */
	public function write ($id, $data)
	{
		$file = 'sess_'.$id;
		$file = realpath($this->_savePath.DIRECTORY_SEPARATOR.$file);

		$data = sprintf("%d\n%d\n%s", $_SESSION->getAttribute(SiTech_Session::ATTR_REMEMBER), $_SESSION->getAttribute(SiTech_Session::ATTR_STRICT), $data);

		if (is_writeable($file)) {
			@file_put_contents($file, $data);
			return(true);
		} else {
			return(false);
		}
	}
}
