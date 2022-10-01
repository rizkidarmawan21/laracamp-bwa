@extends('layouts.app')

@section('content')
      <section class="dashboard my-5">
        <div class="container">
            <div class="row text-left">
                <div class=" col-lg-12 col-12 header-wrap mt-4">
                    <p class="story">
                        DASHBOARD
                    </p>
                    <h2 class="primary-header ">
                        My Bootcamps
                    </h2>
                </div>
            </div>
            <div class="row my-5">
                @include('components.alert')
                <table class="table">
                    <tbody>
                        
                        @forelse ($checkout as $item)
                            <tr class="align-middle">
                            <td width="18%">
                                <img src="/assets/images/item_bootcamp.png" height="120" alt="">
                            </td>
                            <td>
                                <p class="mb-2">
                                    <strong>{{ $item->camp->title }}</strong>
                                </p>
                                <p>
                                    {{ $item->created_at->format('M d, Y') }}
                                </p>
                            </td>
                            <td>
                                <strong>${{ $item->camp->price }}</strong>
                            </td>
                            <td>
                                @if ($item->is_paid)
                                <strong class="text-success" >Payment Success</strong>
                                @else
                                <strong>Waiting for Payment</strong>
                                @endif
                            </td>
                            <td>
                                <a target="_blank" href="https://wa.me/6285161802116?text=Hi, saya ingin bertanya tentang kelas {{ $item->camp->title }}" class="btn btn-primary">
                                    Contact Support
                                </a>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <h1>No Data</h1>
                                </td>
                            </tr>
                        @endforelse
                        
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection