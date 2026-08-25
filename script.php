<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2026 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.5.5
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Installer\InstallerScript;

class com_meedyaInstallerScript extends InstallerScript
{
	protected $minimumJoomla = '5.1';
	protected $com_name = 'com_meedya';
	protected $deleteFolders = [
		'components/com_meedya/controllers',
		'components/com_meedya/models',
		'components/com_meedya/views',
		'components/com_meedya/static',
		'components/com_meedya/helpers',
		'administrator/components/com_meedya/models'];
	protected $deleteFiles = [
		'components/com_meedya/controller.php',
		'components/com_meedya/controller.raw.php',
		'components/com_meedya/meedya.php',
		'components/com_meedya/helpers/imgproc.php',
		'components/com_meedya/helpers/graphicgd.php',
		'components/com_meedya/helpers/graphicim.php',
		'components/com_meedya/helpers/graphicimx.php',
		'components/com_meedya/helpers/meedya.php',
		'components/com_meedya/src/Helper/meedya.php',
		'administrator/components/com_meedya/controller.php',
		'administrator/components/com_meedya/meedya.php'];

	public function install ($parent)
	{
		$parent->getParent()->setRedirectURL('index.php?option='.$this->com_name);
	}

	public function uninstall ($parent)
	{
	}

	public function update ($parent)
	{
		Factory::getApplication()->enqueueMessage('<a href="index.php?option=com_meedya&view=groups">'.Text::_('COM_MEEDYA_UPDATE_MESSAGE').'</a>', 'warning');
	}

	public function preflight ($type, $parent)
	{
		// give the parent first shot
		if (parent::preflight($type, $parent) === false) return false;

		// ensure that SQLite is active in joomla
		$dbs = DatabaseDriver::getConnectors();
		if (!in_array('sqlite', $dbs) && !in_array('Sqlite', $dbs)) {
			Log::add('Joomla support for SQLite(3) is required for this component.', Log::WARNING, 'jerror');
			return false;
		}

		// ensure that the RJUser library is installed
		if (!class_exists('RJCreations\Library\RJUserCom',true)) {
			Log::add('The <a href="https://github.com/ron4mac/joomla_lib_rjuser" target="_blank">RJUser Library</a> is required for this component.', Log::WARNING, 'jerror');
			return false;
		}
		// and is current enough
		if (!(method_exists('RJCreations\Library\RJUserCom','Igaa'))) {
			Log::add('The installed version of <a href="https://github.com/ron4mac/joomla_lib_rjuser" target="_blank">RJUser Library</a> must be updated.', Log::WARNING, 'jerror');
			return false;
		}

		// get the version number being installed/updated
		if (method_exists($parent,'getManifest')) {
			$this->release = $parent->getManifest()->version;
		} else {
			$this->release = $parent->get('manifest')->version;
		}
	}

	public function postflight ($type, $parent)
	{
		$params['version'] = $this->release;
		$this->mySetParams($params, true);
		if ($type == 'install') {
			$params['keep_orig'] = false;
			$params['storQuota'] = 268435456;
			$params['maxUpload'] = 4194304;
			$params['image_proc'] = '';
			$params['max_width'] = 1200;
			$params['max_height'] = 1200;
			$params['thm_width'] = 120;
			$params['thm_height'] = 120;
			$params['show_version'] = true;
			$this->mySetParams($params);
		}
		if ($type === 'update') {
			$this->removeFiles();
		}
	}

	private function mySetParams ($param_array=[], $replace=false)
	{
		if (count($param_array) > 0) {
			// read the existing component value(s)
			$db = Factory::getDbo();
			$db->setQuery('SELECT params FROM #__extensions WHERE name = "'.$this->com_name.'"');
			$params = json_decode($db->loadResult(), true);
			// add the new variable(s) to the existing one(s), replacing existing only if requested
			foreach ($param_array as $name => $value) {
				if (!isset($params[(string) $name]) || $replace)
					$params[(string) $name] = (string) $value;
			}
			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode($params);
			$db->setQuery('UPDATE #__extensions SET params = ' . $db->quote($paramsString) . ' WHERE name = "'.$this->com_name.'"');
			$db->execute();
		}
	}
}
