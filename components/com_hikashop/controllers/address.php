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
class addressController extends hikashopController
{
	public function __construct($config = array(), $skip = false) {
		parent::__construct($config, $skip);

		$this->modify_views = array('edit');
		$this->add = array('add');
		$this->modify = array('save', 'setdefault');
		$this->delete = array('delete');
	}

	public function show() {
		$tmpl = hikaInput::get()->getCmd('tmpl', '');
		if($tmpl == 'component') {
			hikaInput::get()->set('hidemainmenu', 1);
			hikaInput::get()->set('layout', 'show');
			ob_end_clean();
			parent::display();
			exit;
		}
		parent::show();
	}

	public function edit() {
		$tmpl = hikaInput::get()->getCmd('tmpl', '');
		$subtask = hikaInput::get()->getCmd('subtask', '');
		$addrtype = hikaInput::get()->getCmd('address_type', '');
		$config = hikashop_config();
		if($tmpl == 'component' && ($addrtype != '' || $subtask != '') && $config->get('checkout_address_selector', 0)) {
			hikaInput::get()->set('hidemainmenu', 1);
			hikaInput::get()->set('layout', 'show');
			hikaInput::get()->set('edition', true);
			ob_end_clean();
			if(HIKASHOP_J25){
				$app = JFactory::getApplication();
				$messages = $app->getMessageQueue();
				if(!empty($messages)){
					foreach($messages as $message){
						hikashop_display($message['message'],'error');
					}
				}
			}
			parent::display();
			exit;
		}
		parent::edit();
	}

	public function listing() {
		$user = JFactory::getUser();
		if(!$user->guest)
			return parent::listing();

		$app = JFactory::getApplication();
		$app->enqueueMessage(JText::_('PLEASE_LOGIN_FIRST'));

		global $Itemid;
		$url = (!empty($Itemid)) ? '&Itemid='.$Itemid : '';

		if(!HIKASHOP_J16) {
			$url = 'index.php?option=com_user&view=login'.$url;
		} else {
			$url = 'index.php?option=com_users&view=login'.$url;
		}
		$app->redirect(JRoute::_($url.'&return='.urlencode(base64_encode(hikashop_currentUrl('',false))),false));
		return false;
	}

	public function delete() {
		$addressdelete = hikaInput::get()->getInt('address_id', 0);
		if($addressdelete) {
			if(!HIKASHOP_J25) {
				JRequest::checkToken('request') || die('Invalid Token');
			} else {
				JSession::checkToken('request') || die('Invalid Token');
			}
			$addressClass = hikashop_get('class.address');
			$oldData = $addressClass->get($addressdelete);
			if(!empty($oldData)){
				$user_id = hikashop_loadUser();
				if($user_id==$oldData->address_user_id){
					$addressClass->delete($addressdelete);
				}
			}
		} else {
			$cid = hikashop_getCID('cid');
			$tmpl = hikaInput::get()->getCmd('tmpl', '');
			if(!empty($cid)) {
				if(!HIKASHOP_J25) {
					JRequest::checkToken('request') || die('Invalid Token');
				} else {
					JSession::checkToken('request') || die('Invalid Token');
				}
				$addressClass = hikashop_get('class.address');
				$old = $addressClass->get($cid);
				if(!empty($old)) {
					$user_id = hikashop_loadUser(false);
					if($user_id == $old->address_user_id) {
						$ret = $addressClass->delete($cid);
						if($ret && $tmpl == 'component') {
							echo '1';
							exit;
						}
					}
				}
			}
			return $this->show();
		}
		$this->listing();
	}

	function setdefault(){
		$newDefaultId = hikaInput::get()->getInt('address_default', 0);
		if($newDefaultId){
			if(!HIKASHOP_J25) {
				JRequest::checkToken('request') || die('Invalid Token');
			} else {
				JSession::checkToken('request') || die('Invalid Token');
			}
			$addressClass = hikashop_get('class.address');
			$oldData = $addressClass->get($newDefaultId);
			if(!empty($oldData)){
				$user_id = hikashop_loadUser();
				if($user_id==$oldData->address_user_id){
					$oldData->address_default = 1;
					$addressClass->save($oldData);
				}
			}
		}
		$this->listing();
	}

