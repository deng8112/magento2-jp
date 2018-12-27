<?php
namespace MagentoJapan\Price\Plugin\Tax\Pricing;

use Magento\Tax\Pricing\Adjustment;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Catalog\Helper\Data;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use MagentoJapan\Price\Helper\Data as PriceHelper;

class ModifyAdjustment
{
    /**
     * @var TaxHelper
     */
    private $taxHelper;

    /**
     * @var Data
     */
    private $catalogHelper;

    /**
     * Price Helper
     *
     * @var PriceHelper
     */
    private $priceHelper;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param TaxHelper $taxHelper
     * @param Data $catalogHelper
     * @param PriceHelper $priceHelper
     * @param PriceCurrencyInterface $priceCurrency
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        TaxHelper $taxHelper,
        Data $catalogHelper,
        PriceHelper $priceHelper,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->taxHelper = $taxHelper;
        $this->catalogHelper = $catalogHelper;
        $this->priceHelper = $priceHelper;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param Adjustment $subject
     * @param \Closure $proceed
     * @param $amount
     * @param SaleableInterface $saleableItem
     * @param array $context
     * @return float
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExtractAdjustment(
        Adjustment $subject,
        \Closure $proceed,
        $amount,
        SaleableInterface $saleableItem,
        $context = []
    ) {
        $method = $this->priceHelper->getRoundMethod();
        $isRound = false;
        $currency = $this->priceCurrency->getCurrency();

        if ($this->taxHelper->priceIncludesTax()) {
            if($method != 'round' && $currency == 'JPY') {
                $isRound = true;
            }
            $adjustedAmount = $this->catalogHelper->getTaxPrice(
                $saleableItem,
                $amount,
                false,
                null,
                null,
                null,
                null,
                null,
                $isRound
            );
            $result = $amount - $adjustedAmount;
        } else {
            $result = 0.;
        }
        return $result;
    }

    /**
     * @param Adjustment $subject
     * @param \Closure $proceed
     * @param $amount
     * @param SaleableInterface $saleableItem
     * @param array $context
     * @return float
     */
    public function aroundApplyAdjustment(
        Adjustment $subject,
        \Closure $proceed,
        $amount,
        SaleableInterface $saleableItem,
        $context = []
    ) {
        $method = $this->priceHelper->getRoundMethod();
        $isRound = false;
        $currency = $this->priceCurrency->getCurrency();

        if($method != 'round' && $currency->getCode() == 'JPY') {
            $isRound = true;
        }

        return $this->catalogHelper->getTaxPrice(
            $saleableItem,
            $amount,
            true,
            null,
            null,
            null,
            null,
            null,
            $isRound
        );
    }
}
