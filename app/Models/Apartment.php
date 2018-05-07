<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use App\Models\City;
class Apartment extends Model implements Transformable
{
    use TransformableTrait;
    use SoftDeletes;

    protected $fillable = ['user_id']; 

    protected $dates = ['deleted_at'];

    protected $hidden = ['keyword'];

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'apartment';

    /**
     * 房源 status 允许的值
     * @var array
     */
    protected $status_list = [
        1 => '热销中',
        2 => '准备中',
        3 => '预租中',
        4 => '出租中'
    ];

    /**
     * 房源 status 允许的值
     * @var array
     */
    protected $status_value = [
        1 => ['热销中',''],
        2 => ['准备中','https://static.zukehouse.com/images/zbz.jpg'],
        3 => ['预租中','https://static.zukehouse.com/images/yzz.jpg'],
        4 => ['出租中','https://static.zukehouse.com/images/czz.jpg']
    ];

    /**
     * 房源 type 允许的值
     * @var array
     */
    protected $type_value = [
        1 => '整体出租',
        2 => '独立单间',
        3 => '合租房屋',
        4 => '酒店式公寓'
    ];

    /**
     * 房源 rental_type 允许的值
     * @var array
     */
    protected $rental_type_val = [
        0 => '短租',
        1 => '长租',
        2 => '特价'
    ];

    /**
     * 判断是否在欲租中
     * @return boolean
     */
    public function is_in_maintenance()
    {
        return $this->status == 2?true:false;
    }
    
    public function setBathroomUtilsAttribute($value)
    {
        $this->attributes['bathroom_utils'] = implode(',', $value);
    }

    public function setElectricsAttribute($value)
    {
        $this->attributes['electrics'] = implode(',', $value);
    }

    public function setBedAttribute($value)
    {
        $this->attributes['bed'] = implode(',', $value);
    }

    public function setKitchenUtilsAttribute($value)
    {
        $this->attributes['kitchen_utils'] = implode(',', $value);
    }

    public function setFacilitiesAttribute($value)
    {
        $this->attributes['facilities'] = implode(',', $value);
    }

    public function setRequiresAttribute($value)
    {
        $this->attributes['requires'] = implode(',', $value);
    }

    public function setImagesAttribute($images)
    {
        if (is_array($images)) {
            $this->attributes['images'] = json_encode($images);
        }
    }

    public function getImagesAttribute($images)
    {
        return json_decode($images, true);
    }

    public function setSingleBedAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['single_bed'] = json_encode($value);
        }
    }

    public function setDoubleBedAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['double_bed'] = json_encode($value);
        }
    }

    public function setTatamiAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['tatami'] = json_encode($value);
        }
    }

    public function setRoundBedAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['round_bed'] = json_encode($value);
        }
    }

    public function setBigBedAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['big_bed'] = json_encode($value);
        }
    }


    public function getCityForCache()
    {
        $key = 'cache_cityList';
        if (!Cache::has($key)) {
            $cityLists = City::query()->get();
            Cache::set($key, $cityLists,300);
        }
        return Cache::get($key);
    }


    public function getTagForCache()
    {
        $key = 'cache_TagList';
        if (!Cache::has($key)) {
            $tagLists = Tags::query()->get();
            Cache::set($key, $tagLists,300);
        }
        return Cache::get($key);
    }

    /**
     * 对数据进行处理
     * @param Apartment $model
     * @return array
     */
    public function indexListFilter($model)
    {
        $cityLists = $this->getCityForCache();
        $tagLists = $this->getTagForCache();
        $provinceModel = $cityLists->where('id', '=', $model->province)->first();
        $cityModel = $cityLists->where('id', '=', $model->city)->first();
        $districtModel = $cityLists->where('id', '=', $model->district)->first();
        $decorationStyle = $tagLists->where('id', '=', $model->decoration_style)->first()->name;
        $direction = $tagLists->where('id', '=', $model->direction)->first()->name;

        $status = $this->status_value;
        $statusFilter = function($tStatus)use($status){
            return ['name' => $this->status_value[$tStatus][0], 'value' => $tStatus, 'url' => $this->status_value[$tStatus][1]];
        };

        $typeFilter = function($type){
            return ['name' => $this->type_value[$type], 'value' => $type];
        };

        $rentalFilter = function($type){
            return ['name' => $this->rental_type_val[$type], 'value' => $type];
        };

        $facilitiesFilter = function($ids)use($tagLists) {
            $unLists = $tagLists->find(explode(',', $ids));
            $bathArr = [];
            foreach($unLists as $key => $value) {
                $bathArr[] = ['id' => $value->id, 'name' => $value->name];
            }
            return $bathArr;
        };

        $images_arr = $model->images;
        $images = [];
        if ($images_arr) {
            foreach ($images_arr as $image) {
                $images[] = asset(\Storage::disk('oss')->url($image));
            }
        }
        $returnArr =  [
            'id'                    => (int) $model->id,

            'province'              =>  ['title' => $provinceModel->title, 'id' => $model->province],
            'city'                  =>  ['title' => $cityModel->title, 'id' => $model->city],
            'address'               =>  $model->address,
            'status'                =>  $statusFilter($model->status),
            'title'                 =>  $model->title,
            'desc'                  =>  $model->desc,
            'inner_desc'            =>  $model->inner_desc,
            'traffic_desc'          =>  $model->traffic_desc,
            'environment'           =>  $model->environment,
            'type'                  =>  $typeFilter($model->type),
            'total_floor'           =>  $model->total_floor,
            'current_floor'         =>  $model->current_floor,
            'room'                  =>  $model->room,
            'hall'                  =>  $model->hall,
            'bathroom'              =>  $model->bathroom,
            'kitchen'               =>  $model->kitchen,
            'balcony'               =>  $model->balcony,
            'area'                  =>  $model->area,
            'decoration_style'      =>  ['name' => $decorationStyle, 'id' => $model->decoration_style],
            'direction'             =>  ['name' => $direction, 'id' => $model->direction],
            'bathroom_utils'        =>  $facilitiesFilter($model->bathroom_utils),
            'electrics'             =>  $facilitiesFilter($model->electrics),
            'bed'                   =>  $facilitiesFilter($model->bed),
            'kitchen_utils'         =>  $facilitiesFilter($model->kitchen_utils),
            'facilities'            =>  $facilitiesFilter($model->facilities),
            'requires'              =>  $facilitiesFilter($model->requires),
            'images'                =>  $images,
            'search_address'        =>  $model->search_address,
            'rental_type'           =>  $rentalFilter($model->rental_type),
            'rental_price'          =>  $model->rental_price,
            'rental_deposit'        =>  $model->rental_deposit,
            'total_floor'           =>  $model->total_floor,
            'current_floor'         =>  $model->current_floor,
            'Renovation_start_time' => $model->Renovation_start_time,
            'Renovation_day'        => $model->Renovation_day,
            'click_num'             =>  $model->click_num,
            'is_commend'            =>  $model->is_commend,
            'created_at'            =>  $model->created_at,
            'updated_at'            =>  $model->updated_at
        ];

        if ($districtModel) {
            $returnArr['district'] = ['title' => $districtModel->title, 'id' => $model->district];
        }
        return $returnArr;
    }

    public function user()
    {
        $this->belongsTo(User::class, 'user_id', 'id');
    }
}
