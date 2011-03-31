--TEST--
SiTech_ConfigParser(); Load configuration with the XML file format.
--SKIPIF--
<?php
require_once('SiTech_Test.php');
if (!file_exists(dirname(__FILE__).'/test-config.xml')) die('skip the file test-config.xml does not exist.');
?>
--FILE--
<?php
require_once('SiTech_Test.php');
require_once('SiTech/ConfigParser/RawConfigParser.php');
require_once('SiTech/ConfigParser/Handler/XML.php');
try {
	$config = new SiTech\ConfigParser\RawConfigParser(array(SiTech\ConfigParser\RawConfigParser::ATTR_HANDLER => new SiTech\ConfigParser\Handler\XML));
} catch (Exception $e) {
	echo $e->getMessage(),"\n";
}

$return = $config->read(array(dirname(__FILE__).'/test-config.xml'));
if ($return[dirname(__FILE__).'/test-config.xml'] !== true) {
	var_dump($return);
} else {
	var_dump($config->get('foo', 'bar'));
}
?>
--CLEAN--
<?php
require_once('SiTech_Test.php');
$file = dirname(__FILE__).'/test-config.xml';
if (file_exists($file)) unlink($file);
?>
--EXPECT--
string(3) "baz"