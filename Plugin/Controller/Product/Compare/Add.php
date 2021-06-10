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

namespace Magepow\AjaxCompare\Plugin\Controller\Product\Compare;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Add
 * @package Magepow\AjaxCompare\Plugin\Controller\Product\Compare
 */
class Add extends \Magento\Catalog\Controller\Product\Compare\Add implements HttpPostActionInterface
{
    /**
     * @var null
     */
    protected $_coreRegistry = null;

    /**
     * @var AjaxgroupData Data
     */
    protected $_ajaxCompareHelper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonEncode;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $_resultRedirectFactory;

    /**
     * Add constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Catalog\Model\Product\Compare\ItemFactory $compareItemFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory $itemCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Visitor $customerVisitor
     * @param \Magento\Catalog\Model\Product\Compare\ListCompare $catalogProductCompareList
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param StoreManagerInterface $storeManager
     * @param Validator $formKeyValidator
     * @param PageFactory $resultPageFactory
     * @param ProductRepositoryInterface $productRepository
     * @param Registry $registry
     * @param Data $jsonEncode
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     * @param \Magepow\AjaxCompare\Helper\Data $ajaxCompareHelper
     */
    public function __construct(\Magento\Framework\App\Action\Context $context,
                                \Magento\Catalog\Model\Product\Compare\ItemFactory $compareItemFactory,
                                \Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory $itemCollectionFactory,
                                \Magento\Customer\Model\Session $customerSession,
                                \Magento\Customer\Model\Visitor $customerVisitor,
                                \Magento\Catalog\Model\Product\Compare\ListCompare $catalogProductCompareList,
                                \Magento\Catalog\Model\Session $catalogSession,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                Validator $formKeyValidator,
                                PageFactory $resultPageFactory,
                                ProductRepositoryInterface $productRepository,
                                Registry $registry,                            
                                Data $jsonEncode,
                                \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
                                \Magepow\AjaxCompare\Helper\Data $ajaxCompareHelper)
    {
        parent::__construct($context, $compareItemFactory, $itemCollectionFactory, $customerSession, $customerVisitor, $catalogProductCompareList, $catalogSession, $storeManager, $formKeyValidator, $resultPageFactory, $productRepository);
        $this->_resultRedirectFactory = $redirectFactory;
        $this->_coreRegistry = $registry;
        $this->_jsonEncode = $jsonEncode;
        $this->_ajaxCompareHelper = $ajaxCompareHelper;
    }

    /**
     * Init popup ajax compare
     *
     * @param \Magento\Catalog\Controller\Product\Compare\Add $subject
     * @param $proceed
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws NoSuchEntityException
     */
    public function aroundExecute(\Magento\Catalog\Controller\Product\Compare\Add $subject, $proceed)
    {
        $result = [];
        $params = $subject->getRequest()->getParams();
        $productId = (int)$subject->getRequest()->getParam('product');
        if (!empty($params['isCompare'])) {
            if ($productId && ($this->_customerVisitor->getId() || $this->_customerSession->isLoggedIn())) {
                $storeId = $this->_storeManager->getStore()->getId();
                try {
                    $product = $this->productRepository->getById($productId, false, $storeId);
                } catch (NoSuchEntityException $e) {
                    $product = null;
                }

                if ($product) {
                    $this->_catalogProductCompareList->addProduct($product);
                    $this->_eventManager->dispatch('catalog_product_compare_add_product', ['product' => $product]);
                    $this->_coreRegistry->register('product', $product);
                    $this->_coreRegistry->register('current_product', $product);

                    $htmlPopup = $this->_ajaxCompareHelper->getSuccessHtml();
                    $result['success'] = true;
                    $result['html_popup'] = $htmlPopup;
                    $subject->getResponse()->representJson($this->_jsonEncode->jsonEncode($result));
                }
                $this->_objectManager->get(\Magento\Catalog\Helper\Product\Compare::class)->calculate();
            }

        }
        if (!$this->_ajaxCompareHelper->isEnabledAjaxCompare()) {
           return $proceed();
        }
    }

    /**
     * @param $productId
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface
     * @throws NoSuchEntityException
     */
    protected function _initProduct($productId)
    {
        if ($productId) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                $product = $this->_productRepository->getById($productId, false, $storeId);

                return $product;
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }
}
