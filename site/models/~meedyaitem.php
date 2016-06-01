<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

no class MeedyaModelMeedyaItem extends JModelItem
{
	protected $_context = 'com_meedya.meedyaitem';

	public function populateState()
	{
		$app = JFactory::getApplication();
		$params	= $app->getParams();

		// Load the object state.
		$id	= JFactory::getApplication()->input->getInt('id');
		$this->setState('meedyaitem.id', $id);

		// Load the parameters.
		$this->setState('params', $params);
	}

	public function &getItem($id = null)
	{
		if ($this->_item === null)
		{
			$this->_item = false;

			if (empty($id)) {
				$id = $this->getState('meedyaitem.id');
			}

			// Get a level row instance.
			$table = JTable::getInstance('MeedyaItem', 'MeedyaTable');

			// Attempt to load the row.
			if ($table->load($id))
			{
				// Check published state.
				if ($published = $this->getState('filter.published'))
				{
					if ($table->state != $published) {
						return $this->_item;
					}
				}

				// Convert the JTable to a clean JObject.
				$properties = $table->getProperties(1);
				$this->_item = JArrayHelper::toObject($properties, 'JObject');
			}
			else if ($error = $table->getError()) {
				$this->setError($error);
			}
		}

		return $this->_item;
	}

	public function hit($id = null)
	{
		if (empty($id)) {
			$id = $this->getState('meedyaitem.id');
		}

		$meedyaitem = $this->getTable('MeedyaItem', 'MeedyaTable');
		return $meedyaitem->hit($id);
	}
}