<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <label for="{{$id}}" class="col-sm-1 control-label">长(m)</label>

    <div class="col-sm-2">

        @include('admin::form.error')

        <div class="input-group">

            @if ($prepend)
                <span class="input-group-addon">{!! $prepend !!}</span>
            @endif

            <input {!! $attributes !!}/>

            @if ($append)
                <span class="input-group-addon clearfix">{!! $append !!}</span>
            @endif

        </div>

        @include('admin::form.help-block')

    </div>

    <label for="{{$id}}" class="col-sm-1 control-label">宽(m)</label>

    <div class="col-sm-2">

        @include('admin::form.error')

        <div class="input-group">

            @if ($prepend)
                <span class="input-group-addon">{!! $prepend !!}</span>
            @endif

            <input {!! $attributes !!}/>

            @if ($append)
                <span class="input-group-addon clearfix">{!! $append !!}</span>
            @endif

        </div>

        @include('admin::form.help-block')

    </div>

    <label for="{{$id}}" class="col-sm-1 control-label">数量</label>

    <div class="col-sm-2">

        @include('admin::form.error')

        <div class="input-group">

            @if ($prepend)
                <span class="input-group-addon">{!! $prepend !!}</span>
            @endif

            <input {!! $attributes !!}/>

            @if ($append)
                <span class="input-group-addon clearfix">{!! $append !!}</span>
            @endif

        </div>

        @include('admin::form.help-block')

    </div>
</div>