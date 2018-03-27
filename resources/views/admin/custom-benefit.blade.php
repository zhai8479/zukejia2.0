<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$p_id}}" class="col-sm-2 control-label">{{$principal}}</label>

    <div class="col-sm-2">

        @include('admin::form.error')

        <div class="input-group">

            <span class="input-group-addon">{!! $symbol !!}</span>
            <input type="text"  name="benefit[]" value="0" class="form-control {{$p_id}}" placeholder="本金" readonly id="{!! $p_id !!}"/>

        </div>

        @include('admin::form.help-block')

    </div>

    <label for="{{$i_id}}" class="col-sm-1 control-label">{{$income}}</label>

    <div class="col-sm-2">

        @include('admin::form.error')

        <div class="input-group">

                <span class="input-group-addon">{!! $symbol !!}</span>
                <input type="text"  name="benefit[]" value="0" class="form-control {{$i_id}}" placeholder="收益" readonly id="{!! $i_id !!}"/>

        </div>

        @include('admin::form.help-block')

    </div>

    <label for="{!! $pni_id !!}" class="col-sm-1 control-label">{{$pni}}</label>

    <div class="col-sm-2">

        @include('admin::form.error')

        <div class="input-group">
            <span class="input-group-addon">{!! $symbol !!}</span>

            <input type="text"  name="benefit[]" value="0" class="form-control {{$pni}}" placeholder="本息" readonly id="{!! $pni_id !!}"/>
        </div>

        @include('admin::form.help-block')

    </div>
</div>