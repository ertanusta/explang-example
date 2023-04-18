<?php

namespace App\ExpressionProvider;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ArrayFilterProvider implements ExpressionFunctionProviderInterface
{

    public function getFunctions()
    {
        $expLanguage = new ExpressionLanguage();
        return [
            new ExpressionFunction('array_filter', function ($array, $condition) {
            }, function ($arguments, $array, $condition) use ($expLanguage){
                return array_filter($array, function ($filtered) use ($condition,$expLanguage){
                    return $expLanguage->evaluate($condition,[
                        'value' => $filtered
                    ]);
                });
            }),
        ];
    }

}