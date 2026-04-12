@php
    $displayLimit = $limit ?? null;
    $displayedServices = $displayLimit ? collect($services)->take($displayLimit) : $services;
@endphp

<div class="table-responsive">
    <table class="table table-sm table-striped table-hover table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th scope="col">Service Name</th>
                <th scope="col">Code</th>
                <th scope="col">Price</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($displayedServices as $service)
                <tr>
                    <td>{{ $service['name'] ?? 'N/A' }}</td>
                    <td>{{ $service['code'] ?? 'N/A' }}</td>
                    <td>$ {{ $service['price'] ?? '0.00' }}</td>
                    <td>
                        @if(!empty($service['available']))
                            <span class="badge bg-success">Available</span>
                        @else
                            <span class="badge bg-secondary">Unavailable</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">No services found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

