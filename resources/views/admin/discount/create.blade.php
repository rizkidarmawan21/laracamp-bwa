@extends('layouts.app')

@section('content')
    <div class="container mt-5 ">
        <div class="row ">
            <div class="mx-auto">
                <div class="card">
                    <div class="card-header">
                        Insert a new discount
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('admin.discount.store') }}" method="post">
                            @csrf
                            <div class="form-group mb-4">
                                <label class="form-label" for="">Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label" for="">Code (max 5 character)</label>
                                <input type="text" class="form-control" name="code" required>
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label" for="">Description</label>
                                <textarea class="form-control" name="description">-</textarea>
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label" for="">Percentage</label>
                                <input type="number" max="100" min="1" class="form-control" required
                                    name="percentage">
                            </div>
                            <div class="form-group mb-4 d-block">
                                <button type="submit" class="btn btn-primary d-block">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
