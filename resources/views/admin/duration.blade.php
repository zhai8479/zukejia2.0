<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="col-sm-2">

        @include('admin::form.error')

        <div class="input-group">
            <select name="duration[]" class="form-control {!! $year_id !!}" id="{!! $year_id !!}">
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
                <option value="13">13</option>
                <option value="14">14</option>
                <option value="15">15</option>
            </select>

            @if ($year_value)
                <span class="input-group-addon clearfix" style="{!! $style !!}">{!! $year_value !!}</span>
            @endif

        </div>

        @include('admin::form.help-block')

    </div>
    <div class="col-sm-2">

        @include('admin::form.error')

        <div class="input-group">
            <select name="duration[]" class="form-control {!! $month_id !!}" id="{!! $month_id !!}">
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
            </select>

            @if ($month_value)
                <span class="input-group-addon clearfix" style="{!! $style !!}">{!! $month_value !!}</span>
            @endif

        </div>

        @include('admin::form.help-block')

    </div>

    <div class="col-sm-2">

        @include('admin::form.error')

        <div class="input-group">
            <select name="duration[]" class="form-control {!! $day_id !!}" id="{!! $day_id !!}">
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
                <option value="13">13</option>
                <option value="14">14</option>
                <option value="15">15</option>
                <option value="16">16</option>
                <option value="17">17</option>
                <option value="18">18</option>
                <option value="19">19</option>
                <option value="20">20</option>
                <option value="21">21</option>
                <option value="22">22</option>
                <option value="23">23</option>
                <option value="24">24</option>
                <option value="25">25</option>
                <option value="26">26</option>
                <option value="27">27</option>
                <option value="28">28</option>
                <option value="29">29</option>
                <option value="30">30</option>
                <option value="31">31</option>
            </select>

            @if ($day_value)
                <span class="input-group-addon clearfix" style="{!! $style !!}">{!! $day_value !!}</span>
            @endif

        </div>
        @include('admin::form.help-block')

    </div>
</div>