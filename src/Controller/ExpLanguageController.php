<?php

namespace App\Controller;

use App\ExpressionProvider\ArrayFilterProvider;
use App\ExpressionProvider\CountProvider;
use App\ExpressionProvider\DiscountCartItemProvider;
use App\Extend\CustomExpressionLanguage;
use App\Model\Cart;
use App\Model\CartItem;
use App\Model\CaseOneModel;
use App\Model\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExpLanguageController extends AbstractController
{
    #[Route('/case-one', name: 'case_one')]
    public function index(): Response
    {
        // https://symfony.com/doc/current/components/expression_language.html
        // https://symfony.com/doc/current/reference/formats/expression_language.html#array-operators
        $expLang = new ExpressionLanguage();
        $sumExp = "1+2";
        $inExamp = ['test1', 'test2'];
        $caseOneModel = new CaseOneModel();
        dd(
            [
                "1" => $expLang->evaluate($sumExp),
                "2" => $expLang->evaluate("'test1' in array",
                    [
                        'array' => $inExamp
                    ]),
                "3" => $expLang->evaluate("'test5' in array",
                    [
                        'array' => $inExamp
                    ]),
                "4" => $expLang->evaluate('model.getProperty()', ['model' => $caseOneModel]),
                "5" => $expLang->evaluate(
                    'model.getProperty() < x', [
                        'model' => $caseOneModel, 'x' => 5
                    ]
                ),
                "6" => $expLang->evaluate('model.getProperty() < 5 and model.getPropertyTwo() < 1', ['model' => $caseOneModel]),
                "7" => $expLang->evaluate('model.setProperty(5)', ['model' => $caseOneModel]),
                "8" => $caseOneModel
            ]
        );
        // https://symfony.com/doc/current/routing.html#routing-matching-expressions
        // https://symfony.com/doc/current/service_container/expression_language.html
        // https://symfony.com/doc/current/security/expressions.html
    }

    #[Route('/case-two', name: 'case_two')]
    public function caseTwo()
    {
        $expLang = new ExpressionLanguage(null, [
            new DiscountCartItemProvider(),
            new CountProvider(),
            new ArrayFilterProvider()
        ]);
        $products = [
            new Product(1, "Ürün-1", "CatA", "BraA", 10),
            new Product(2, "Ürün-2", "CatA", "BraB", 20),
            new Product(3, "Ürün-3", "CatB", "BraC", 30),
            new Product(4, "Ürün-4", "CatB", "BraD", 40)
        ];
        $cartItems = [
            new CartItem($products[0], 10),
            new CartItem($products[1], 10),
            new CartItem($products[2], 10),
            new CartItem($products[3], 10),
        ];
        $cart = new Cart($cartItems);
        $promotions = [
            "A_kategorisine_yuzde_50_İndirim" => [
                'condition' => [
                    'count(array_filter(cart.getCartItems(),condition)) > 0',
                    [
                        'cart' => $cart,
                        'condition' => "value.getProduct().getCategory() === 'CatA'"
                    ]
                ],
                'discount' => [
                    'discountCartItem(array_filter(cart.getCartItems(),condition),discount)',
                    [
                        'cart' => $cart,
                        'condition' => "value.getProduct().getCategory() === 'CatA'",
                        'discount' => "cartItem.setDiscount(cartItem.getQuantity() * cartItem.getProduct().getPrice() * 50 / 100)"
                    ]
                ]
            ],
            "B_kategorisine_10_lira_indrim" => [
                'condition' => [
                    'count(array_filter(cart.getCartItems(),condition)) > 0',
                    [
                        'cart' => $cart,
                        'condition' => "value.getProduct().getCategory() === 'CatB'"
                    ]
                ],
                'discount' => [
                    'discountCartItem(array_filter(cart.getCartItems(),condition),discount)',
                    [
                        'cart' => $cart,
                        'condition' => "value.getProduct().getCategory() === 'CatB'",
                        'discount' => "cartItem.setDiscount((cartItem.getProduct().getPrice() - 10) < 1 ? 0 : 10)"
                    ]
                ]
            ],
            "A_kategorisi_ve_B_markasına_10_lira_indirim" => [
                'condition' => [
                    'count(array_filter(cart.getCartItems(),condition)) > 0',
                    [
                        'cart' => $cart,
                        'condition' => "value.getProduct().getCategory() === 'CatA' and value.getProduct().getBrand() === 'BraB'"
                    ]
                ],
                'discount' => [
                    'discountCartItem(array_filter(cart.getCartItems(),condition),discount)',
                    [
                        'cart' => $cart,
                        'condition' => "value.getProduct().getCategory() === 'CatA' and value.getProduct().getBrand() === 'BraB'",
                        'discount' => "cartItem.setDiscount((cartItem.getProduct().getPrice() - 10) < 1 ? 0 : 10)"
                    ]
                ]
            ],
            "A_kategorisinden_3_ürün_alinmis_ise_sepete_100_lira_indirim" => [
                'condition' => [
                    'count(array_filter(cart.getCartItems(),condition)) > 0',
                    [
                        'cart' => $cart,
                        'condition' => "value.getProduct().getCategory() === 'CatA' && value.getQuantity() >= 3",
                    ]
                ],
                'discount' => [
                    "cart.setTotalDiscount(value)",
                    [
                        'cart' => $cart,
                        'value' => 100
                    ]
                ]
            ],
            "Urun_1_urununden_50_lira_ve_ustu_Urun_2_urununden_3_adet_alinmis_ise_100_lira_sepete_indirim" => [
                'condition' => [
                    'count(array_filter(cart.getCartItems(),conditionOne)) > 0 and count(array_filter(cart.getCartItems(),conditionTwo))>0',
                    [
                        'cart' => $cart,
                        'conditionOne' => 'value.getProduct().getName() == "Ürün-1" and value.getTotalPrice() > 50',
                        'conditionTwo' => 'value.getProduct().getName() == "Ürün-2" and value.getQuantity() == 3',
                    ]
                ],
                'discount' => [
                    "cart.setTotalDiscount(value)",
                    [
                        'cart' => $cart,
                        'value' => 100
                    ]
                ]
            ],
            "sepetteki_urun_sayisi_0_2_ise_yuzde_10_3_5_ise_yuzde_20_6_8_ise_yuzde_30_10_12_ise_40_indirim_13_15_ise_yuzde_50_indirim_sepete" => [
                'condition' => [
                    'cart.getTotalQuantity() <= 15 and cart.getTotalQuantity() >= 13 |
                    cart.getTotalQuantity() <= 12 and cart.getTotalQuantity() >= 10 |
                    cart.getTotalQuantity() <= 8 and cart.getTotalQuantity() >= 6 |
                    cart.getTotalQuantity() <= 5 and cart.getTotalQuantity() >= 3 |
                    cart.getTotalQuantity() <= 2 and cart.getTotalQuantity() >0 
                   ',
                    [
                        'cart' => $cart,
                    ]
                ],
                'discount' => [
                    'cart.getTotalQuantity() <= 15 and cart.getTotalQuantity() >= 13 ? cart.setTotalDiscount(cart.getTotalPrice() * 50 / 100):
                    cart.getTotalQuantity() <= 12 and cart.getTotalQuantity() >= 10 ? cart.setTotalDiscount(cart.getTotalPrice() * 40 / 100):
                    cart.getTotalQuantity() <= 8 and cart.getTotalQuantity() >= 6 ? cart.setTotalDiscount(cart.getTotalPrice() * 30 / 100):
                    cart.getTotalQuantity() <= 5 and cart.getTotalQuantity() >= 3 ? cart.setTotalDiscount(cart.getTotalPrice() * 20 / 100):
                    cart.getTotalQuantity() <= 2 and cart.getTotalQuantity() > 0 ? cart.setTotalDiscount(cart.getTotalPrice() * 10 / 100)
                    ',
                    [
                        'cart' => $cart,
                    ]
                ]
            ],
            'bir_urunden_2_adet_alinmis_ise_yuzde_urune_yuzde_40_veya_3_5_tane_alinmis_ise_yuzde_60_urune_indirim_uygula' => [
                'condition' => [
                    '
                    array_filter(cart.getCartItems(),conditionOne) or
                    array_filter(cart.getCartItems(),conditionTwo) 
                    ',
                    [
                        'cart' => $cart,
                        'conditionOne' => 'value.getQuantity() <= 5 and value.getQuantity() >= 3',
                        'conditionTwo' => 'value.getQuantity() <= 2 and value.getQuantity() > 0',
                    ]
                ],
                'discount' => [
                    'count(array_filter(cart.getCartItems(),conditionOne)) ? 
                    discountCartItem(array_filter(cart.getCartItems(),conditionOne),discountOne) :
                     count(array_filter(cart.getCartItems(),conditionTwo)) ? 
                     discountCartItem(array_filter(cart.getCartItems(),conditionTwo),discountTwo)
                     ',
                    [
                        'cart' => $cart,
                        'conditionOne' => 'value.getQuantity() <= 5 and value.getQuantity() >= 3',
                        'conditionTwo' => 'value.getQuantity() <= 2 and value.getQuantity() > 0',
                        'discountOne' => 'cartItem.setDiscount((cartItem.getProduct().getPrice() * 60 /100) )',
                        'discountTwo' => 'cartItem.setDiscount(cartItem.getProduct().getPrice() * 40 /100)'
                    ]
                ]
            ],
            'sepet_toplami_100_lira_ve_Urun_1_urunu_alindiginda_A_urunune_yuzde_50_indirim' => [
                'condition' => [
                    'cart.getTotalPrice() >= 100 and count(array_filter(cart.getCartItems(),condition)) > 0', [
                        'cart' => $cart,
                        'condition' => 'value.getProduct().getName() === "Ürün-1"'
                    ]
                ],
                'discount' => [
                    'discountCartItem(array_filter(cart.getCartItems(),condition),discount)', [
                        'cart' => $cart,
                        'condition' => 'value.getProduct().getName() === "Ürün-1"',
                        'discount' => 'cartItem.setDiscount(cartItem.getTotalPrice() * 50 / 100 )'
                    ]
                ]
            ],
            'sepet_bedava_patron_cildirdi'=>[
                'condition' => [
                    'cart.getTotalQuantity() >= 30',
                    [
                        'cart' => $cart
                    ]
                ],
                'discount' => [
                    'cart.setTotalDiscount(cart.getTotalPrice())',
                    [
                        'cart' => $cart
                    ]
                ]
            ]
        ];
        $discount = 0;
        $optimalPromotion = "";
        foreach ($promotions as $key => $promotion){
            $tempCart = clone $cart;
            $promotion['condition'][1]['cart']= $tempCart;
            $promotion['discount'][1]['cart']= $tempCart;
            if ($expLang->evaluate(
                $promotion['condition'][0],
                $promotion['condition'][1]
            )) {
                $expLang->evaluate(
                    $promotion['discount'][0],
                    $promotion['discount'][1]
                );
                $tempCart->calculate();
                if ($discount <= $tempCart->getTotalDiscount()){
                    $discount = $tempCart->getTotalDiscount();
                    $optimalPromotion = $key;
                }
            }
        }
        // burası açılarak tüm promosyon kullanımlarında hangisinin uygun olduğu görülebilir
       // dd($optimalPromotion,$discount);

        // $key'i $promostions içerisinde ki bir key ile değiştirerek tek tek deneyebilirsiniz.
        $key = "A_kategorisine_yuzde_50_İndirim";
        if ($expLang->evaluate(
            $promotions[$key]['condition'][0],
            $promotions[$key]['condition'][1]
        )) {
            $expLang->evaluate(
                $promotions[$key]['discount'][0],
                $promotions[$key]['discount'][1]
            );
            $cart->setAppliedPromotion($key);
        }
        dd(
            $cart->calculate()
        );

    }

    #[Route('/case-three', name: 'case_three')]
    public function caseThree()
    {
        /**
         * https://en.wikipedia.org/wiki/Abstract_syntax_tree
         * https://symfony.com/doc/current/components/expression_language.html#ast-dumping-and-editing
         * AST (Abstract Syntax Tree - Soyut Sözdizimi Ağacı) kullanarak ifadelerin derlenmesini ve analizini yapar.
         * AST, ifadelerin sözdizimlerinin soyut bir temsilidir.
         * Yani, ifadelerin sözdizimlerinin anlamlarını daha kolay anlamak ve işlemek için yapısal olarak bir araya getirir.
         *
         *            +
                    /   \
                   5     *
                        / \
                      6    7
         *
         */
        $ast = (new ExpressionLanguage())
            ->parse('1 + 6 * 7', [])
            ->getNodes();

        //dd($ast->toArray());

        $expLang = new ExpressionLanguage(null, [
            new DiscountCartItemProvider(),
            new CountProvider(),
            new ArrayFilterProvider()
        ]);

        dd($expLang->parse(
            'count(array_filter(cart.getCartItems(),condition)) > 0',
            [
                'cart', 'condition'
            ])
            ->getNodes()
            ->toArray()
        );
    }

    #[Route('/case-four', name: 'case_four')]
    public function caseFour(CustomExpressionLanguage $expressionLanguage)
    {
        /**
         * compile veya evaluate edilmiş ifadeler cachelenir ve tekrar kullanımı sağlanır
         * cache dizini içerisinde görebilirsiniz.
         */
        dd($expressionLanguage->evaluate(
            'array_filter(items,condition)',
            [
                'items' => [1, 2, 3, 4, 5],
                'condition' => 'value < 5'
            ]
        ));
    }
}
