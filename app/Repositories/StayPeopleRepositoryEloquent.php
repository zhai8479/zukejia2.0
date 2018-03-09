<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\StayPeopleRepository;
use App\Models\StayPeople;
use App\Validators\StayPeopleValidator;

/**
 * Class StayPeopleRepositoryEloquent
 * @package namespace App\Repositories;
 */
class StayPeopleRepositoryEloquent extends BaseRepository implements StayPeopleRepository
{
    protected $fieldSearchable = [
        'id', 'real_name', 'id_card', 'mobile', 'is_check_id_card', 'user_id'
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return StayPeople::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
