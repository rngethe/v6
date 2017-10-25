<?php
/**
 * @package	HikaShop for Joomla!
 * @version	3.2.1
 * @author	hikashop.com
 * @copyright	(C) 2010-2017 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class hikashopCssType {
	var $type = 'component';

	public function load($type) {
		$this->values = array(
			JHTML::_('select.option', '',JText::_('HIKA_NONE'))
		);

		jimport('joomla.filesystem.folder');
		$regex = '^'.$type.'_([-_A-Za-z0-9]*)\.css$';
		$allCSSFiles = JFolder::files( HIKASHOP_MEDIA.'css', $regex);
		foreach($allCSSFiles as $oneFile) {
			preg_match('#'.$regex.'#i', $oneFile, $results);
			$this->values[] = JHTML::_('select.option', $results[1], $results[1]);
		}
	}

	public function display($map, $value, $type = null) {
		if($type === null)
			$type = $this->type;
		$this->load($type);

		$aStyle = empty($value) ? ' style="display:none"' : '';
		$html = JHTML::_('select.genericlist',   $this->values, $map, 'class="inputbox" size="1"', 'value', 'text', $value, $type.'_choice' );

		$config =& hikashop_config();
		$manage = hikashop_isAllowed($config->get('acl_config_manage','all'));

		if(!$manage) {
			$html .= '<a target="_blank" href="'.HIKASHOP_REDIRECT.'hikashop-styles'.'">'.hikashop_tooltip(JText::_('STYLE_TOOLTIP_TEXT'), JText::_('STYLE_TOOLTIP_TITLE'), '', JText::_('STYLE_HIKASHOP')).'</a>';
			return $html;
		}

		$popupHelper = hikashop_get('helper.popup');
		$html .= $popupHelper->display(
			'<img src="'. HIKASHOP_IMAGES.'edit.png" alt="'.JText::_('HIKA_EDIT').'"/>',
			'CSS',
			'\''.'index.php?option=com_hikashop&amp;tmpl=component&amp;ctrl=config&amp;task=css&amp;file='.$type.'_\'+document.getElementById(\''.$type.'_choice'.'\').value+\'&amp;var='.$type.'\'',
			$type.'_link',
			760,480, $aStyle, '', 'link',true
		);

		$html .= $popupHelper->display(
			'<img src="'. HIKASHOP_IMAGES.'plus.png" style="vertical-align:middle;" alt="'.JText::_('HIKA_NEW').'"/>',
			'CSS',
			hikashop_completeLink('config&task=css&var='.$type, true),
			'hikamarket_css_'.$type.'_new',
			760,480, '', '', 'link'
		);

		if(count($this->values) == 1 && $type == 'style') {
			$html .= '<a target="_blank" href="'.HIKASHOP_REDIRECT.'hikashop-styles'.'">'.hikashop_tooltip(JText::_('STYLE_TOOLTIP_TEXT'), JText::_('STYLE_TOOLTIP_TITLE'), '', JText::_('STYLE_HIKASHOP')).'</a>';
		}

		return $html;
	}
}
