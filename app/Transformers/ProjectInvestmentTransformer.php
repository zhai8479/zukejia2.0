<?php

namespace App\Transformers;

use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Upload;
use League\Fractal\TransformerAbstract;
use App\Models\ProjectInvestment;

/**
 * Class ProjectInvestmentTransformer
 * @package namespace App\Transformers;
 */
class ProjectInvestmentTransformer extends TransformerAbstract
{

    /**
     * Transform the ProjectInvestment entity
     * @param ProjectInvestment $model
     *
     * @return array
     */
    public function transform(ProjectInvestment $model)
    {
        $arr = $model->toArray();
        $arr['status_str'] = ProjectInvestment::$status_list[$arr['status']];
        $project = Project::find($arr['project_id']);
        $type = ProjectType::find($project->type_id);
        $arr['project_info'] = [
            'name' => $project->name,
            'house_address' => $project->house_address,
            'issue_total_num' => $project->issue_total_num,
            'issue_day_num' => $project->issue_day_num,
            'money' => $project->money,
            'issue_profit_money' => 0.8 * ($project->rental_money - $project->collect_money),   // 每期收益
            'contract_file_url' => empty($project->contract_file_name)?null:asset(\Storage::url($project->contract_file_name)),
            'start_at' => $project->start_at,
            'end_at' => $project->end_at,
            'status' => $project->status,
            'buy_over_time' => $project->buy_over_time,
            'type_info' => [
                'name' => $type->name,
                'repayment_type' => $type->repayment_type,
                'repayment_type_str' => ProjectType::$repayment_type_list[$type->repayment_type],
                'guarantee_type' => $type->guarantee_type,
                'guarantee_type_str' => ProjectType::$guarantee_type_list[$type->guarantee_type],
                'interest_day' => $type->interest_day,
            ]
        ];

        return $arr;
    }
}
