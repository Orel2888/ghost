<div class="modal fade" tabindex="-1" role="dialog" id="modal-select-goods">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Выбор товара</h4>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    @foreach ($cities as $city)
                        <div class="list-group-item">
                            <h4 class="list-group-item-heading">
                                <i class="glyphicon glyphicon-home"></i> {{ $city->name }}
                            </h4>
                            <p class="list-group-item-text">
                                @forelse ($city->goods as $goods)
                                    <a class="btn btn-xs btn-success select-goods" data-goods-id="{{ $goods->id }}" data-goods-name="{{ $goods->name }}" data-city-name="{{ $city->name }}">
                                        <i class="glyphicon glyphicon-ok"></i>
                                    </a>
                                    ID: {{ $goods->id }} <i class="glyphicon glyphicon-shopping-cart"></i> {{ $goods->name }}
                                    <br>
                                    <br>
                                @empty
                                    <li class="list-group-item">Нет товара в прайсе</li>
                                @endforelse
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                {{--<button type="button" class="btn btn-primary">Выбрать</button>--}}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
