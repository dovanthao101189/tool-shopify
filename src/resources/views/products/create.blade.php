@extends('base')

@section('main')
    <div class="row">
        <div class="col-sm-8 offset-sm-2">
            <h1 class="display-3">Add a product</h1>
            <div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div><br/>
                @endif

                @if(isset($success))
                    <div class="alert alert-success">
                        Success
                    </div>
                @endif
                <br/>
                <form method="post" action="{{ route('products.create') }}">
                    @csrf
                    <div class="form-group">
                        <label for="first_name">Link:</label>
                        <input type="text" required class="form-control" name="link"/>
                    </div>

                    <button type="submit" class="btn btn-primary">Add</button>
                </form>
            </div>
        </div>
    </div>
@endsection