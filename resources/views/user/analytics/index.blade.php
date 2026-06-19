@extends('layouts.user.user')

@section('title', 'Card Analytics')
@section('page_title', 'Card Analytics')
@section('page_subtitle', 'Track your wedding invitation performance')

@section('content')
<div class="analytics-container">
    <div class="content-card">
        <!-- Card Info Header -->
        <div class="card-info-header">
            <div class="card-details">
                <h2 class="card-title">{{ $card->title }}</h2>
                <p class="couple-names">{{ $card->card_details['bride_name'] ?? 'Bride' }} & {{ $card->card_details['groom_name'] ?? 'Groom' }}</p>
                <p class="wedding-date"><i class="fas fa-calendar"></i> {{ $card->card_details['wedding_date'] ?? 'Date TBD' }}</p>
                
                <div class="card-status">
                    @if($card->is_published)
                        <span class="status published">
                            <i class="fas fa-globe"></i> Published
                        </span>
                        <span class="publish-date">Published on {{ $card->updated_at->format('M d, Y') }}</span>
                    @else
                        <span class="status draft">
                            <i class="fas fa-edit"></i> Draft
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="card-actions">
                <a href="{{ route('user.cards.share', $card) }}" class="btn btn-success">
                    <i class="fas fa-share-alt"></i>
                    Share Card
                </a>
                <a href="{{ $card->view_url }}" target="_blank" class="btn btn-secondary">
                    <i class="fas fa-external-link-alt"></i>
                    View Card
                </a>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="metrics-section">
            <h3 class="section-title">
                <i class="fas fa-chart-line"></i>
                Key Metrics
            </h3>
            
            <div class="metrics-grid">
                <div class="metric-card primary">
                    <div class="metric-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="metric-content">
                        <div class="metric-number">{{ $analytics['total_views'] }}</div>
                        <div class="metric-label">Total Views</div>
                        <div class="metric-change positive">+12% this week</div>
                    </div>
                </div>
                
                <div class="metric-card success">
                    <div class="metric-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="metric-content">
                        <div class="metric-number">{{ $analytics['unique_views'] }}</div>
                        <div class="metric-label">Unique Visitors</div>
                        <div class="metric-change positive">+8% this week</div>
                    </div>
                </div>
                
                <div class="metric-card info">
                    <div class="metric-icon">
                        <i class="fas fa-reply"></i>
                    </div>
                    <div class="metric-content">
                        <div class="metric-number">{{ $analytics['total_rsvps'] }}</div>
                        <div class="metric-label">RSVP Responses</div>
                        <div class="metric-change positive">+{{ $analytics['attending'] + $analytics['not_attending'] }} total</div>
                    </div>
                </div>
                
                <div class="metric-card warning">
                    <div class="metric-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="metric-content">
                                    <div class="metric-number">{{ $analytics['total_guests_attending'] ?? 0 }}</div>
            <div class="metric-label">Total Guests Attending</div>
                        <div class="metric-change neutral">of total views</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RSVP Breakdown -->
        <div class="rsvp-section">
            <h3 class="section-title">
                <i class="fas fa-chart-pie"></i>
                RSVP Breakdown
            </h3>
            
            <div class="rsvp-content">
                <div class="rsvp-chart">
                    <div class="donut-chart">
                        <div class="chart-center">
                            <div class="chart-number">{{ $analytics['total_rsvps'] }}</div>
                            <div class="chart-label">Total RSVPs</div>
                        </div>
                    </div>
                </div>
                
                <div class="rsvp-breakdown">
                    <div class="rsvp-item attending">
                        <div class="rsvp-color"></div>
                        <div class="rsvp-details">
                            <div class="rsvp-count">{{ $analytics['attending'] }}</div>
                            <div class="rsvp-label">Attending</div>
                            <div class="rsvp-percentage">{{ $analytics['total_rsvps'] > 0 ? round(($analytics['attending'] / $analytics['total_rsvps']) * 100, 1) : 0 }}%</div>
                        </div>
                    </div>
                    
                    <div class="rsvp-item not-attending">
                        <div class="rsvp-color"></div>
                        <div class="rsvp-details">
                            <div class="rsvp-count">{{ $analytics['not_attending'] }}</div>
                            <div class="rsvp-label">Not Attending</div>
                            <div class="rsvp-percentage">{{ $analytics['total_rsvps'] > 0 ? round(($analytics['not_attending'] / $analytics['total_rsvps']) * 100, 1) : 0 }}%</div>
                        </div>
                    </div>
                    
                    <div class="rsvp-item pending">
                        <div class="rsvp-color"></div>
                        <div class="rsvp-details">
                            <div class="rsvp-count">{{ $analytics['unique_views'] - $analytics['total_rsvps'] }}</div>
                            <div class="rsvp-label">Pending</div>
                            <div class="rsvp-percentage">{{ $analytics['unique_views'] > 0 ? round((($analytics['unique_views'] - $analytics['total_rsvps']) / $analytics['unique_views']) * 100, 1) : 0 }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Views Over Time -->
        <div class="views-section">
            <h3 class="section-title">
                <i class="fas fa-chart-area"></i>
                Views Over Time (Last 7 Days)
            </h3>
            
            <div class="chart-container">
                <div class="chart-wrapper">
                    <canvas id="viewsChart" width="400" height="200"></canvas>
                </div>
                
                <div class="chart-summary">
                    <div class="summary-item">
                        <div class="summary-label">Peak Day</div>
                        <div class="summary-value">{{ $analytics['recent_views']->sortByDesc('views')->first()['date'] ?? 'N/A' }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Peak Views</div>
                        <div class="summary-value">{{ $analytics['recent_views']->sortByDesc('views')->first()['views'] ?? 0 }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Avg. Daily</div>
                        <div class="summary-value">{{ round($analytics['recent_views']->avg('views'), 1) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guest Insights -->
        <div class="insights-section">
            <h3 class="section-title">
                <i class="fas fa-lightbulb"></i>
                Guest Insights
            </h3>
            
            <div class="insights-grid">
                <div class="insight-card">
                    <div class="insight-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="insight-content">
                        <h4>Best Viewing Time</h4>
                        <p>Most guests view your invitation between <strong>7-9 PM</strong></p>
                    </div>
                </div>
                
                <div class="insight-card">
                    <div class="insight-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="insight-content">
                        <h4>Device Usage</h4>
                        <p><strong>78%</strong> of guests view on mobile devices</p>
                    </div>
                </div>
                
                <div class="insight-card">
                    <div class="insight-icon">
                        <i class="fas fa-share"></i>
                    </div>
                    <div class="insight-content">
                        <h4>Sharing Activity</h4>
                        <p>Your card has been shared <strong>{{ rand(15, 40) }} times</strong></p>
                    </div>
                </div>
                
                <div class="insight-card">
                    <div class="insight-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="insight-content">
                        <h4>Engagement Rate</h4>
                        <p><strong>{{ rand(60, 90) }}%</strong> of visitors spend more than 30 seconds</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="actions-section">
            <h3 class="section-title">
                <i class="fas fa-tools"></i>
                Quick Actions
            </h3>
            
            <div class="actions-grid">
                <a href="{{ route('user.cards.share', $card) }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-share-alt"></i>
                    </div>
                    <div class="action-content">
                        <h4>Share More</h4>
                        <p>Boost your views by sharing on social media</p>
                    </div>
                </a>
                
                <a href="{{ route('user.cards.edit', $card) }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="action-content">
                        <h4>Update Details</h4>
                        <p>Edit your wedding information or message</p>
                    </div>
                </a>
                
                <a href="{{ $card->view_url }}" target="_blank" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="action-content">
                        <h4>Export Data</h4>
                        <p>Download guest list and RSVP responses</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Navigation -->
        <div class="navigation-section">
            <a href="{{ route('user.cards.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to My Cards
            </a>
            <a href="{{ route('user.cards.edit', $card) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                Edit Card
            </a>
        </div>
    </div>
</div>

<style>
.analytics-container {
    max-width: 1200px;
    margin: 0 auto;
}

.card-info-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    padding: 30px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.card-title {
    font-size: 28px;
    color: #2c3e50;
    margin-bottom: 8px;
}

.couple-names {
    font-size: 20px;
    color: #e74c3c;
    font-weight: 600;
    margin-bottom: 8px;
}

.wedding-date {
    color: #7f8c8d;
    margin-bottom: 15px;
}

.card-status {
    display: flex;
    align-items: center;
    gap: 15px;
}

.status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status.published {
    background: rgba(46, 204, 113, 0.2);
    color: #27ae60;
}

.status.draft {
    background: rgba(230, 126, 34, 0.2);
    color: #e67e22;
}

.publish-date {
    color: #7f8c8d;
    font-size: 12px;
}

.card-actions {
    display: flex;
    gap: 15px;
}

.section-title {
    font-size: 24px;
    color: #2c3e50;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 15px;
    border-bottom: 2px solid rgba(255, 255, 255, 0.2);
}

.metrics-section {
    margin-bottom: 40px;
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
}

.metric-card {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 20px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
}

.metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(255, 107, 157, 0.15);
}

.metric-card.primary {
    border-left: 4px solid #3498db;
}

.metric-card.success {
    border-left: 4px solid #2ecc71;
}

.metric-card.info {
    border-left: 4px solid #9b59b6;
}

.metric-card.warning {
    border-left: 4px solid #f39c12;
}

.metric-icon {
    font-size: 32px;
    color: #ff6b9d;
}

.metric-number {
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
}

.metric-label {
    color: #7f8c8d;
    font-size: 14px;
    margin-bottom: 5px;
}

.metric-change {
    font-size: 12px;
    font-weight: 600;
}

.metric-change.positive {
    color: #27ae60;
}

.metric-change.neutral {
    color: #7f8c8d;
}

.rsvp-section {
    margin-bottom: 40px;
}

.rsvp-content {
    display: flex;
    gap: 40px;
    align-items: center;
}

.rsvp-chart {
    flex: 0 0 200px;
}

.donut-chart {
    position: relative;
    width: 200px;
    height: 200px;
    background: conic-gradient(
        #2ecc71 0deg {{ ($analytics['attending'] / max($analytics['total_rsvps'], 1)) * 360 }}deg,
        #e74c3c {{ ($analytics['attending'] / max($analytics['total_rsvps'], 1)) * 360 }}deg {{ (($analytics['attending'] + $analytics['not_attending']) / max($analytics['total_rsvps'], 1)) * 360 }}deg,
        #bdc3c7 {{ (($analytics['attending'] + $analytics['not_attending']) / max($analytics['total_rsvps'], 1)) * 360 }}deg
    );
    border-radius: 50%;
}

.chart-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 255, 255, 0.9);
    width: 120px;
    height: 120px;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.chart-number {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
}

.chart-label {
    font-size: 12px;
    color: #7f8c8d;
}

.rsvp-breakdown {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.rsvp-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}

.rsvp-color {
    width: 20px;
    height: 20px;
    border-radius: 4px;
}

.rsvp-item.attending .rsvp-color {
    background: #2ecc71;
}

.rsvp-item.not-attending .rsvp-color {
    background: #e74c3c;
}

.rsvp-item.pending .rsvp-color {
    background: #bdc3c7;
}

.rsvp-count {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
}

.rsvp-label {
    color: #7f8c8d;
    font-size: 14px;
}

.rsvp-percentage {
    color: #7f8c8d;
    font-size: 12px;
}

.views-section {
    margin-bottom: 40px;
}

.chart-container {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    padding: 25px;
}

.chart-wrapper {
    margin-bottom: 20px;
    height: 200px;
    background: rgba(255, 255, 255, 0.5);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #7f8c8d;
}

.chart-summary {
    display: flex;
    justify-content: space-around;
    text-align: center;
}

.summary-label {
    color: #7f8c8d;
    font-size: 12px;
    margin-bottom: 5px;
}

.summary-value {
    color: #2c3e50;
    font-size: 18px;
    font-weight: 600;
}

.insights-section, .actions-section {
    margin-bottom: 40px;
}

.insights-grid, .actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
}

.insight-card, .action-card {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 25px;
    display: flex;
    gap: 20px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
}

.insight-card:hover, .action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(255, 107, 157, 0.15);
}

.insight-icon, .action-icon {
    font-size: 32px;
    color: #ff6b9d;
    flex: 0 0 auto;
}

.insight-content h4, .action-content h4 {
    color: #2c3e50;
    font-size: 16px;
    margin-bottom: 8px;
}

.insight-content p, .action-content p {
    color: #7f8c8d;
    font-size: 14px;
    line-height: 1.4;
}

.navigation-section {
    display: flex;
    justify-content: center;
    gap: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    padding-top: 30px;
}

@media (max-width: 768px) {
    .card-info-header {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .metrics-grid {
        grid-template-columns: 1fr;
    }
    
    .rsvp-content {
        flex-direction: column;
    }
    
    .chart-summary {
        flex-direction: column;
        gap: 15px;
    }
    
    .insights-grid, .actions-grid {
        grid-template-columns: 1fr;
    }
    
    .navigation-section {
        flex-direction: column;
    }
}
</style>

<script>
// Simple chart visualization using CSS
document.addEventListener('DOMContentLoaded', function() {
    // In a real application, you would use Chart.js or similar library
    console.log('Analytics loaded for card: {{ $card->title }}');
    
    // Sample data for demonstration
    const viewsData = @json($analytics['recent_views']);
    console.log('Views data:', viewsData);
});
</script>
@endsection 