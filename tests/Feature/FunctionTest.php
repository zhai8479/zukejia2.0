<?php

namespace Tests\Feature;

use App\Repositories\OrderRepository;
use App\Repositories\OrderRepositoryEloquent;
use Illuminate\Auth\Events\Logout;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FunctionTest extends TestCase
{
    public function testCountHousingNumbers()
    {
        // 短租测试
        $start = '2017-09-26';
        $end = '2017-09-26';
        $d = $this->count_housing_numbers($start, $end, 1);
        $this->assertEquals($d, 1);

        // 长租测试
        $start = '2017-9-1';
        $end = date('Y-m-d', strtotime($start . ' +61 day'));
        $this->assertEquals($this->count_housing_numbers($start, $end, 2), 2);
    }

    public function count_housing_numbers($start_date, $end_date, $rent_type)
    {
        $start =  strtotime($start_date . ' -1 day');
        $end = strtotime($end_date);
        $day = ceil(abs($end - $start)/86400);
        if ($rent_type == 2) {
            // 按月
            return intval($day / 31);
        } elseif ($rent_type == 1) {
            // 按日
            return $day;
        } else {
            throw new \Exception('rent_type 类型错误');
        }
    }
}
