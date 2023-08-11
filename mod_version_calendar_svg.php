<?php
/**
 * @package    Joomla.CMS
 * @maintainer Llewellyn van der Merwe <https://git.vdm.dev/Llewellyn>
 *
 * @created    29th July, 2020
 * @copyright  (C) 2020 Open Source Matters, Inc. <http://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Include the helper functions only once
JLoader::register('ModVersion_Calendar_svgHelper', __DIR__ . '/helper.php');

try
{
	// Get the Helper class
	$helper = new ModVersion_Calendar_svgHelper($params);

	// set the branches
	$branches = $helper->branches();

	// set branch qty
	$qty = count($branches);

	// get the module class sfx (local)
	$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

	// load the default Tmpl
	require JModuleHelper::getLayoutPath('mod_version_calendar_svg', $params->get('layout', 'default'));
} 
catch (Exception $e) 
{
	// Output a warning message along with the exception message
	echo "Warning: " . $e->getMessage();
}
