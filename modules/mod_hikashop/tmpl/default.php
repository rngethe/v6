<?php
/**
 * @package	HikaShop for Joomla!
 * @version	3.4.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2018 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php if(!empty($html)){ ?>
<div id="hikashop_module_<?php echo $module->id;?>" class="hikashop_module <?php echo @$module->params['moduleclass_sfx']; ?>">
<?php echo $html; ?>
</div>
<?php } ?>
