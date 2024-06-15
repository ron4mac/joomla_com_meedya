<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.3.4
*/
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

extract($displayData);	//view,options

// Receive overridable options
$options = $options ?? [];

$noResultsText     = '';
$hideActiveFilters = false;
$showFilterButton  = false;
$showSelector      = false;
$selectorFieldName = $options['selectorFieldName'] ?? 'client_id';

// If a filter form exists.
if (!empty($view->filterForm))
{
	// Checks if a selector (e.g. client_id) exists.
	if ($selectorField = $view->filterForm->getField($selectorFieldName))
	{
		$showSelector = $selectorField->getAttribute('filtermode', '') == 'selector' ? true : $showSelector;

		// Checks if a selector should be shown in the current layout.
		if (isset($view->layout))
		{
			$showSelector = $selectorField->getAttribute('layout', 'default') != $view->layout ? false : $showSelector;
		}

		// Unset the selector field from active filters group.
		unset($view->activeFilters[$selectorFieldName]);
	}

	// Checks if the filters button should exist.
	$filters = $view->filterForm->getGroup('filter');
	$showFilterButton = isset($filters['filter_search']) && count($filters) === 1 ? false : true;

	// Checks if it should show the be hidden.
	$hideActiveFilters = empty($view->activeFilters);

	// Check if the no results message should appear.
	if (isset($view->total) && (int)$view->total === 0)
	{
		$noResults = $view->filterForm->getFieldAttribute('search', 'noresults', '', 'filter');
		if (!empty($noResults))
		{
			$noResultsText = Text::_($noResults);
		}
	}
}

// Set some basic options.
$customOptions = [
	'filtersHidden'       => $options['filtersHidden'] ?? $hideActiveFilters,
	'filterButton'        => $options['filterButton'] ?? $showFilterButton,
	'defaultLimit'        => $options['defaultLimit'] ?? Factory::getApplication()->get('list_limit', 50),
	'searchFieldSelector' => '#filter_search',
	'selectorFieldName'   => $selectorFieldName,
	'showSelector'        => $showSelector,
	'orderFieldSelector'  => '#list_fullordering',
	'showNoResults'       => !empty($noResultsText) ? true : false,
	'noResultsText'       => !empty($noResultsText) ? $noResultsText : '',
	'formSelector'        => $options['formSelector'] ?? '#adminForm',
];

// Merge custom options in the options array.
$options = array_merge($customOptions, $options);

// Add class to hide the active filters if needed.
$filtersActiveClass = $hideActiveFilters ? '' : ' js-stools-container-filters-visible';

// Load search tools
HTMLHelper::_('searchtools.form', $options['formSelector'], $options);
?>
<div class="js-stools clearfix">
	<div class="clearfix">
		<?php if ($options['showSelector']) : ?>
		<div class="js-stools-container-selector">
			<?php echo LayoutHelper::render('joomla.searchtools.default.selector', $displayData); ?>
		</div>
		<?php endif; ?>
		<div class="js-stools-container-bar">
			<?php echo $this->sublayout('bar', $displayData); ?>
		</div>
		<div class="js-stools-container-list hidden-phone hidden-tablet">
			<?php echo $this->sublayout('list', $displayData); ?>
		</div>
	</div>
	<!-- Filters div -->
	<?php if ($options['filterButton']) : ?>
	<div class="js-stools-container-filters hidden-phone clearfix<?php echo $filtersActiveClass; ?>">
		<?php echo $this->sublayout('filters', $displayData); ?>
	</div>
	<?php endif; ?>
</div>
<?php if ($options['showNoResults']) : ?>
	<?php echo $this->sublayout('noitems', ['options'=>$options]); ?>
<?php endif; ?>
