<?php

namespace App\ExpressionProvider;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class DiscountCartItemProvider implements ExpressionFunctionProviderInterface
{

    public function getFunctions()
    {
        $expLanguage = new ExpressionLanguage();
        return [
            new ExpressionFunction('discountCartItem', function ($cartItems,$discount) {
            }, function (array $variables, $cartItems, $discount) use ($expLanguage) {
                foreach ($cartItems as  $cartItem){
                    $expLanguage->evaluate($discount,['cartItem' => $cartItem]);
                }
            }),
        ];
    }
}