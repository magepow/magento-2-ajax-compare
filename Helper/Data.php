<?php
/**
 * @copyright Copyright © 2021 Magepow. All rights reserved.
 * @author    Magepow
 * @category Magepow
 * @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */

namespace Magepow\AjaxCompare\Helper;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Data
 * @package Magepow\AjaxCompare\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var LayoutFactory
     */
    protected $_layoutFactory;

    /**
     * @var EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var DecoderInterface
     */
    protected $_jsonDecoder;


    /**
     * Data constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Registry $coreRegistry
     * @param CustomerSession $customerSession
     * @param LayoutFactory $layoutFactory
     * @param EncoderInterface $jsonEncoder
     * @param DecoderInterface $jsonDecoder
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Registry $coreRegistry,
        CustomerSession $customerSession,
        LayoutFactory $layoutFactory,
        EncoderInterface $jsonEncoder,
        DecoderInterface $jsonDecoder
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_customerSession = $customerSession;
        $this->_layoutFactory = $layoutFactory;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_jsonDecoder = $jsonDecoder;

    }
    
     public function getConfig($cfg='')
    {
        if($cfg) return $this->scopeConfig->getValue( $cfg, \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
        return $this->scopeConfig;
    }

    /**
     * @return string
     */
    public function getAjaxCompareInitOptions()
    {
        $options = [
            'ajaxCompare' => [
                'enabled' => $this->isEnabledAjaxCompare(),
                'ajaxCompareUrl' => $this->_getUrl('catalog/product_compare/add'),
                'popupTTL' => $this->getConfig("magepow_ajaxcompare/general/popupttl"),
                'showLoader' =>  (bool) $this->getConfig('magepow_ajaxcompare/general/loader'),
            ],
        ];

        return $this->_jsonEncoder->encode($options);
    }

    /**
     * @return bool
     */
    public function isEnabledAjaxCompare()
    {
        return (bool)$this->getConfig('magepow_ajaxcompare/general/enabled');
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSuccessHtml()
    {
        $layout = $this->_layoutFactory->create(['cacheable' => false]);
        $layout->getUpdate()->addHandle('ajaxcompare_success_message')->load();
        $layout->generateXml();
        $layout->generateElements();
        $result = $layout->getOutput();
        $layout->__destruct();
        return $result;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getErrorHtml()
    {
        $layout = $this->_layoutFactory->create(['cacheable' => false]);
        $layout->getUpdate()->addHandle('ajaxcompare_error_message')->load();
        $layout->generateXml();
        $layout->generateElements();
        $result = $layout->getOutput();
        $layout->__destruct();
        return $result;
    }

    public function getBaseUrlMedia()
    {
       return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
}
