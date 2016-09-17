@if (session()->has('notify'))
    <div class="alert alert-success">
        {{ session('notify') }}
    </div>
@endif