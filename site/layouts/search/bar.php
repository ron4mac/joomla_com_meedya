<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('JPATH_BASE') or die;

use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

extract($displayData);	//view,options

// Receive overridable options
$options = $options ?? [];

if (is_array($options))
{
	$options = new Registry($options);
}

// Options
$filterButton = $options->get('filterButton', true);
$searchButton = $options->get('searchButton', true);

$filters = $view->filterForm->getGroup('filter');
?>

<?php if (!empty($filters['filter_search'])) : ?>
	<?php if ($searchButton) : ?>
		<label for="filter_search" class="element-invisible">
			<?php if (isset($filters['filter_search']->label)) : ?>
				<?php echo Text::_($filters['filter_search']->label); ?>
			<?php else : ?>
				<?php echo Text::_('JSEARCH_FILTER'); ?>
			<?php endif; ?>
		</label>
		<div class="btn-wrapper input-append">
			<?php echo $filters['filter_search']->input; ?>
			<?php if ($filters['filter_search']->description) : ?>
				<?php JHtmlBootstrap::tooltip('#filter_search', ['title' => Text::_($filters['filter_search']->description)]); ?>
			<?php endif; ?>
			<button type="submit" class="btn hasTooltip" title="<?php echo HTMLHelper::_('tooltipText', 'JSEARCH_FILTER_SUBMIT'); ?>" aria-label="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
				<span class="icon-search" aria-hidden="true"></span>
			</button>
		</div>
		<?php if ($filterButton) : ?>
			<div class="btn-wrapper hidden-phone">
				<button type="button" class="btn hasTooltip js-stools-btn-filter" title="<?php echo HTMLHelper::_('tooltipText', 'JSEARCH_TOOLS_DESC'); ?>">
					<?php echo Text::_('JSEARCH_TOOLS');?> <span class="caret"></span>
				</button>
			</div>
		<?php endif; ?>
		<div class="btn-wrapper">
			<button type="button" class="btn hasTooltip js-stools-btn-clear" title="<?php echo HTMLHelper::_('tooltipText', 'JSEARCH_FILTER_CLEAR'); ?>">
				<?php echo Text::_('JSEARCH_FILTER_CLEAR');?>
			</button>
		</div>
	<?php endif; ?>
<?php endif;
