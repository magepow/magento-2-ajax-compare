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

use Magento\Framework\View\Element\Template\Context;
use Magepow\AjaxCompare\Helper\Data;


class Js extends \Magento\Framework\View\Element\Template
{

    /**
     * @var string
     */
    protected $_template = 'js/main.phtml';

    /**
     * @var Data
     */
    protected $_ajaxCompareHelper;

    /**
     * Js constructor.
     * @param Context $context
     * @param Data $ajaxCompareHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $ajaxCompareHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_ajaxCompareHelper = $ajaxCompareHelper;
    }

    /**
     * @return string
     */
    public function getAjaxCompareInitOptions()
    {
        return $this->_ajaxCompareHelper->getAjaxCompareInitOptions();
    }
}