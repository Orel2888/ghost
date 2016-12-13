💡 *Уведомление о {{ count($transactions) == 1 ? 'абузной транзакции' : 'абузных транзакциях' }} ({{ count($transactions) }})*

@foreach ($transactions as $transaction)
Кошелек: {{ $transaction->purse }}
Сумма: {{ $transaction->amount }}
Комментарий: {{ $transaction->comment or 'Нету' }}
Провайдер: {{ $transaction->provider }}
Дата: {{ $transaction->qiwi_date }}
{{ $sep }}
@endforeach