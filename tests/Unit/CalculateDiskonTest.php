<?php

namespace Tests\Unit;

use App\Services\DiscountService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CalculateDiskonTest extends TestCase
{
    private DiscountService $discountService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->discountService = new DiscountService();
    }

    // ==================== TEST SUKSES ====================

    public function testProsesDiskonHarusBerhasilDenganInputValid()
    {
        $this->assertEquals(90000, $this->discountService->calculateDiskon(100000, 10));
        $this->assertEquals(80000, $this->discountService->calculateDiskon(100000, 20));
        $this->assertEquals(50000, $this->discountService->calculateDiskon(100000, 50));
        $this->assertEquals(100000, $this->discountService->calculateDiskon(100000, 0));
        $this->assertEquals(0, $this->discountService->calculateDiskon(100000, 100));
    }

    public function testProsesDiskonHarusBerhasilDenganDiskonDesimal()
    {
        $this->assertEquals(95000, $this->discountService->calculateDiskon(100000, 5.5));
        $this->assertEquals(87500, $this->discountService->calculateDiskon(100000, 12.5));
    }

    public function testProsesDiskonHarusBerhasilDenganHargaDesimal()
    {
        $this->assertEquals(8550, $this->discountService->calculateDiskon(9500, 10));
    }

    public function testProsesDiskonHarusMenghasilkanNolKetikaHargaNol()
    {
        $this->assertEquals(0, $this->discountService->calculateDiskon(0, 10));
        $this->assertEquals(0, $this->discountService->calculateDiskon(0, 100));
    }

    // ==================== TEST GAGAL (EXCEPTION) ====================

    public function testProsesDiskonHarusGagalKetikaDiskonNegatif()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Diskon harus antara 0-100 persen");
        
        $this->discountService->calculateDiskon(100000, -10);
    }

    public function testProsesDiskonHarusGagalKetikaDiskonLebihDariSeratus()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Diskon harus antara 0-100 persen");
        
        $this->discountService->calculateDiskon(100000, 101);
    }

    public function testProsesDiskonHarusGagalKetikaHargaNegatif()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Harga tidak boleh negatif");
        
        $this->discountService->calculateDiskon(-50000, 20);
    }

    public function testProsesDiskonHarusGagalKetikaHargaNegatifDanDiskonTidakValid()
    {
        $this->expectException(InvalidArgumentException::class);
        
        // Harga negatif akan dicek terlebih dahulu
        $this->discountService->calculateDiskon(-100000, 150);
    }

    // ==================== TEST TAMBAHAN ====================

    public function testProsesDiskonHarusBerhasilKetikaDiskonNolPersen()
    {
        $this->assertEquals(150000, $this->discountService->calculateDiskon(150000, 0));
    }

    public function testProsesDiskonHarusMenghasilkanHargaNolKetikaDiskonSeratusPersen()
    {
        $this->assertEquals(0, $this->discountService->calculateDiskon(250000, 100));
    }
}