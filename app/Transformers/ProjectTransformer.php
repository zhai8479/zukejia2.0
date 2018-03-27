<?php

namespace App\Transformers;

use App\Models\ProjectInvestment;
use App\Models\ProjectType;
use App\Models\Upload;
use League\Fractal\TransformerAbstract;
use App\Models\Project;

/**
 * Class ProjectTransformer
 * @package namespace App\Transformers;
 */
class ProjectTransformer extends TransformerAbstract
{

    /**
     * Transform the Project entity
     * @param Project $model
     * @return array
     */
    public function transform(Project $model)
    {
        // 房屋证件url
        if (isset($model->house_contract_img_ids)) {
            $ids = $model->house_contract_img_ids;
            $urls = [];
            foreach ($ids as $url) {
                $urls[] = asset(\Storage::url($url));
            }
            $model->house_contract_img_urls = $urls;
        }
        // 合同url
        if (!empty($model->contract_file_id)) {
            $model->contract_file_url = Upload::id_to_url($model->contract_file_id);
        }

        if (!empty($model->contract_file_name)) {
            $model->contract_file_url = asset(\Storage::url($model->contract_file_name));
        }

        if ($model->status == Project::STATUS_REPAYMENT) {
            $model->buy_over_time = ProjectInvestment::query()->where('project_id', $model->id)->whereNotNull('pay_at')->value('pay_at');
        } else {
            $model->buy_over_time = $model->end_at;
        }
        if ($model->status == Project::STATUS_OVER) {
            $model->repayment_over_time = ProjectInvestment::query()->where('project_id', $model->id)->whereNotNull('end_at')->value('end_at');
        } else {
            $model->repayment_over_time = null;
        }

        $model->status_str = $model->status_str();
        $model->house_status_str = $model->house_status_str();
        $model->house_management_status_str = $model->house_management_status_str();

        // 类型信息
        $type = ProjectType::find($model->type_id);
        $model->type_info = [
            'name' => $type->name,
            'repayment_type' => $type->repayment_type,
            'repayment_type_str' => ProjectType::$repayment_type_list[$type->repayment_type],
            'guarantee_type' => $type->guarantee_type,
            'guarantee_type_str' => ProjectType::$guarantee_type_list[$type->guarantee_type],
            'interest_day' => $type->interest_day,
        ];

        return $model->toArray();
    }
}
