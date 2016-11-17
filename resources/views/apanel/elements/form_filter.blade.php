<form class="form-inline">
    <label>Поиск по полям</label><br>

    @if (isset($scheme['inputs']))
    @foreach ($scheme['inputs'] as $labelText => $rules)
        <div class="form-group" style="margin-bottom: 4px;">
            <label for="f_{{ $rules['name'] }}">{{ $labelText }}</label>

            <select name="f_compare_{{ $rules['name'] }}" class="form-control">
            @if (isset($rules['compare']))
                @foreach ($rules['compare'] as $compare)
                    @php($compareName = 'f_compare_'. $rules['name'])
                    <option value="{!! $compare !!}" @if (isset($inputs[$compareName]) && $inputs[$compareName] == $compare)selected @endif>{!! $compare !!}</option>
                @endforeach
                @else
                <option value="=" @if (isset($inputs['f_compare_'. $rules['name']]) && $inputs['f_compare_'. $rules['name']] == '=')selected @endif>=</option>
                <option value="!=" @if (isset($inputs['f_compare_'. $rules['name']]) && $inputs['f_compare_'. $rules['name']] == '!=')selected @endif>!=</option>
                <option value=">" @if (isset($inputs['f_compare_'. $rules['name']]) && $inputs['f_compare_'. $rules['name']] == '>')selected @endif>></option>
                <option value=">=" @if (isset($inputs['f_compare_'. $rules['name']]) && $inputs['f_compare_'. $rules['name']] == '>=')selected @endif>>=</option>
                <option value="<" @if (isset($inputs['f_compare_'. $rules['name']]) && $inputs['f_compare_'. $rules['name']] == '<')selected @endif>></option>
                <option value="<=" @if (isset($inputs['f_compare_'. $rules['name']]) && $inputs['f_compare_'. $rules['name']] == '<=')selected @endif><=</option>
            @endif
            </select>

            <input type="text" class="form-control" name="f_{{ $rules['name'] }}" value="{!! $inputs['f_'. $rules['name']] or '' !!}">
        </div>
    @endforeach
    @endif

    @if (isset($scheme['selects']))
    @foreach ($scheme['selects'] as $labelText => $rules)
        <div class="form-group">
            <label for="f_{{ $rules['name'] }}">{{ $labelText }}</label>
            <select name="f_{{ $rules['name'] }}" class="form-control" id="f_{{ $rules['name'] }}">
                @foreach ($rules['fields'] as $value => $name)
                    <option value="{{ $value }}" @if ((isset($inputs['f_'. $rules['name']]) && $inputs['f_'. $rules['name']] == $value) || $rules['selected'] == $value) selected @endif>{{ $name }}</option>
                @endforeach
            </select>
        </div>
    @endforeach
        <br>
    @endif

    <label>Сортировать</label><br>
    @if (isset($scheme['sorting']))
        <label for="f-sort">По</label>
        <select name="f_sorting_column" id="f-sort" class="form-control">
            @foreach ($scheme['sorting']['columns'] as $name => $val)
                <option value="{{ $val }}" @if (isset($inputs['f_sorting_column']) && $inputs['f_sorting_column'] == $val)selected @endif>{{ $name }}</option>
            @endforeach
        </select>
    @endif

    <select name="f_sorting_by" class="form-control">
        <option value="asc" @if (isset($inputs['f_sorting_by']) && $inputs['f_sorting_by'] == 'asc')selected @endif>По возрастанию</option>
        <option value="desc" @if (isset($inputs['f_sorting_by']) && $inputs['f_sorting_by'] == 'desc')selected @endif>По убыванию</option>
    </select>
    <br>
    <br>

    @if (isset($scheme['period_date']))
        <div class="well">
            <div class="row">
                <div class="col-sm-12 col-md-5">
                    <label>Период времени</label><br>
                    <div class="form-group">
                        <label>С</label>
                        <input type="text" class="form-control" name="f_created_at_to" id="f_created_at_to" value="{{ $date_from }}">
                    </div>
                    <div class="form-group">
                        <label>По</label>
                        <input type="text" class="form-control" name="f_created_at_from" id="f_created_at_from" value="{{ $date_current }}">
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="f_period_date" value="1" @if (isset($inputs['f_period_date'])) checked @endif>
                            Выбор по дате
                        </label>
                    </div><br>
                </div>
                <div class="col-sm-12 col-md-7" style="margin: 23px 0;">
                    <div class="btn-group" id="btn-group-select">
                        <button class="btn btn-primary" data-period="1">Сегодня</button>
                        <button class="btn btn-primary" data-period="2">Вчера</button>
                        <button class="btn btn-primary" data-period="3">Позавчера</button>
                        <button class="btn btn-primary" data-period="4">За тек. неделю</button>
                        <button class="btn btn-primary" data-period="5">За тек. месяц</button>
                        <button class="btn btn-primary" data-period="6">За прошлый месяц</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <input type="submit" class="btn btn-success" name="filter" value="Запрос">
    <input type="submit" class="btn btn-success" name="filter_reset" value="Сброс">
</form>