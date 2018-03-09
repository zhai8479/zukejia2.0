<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Apartment;

/**
 * Class ApartmentTransformer
 * @package namespace App\Transformers;
 */
class ApartmentTransformer extends TransformerAbstract
{
    /**
     * 房源 status 允许的值
     * @var array
     */
    protected $status_value = [
        1 => '热销中',
        2 => '维修中',
        3 => '出租中',
        4 => '推荐',
        5 => '热门'
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
     * Transform the \Apartment entity
     * @param \App\Models\Apartment $model
     *
     * @return array
     */
    public function transform(Apartment $model)
    {
        $provinceModel = \DB::table('chain_district')->where('id', '=', $model->province)->first();
        $cityModel = \DB::table('chain_district')->where('id', '=', $model->city)->first();
        $districtModel = \DB::table('chain_district')->where('id', '=', $model->district)->first();
        $decorationStyle = \DB::table('tag')->where('id', '=', $model->decoration_style)->first();
        $direction = \DB::table('tag')->where('id', '=', $model->direction)->first();

        $status = $this->status_value;
        $statusFilter = function($tStatus)use($status){
            return ['name' => $this->status_value[$tStatus], 'value' => $tStatus];
        };

        $typeFilter = function($type){
            return ['name' => $this->type_value[$type], 'value' => $type];
        };

        $rentalFilter = function($type){
            return ['name' => $this->rental_type_val[$type], 'value' => $type];
        };

        $facilitiesFilter = function($ids) {
            $utils = \DB::table('tag')->whereIn('id',  explode(',', $ids))->get();
            $bathArr = [];
            foreach($utils as $key => $value) {
                $bathArr[] = ['id' => $value->id, 'name' => $value->name];
            }
            return $bathArr;
        };
        $images_arr = $model->images;
        $images = [];
        if ($images_arr) {
            foreach ($images_arr as $image) {
                $images[] = asset(\Storage::disk('admin')->url($image));
            }
        }
        $returnArr = [
            'id'                    => (int) $model->id,

            'province'              =>  ['name' => $provinceModel->name, 'id' => $model->province],
            'city'                  =>  ['name' => $cityModel->name, 'id' => $model->city],
            'address'               =>  $model->address,
            'status'                =>  $statusFilter($model->status),
            'title'                 =>  $model->title,
            'desc'                  =>  $model->desc,
            'inner_desc'            =>  $model->inner_desc,
            'traffic_desc'          =>  $model->traffic_desc,
            'environment'           =>  $model->environment,
            'type'                  =>  $typeFilter($model->type),
            'room'                  =>  $model->room,
            'hall'                  =>  $model->hall,
            'bathroom'              =>  $model->bathroom,
            'kitchen'               =>  $model->kitchen,
            'balcony'               =>  $model->balcony,
            'area'                  =>  $model->area,
            'decoration_style'      =>  ['name' => $decorationStyle->name, 'id' => $model->decoration_style],
            'direction'             =>  ['name' => $direction->name, 'id' => $model->direction],
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

            'created_at'            =>  $model->created_at,
            'updated_at'            =>  $model->updated_at
        ];

        if ($districtModel) {
            $returnArr['district'] = ['name' => $districtModel->name, 'id' => $model->district];
        }
        return $returnArr;
    }
}
