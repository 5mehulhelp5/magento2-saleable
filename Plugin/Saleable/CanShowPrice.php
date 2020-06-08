<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Saleable\Plugin\Saleable;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Opengento\Saleable\Api\CanShowPriceInterface;

final class CanShowPrice
{
    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var CanShowPriceInterface
     */
    private $canShowPrice;

    public function __construct(
        HttpContext $httpContext,
        CanShowPriceInterface $canShowPrice
    ) {
        $this->httpContext = $httpContext;
        $this->canShowPrice = $canShowPrice;
    }

    public function afterGetData(Product $product, $result, $key = '')
    {
        if ($key === 'can_show_price') {
            return (bool) ($result ?? true)
                ? $this->canShowPrice->canShowPrice((int) $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP))
                : false;
        }

        return $result;
    }
}
