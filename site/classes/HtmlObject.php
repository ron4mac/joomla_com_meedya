<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2020 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

class HtmlElementObject
{
	protected
		$tag = '',
		$stag = '',
		$atts = [],
		$datts = false,
		$cont = [],
		$rcont = '',
		$dcont = true,
		$head = null,
		$foot = null;

	public function __construct ($tag, $cont=null, $head=null, $foot=null)
	{
		$this->tag = $tag;
		$this->stag = '<'.$tag;
		if ($cont) $this->cont[] = $cont;
		$this->head = $head;
		$this->foot = $foot;
	}

	public function setAttr ($attn, $attv=null)
	{
		if (is_array($attn)) {
			$this->atts = array_merge($this->atts, $attn);
		} else {
			if (is_array($attv)) {
				$kys = explode(',', $attn);
				foreach ($kys as $k) {
					$k = trim($k);
					$this->atts[$k] = $attv[$k];
				}
			} else
				$this->atts[$attn] = $attv;
		}
		$this->datts = true;
	}

	public function setCont ($contv)
	{
		$this->cont = [$contv];
		$this->dcont = true;
	}

	public function addCont ($contv)
	{
		$this->cont[] = $contv;
		$this->dcont = true;
	}

	public function sethead ($headv)
	{
		$this->head = $headv;
	}

	public function setFoot ($footv)
	{
		$this->foot = $footv;
	}

	public function render ()
	{
		if ($this->datts) {
			$this->stag = '<'.$this->tag;
			foreach ($this->atts as $a=>$v) {
				$this->stag .= ' '.$a.'="'.$v.'"';
			}
			$this->datts = false;
		}
		$htm = $this->stag . '>';
		$htm .= is_object($this->head) ? $this->head->render() : $this->head;
		if ($this->dcont) {
			foreach ($this->cont as $cont) {
				$this->rcont .= is_object($cont) ? $cont->render() : $cont;
			}
			$this->dcont = false;
		}
		$htm .= $this->rcont;
		$htm .= is_object($this->foot) ? $this->foot->render() : $this->foot;
		$htm .= '</'.$this->tag.'>';;
		return "\n{$htm}\n";
	}

}
