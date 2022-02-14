<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

$data = $displayData;

// Receive overridable options
$data['options'] = !empty($data['options']) ? $data['options'] : [];

$noResultsText     = '';
$hideActiveFilters = false;
$showFilterButton  = false;
$showSelector      = false;
$selectorFieldName = isset($data['options']['selectorFieldName']) ? $data['options']['selectorFieldName'] : 'client_id';

// If a filter form exists.
if (isset($data['view']->filterForm) && !empty($data['view']->filterForm))
{
	// Checks if a selector (e.g. client_id) exists.
	if ($selectorField = $data['view']->filterForm->getField($selectorFieldName))
	{
		$showSelector = $selectorField->getAttribute('filtermode', '') == 'selector' ? true : $showSelector;

		// Checks if a selector should be shown in the current layout.
		if (isset($data['view']->layout))
		{
			$showSelector = $selectorField->getAttribute('layout', 'default') != $data['view']->layout ? false : $showSelector;
		}

		// Unset the selector field from active filters group.
		unset($data['view']->activeFilters[$selectorFieldName]);
	}

	// Checks if the filters button should exist.
	$filters = $data['view']->filterForm->getGroup('filter');
	$showFilterButton = isset($filters['filter_search']) && count($filters) === 1 ? false : true;

	// Checks if it should show the be hidden.
	$hideActiveFilters = empty($data['view']->activeFilters);

	// Check if the no results message should appear.
	if (isset($data['view']->total) && (int) $data['view']->total === 0)
	{
		$noResults = $data['view']->filterForm->getFieldAttribute('search', 'noresults', '', 'filter');
		if (!empty($noResults))
		{
			$noResultsText = Text::_($noResults);
		}
	}
}

// Set some basic options.
$customOptions = [
	'filtersHidden'       => isset($data['options']['filtersHidden']) && $data['options']['filtersHidden'] ? $data['options']['filtersHidden'] : $hideActiveFilters,
	'filterButton'        => isset($data['options']['filterButton']) && $data['options']['filterButton'] ? $data['options']['filterButton'] : $showFilterButton,
	'defaultLimit'        => isset($data['options']['defaultLimit']) ? $data['options']['defaultLimit'] : Factory::getApplication()->get('list_limit', 50),
	'searchFieldSelector' => '#filter_search',
	'selectorFieldName'   => $selectorFieldName,
	'showSelector'        => $showSelector,
	'orderFieldSelector'  => '#list_fullordering',
	'showNoResults'       => !empty($noResultsText) ? true : false,
	'noResultsText'       => !empty($noResultsText) ? $noResultsText : '',
	'formSelector'        => !empty($data['options']['formSelector']) ? $data['options']['formSelector'] : '#adminForm',
];

// Merge custom options in the options array.
$data['options'] = array_merge($customOptions, $data['options']);

// Add class to hide the active filters if needed.
$filtersActiveClass = $hideActiveFilters ? '' : ' js-stools-container-filters-visible';

// Load search tools
HTMLHelper::_('searchtools.form', $data['options']['formSelector'], $data['options']);
?>


<div class="btn-toolbar">
			
			<div class="btn-group">
			<div class="input-group">
				

	
	<input type="text" name="filter[search]" id="filter_search" value="" class="form-control" aria-describedby="filter[search]-desc" placeholder="Search" inputmode="search">

	

								<div role="tooltip" id="filter[search]-desc">
					Search in title, alias and notes. Prefix with ID: to search for a menu item ID.				</div>
								<span class="visually-hidden">
					<label id="filter_search-lbl" for="filter_search">
	Search Menu Items</label>
				</span>
				<button type="submit" class="btn btn-primary" aria-label="Search">
					<span class="icon-search" aria-hidden="true"></span>
				</button>
			</div>
		</div>
		<div class="btn-group">
							<button type="button" class="btn btn-primary js-stools-btn-filter">
					Filter Options					<span class="icon-angle-down" aria-hidden="true"></span>
				</button>
						<button type="button" class="btn btn-primary js-stools-btn-clear" disabled="">
				Clear			</button>
		</div>
					<div class="ordering-select">
					<div class="js-stools-field-list">
				<span class="visually-hidden"><label id="list_fullordering-lbl" for="list_fullordering">
	Sort Table By:</label>
</span>
				<select id="list_fullordering" name="list[fullordering]" class="form-select" onchange="this.form.submit();">
	<option value="">Sort Table By:</option>
	<option value="a.lft ASC" selected="selected">Ordering ascending</option>
	<option value="a.lft DESC">Ordering descending</option>
	<option value="a.published ASC">Status ascending</option>
	<option value="a.published DESC">Status descending</option>
	<option value="a.title ASC">Title ascending</option>
	<option value="a.title DESC">Title descending</option>
	<option value="menutype_title ASC">Menu ascending</option>
	<option value="menutype_title DESC">Menu descending</option>
	<option value="a.home ASC">Home ascending</option>
	<option value="a.home DESC">Home descending</option>
	<option value="a.access ASC">Access ascending</option>
	<option value="a.access DESC">Access descending</option>
	<option value="a.id ASC">ID ascending</option>
	<option value="a.id DESC">ID descending</option>
</select>
			</div>
					<div class="js-stools-field-list">
				<span class="visually-hidden"><label id="list_limit-lbl" for="list_limit">
	Select number of items per page.</label>
</span>
				<select id="list_limit" name="list[limit]" class="form-select" onchange="this.form.submit();">
	<option value="5">5</option>
	<option value="10">10</option>
	<option value="15">15</option>
	<option value="20" selected="selected">20</option>
	<option value="25">25</option>
	<option value="30">30</option>
	<option value="50">50</option>
	<option value="100">100</option>
	<option value="200">200</option>
	<option value="500">500</option>
	<option value="0">All</option>
</select>
			</div>
			</div>
	<?php if ($data['options']['filterButton']) : ?>
	<div class="js-stools-container-filters hidden-phone clearfix<?php echo $filtersActiveClass; ?>">
		<?php echo $this->sublayout('filters', $data); ?>
	</div>
	<?php endif; ?>
		</div>



<!-- <div class="js-stools clearfix">
	<div class="clearfix">
		<?php if ($data['options']['showSelector']) : ?>
		<div class="js-stools-container-selector">
			<?php echo LayoutHelper::render('joomla.searchtools.default.selector', $data); ?>
		</div>
		<?php endif; ?>
		<div class="js-stools-container-bar">
			<?php echo $this->sublayout('bar', $data); ?>
		</div>
		<div class="js-stools-container-list hidden-phone hidden-tablet">
			<?php echo $this->sublayout('list', $data); ?>
		</div>
	</div>
	<?php if ($data['options']['filterButton']) : ?>
	<div class="js-stools-container-filters hidden-phone clearfix<?php echo $filtersActiveClass; ?>">
		<?php echo $this->sublayout('filters', $data); ?>
	</div>
	<?php endif; ?>
</div> -->
<?php if ($data['options']['showNoResults']) : ?>
	<?php echo $this->sublayout('noitems', $data); ?>
<?php endif; ?>
