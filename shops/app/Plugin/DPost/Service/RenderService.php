<?php
/*
 * Copyright(c) 2015 SYSTEM_KD
 */

namespace Plugin\DPost\Service;

use Symfony\Component\HttpFoundation\Response;

class RenderService {

    /** @var \Eccube\Application */
    public $app;

    /** @var $dom \DOMDocument */
    private $dom;


    private $xpath;

    public function __construct(\Eccube\Application $app) {

        $this->app = $app;
        $dom = array();
    }

    /**
     * RenderControl初期化
     * @param Response $response
     */
    public function initRenderControl(Response $response) {

        $html = $response->getContent();
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        $dom->encoding = "UTF-8";
        $dom->formatOutput = true;

        $this->dom = $dom;
    }

    public function filter($filter) {

        if(empty($this->dom)) {
            return null;
        }

        $this->xpath = new \DOMXPath($this->dom);

        if(!is_array($filter)) {
            $filter = array($filter);
        }

        $where = $filter[0];

        if(count($filter) > 1) {
            $index = $filter[1];
        } else {
            $index = 0;
        }

        $navElement = $this->xpath->query($where)->item($index);

        if (!$navElement instanceof \DOMElement) {
            return null;
        }

        return $navElement;
    }

    public function createNode($twigFile, $param = array()) {

        $twig = $this->app->render($twigFile, $param);

        $template = $this->dom->createDocumentFragment();
        $template->appendXML($twig);

        $node = $this->dom->importNode($template, true);

        return $node;
    }

    public function createStringToNode($content) {

        $template = $this->dom->createDocumentFragment();
        $template->appendXML($content);

        $node = $this->dom->importNode($template, true);

        return $node;
    }

    public function getContent() {
        return $this->dom->saveHTML();
    }

    public function replace($filter, $content) {

        $navElement = $this->filter($filter);

        if(empty($navElement)) {
            return false;
        }

        $node = $this->createStringToNode($content);

        $navElement->parentNode->replaceChild($node, $navElement);

        return true;
    }

    public function replaceTwig($filter, $twigFile, $params = array()) {

        $navElement = $this->filter($filter);

        if(empty($navElement)) {
            return false;
        }

        $node = $this->createNode($twigFile, $params);

        $navElement->parentNode->replaceChild($node, $navElement);

        return true;
    }

    public function insertBefore($filter, $content) {

        $navElement = $this->filter($filter);

        if(empty($navElement)) {
            return false;
        }

        $node = $this->createStringToNode($content);

        $navElement->parentNode->insertBefore($node, $navElement);

        return true;
    }

    public function insertBeforeTwig($filter, $twigFile, $params = array()) {

        $navElement = $this->filter($filter);

        if(empty($navElement)) {
            return false;
        }

        $node = $this->createNode($twigFile, $params);

        $navElement->parentNode->insertBefore($node, $navElement);

        return true;
    }

    public function insertAfter($filter, $content) {

        $navElement = $this->filter($filter);

        if(empty($navElement)) {
            return false;
        }

        $node = $this->createStringToNode($content);

        $navElement->parentNode->insertBefore($node, $navElement->nextSibling);

        return true;
    }

    public function insertAfterTwig($filter, $twigFile, $params = array()) {

        $navElement = $this->filter($filter);

        if(empty($navElement)) {
            return false;
        }

        $node = $this->createNode($twigFile, $params);

        $navElement->parentNode->insertBefore($node, $navElement->nextSibling);

        return true;
    }

    public function appendFirst($filter, $content) {

        $navElement = $this->filter($filter);

        if(empty($navElement)) {
            return false;
        }

        $node = $this->createStringToNode($content);

        $navElement->insertBefore($node, $navElement->firstChild);

        return true;
    }

    public function appendFirstTwig($filter, $twigFile, $params = array()) {

        $navElement = $this->filter($filter);

        if(empty($navElement)) {
            return false;
        }

        $node = $this->createNode($twigFile, $params);

        $navElement->insertBefore($node, $navElement->firstChild);

        return true;
    }

    public function appendChild($filter, $content) {

        $navElement = $this->filter($filter);

        if(empty($navElement)) {
            return false;
        }

        $node = $this->createStringToNode($content);

        $navElement->appendChild($node);

        return true;
    }

    public function appendChildTwig($filter, $twigFile, $params = array()) {

        $navElement = $this->filter($filter);

        if(empty($navElement)) {
            return false;
        }

        $node = $this->createNode($twigFile, $params);

        $navElement->appendChild($node);

        return true;
    }
}
