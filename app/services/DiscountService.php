<?php

namespace App\Services;

use InvalidArgumentException;

class DiscountService
{
    /**
     * Hitung harga setelah diskon
     *
     * @param float|int $price
     * @param float|int $discountPercent
     * @return float|int
     * @throws InvalidArgumentException
     */
    public function calculateDiskon($price, $discountPercent)
    {
        if ($discountPercent < 0 || $discountPercent > 100) {
            throw new InvalidArgumentException("Diskon harus antara 0-100 persen");
        }
        
        if ($price < 0) {
            throw new InvalidArgumentException("Harga tidak boleh negatif");
        }
        
        $discountAmount = $price * ($discountPercent / 100);
        return $price - $discountAmount;
    }
}