@foreach ($couriers as $courier)
    @include('dashboard.admin.couriers.show', ['courier' => $courier])
    @include('dashboard.admin.couriers.edit', ['courier' => $courier])
    @include('dashboard.admin.couriers.note', ['courier' => $courier])
    @include('dashboard.admin.couriers.delete', ['courier' => $courier])
    @include('dashboard.admin.couriers.performance', ['courier' => $courier])
@endforeach
