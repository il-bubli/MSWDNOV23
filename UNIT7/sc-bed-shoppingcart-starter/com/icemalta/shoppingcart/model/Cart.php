<?php
namespace com\icemalta\shoppingcart\model;

class CartItem
{
    public Product $product;
    public int $qty;

    public function __construct(Product $product, int $qty)
    {
        $this->product = $product;
        $this->qty = $qty;
    }

    public function getSubtotal(): float
    {
        return $this->qty * $this->product->getPrice();
    }
}

class Cart
{
    public array $cartItems = [];

    public function upsertProduct(Product $product, int $qty): array
    {
        if ($qty < 1) {
            return $this->removeProduct($product->getID());
        }

        $item = null;
        foreach($this->cartItems as &$cartItem) {
            // Is the product already in the cart?
            if($cartItem->product->getId() === $product->getId()) {
                $item = &$cartItem;
            }
        }

        if($item) {
            // Update item quantity
            $item->qty = $qty;
        } else {
            // Insert new product in cart
            $this->cartItems[] = new CartItem($product, $qty);
        }

        return $this->cartItems;
    }

    public function removeProduct(int $productId): array
    {
        foreach( $this->cartItems as $key=>$cartItem) {
            if($cartItem->product->getId() === $productId)
            {
                unset($this->cartItems[$key]);
            }
        }
        return $this->cartItems;
    }

    public function getTotal() : float
    {
        $total = 0;
        foreach($this->cartItems as $cartItem) {
            $total += $cartItem->getSubtotal();
        }
        return $total;
    }

    public function getItemCount() : int
    {
        $total = 0;
        foreach($this->cartItems as $cartItem) {
            $total += $cartItem->qty;
        }
        return $total;
    }
}