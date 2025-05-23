<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2025 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.1
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Installer\InstallerScript;

class com_meedyaInstallerScript extends InstallerScript
{
	protected $minimumJoomla = '4.1';
	protected $com_name = 'com_meedya';

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

	/**
	 * Method to extract the name of a discreet installation sql file from the installation manifest file.
	 *
	 * @param   object  $element  The XML node to process
	 *
	 * @return  mixed  Number of queries processed or False on error
	 *
	 * @since   3.1
	 */
	public function parseSQLFiles($element)
	{
		if (!$element || !count($element->children()))
		{
			// The tag does not exist.
			return 0;
		}

		$db = & $this->_db;

		// TODO - At 4.0 we can change this to use `getServerType()` since SQL Server will not be supported
		$dbDriver = strtolower($db->name);

		if ($db->getServerType() === 'mysql')
		{
			$dbDriver = 'mysql';
		}
		elseif ($db->getServerType() === 'postgresql')
		{
			$dbDriver = 'postgresql';
		}

		$update_count = 0;

		// Get the name of the sql file to process
		foreach ($element->children() as $file)
		{
			$fCharset = strtolower($file->attributes()->charset) === 'utf8' ? 'utf8' : '';
			$fDriver  = strtolower($file->attributes()->driver);

			if ($fDriver === 'mysqli' || $fDriver === 'pdomysql')
			{
				$fDriver = 'mysql';
			}
			elseif ($fDriver === 'pgsql')
			{
				$fDriver = 'postgresql';
			}

			if ($fCharset === 'utf8' && $fDriver == $dbDriver)
			{
				$sqlfile = $this->getPath('extension_root') . '/' . trim($file);

				// Check that sql files exists before reading. Otherwise raise error for rollback
				if (!file_exists($sqlfile))
				{
					\JLog::add(\JText::sprintf('JLIB_INSTALLER_ERROR_SQL_FILENOTFOUND', $sqlfile), \JLog::WARNING, 'jerror');

					return false;
				}

				$buffer = file_get_contents($sqlfile);

				// Graceful exit and rollback if read not successful
				if ($buffer === false)
				{
					\JLog::add(\JText::_('JLIB_INSTALLER_ERROR_SQL_READBUFFER'), \JLog::WARNING, 'jerror');

					return false;
				}

				// Create an array of queries from the sql file
				$queries = \JDatabaseDriver::splitSql($buffer);

				if (count($queries) === 0)
				{
					// No queries to process
					continue;
				}

				// Process each query in the $queries array (split out of sql file).
				foreach ($queries as $query)
				{
					$db->setQuery($db->convertUtf8mb4QueryToUtf8($query));

					try
					{
						$db->execute();
					}
					catch (\JDatabaseExceptionExecuting $e)
					{
						\JLog::add(\JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $e->getMessage()), \JLog::WARNING, 'jerror');

						return false;
					}

					$update_count++;
				}
			}
		}

		return $update_count;
	}


	public function preflight ($type, $parent)
	{
		// give the parent first shot
		if (parent::preflight($type, $parent) === false) return false;

		// ensure that SQLite is active in joomla
		$dbs = JDatabaseDriver::getConnectors();
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
