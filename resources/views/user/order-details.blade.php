@extends('layouts.app')

@section('content')
    <style>
         .table> tr>th {
            padding: 0.625rem 1.5rem .625rem !important;
            background-color: #6a6e51 !important;
        }
         .table> :not(caption)>tr>th {
            padding: 0.625rem 1.5rem .625rem !important;
            background-color: #6a6e51 !important;
        }

        .table>tr>td {
            padding: 0.625rem 1.5rem .625rem !important;
        }

        .table-bordered> :not(caption)>tr>th,
        .table-bordered> :not(caption)>tr>td {
            border-width: 1px 1px;
            border-color: #6a6e51;
        }

        .table> :not(caption)>tr>td {
            padding: .8rem 1rem !important;
        }

        .bg-success {
            background-color: #40c710 !important;
        }

        .bg-danger {
            background-color: #f44032 !important;
        }

        .bg-warning {
            background-color: #f5d700 !important;
            color: #000;
        }
    </style>
    <main class="pt-90" style="padding-top: 0px;">
        <div class="mb-4 pb-4"></div>
        <section class="my-account container">
            <h2 class="page-title">Order Details</h2>
            <div class="row">
                <div class="col-lg-2">
                    @include('user.account-nav')
                </div>


                <div class="col-lg-10">
                    <div class="wg-box">
                        <div class="flex items-center justify-between gap10 flex-wrap">
                            <div class="wg-filter flex-grow">
                                <h5>Order Details</h5>
                            </div>
                            <a class="tf-button style-1 w208" href="{{ route('admin.orders') }}">Back</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-transaction">
                                <thead>
                                    <tr>
                                        <td>Order Number</td>
                                        <td>{{ $order->id }}</td>
                                        <td>Mobile</td>
                                        <td>{{ $order->phone }}</td>
                                        <td>Zip Code</td>
                                        <td>{{ $order->zip }}</td>
                                    </tr>
                                    <tr>
                                        <td>Order Date</td>
                                        <td>{{ $order->created_at }}</td>
                                        <td>Delivery Date</td>
                                        <td>{{ $order->delivered_date }}</td>
                                        <td>Cancelled Date</td>
                                        <td>{{ $order->cancelled_date }}</td>
                                    </tr>
                                    <tr>
                                        <td>Order Status</td>
                                        <td colspan="5">
                                            @if ($order->status == 'delivered')
                                                <span class="badge bg-success">Delivered</span>
                                            @elseif($order->status == 'cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @endif
                                            <span class="badge bg-warning">Ordered</span>
                                        </td>
                                    </tr>
                                </thead>

                            </table>
                        </div>

                    </div>

                    <div class="wg-box">
                        <div class="flex items-center justify-between gap10 flex-wrap">
                            <div class="wg-filter flex-grow">
                                <h5>Ordered Items</h5>
                            </div>
                        </div>
                        <div class="table-responsive">
                            @if(Session::has('status'))
                                <p class="alert alert-success">{{ Session::get('status') }}</p>
                             @endif
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">SKU</th>
                                        <th class="text-center">Category</th>
                                        <th class="text-center">Brand</th>
                                        <th class="text-center">Options</th>
                                        <th class="text-center">Return Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderItems as $item)
                                        <tr>

                                            <td class="pname">
                                                <div class="image">
                                                    <img src="{{ asset('uploads/products/thumbnails') }}/{{ $item->product->image }}"
                                                        alt="{{ $item->product->name }}" class="image">
                                                </div>
                                                <div class="name">
                                                    <a href="{{ route('shop.product.details', ['product_slug' => $item->product->slug]) }}"
                                                        target="_blank" class="body-title-2">{{ $item->product->name }}</a>
                                                </div>
                                            </td>
                                            <td class="text-center">${{ $item->price }}</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-center">{{ $item->product->SKU }}</td>
                                            <td class="text-center">{{ $item->product->category->name }}</td>
                                            <td class="text-center">{{ $item->product->brand->name }}</td>
                                            <td class="text-center">{{ $item->options }}</td>
                                            <td class="text-center">{{ $item->rstatus == 0 ? 'No' : 'Yes' }}</td>
                                            <td class="text-center">
                                                <div class="list-icon-function view-icon">
                                                    <div class="item eye">
                                                        <a href="{{ route('shop.product.details', ['product_slug' => $item->product->slug]) }}"
                                                            target="_blank" class="body-title-2"
                                                            title="{{ $item->product->name }}">
                                                            <i class="icon-eye"></i>
                                                        </a>

                                                        <a href="#" target="_blank" rel="noopener noreferrer">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                        <div class="divider"></div>
                        <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                            {{ $orderItems->links('pagination::bootstrap-5') }}
                        </div>
                    </div>

                    <div class="wg-box mt-5">
                        <h5>Shipping Address</h5>
                        <div class="my-account__address-item col-md-6">
                            <div class="my-account__address-item__detail">
                                <p>{{ $order->name }}</p>
                                <p>{{ $order->address }}</p>
                                <p>{{ $order->locality }}, DEF</p>
                                <p>{{ $order->city }}, {{ $order->state }}, {{ $order->country }} </p>
                                <p>{{ $order->landmark }}</p>
                                <p>{{ $order->zip }}</p>
                                <br>
                                <p>Mobile : {{ $order->phone }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="wg-box mt-5">
                        <h5>Transactions</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-transaction">
                                <tbody>
                                    <tr>
                                        <th>Subtotal</th>
                                        <td>${{ number_format(floatval($order->subtotal)) }}</td>
                                        <th>Tax</th>
                                        <td>${{ number_format(floatval($order->tax)) }}</td>
                                        <th>Discount</th>
                                        <td>${{ $order->discount }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total</th>
                                        <td>${{ $order->total }}</td>
                                        <th>Payment Mode</th>
                                        <td>{{ $transaction->mode }}</td>
                                        <th>Status</th>
                                        <td>
                                            @if ($transaction->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($transaction->status == 'declined')
                                                <span class="badge bg-danger">Declined</span>
                                            @elseif($transaction->status == 'refunded')
                                                <span class="badge bg-secondary">Refunded</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="wg-box mt-5">
                         <form action="{{ route('admin.order.status.update') }}" method="post">
                            @csrf
                            @method('put')
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <button type="submit" class="btn btn-primary tf-button ">Cancel Order</button>
                        </form>
                    </div>
                </div>

            </div>
        </section>
    </main>
@endsection
