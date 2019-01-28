@if(session('success'))
    <div class="alert alert-success alert-block" role="alert">
        <button class="close" data-dismiss="alert"></button>
        {{ session('success') }}
    </div>
@endif