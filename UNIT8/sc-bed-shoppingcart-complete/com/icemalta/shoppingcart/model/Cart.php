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
    private DBConnect $db;
    private ?int $userId;
    public int $id = 0;


    public function __construct(?int $userId = 0)
    {
        $this->userId = $userId;
        $this->db = DBConnect::getInstance();
    }

    public function __serialize(): array
    {
        return [
            'userId' => $this->userId,
            'id' => $this->Id,
            'cartItems' => $this->cartItems
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->userId = $data['userId'];
        $this->id = $data['id'];
        $this->cartItems = $data['cartItems'];
        $this->db = DBConnect::getInstance();
    }

    public function upsertProduct(Product $product, int $qty): array
    {
        if ($qty < 1) {
            return $this->removeProduct($product->getId());
        }

        $item = null;
        foreach ($this->cartItems as &$cartItem) {
            if ($cartItem->product->getId() === $product->getId()) {
                $item = &$cartItem;
            }
        }

        if ($item) {
            // Update quantity
            $item->qty = $qty === 1 ? $item->qty + 1 : $qty;
        } else {
            // Insert new product
            $this->cartItems[] = new CartItem($product, $qty);
        }
        return $this->cartItems;
    }

    public function removeProduct(int $productId): array
    {
        foreach ($this->cartItems as $key => $cartItem) {
            if ($cartItem->product->getId() === $productId) {
                unset($this->cartItems[$key]);
            }
        }

        return $this->cartItems;
    }

    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->cartItems as $cartItem) {
            $total += $cartItem->product->getPrice() * $cartItem->qty;
        }
        return $total;
    }

    public function getItemCount(): int
    {
        $total = 0;
        foreach ($this->cartItems as $cartItem) {
            $total += $cartItem->qty;
        }
        return $total;
    }

    public function save(): int
    {
        $rowsAffected = 0;
        if ($this->id === 0) {
            // Insert a new cart
            $sql = 'INSERT INTO Cart(userId) VALUES (:userId)';
            $sth = $this->db->getConnection()->prepare($sql);
            $sth->bindValue('userId', $this->userId);
            $sth->execute();
            $this->id = $this->db->getConnection()->lastInsertId();
            $rowsAffected++;
        }
        // Delete existing cart items
        $sql = 'DELETE FROM CartItem WHERE CartItem.cartId = :cartId';
        $sth = $this->db->getConnection()->prepare($sql);
        $sth->bindValue('cartId', $this->id);
        $sth->execute();
        $rowsAffected += $sth->rowCount();
        // Insert cart items
        $sql = 'INSERT INTO CartItem(cartId, productId, price, qty) VALUES (:cartId, :productId, :price, :qty)';
        $sth = $this->db->getConnection()->prepare($sql);
        $productId = null;
        $price = 0;
        $qty = 0;
        $sth->bindValue('cartId', $this->id);
        $sth->bindParam('productId', $productId);
        $sth->bindParam('price', $price);
        $sth->bindParam('qty', $qty);
        foreach ($this->cartItems as $cartItem) {
            $productId = $cartItem->product->getId();
            $price = $cartItem->product->getPrice();
            $qty = $cartItem->qty;
            $sth->execute();
            $rowsAffected += $sth->rowCount();
        }
        return $sth->rowCount();
    }
    public function load(): self
    {
        $sql = 'SELECT * FROM Cart WHERE userId = :userId AND status = :status ORDER BY id DESC';
        $sth = $this->db->getConnection()->prepare($sql);
        $sth->bindValue('userId', $this->userId);
        $sth->bindValue('status', 'PENDING');
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_OBJ);
        if (count($result) > 0) {
            $this->id = $result[0]->id;
            $sql = 'SELECT * FROM CartItem WHERE cartId = :cartId';
            $sth = $this->db->getConnection()->prepare($sql);
            $sth->bindValue('cartId', $this->id);
            $sth->execute();
            $cartItems = $sth->fetchAll(PDO::FETCH_OBJ);
            $products = Product::getAll();
            foreach ($cartItems as $cartItem) {
                $product = current(array_filter($products, fn($product) => $product->getId() == $cartItem->productId));
                $this->upsertProduct($product, $cartItem->qty);
            }
        }
        return $this;
    }
    public function checkout(): bool
    {
        $sql = 'UPDATE Cart SET status = :status WHERE id = :id';
        $sth = $this->db->getConnection()->prepare($sql);
        $sth->bindValue('status', 'CHECKOUT');
        $sth->bindValue('id', $this->id);
        $sth->execute();
        return $sth->rowCount() > 0;
    }
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }
}