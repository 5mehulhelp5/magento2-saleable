<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Saleable\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Opengento\Saleable\Api\CanShowPriceInterface;
use function array_filter;
use function array_map;
use function explode;
use function in_array;

final class CanShowPrice implements CanShowPriceInterface
{
    private const CONFIG_PATH_RESTRICT_SHOW_PRICE = 'catalog/price/restrict_show_price';
    private const CONFIG_PATH_CAN_SHOW_PRICE_GROUPS = 'catalog/price/can_show_price_groups';

    private ?array $allowedGroups = null;

    private ?bool $isEnabled = null;

    public function __construct(private ScopeConfigInterface $scopeConfig) {}

    public function canShowPrice(int $customerGroupId): bool
    {
        return !$this->isEnabled() || in_array($customerGroupId, $this->resolveAllowedGroups(), true);
    }

    private function isEnabled(): bool
    {
        return $this->isEnabled ??= $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_RESTRICT_SHOW_PRICE,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    private function resolveAllowedGroups(): array
    {
        return $this->allowedGroups ??= array_map('\intval', array_filter(
            explode(',', (string) $this->scopeConfig->getValue(
                self::CONFIG_PATH_CAN_SHOW_PRICE_GROUPS,
                ScopeInterface::SCOPE_WEBSITE
            ))
        ));
    }
}
