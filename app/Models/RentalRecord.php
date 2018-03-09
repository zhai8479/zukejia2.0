<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 租房记录模型，记录房出租日期
 * Class RentalRecord
 * @package App\Models
 */
class RentalRecord extends Model
{
    protected $table = 'rental_records';

    public $timestamps = true;

    public $guarded = [];

    /**
     * 判断房间是否已经被出租
     * @param $apartment_id
     * @param $start_date
     * @param $end_date
     * @return bool
     */
    public static function check_room_is_rental($apartment_id, $start_date, $end_date)
    {
        return \DB::table('rental_records')
            ->where('apartment_id', $apartment_id)
            ->where(function ($query) use ($start_date, $end_date) {
                $query
                    ->where(function ($query) use ($start_date) {
                        $query->whereDate('start_date', '<=', $start_date)->whereDate('end_date', '>=', $start_date);
                    })
                    ->orWhere(function ($query) use ($end_date) {
                        $query->whereDate('start_date', '<=', $end_date)->whereDate('end_date', '>=', $end_date);
                    });
            })
            ->exists();
    }
}
