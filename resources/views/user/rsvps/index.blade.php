@extends('layouts.user.user')

@section('title', 'RSVPs - ' . $card->title)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">RSVP Responses</h2>
                    <p class="text-muted mb-0">{{ $card->title }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('user.cards.analytics', $card) }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-bar"></i> Analytics
                    </a>
                    <a href="{{ route('user.cards.share', $card) }}" class="btn btn-primary">
                        <i class="fas fa-share-alt"></i> Share Card
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                    <p class="mb-0">Total RSVPs</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-envelope fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0">{{ $stats['attending'] }}</h3>
                                    <p class="mb-0">Attending</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0">{{ $stats['not_attending'] }}</h3>
                                    <p class="mb-0">Not Attending</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-times fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="mb-0">{{ $stats['total_guests'] }}</h3>
                                    <p class="mb-0">Total Guests</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RSVPs Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">RSVP Responses</h5>
                    @if($rsvps->count() > 0)
                    <button class="btn btn-outline-success btn-sm" onclick="exportRsvps()">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                    @endif
                </div>
                <div class="card-body">
                    @if($rsvps->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Guest Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Guests</th>
                                        <th>Message</th>
                                        <th>Submitted</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rsvps as $rsvp)
                                    <tr>
                                        <td>
                                            <strong>{{ $rsvp->guest_name }}</strong>
                                        </td>
                                        <td>
                                            <a href="mailto:{{ $rsvp->guest_email }}">{{ $rsvp->guest_email }}</a>
                                        </td>
                                        <td>
                                            @if($rsvp->guest_phone)
                                                <a href="tel:{{ $rsvp->guest_phone }}">{{ $rsvp->guest_phone }}</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($rsvp->attendance_status === 'yes')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check"></i> Attending
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times"></i> Not Attending
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($rsvp->attendance_status === 'yes')
                                                <span class="badge bg-info">{{ $rsvp->number_of_guests }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($rsvp->message)
                                                <span data-bs-toggle="tooltip" title="{{ $rsvp->message }}">
                                                    <i class="fas fa-comment text-primary"></i>
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $rsvp->created_at->format('M d, Y') }}<br>
                                                {{ $rsvp->created_at->format('h:i A') }}
                                            </small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        {{ $rsvps->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-envelope-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No RSVPs Yet</h5>
                            <p class="text-muted">RSVPs will appear here once guests start responding to your invitation.</p>
                            @if(!$card->is_published)
                                <a href="{{ route('user.cards.edit', $card) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Publish Your Card
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Export RSVPs to CSV
function exportRsvps() {
    const cardId = {{ $card->id }};
    const url = `{{ route('user.cards.rsvps', $card) }}?export=csv`;
    window.open(url, '_blank');
}
</script>
@endpush
@endsection 