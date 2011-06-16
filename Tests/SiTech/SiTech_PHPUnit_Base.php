<?php

/**
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
 */

/**
 * Description of SiTech_PHPUnit_Base
 *
 * @author Eric Gach <eric@php-oop.net>
 */
class SiTech_PHPUnit_Base extends PHPUnit_Framework_TestCase
{
	public static function setUpBeforeClass()
	{
		define('SITECH_TEST_BASE', dirname(dirname(__FILE__)));
		define('SITECH_TEST_FILES', SITECH_TEST_BASE.DIRECTORY_SEPARATOR.'_files');
		set_include_path(dirname(SITECH_TEST_BASE).DIRECTORY_SEPARATOR.'lib'.PATH_SEPARATOR.  get_include_path());
	}
}
