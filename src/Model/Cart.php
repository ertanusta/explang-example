<?php

namespace App\Model;

class Cart
{
    /** @var CartItem[] */
    private array $cartItems;
    private $totalPrice;
    private $totalDiscount;
    private $appliedPromotion;

    public function __construct($cartItems)
    {
        $this->cartItems = $cartItems;
        $this->calculate();
    }

    public function calculate()
    {
        $totalPrice = 0;
        $totalDiscount = $this->totalDiscount;
        foreach ($this->cartItems as $cartItem) {
            $totalPrice += $cartItem->calculateTotalPrice();
            $totalDiscount += $cartItem->getDiscount();
        }
        $this->totalPrice = $totalPrice;
        $this->totalDiscount = $totalDiscount;
        return $this;
    }

    /**
     * @return array
     */
    public function getCartItems(): array
    {
        return $this->cartItems;
    }

    /**
     * @param array $cartItems
     */
    public function setCartItems(array $cartItems): void
    {
        $this->cartItems = $cartItems;
    }

    /**
     * @return mixed
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @param mixed $totalPrice
     */
    public function setTotalPrice($totalPrice): void
    {
        $this->totalPrice = $totalPrice;
    }

    /**
     * @return mixed
     */
    public function getTotalDiscount()
    {
        return $this->totalDiscount;
    }

    /**
     * @param mixed $totalDiscount
     */
    public function setTotalDiscount($totalDiscount): void
    {
        $this->totalDiscount = $totalDiscount;
    }

    /**
     * @return mixed
     */
    public function getAppliedPromotion()
    {
        return $this->appliedPromotion;
    }

    /**
     * @param mixed $appliedPromotion
     */
    public function setAppliedPromotion($appliedPromotion): void
    {
        $this->appliedPromotion = $appliedPromotion;
    }

    public function getTotalQuantity()
    {
        $quantity = 0;
        foreach ($this->cartItems as $cartItem) {
            $quantity += $cartItem->getQuantity();
        }
        return $quantity;
    }

    public function __clone(): void {
        foreach(get_object_vars($this) as $name => $value) {
            if (is_object($value)) {
                $this->{$name} = clone $value;
            }
        }
       $newCartItems = [];
        foreach ($this->cartItems as $cartItem) {
            $newCartItems[] = clone $cartItem;
        }
        $this->cartItems = $newCartItems;
    }
}