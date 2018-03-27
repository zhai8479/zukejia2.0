<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */
use App\Admin\Extensions\BedLine;
use App\Admin\Extensions\CustomBenefit;
use App\Admin\Extensions\Duration;
use App\Admin\Extensions\FinancialTerm;
use App\Admin\Extensions\LastBenefit;
use Encore\Admin\Form;
use App\Admin\Extensions\uEditor;

Encore\Admin\Form::forget(['map', 'editor']);

Form::extend('bed_line', BedLine::class);
Form::extend('benefit', CustomBenefit::class);
Form::extend('last_benefit', LastBenefit::class);
Form::extend('financial_term', FinancialTerm::class);
Form::extend('duration', Duration::class);
Form::extend('editor', uEditor::class);