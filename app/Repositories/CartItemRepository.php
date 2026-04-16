<?php

namespace App\Repositories;

use App\Contracts\Repositories\CartItemRepositoryInterface;
use App\Models\CartItem;

class CartItemRepository implements CartItemRepositoryInterface
{
    public function create(array $data)
    {
        return CartItem::create($data);
    }

    public function getFirstWhere(array $params)
    {
        return CartItem::where($params)->first();
    }

    public function getListWhere(array $params)
    {
        return CartItem::where($params)->get();
    }

    public function delete(int $id)
    {
        return CartItem::find($id)?->delete();
    }
}