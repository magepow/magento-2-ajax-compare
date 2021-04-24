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

namespace Magepow\AjaxCompare\Block;

/**
 * Class Message
 * @package Magepow\AjaxCompare\Block
 */
class Message extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magepow\AjaxCompare\Helper\Data
     */
    protected $_ajaxCompareHelper;

    /**
     * Message constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magepow\AjaxCompare\Helper\Data $_ajaxCompareHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magepow\AjaxCompare\Helper\Data $_ajaxCompareHelper,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->_ajaxCompareHelper = $_ajaxCompareHelper;
    }

    /**
     * @return mixed|string
     */
    public function getMessage()
    {
        $message = $this->_ajaxCompareHelper->getConfig('magepow_ajaxcompare/general/message');
        if (!$message) {
            $message = __('You added this product to the comparison list');
        }
        return $message;
    }


}