	public function save() {
		if(!HIKASHOP_J25) {
			JRequest::checkToken('request') || die('Invalid Token');
		} else {
			JSession::checkToken('request') || die('Invalid Token');
		}

		$app = JFactory::getApplication();
		$addressClass = hikashop_get('class.address');
		$fieldClass = hikashop_get('class.field');
		$task = hikaInput::get()->getVar('subtask', '');

		if(!empty($task)) {
			if(substr($task, -8) != '_address')
				$task .= '_address';
			$result = $addressClass->frontSaveForm($task);
			if(!empty($result) && !empty($result[$task])) {
				hikaInput::get()->set('previous_cid', @$result[$task]->previous_id);
				hikaInput::get()->set('cid', $result[$task]->id);
				return $this->show();
			}
			return $this->edit();
		}

		$oldData = null;
		$formData = hikaInput::get()->get('address', array(), 'array');
		$already = isset($formData['address_id']) ? (int)$formData['address_id'] : 0;
		unset($formData);

		if(!empty($already))
			$oldData = $addressClass->get($already);

		$addressData = $fieldClass->getInput('address', $oldData);
		$ok = true;

		if(empty($addressData)) {
			$ok = false;
		} else {
			$user_id = hikashop_loadUser();
			$addressData->address_user_id=$user_id;
			hikaInput::get()->set('fail', $addressData);
			$address_id = $addressClass->save($addressData);
		}

		if(!$ok || !$address_id) {
			$message = '';
			if(isset($addressClass->message))
				$message = 'alert(\''.addslashes($addressClass->message).'\');';

			if(!HIKASHOP_J16) {
				$app = JFactory::getApplication();
				$session = JFactory::getSession();
				$session->set('application.queue', $app->_messageQueue);
			}

			$this->edit();
			return;
		}

		global $Itemid;
		$url = (!empty($Itemid)) ? '&Itemid='.$Itemid : '';

		$redirect = hikaInput::get()->getWord('redirect','');
		if($redirect == 'checkout') {
			$makenew = hikaInput::get()->getInt('makenew');
			switch(hikaInput::get()->getVar('type')) {
				case 'shipping':
					if(hikaInput::get()->getVar('action')== 'add' && $makenew){
						$app->setUserState( HIKASHOP_COMPONENT.'.billing_address',$address_id );
					}
					$app->setUserState( HIKASHOP_COMPONENT.'.shipping_address', $address_id );
					break;
				case 'billing':
					if(hikaInput::get()->getVar('action')== 'add' && $makenew){
						$app->setUserState( HIKASHOP_COMPONENT.'.shipping_address',$address_id );
					}
					$app->setUserState( HIKASHOP_COMPONENT.'.billing_address', $address_id );
					break;
				default:
					$app->setUserState( HIKASHOP_COMPONENT.'.shipping_address',$address_id );
					$app->setUserState( HIKASHOP_COMPONENT.'.billing_address',$address_id );
					break;
			}

			$app->setUserState(HIKASHOP_COMPONENT.'.shipping_data', null);
			$app->setUserState(HIKASHOP_COMPONENT.'.shipping_method', null);
			$app->setUserState(HIKASHOP_COMPONENT.'.shipping_id', null);
			$app->setUserState(HIKASHOP_COMPONENT.'.payment_data', '');
			$app->setUserState(HIKASHOP_COMPONENT.'.payment_method', '');
			$app->setUserState(HIKASHOP_COMPONENT.'.payment_id', 0);
			if(!$already){
				$checkoutController = hikashop_get('controller.checkout');
				$cart = $checkoutController->initCart();
				$checkoutController->update_cart = true;
				if($cart->has_shipping){
					$checkoutController->before_shipping(true);
				}
				$checkoutController->before_payment(true);
			}
			$url = hikashop_completeLink('checkout&task=step&step='.hikaInput::get()->getInt('step',0).$url,false,true);
		} else {
			$url = hikashop_completeLink('address'.$url, false, true);
		}

		$tmpl = hikaInput::get()->getCmd('tmpl', '');
		if($tmpl == 'component') {
			ob_clean();
			echo '<html><head><script type="text/javascript">window.parent.location.href=\''.$url.'\';</script></head><body></body></html>';
			exit;
		}

		$app->redirect($url);
		return false;
	}
}
