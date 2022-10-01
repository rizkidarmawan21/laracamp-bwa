@extends('layouts.app')

@section('content')
    <div class="container mt-5 ">
        <div class="row ">
            <div class="mx-auto">
                <div class="card">
                    <div class="card-header">
                        My Camps
                    </div>
                    <div class="card-body">
                        @include('components.alert')
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>User</th>
                                    <th>Camp</th>
                                    <th>Price</th>
                                    <th>Register Date</th>
                                    <th>Paid Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($checkout as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->user->name }}</td>
                                        <td>{{ $item->camp->title }}</td>
                                        <td>$ {{ $item->camp->price }}</td>
                                        <td>{{ $item->created_at }}</td>
                                        <td>
                                            @if ($item->is_paid)
                                                <span class="badge bg-success">
                                                    Paid
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    Waiting
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (!$item->is_paid)
                                            <form action="{{ route('admin.checkout.update',$item->id) }}" method="post">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    Set to Paid
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7"></td>
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
