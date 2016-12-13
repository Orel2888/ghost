💡 *Уведомление о {{ count($orders) == 1 ? 'покупке' : 'покупках' }} ({{ count($orders) }})*

@foreach ($orders as $order)
👱 Покупатель: {{ $order->client->name }} {{ '@'. $order->client->tg_username }}
🏡 Город: *{{ $order->goods->city->name }}*
🎁 Товар: *{{ $order->goods->name }}*
📦 Вес: *{{ wcorrect($order->weight) }}*
💰 Цена: *{{ $order->cost }}*
📆 Дата: *{{ $order->purchase->created_at->format('d.m.Y H:i:s') }}*
🏃 Адрес: {{ $order->purchase->address }}
{{ $sep }}
@endforeach