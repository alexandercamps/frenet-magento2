<?php
/**
 * Frenet Shipping Gateway
 *
 * @category Frenet
 * @package Frenet\Shipping
 *
 * @author Tiago Sampaio <tiago@tiagosampaio.com>
 * @link https://github.com/tiagosampaio
 * @link https://tiagosampaio.com
 *
 * Copyright (c) 2020.
 */
declare(strict_types = 1);

namespace Frenet\Shipping\Model\Packages;

use Frenet\Shipping\Model\Quote\QuoteItemValidatorInterface;
use Frenet\Shipping\Model\Quote\ItemQuantityCalculator;
use Frenet\Shipping\Service\RateRequestProvider;
use Magento\Quote\Model\Quote\Item as QuoteItem;

/**
 * Class PackageItemDistributor
 *
 * @package Frenet\Shipping\Model\Packages
 */
class PackageItemDistributor
{
    /**
     * @var QuoteItemValidatorInterface
     */
    private $quoteItemValidator;

    /**
     * @var ItemQuantityCalculator
     */
    private $itemQuantityCalculator;

    /**
     * @var RateRequestProvider
     */
    private $rateRequestProvider;

    public function __construct(
        QuoteItemValidatorInterface $quoteItemValidator,
        ItemQuantityCalculator $itemQuantityCalculator,
        RateRequestProvider $rateRequestProvider
    ) {
        $this->quoteItemValidator = $quoteItemValidator;
        $this->itemQuantityCalculator = $itemQuantityCalculator;
        $this->rateRequestProvider = $rateRequestProvider;
    }

    /**
     * @return array
     */
    public function distribute() : array
    {
        return $this->getUnitItems();
    }

    /**
     * @return array
     */
    private function getUnitItems() : array
    {
        $rateRequest = $this->rateRequestProvider->getRateRequest();
        $unitItems = [];

        /** @var QuoteItem $item */
        foreach ($rateRequest->getAllItems() as $item) {
            if (!$this->quoteItemValidator->validate($item)) {
                continue;
            }

            $qty = $this->itemQuantityCalculator->calculate($item);

            for ($i = 1; $i <= $qty; $i++) {
                $unitItems[] = $item;
            }
        }

        return $unitItems;
    }
}
