@include('dashboard.admin.customers.create', ['customerCategories' => $customerCategories, 'couriers' => $couriers])

@foreach ($customers as $customer)
    @include('dashboard.admin.customers.show', ['customer' => $customer])
    @include('dashboard.admin.customers.edit', ['customer' => $customer, 'customerCategories' => $customerCategories, 'couriers' => $couriers])
    @include('dashboard.admin.customers.note', ['customer' => $customer])
    @include('dashboard.admin.customers.delete', ['customer' => $customer])
    @include('dashboard.admin.customers.rekap', ['customer' => $customer])
@endforeach
