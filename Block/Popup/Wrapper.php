<?php
/**
 * 
 * @category: Magepow
 * @Copyright (c) 2014 Magepow  (<https://www.magepow.com>)
 * @authors: Magepow (<magepow<support@magepow.com>>)
 * @license: <http://www.magepow.com/license-agreement>
 * @github: <https://github.com/magepow> 
 */

namespace Magepow\AjaxCompare\Block\Popup;

/**
 * Class Wrapper
 * @package Magepow\AjaxCompare\Block\Popup
 */
class Wrapper extends \Magento\Framework\View\Element\Template
{

    /**
     * Wrapper constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }


}