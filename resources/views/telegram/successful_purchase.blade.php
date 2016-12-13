✅ Успешная покупка! Заказ *№{{ $order->id }}*

🏡 Город: *{{ $order->goods->city->name }}*
🎁 Товар: *{{ $order->goods->name }}*
📦 Вес: *{{ wcorrect($order->weight) }}*
💰 Цена: *{{ $order->cost }}*
📆 Дата: *{{ $order->purchase->created_at->format('d.m.Y H:i:s') }}*
🏃 Адрес: {{ $order->purchase->address }}

Спасибо за покупку! 😊