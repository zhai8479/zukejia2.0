<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$last_p_id}}" class="col-sm-2 control-label">{{$last_principal}}</label>

    <div class="col-sm-2">

        @include('admin::form.error')

        <div class="input-group">

            <span class="input-group-addon">{!! $symbol !!}</span>
            <input type="text"  name="last_benefit[]" value="0" class="form-control {{$last_p_id}}" placeholder="本金" readonly id="{!! $last_p_id !!}"/>

        </div>

        @include('admin::form.help-block')

    </div>

    <label for="{{$last_i_id}}" class="col-sm-1 control-label">{{$last_income}}</label>

    <div class="col-sm-2">

        @include('admin::form.error')

        <div class="input-group">

            <span class="input-group-addon">{!! $symbol !!}</span>
            <input type="text"  name="last_benefit[]" value="0" class="form-control {{$last_i_id}}" placeholder="收益" readonly id="{!! $last_i_id !!}"/>

        </div>

        @include('admin::form.help-block')

    </div>

    <label for="{!! $last_pni_id !!}" class="col-sm-1 control-label">{{$last_pni}}</label>

    <div class="col-sm-2">

        @include('admin::form.error')

        <div class="input-group">

            <span class="input-group-addon">{!! $symbol !!}</span>
            <input type="text"  name="last_benefit[]" value="0" class="form-control {{$last_pni_id}}" placeholder="本息" readonly id="{!! $last_pni_id !!}"/>

        </div>

        @include('admin::form.help-block')

    </div>
</div>