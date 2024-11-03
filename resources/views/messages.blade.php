@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<style>
    .alert {
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 4px;
    font-size: 14px;
    }
    .alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    }
    .alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    }
</style>
