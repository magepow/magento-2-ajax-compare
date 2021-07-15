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
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Add
 * @package Magepow\AjaxCompare\Plugin\Controller\Product\Compare
 */
class Add extends \Magento\Catalog\Controller\Product\Compare\Add
{

    /**
     * Init popup ajax compare
     *
     * @param \Magento\Catalog\Controller\Product\Compare\Add $subject
     * @param $result
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws NoSuchEntityException
     */
    public function afterExecute(\Magento\Catalog\Controller\Product\Compare\Add $subject, $result)
    {
        if ($subject->getRequest()->isAjax()) {
            $this->_view->loadLayout();
            $productId = (int)$this->getRequest()->getParam('product');
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                /** @var \Magento\Catalog\Model\Product $product */
                $product = $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                $product = null;
            }

            if ($product) {
                $this->_catalogProductCompareList->addProduct($product);
                $this->_eventManager->dispatch('catalog_product_compare_add_product', ['product' => $product]);
                $response = [];
                $popup    = $this->_view->getLayout()->createBlock('Magento\Catalog\Block\Product\AbstractProduct')
                                        ->setData('product', $product)
                                        ->setTemplate('Magepow_AjaxCompare::popup.phtml')
                                        ->toHtml();
                $response['success'] = true;
                $response['popup']   = $popup;
                $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                $resultJson->setData($response);
                $this->_objectManager->get(\Magento\Catalog\Helper\Product\Compare::class)->calculate();
                return $resultJson;
            }

            return false;
        }

        return $result;

    }

}
