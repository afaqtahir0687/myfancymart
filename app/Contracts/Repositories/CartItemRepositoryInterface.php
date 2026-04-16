<?php

namespace App\Contracts\Repositories;

interface CartItemRepositoryInterface
{
    /**
     * Create new cart item
     */
    public function create(array $data);

    /**
     * Get cart items by conditions
     */
    public function getFirstWhere(array $params);

    /**
     * Get all cart items by conditions
     */
    public function getListWhere(array $params);

    /**
     * Delete cart item
     */
    public function delete(int $id);
}