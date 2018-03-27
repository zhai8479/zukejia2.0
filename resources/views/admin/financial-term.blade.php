<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="col-sm-2">

        @include('admin::form.error')

        <div class="input-group">
            <input type="text"  name="issue_total_num[]" value="0" class="form-control terms" placeholder="输入 天数" readonly id="{!! $t_id !!}"/>

            @if ($term)
                <span class="input-group-addon clearfix" style="{!! $style !!}">{!! $term !!}</span>
            @endif

        </div>

        @include('admin::form.help-block')

    </div>
    <div class="col-sm-2">

        @include('admin::form.error')

        <div class="input-group">
            <input type="text"  name="issue_total_num[]" value="0" class="form-control terms" placeholder="输入 天数" readonly id="{!! $d_id !!}"/>

            @if ($day)
                <span class="input-group-addon clearfix" style="{!! $style !!}">{!! $day !!}</span>
            @endif

        </div>

        @include('admin::form.help-block')

    </div>
</div>