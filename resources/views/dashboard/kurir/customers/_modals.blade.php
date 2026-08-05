@foreach ($customers as $customer)
    @include('dashboard.kurir.customers.show', ['customer' => $customer])
    @include('dashboard.kurir.customers.edit', ['customer' => $customer, 'customerCategories' => $customerCategories])
    @include('dashboard.kurir.customers.note', ['customer' => $customer])
    @include('dashboard.kurir.customers.delete', ['customer' => $customer])
@endforeach
