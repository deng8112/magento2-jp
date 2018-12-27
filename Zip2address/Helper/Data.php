<?php

namespace MagentoJapan\Zip2address\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $localeResolver
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Locale\ResolverInterface $localeResolver
    ) {
        parent::__construct($context);
        $this->localeResolver = $localeResolver;
    }

    /**
     * Return current locale code.
     *
     * @return string
     */
    public function getCurrentLocale()
    {
        if ($this->localeResolver->getLocale() == 'ja_JP') {
            return 'ja';
        }

        return 'en';
    }
}
