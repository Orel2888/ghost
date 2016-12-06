💡 *Уведомление о {{ count($orders) == 1 ? 'покупке' : 'покупках' }} ({{ count($orders) }})*

@foreach ($orders as $order)
👱 Покупатель: {{ $order->client->name }} {{ '@'. $order->client->tg_username }}
🏡 Город: *{{ $order->goods->city->name }}*
🎁 Товар: *{{ $order->goods->name }}*
📦 Вес: *{{ $order->weight }}*
💰 Цена: *{{ $order->cost }}*
🏃 Адрес: {{ $order->purchase->address }}
{{ $sep }}
@endforeach