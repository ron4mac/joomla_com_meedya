<?php
/**
 * @package    com_meedya
 *
 * @copyright  Copyright (C) 2021 RJCreations - All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

JFormHelper::loadFieldClass('number');

class JFormFieldDefNumber extends JFormFieldNumber
{
	const COMP = 'com_meedya';
	protected $type = 'DefNumber';

	protected function getInput()
	{
		$compdef = $this->element['compdef'];
		$parts = explode('/', $compdef);
		if (isset($parts[1])) {
			$compdef = $this->compoptv($parts[1], (int)$parts[0]);
		}

		$html[] = '<input type="checkbox" id="'.$this->id.'_dchk" onclick="DEFNff.sDef(this)" '.($this->value ? '' : 'checked ').'style="vertical-align:initial" />';
		$html[] = '<label for="'.$this->id.'_dchk" style="display:inline;margin-right:1em">'.Text::_('JDEFAULT').'</label>';
		$html[] = '<span id="'.$this->id.'_spn" class="mydefn'.($this->value ? '' : ' hidden').'">';
		$html[] = parent::getInput();
		$html[] = '</span>';
		$html[] = '<input type="hidden" class="defn-valu" value="'.$compdef.'" />';
		$html[] = '<span class="defn-dflt'.($this->value ? ' hidden' : '').'">'.$compdef.'</span>';

		static $scripted;
		if (!$scripted) {
			$scripted = true;
			$jdoc = Factory::getDocument();
			$script = '
var DEFNff = (function($) {
	return {
		sDef: function (elm) {
			var dflt = $(elm).siblings(".defn-dflt");
			var numi = $(elm).siblings(".mydefn").children("input");
			var ngrp = $(elm).siblings(".mydefn");
			var stor = $(elm).siblings(".defn-valu");
			if (elm.checked) {
				ngrp.addClass("hidden");
				dflt.removeClass("hidden");
				var valu = numi.val();
				numi.val(0);
				stor.val(valu);
			} else {
				var valu = stor.val();
				numi.val(valu);
				dflt.addClass("hidden");
				ngrp.removeClass("hidden");
			}
		}
	};
})(jQuery);
';
			$jdoc->addScriptDeclaration($script);
			$style = [];
			$style[] = '.defn-dflt { opacity:0.5;display:inline-block;padding-top:4px; }';
			$style[] = '.mydefn input { width:8em; }';
			$jdoc->addStyleDeclaration(implode(chr(13), $style));
		}

		return implode("\n", $html);
	}

	// get a component option value
	private function compoptv ($opt, $def)
	{
		static $opts = null;
		if (!$opts) {
			$opts = JComponentHelper::getParams(self::COMP);
		}
		$val = (int)$opts->get($opt);
		return $val ?: $def;
	}

}
