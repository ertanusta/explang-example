<?php

namespace App\ExpressionProvider;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class CountProvider implements ExpressionFunctionProviderInterface
{

    public function getFunctions()
    {
        return [
            new ExpressionFunction('count', function ($array) {
            }, function ($arguments, $array) {
                return count($array);
            }),
        ];
    }

}