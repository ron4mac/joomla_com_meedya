<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class JFormFieldGmkbValue extends JFormField
{
	const COMP = 'com_meedya';
	protected $type = 'GmkbValue';

	protected function getInput ()
	{
		$allowEdit = ((string) $this->element['edit'] == 'true') ? true : false;
		$allowClear = ((string) $this->element['clear'] != 'false') ? true : false;

		// Load language
		Factory::getLanguage()->load(self::COMP, JPATH_ADMINISTRATOR);

		// create the component default display
		list($cdv,$cdm) = $this->num2gmkv($this->element['compdef']);
		$mc = ['KB','MB','GB'];
		$compdef = $cdv.$mc[$cdm];

		// class='required' for client side validation
		$class = '';

		if ($this->required) {
			$class = ' class="required modal-value"';
		}

		// turn the value into GMK and number
		list($uplsiz,$uplsizm) = $this->num2gmkv($this->value?:$this->element['compdef']);

		// Setup variables for display.
		$html	= [];

		$html[] = '<input type="checkbox" id="'.$this->id.'_dchk" onclick="GMKBff.sDef(this)" '.($this->value ? '' : 'checked ').'style="vertical-align:initial" />';
		$html[] = '<label for="'.$this->id.'_dchk" style="display:inline;margin-right:1em">'.Text::_('JGLOBAL_USE_GLOBAL').'</label>';

		$html[] = '<span class="input-gmkb'.($this->value ? '' : ' hidden').'">';
		$html[] = '<input type="number" step="1" min="1" class="gkmb-num" id="' . $this->id . '_name" value="' . $uplsiz .'" onchange="GMKBff.sVal(this.parentNode)" onkeyup="GMKBff.sVal(this.parentNode)" style="width:4em;text-align:right" />';
		$html[] = '<select id="' . $this->id . '_gmkb" class="gkmb-sel" onchange="GMKBff.sVal(this.parentNode)" style="width:5em">';
		$html[] = '<option value="1024"'.($uplsizm==0?' selected="selected"':'').'>KB</option>';
		$html[] = '<option value="1048576"'.($uplsizm==1?' selected="selected"':'').'>MB</option>';
		$html[] = '<option value="1073741824"'.($uplsizm==2?' selected="selected"':'').'>GB</option>';
		$html[] = '</select>';
		$html[] = '<input type="hidden" class="gmkb-valu" id="' . $this->id . '_id"' . $class . ' name="' . $this->name . '" value="' . $this->value . '" />';
		$html[] = '</span>';

		$html[] = '<span class="gmkb-dflt'.($this->value ? ' hidden' : '').'">'.$compdef.'</span>';

		static $scripted;
		if (!$scripted) {
			$scripted = true;
			$jdoc = Factory::getDocument();
			$script = '
var GMKBff = (() => {
	return {
		sDef: (elm) => {
			let pare = elm.parentElement;
			let gkmb = pare.querySelector(".input-gmkb");
			if (elm.checked) {
				gkmb.querySelector(".gmkb-valu").value = 0;
				gkmb.classList.add("hidden");
				pare.querySelector(".gmkb-dflt").classList.remove("hidden");
			} else {
				pare.querySelector(".gmkb-dflt").classList.add("hidden");
				gkmb.classList.remove("hidden");
				GMKBff.sVal(gkmb);
			}
		},
		sVal: (elm) => {
			let valu = elm.querySelector(".gmkb-valu");
			let numb = +elm.querySelector(".gkmb-num").value;
			let shft = +elm.querySelector(".gkmb-sel").value;
			valu.value = numb * shft;
		}
	};
})();
'		;
			$jdoc->addScriptDeclaration($script);
			$jdoc->addStyleDeclaration('.gmkb-dflt { opacity:0.5;padding-top:4px }');
		}
		return implode("\n", $html);
	}

	private function num2gmkv ($num)
	{
		$parts = explode('/', $num);
		if (isset($parts[1])) {
			$num = $this->compoptv($parts[1], (int)$parts[0]);
		} else {
			$num = (int)$num;
		}

		$sizm = 0;
		if ($num) {
			if ($num % 1073741824 == 0) {
				$sizm = 2;
				$num = $num >> 30;
			} elseif ($num % 1048576 == 0) {
				$sizm = 1;
				$num = $num >> 20;
			} else {
				$num = $num >> 10;
			}
		} else {
			$num = '';
		}
		return [$num, $sizm];
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
