✅ Успешная покупка! Заказ *№{{ $order->id }}*

🏡 Город: *{{ $order->goods->city->name }}*
🎁 Товар: *{{ $order->goods->name }}*
📦 Вес: *{{ wcorrect($order->weight) }}*
💰 Цена: *{{ $order->cost }}*
🏃 Адрес: {{ $order->purchase->address }}

Спасибо за покупку! 😊
Решение проблем, обращаться в @sonicsupklad