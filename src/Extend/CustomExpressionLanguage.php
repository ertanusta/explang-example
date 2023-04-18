<?php

namespace App\Extend;

use App\ExpressionProvider\ArrayFilterProvider;
use App\ExpressionProvider\CountProvider;
use App\ExpressionProvider\DiscountCartItemProvider;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;

class CustomExpressionLanguage extends BaseExpressionLanguage
{

    public function __construct(CacheItemPoolInterface $cacheFile, array $providers = [])
    {
        array_unshift($providers,
                new ArrayFilterProvider(),
                new CountProvider(),
                new DiscountCartItemProvider()
        );
        parent::__construct($cacheFile, $providers);
    }
}