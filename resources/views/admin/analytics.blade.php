@extends('layouts.admin.admin')

@section('title', 'Analytics')

@section('content')
<div class="page-content">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-chart-line"></i>
            Analytics Dashboard
        </h1>
        <p class="page-subtitle">System performance and usage statistics</p>
    </div>

    <div class="analytics-grid">
        <!-- Monthly Cards Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <h3>Wedding Cards Created This Year</h3>
                <p>Monthly breakdown of card creation</p>
            </div>
            <div class="chart-content">
                <div class="simple-chart">
                    @for($i = 1; $i <= 12; $i++)
                        <div class="chart-bar">
                            <div class="bar" style="height: {{ ($monthlyCards[$i] ?? 0) * 20 }}px"></div>
                            <span class="month">{{ date('M', mktime(0, 0, 0, $i, 1)) }}</span>
                            <span class="count">{{ $monthlyCards[$i] ?? 0 }}</span>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Template Usage -->
        <div class="chart-card">
            <div class="chart-header">
                <h3>Most Popular Templates</h3>
                <p>Templates ranked by usage</p>
            </div>
            <div class="chart-content">
                <div class="template-list">
                    @foreach($templateUsage as $template)
                        <div class="template-stat">
                            <div class="template-name">{{ $template->name }}</div>
                            <div class="usage-bar">
                                <div class="bar-fill" style="width: {{ $template->wedding_cards_count * 10 }}%"></div>
                                <span class="count">{{ $template->wedding_cards_count }} cards</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.page-content {
    padding: 20px;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    overflow-x: hidden;
}

.page-header {
    margin-bottom: 30px;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.page-title {
    color: #2d3748;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-subtitle {
    color: #718096;
    margin: 0;
}

.analytics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 20px;
}

.chart-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.chart-header {
    padding: 20px;
    border-bottom: 1px solid #e2e8f0;
}

.chart-header h3 {
    color: #2d3748;
    margin: 0 0 5px 0;
}

.chart-header p {
    color: #718096;
    margin: 0;
    font-size: 14px;
}

.chart-content {
    padding: 20px;
}

.simple-chart {
    display: flex;
    gap: 10px;
    align-items: end;
    justify-content: space-between;
    height: 200px;
}

.chart-bar {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
}

.bar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    width: 20px;
    border-radius: 4px 4px 0 0;
    min-height: 5px;
}

.month {
    font-size: 12px;
    color: #718096;
}

.count {
    font-size: 10px;
    color: #4a5568;
    font-weight: 600;
}

.template-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.template-stat {
    padding: 10px 0;
}

.template-name {
    color: #2d3748;
    margin-bottom: 8px;
    font-weight: 500;
}

.usage-bar {
    position: relative;
    background: #e2e8f0;
    height: 25px;
    border-radius: 12px;
    overflow: hidden;
}

.bar-fill {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    height: 100%;
    border-radius: 12px;
    min-width: 5%;
}

.usage-bar .count {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #4a5568;
    font-size: 12px;
    font-weight: 600;
}

/* Responsive adjustments for analytics */
@media (max-width: 768px) {
    /* CRITICAL: Ensure header and burger button work properly */
    .header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 0 15px !important;
    }
    
    .header-left {
        display: flex !important;
        align-items: center !important;
        gap: 15px !important;
    }
    
    .menu-toggle {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    .page-content {
        padding: 15px 10px !important;
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
    }
    
    .analytics-grid {
        grid-template-columns: 1fr;
        gap: 15px;
        width: 100% !important;
        max-width: 100% !important;
    }
    
    .chart-card {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        margin: 0 !important;
    }
    
    .page-header {
        margin-bottom: 25px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .page-title {
        font-size: 1.5rem;
        flex-direction: column;
        gap: 8px;
    }
    
    .page-subtitle {
        font-size: 1rem;
    }
    
    .chart-header {
        padding: 15px;
    }
    
    .chart-content {
        padding: 15px;
    }
    
    .simple-chart {
        gap: 5px;
        height: 150px;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .chart-bar {
        min-width: 25px;
    }
    
    .bar {
        width: 15px;
    }
    
    .template-list {
        gap: 12px;
    }
    
    .usage-bar .count {
        font-size: 11px;
        right: 8px;
    }
}

@media (max-width: 480px) {
    .page-content {
        padding: 10px 5px !important;
        width: 100% !important;
        max-width: 100vw !important;
        overflow-x: hidden !important;
    }
    
    .page-title {
        font-size: 1.3rem;
    }
    
    .page-subtitle {
        font-size: 0.9rem;
    }
    
    .chart-header {
        padding: 12px;
    }
    
    .chart-content {
        padding: 12px;
    }
    
    .chart-header h3 {
        font-size: 1rem;
        margin-bottom: 3px;
    }
    
    .chart-header p {
        font-size: 12px;
    }
    
    .simple-chart {
        height: 120px;
        gap: 3px;
    }
    
    .bar {
        width: 12px;
    }
    
    .month {
        font-size: 10px;
    }
    
    .count {
        font-size: 9px;
    }
    
    .template-name {
        font-size: 0.9rem;
        margin-bottom: 6px;
    }
    
    .usage-bar {
        height: 20px;
    }
    
    .usage-bar .count {
        font-size: 10px;
        right: 6px;
    }
}
</style>
@endsection 