<?php
/*
 * Copyright(c) 2015 GMO Payment Gateway, Inc. All rights reserved.
 * http://www.gmo-pg.com/
 */

namespace Plugin\GmoPaymentGateway\Controller;

use Eccube\Application;
use Plugin\GmoPaymentGateway\Service\client\PG_MULPAY_Client_Member;
use Plugin\GmoPaymentGateway\Form\Type\MyPageRegistCreditType;
use Plugin\GmoPaymentGateway\Controller\Util\PaymentUtil;
use Plugin\GmoPaymentGateway\Controller\Util\PluginUtil;
use Eccube\Common\Constant;

/*
 * Class for search card, save and delete card
 * 
 */
class MypageCardEditController
{
  public function index(Application $app)
  { 
    $objMdl =& PluginUtil::getInstance($app);
    $subData = $objMdl->getUserSettings();
    
    // if user try to access change card page by link, redirect back to my page
    if ($subData['card_regist_flg'] === 0 ||
        (!in_array($app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYID_CREDIT'], $subData['enable_payment_type']) &&
         !in_array($app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYID_TOKEN'], $subData['enable_payment_type']))) {
      return $app->redirect($app->url('mypage'));
    }

    $errorsAdd = null;

    $customer = $app['user'];
    $objCustomer = $app['orm.em']->getRepository('Eccube\Entity\Customer')->find($customer->getId());

    $OrderExtension = new \Plugin\GmoPaymentGateway\Controller\DataObj\OrderExtension();
    $OrderExtension->setCustomer($objCustomer);

    $regisCard = new MyPageRegistCreditType($app);
    $objClient = new PG_MULPAY_Client_Member($app);

    // get add from and delete form
    $addForm = $app['form.factory']->createBuilder($regisCard)->getForm();
    $delForm = $app['form.factory']->createBuilder()
      ->add('cardSeq', 'collection', array(
        'type' => 'checkbox',
        'prototype' => true,
        'allow_add' => true,
        ))
      ->getForm();
    $tokenAddForm = $app['form.factory']->createBuilder()
        ->add('token', 'hidden')
        ->add('CardSeq', 'hidden')
        ->getForm();

    // Follow style heading of eccube 3.0.6,3.0.7
    $listOldVersion = array('3.0.6', '3.0.7');
    if (in_array(Constant::VERSION, $listOldVersion)) {
      $heading = '/カード情報編集';
    }else{
      $heading = '';
    }

    // check config
    $objMdl =& PluginUtil::getInstance($app);
    $gmoSetting = $objMdl->getUserSettings();
    if(empty($gmoSetting['server_url'])){
      $cardList = array();
      $errors = array(0 => '※ 接続先サーバーURLが設定されていません。');

      return $app['view']->render('GmoPaymentGateway/View/mypage_card_management.twig', array(
      'cardList' => $cardList,
      'form' => $addForm->createView(),
      'form2' => $delForm->createView(),
      'errorsAdd' => $errorsAdd,
      'errors' => $errors,
      'heading' => $heading,  
      ));
    }

    $server_url = $gmoSetting['server_url'];
    preg_match('@^(.*://[^/]+)(.*)$@i', $server_url, $matches);
    $js_urlpath = $matches[1];
    $tshop = $gmoSetting['ShopID'];
    $isToken = $gmoSetting['credit_token'];

    $objUtil = new PaymentUtil($app);
    $const = $app['config']['GmoPaymentGateway']['const'];
    $internalCode = $const['PG_MULPAY_PAYID_TOKEN'];
    if ($isToken == 0) {
        $internalCode = $const['PG_MULPAY_PAYID_CREDIT'];
    }
    $payInfo = $objUtil->getPaymentInfoFromInternalCode($internalCode);
    $accountLockMsg = $objUtil->checkAccountLock($payInfo);
    if (!empty($accountLockMsg)) {
        return $app['view']->render('error.twig', $accountLockMsg);
    }

    // in case user add new regis credit or delete registered credit
    if ('POST' === $app['request']->getMethod()) {
      $addForm->handleRequest($app['request']);
      $delForm->handleRequest($app['request']);
      $tokenAddForm->handleRequest($app['request']);

      if ($isToken == 0) {
          if ($addForm->getData() != null) {
              if ($addForm->isValid()) {
                  $objClient2 = new PG_MULPAY_Client_Member($app);
                  $formData = $addForm->getData();
                  $errorsAdd =
                      $this->saveCard($formData, $objClient2, $OrderExtension);

                  if (empty($errorsAdd)) {
                      $app->addInfo('正常に更新されました。', 'mypage');
                      return $app->redirect
                          ($app->url('gmo_mypage_change_card'));
                  } else {
                      $payInfo = $objUtil->getPaymentInfoFromInternalCode($app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYID_CREDIT']);
                      list($isAccountLock, $msg) =
                          $objUtil->checkErrorLimit($payInfo);
                      if ($isAccountLock) {
                          return $app['view']->render('error.twig', $msg);
                      }
                  }
              }
          }
      } else {
          $formData = $tokenAddForm->getData();
          if ($formData != null && !empty($formData['token'])) {
              $objClient2 = new PG_MULPAY_Client_Member($app);
              $formData = $tokenAddForm->getData();
              $errorsAdd =
                  $this->saveCard($formData, $objClient2, $OrderExtension);

              if (empty($errorsAdd)) {
                  $app->addInfo('正常に更新されました。', 'mypage');
                  return $app->redirect($app->url('gmo_mypage_change_card'));
              } else {
                  $payInfo = $objUtil->getPaymentInfoFromInternalCode($app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYID_TOKEN']);
                  list($isAccountLock, $msg) =
                      $objUtil->checkErrorLimit($payInfo);
                  if ($isAccountLock) {
                      return $app['view']->render('error.twig', $msg);
                  }
              }
          }
      }

      if ($delForm->getData() != null) {
          // delete card
          $formData = $delForm->getData();
          if ($formData != null && $delForm->isValid()) {
              $this->delCard($formData, $objClient, $OrderExtension);

              $app->addInfo('正常に更新されました。', 'mypage');
              // $app->addError('正常に更新さ。', 'mypage');
              return $app->redirect($app->url('gmo_mypage_change_card'));
          }
      }
    }

    // search card
    if(!$objClient->getMember($OrderExtension)){
      $objClient->saveMember($OrderExtension);
      $cardList = array();
      $errors = null;
    }
    elseif($objClient->searchCard($OrderExtension, null, null, true)){
      $cardList = $this->filterCard($objClient);
      $errors = $objClient->error;
    }
    else{
      $cardList = array();
      $errors = $objClient->error;
      if ($errors[0] == '指定されたカードが存在しません。(E01-E01240002)') {
        $errors = '';
      }
    }

    $addFormView = $addForm->createView();
    // クレジットカード番号をクリアする
    $addFormView['CardNo']->vars['value'] = '';
    // 有効期限をクリアする
    $addFormView['expire_month']->vars['value'] = '';
    $addFormView['expire_year']->vars['value'] = '';
    // 名義人名をクリアする
    $addFormView['card_name1']->vars['value'] = '';

    return $app['view']->render('GmoPaymentGateway/View/mypage_card_management.twig', array(
      'cardList' => $cardList,
      'form' => $addFormView,
      'form2' => $delForm->createView(),
      'tokenForm' => $tokenAddForm->createView(),
      'errorsAdd' => $errorsAdd,
      'errors' => $errors,
      'heading' => $heading,  
      'tshop' => $tshop,
      'js_urlpath' => $js_urlpath,
      'isToken' => $isToken,
      ));
  }

  protected function saveCard($formData, $objClient, $OrderExtension){
    $formData['card_name2'] = null;
    $objClient->saveCard($OrderExtension, $formData, $formData["CardSeq"]);
    return $errorsAdd = $objClient->error;
  }

  protected function delCard($formData, $objClient, $OrderExtension){
    $a = array_keys($formData['cardSeq']);
    foreach($a as $cardSeq)
    {
      $listParam = array('CardSeq' => $cardSeq);
      $objClient->deleteCard($OrderExtension, $listParam);
    }
  }

  protected function filterCard($objClient){
    $cardList = array();
    foreach($objClient->results as $result)
    {   
      if(!$result['DeleteFlag'])
      {
        array_push($cardList, $result);
      }
    }

    for($i = 0; $i < count($cardList); $i++)
    {
      $cardList[$i]['expire_month'] = substr($cardList[$i]['Expire'], 2);
      $cardList[$i]['expire_year'] = substr($cardList[$i]['Expire'], 0, 2);
    }
    return $cardList;
  }
}