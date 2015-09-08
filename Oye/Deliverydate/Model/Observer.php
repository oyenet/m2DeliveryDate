<?php

namespace Oye\Deliverydate\Model;

class Observer
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectmanager)
    {
        $this->_objectManager = $objectmanager;
    }

    public function saveDeliveryDateToOrder($observer)
    {
        $order = $observer->getOrder();
        $quoteRepository = $this->_objectManager->create('Magento\Quote\Model\QuoteRepository');
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $quoteRepository->get($order->getQuoteId());
        $order->setDeliveryDate( $quote->getDeliveryDate() );
    }

    public function addHtmlToOrderShippingView($observer)
    {
        if($observer->getElementName() == 'order_shipping_view')
        {
            $orderShippingViewBlock = $observer->getLayout()->getBlock($observer->getElementName());
            $order = $orderShippingViewBlock->getOrder();
            $localeDate = $this->_objectManager->create('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
            $formattedDate = $localeDate->formatDate(
                $localeDate->scopeDate(
                    $order->getStore(),
                    $order->getDeliveryDate(),
                    true
                ),
                \IntlDateFormatter::MEDIUM,
                false
            );

            $deliveryDateBlock = $this->_objectManager->create('Magento\Framework\View\Element\Template');
            $deliveryDateBlock->setDeliveryDate($formattedDate);
            $deliveryDateBlock->setTemplate('Oye_Deliverydate::order_info_shipping_info.phtml');
            $html = $observer->getTransport()->getOutput() . $deliveryDateBlock->toHtml();
            $observer->getTransport()->setOutput($html);
        }
    }
}
