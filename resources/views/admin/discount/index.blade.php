@extends('layouts.app')

@section('content')
    <div class="container mt-5 ">
        <div class="row ">
            <div class="mx-auto">
                <div class="card">
                    <div class="card-header">
                        Discount
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-12 d-flex flex-row-reverse">
                                <a href="{{ route('admin.discount.create') }}" class="btn btn-primary btn-sm">
                                    Add Discount
                                </a>
                            </div>
                        </div>
                        @include('components.alert')
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Percentage</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($discounts as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td><span class="badge rounded-pill bg-primary">{{ $item->code }}</span></td>
                                        <td>{{ $item->percentage }}%</td>
                                        <td>{{ $item->description }}</td>
                                        <td>
                                            <a href="{{ route('admin.discount.edit',$item->id) }}"
                                                class="btn btn-sm btn-warning">Edit</a>
                                            <br>
                                            <form action="{{ route('admin.discount.destroy',$item->id) }}" method="post">
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-sm btn-danger mt-1">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty

                                    <tr>
                                        <td colspan="6">Not data discount</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
