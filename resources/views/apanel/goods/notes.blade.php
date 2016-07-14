@if (session()->has('note'))
    <div class="alert alert-success">{{ session('note') }}</div>
@endif