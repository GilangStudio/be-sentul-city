@extends('layouts.main')

@section('title', 'Dashboard')

@push('styles')
<style>
    .metric-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        border: 1px solid var(--tblr-border-color);
    }
    
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .recent-item {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        transition: background-color 0.2s ease;
    }
    
    .recent-item:hover {
        background-color: var(--tblr-bg-surface-secondary);
    }
    
    .welcome-card {
        background: linear-gradient(135deg, var(--tblr-primary) 0%, #667eea 100%);
        color: white;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .chart-container {
        height: 300px;
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .activity-timeline {
        position: relative;
        padding-left: 1.5rem;
    }
    
    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 0.5rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--tblr-border-color);
    }
    
    .activity-item {
        position: relative;
        margin-bottom: 1rem;
    }
    
    .activity-item::before {
        content: '';
        position: absolute;
        left: -1rem;
        top: 0.5rem;
        width: 8px;
        height: 8px;
        background: var(--tblr-primary);
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 0 0 2px var(--tblr-primary);
    }
</style>
@endpush

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h2 class="page-title">Dashboard</h2>
        <div class="page-subtitle">Welcome back! Here's what's happening with your Sentul City CMS.</div>
    </div>
    <div class="btn-list">
        <span class="text-secondary">
            <i class="ti ti-clock me-1"></i>
            {{ now()->format('l, d F Y') }}
        </span>
    </div>
</div>
@endsection

@section('content')

{{-- Welcome Card --}}
<div class="col-12">
    <div class="card welcome-card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-2">Welcome to Sentul City CMS</h3>
                    <p class="mb-3 opacity-75">
                        Manage your website content, track applications, and monitor your digital presence all in one place.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('news.create') }}" class="btn btn-light btn-sm">
                            <i class="ti ti-plus me-1"></i>
                            Add News
                        </a>
                        <a href="{{ route('promos.index') }}" class="btn btn-light btn-sm">
                            <i class="ti ti-photo me-1"></i>
                            Manage Promos
                        </a>
                    </div>
                </div>
                <div class="col-auto d-none d-lg-block">
                    <div class="text-end">
                        <div class="display-6 opacity-75">
                            <i class="ti ti-building-bank"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Key Metrics Row --}}
<div class="col-xl-3 col-md-6">
    <div class="card metric-card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="metric-icon bg-primary-lt text-primary me-3">
                    <i class="ti ti-news"></i>
                </div>
                <div>
                    <div class="h3 mb-0">{{ $stats['published_news'] }}</div>
                    <div class="text-secondary">Published News</div>
                    @if($stats['draft_news'] > 0)
                    <small class="text-warning">
                        <i class="ti ti-clock me-1"></i>
                        {{ $stats['draft_news'] }} drafts
                    </small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-xl-3 col-md-6">
    <div class="card metric-card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="metric-icon bg-success-lt text-success me-3">
                    <i class="ti ti-photo"></i>
                </div>
                <div>
                    <div class="h3 mb-0">{{ $stats['active_promos'] }}</div>
                    <div class="text-secondary">Active Promos</div>
                    @if($stats['total_promos'] > $stats['active_promos'])
                    <small class="text-secondary">
                        {{ $stats['total_promos'] - $stats['active_promos'] }} inactive
                    </small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-xl-3 col-md-6">
    <div class="card metric-card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="metric-icon bg-warning-lt text-warning me-3">
                    <i class="ti ti-briefcase"></i>
                </div>
                <div>
                    <div class="h3 mb-0">{{ $stats['career_positions'] }}</div>
                    <div class="text-secondary">Open Positions</div>
                    @if($stats['pending_applications'] > 0)
                    <small class="text-danger">
                        <i class="ti ti-alert-circle me-1"></i>
                        {{ $stats['pending_applications'] }} pending
                    </small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-xl-3 col-md-6">
    <div class="card metric-card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="metric-icon bg-info-lt text-info me-3">
                    <i class="ti ti-home"></i>
                </div>
                <div>
                    <div class="h3 mb-0">{{ $stats['homepage_banners'] }}</div>
                    <div class="text-secondary">Homepage Banners</div>
                    <small class="text-success">
                        <i class="ti ti-check me-1"></i>
                        Active
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Content Overview --}}
<div class="col-lg-8">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-chart-line me-2"></i>
                Content Creation Trends
            </h3>
            <div class="card-actions">
                <div class="dropdown">
                    <a href="#" class="btn-action dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="ti ti-dots-vertical"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="{{ route('news.index') }}">
                            <i class="ti ti-news me-2"></i>
                            View All News
                        </a>
                        <a class="dropdown-item" href="{{ route('promos.index') }}">
                            <i class="ti ti-photo me-2"></i>
                            View All Promos
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="chart-content-trends" class="chart-container"></div>
        </div>
    </div>
</div>

{{-- Quick Stats --}}
<div class="col-lg-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-layout-grid me-2"></i>
                Content Summary
            </h3>
        </div>
        <div class="card-body">
            <div class="list-group list-group-flush">
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar bg-primary-lt">
                                <i class="ti ti-folder"></i>
                            </span>
                        </div>
                        <div class="col text-truncate">
                            <div class="text-body d-block">News Categories</div>
                            <small class="d-block text-secondary text-truncate mt-n1">
                                {{ $stats['news_categories'] }} active categories
                            </small>
                        </div>
                        <div class="col-auto">
                            <div class="badge bg-primary text-white">{{ $stats['news_categories'] }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar bg-success-lt">
                                <i class="ti ti-users-group"></i>
                            </span>
                        </div>
                        <div class="col text-truncate">
                            <div class="text-body d-block">Partnership Items</div>
                            <small class="d-block text-secondary text-truncate mt-n1">
                                Active partnerships & programs
                            </small>
                        </div>
                        <div class="col-auto">
                            <div class="badge bg-success text-white">{{ $stats['partnership_items'] }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar bg-info-lt">
                                <i class="ti ti-tools"></i>
                            </span>
                        </div>
                        <div class="col text-truncate">
                            <div class="text-body d-block">Service Sections</div>
                            <small class="d-block text-secondary text-truncate mt-n1">
                                Services page content
                            </small>
                        </div>
                        <div class="col-auto">
                            <div class="badge bg-info text-white">{{ $stats['service_sections'] }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar bg-warning-lt">
                                <i class="ti ti-map-pin"></i>
                            </span>
                        </div>
                        <div class="col text-truncate">
                            <div class="text-body d-block">Practical Places</div>
                            <small class="d-block text-secondary text-truncate mt-n1">
                                New residents guide locations
                            </small>
                        </div>
                        <div class="col-auto">
                            <div class="badge bg-warning text-white">{{ $stats['practical_places'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent News --}}
<div class="col-lg-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-news me-2"></i>
                Recent News
            </h3>
            <div class="card-actions">
                <a href="{{ route('news.index') }}" class="btn btn-outline-primary btn-sm">
                    View All
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($recentNews->count() > 0)
            <div class="list-group list-group-flush">
                @foreach($recentNews as $news)
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            @if($news->image_url)
                            <img src="{{ $news->image_url }}" class="avatar" alt="{{ $news->title }}">
                            @else
                            <span class="avatar bg-secondary-lt">
                                <i class="ti ti-news"></i>
                            </span>
                            @endif
                        </div>
                        <div class="col text-truncate">
                            <a href="{{ route('news.edit', $news) }}" class="text-body d-block font-weight-medium">
                                {{ $news->title }}
                            </a>
                            <small class="d-block text-secondary text-truncate mt-n1">
                                <i class="ti ti-folder me-1"></i>
                                {{ $news->category->name }}
                                <span class="mx-1">•</span>
                                {{ $news->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-{{ $news->status === 'published' ? 'success' : 'warning' }}-lt">
                                {{ $news->status_text }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty py-4">
                <div class="empty-icon">
                    <i class="ti ti-news icon"></i>
                </div>
                <p class="empty-title">No news articles yet</p>
                <p class="empty-subtitle text-secondary">
                    Start by creating your first news article.
                </p>
                <div class="empty-action">
                    <a href="{{ route('news.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>
                        Create News
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Recent Career Applications --}}
<div class="col-lg-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-briefcase me-2"></i>
                Recent Applications
            </h3>
            <div class="card-actions">
                <a href="{{ route('careers.positions.index') }}" class="btn btn-outline-primary btn-sm">
                    View All
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($recentApplications->count() > 0)
            <div class="list-group list-group-flush">
                @foreach($recentApplications as $application)
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar">
                                {{ substr($application->name, 0, 1) }}
                            </span>
                        </div>
                        <div class="col text-truncate">
                            <a href="{{ route('careers.positions.applications.show', [$application->position, $application]) }}" 
                               class="text-body d-block font-weight-medium">
                                {{ $application->name }}
                            </a>
                            <small class="d-block text-secondary text-truncate mt-n1">
                                <i class="ti ti-briefcase me-1"></i>
                                {{ $application->position->title }}
                                <span class="mx-1">•</span>
                                {{ $application->applied_ago }}
                            </small>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-{{ $application->status_color }}-lt">
                                {{ $application->status_text }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty py-4">
                <div class="empty-icon">
                    <i class="ti ti-briefcase icon"></i>
                </div>
                <p class="empty-title">No applications yet</p>
                <p class="empty-subtitle text-secondary">
                    Applications will appear here when candidates apply.
                </p>
                <div class="empty-action">
                    <a href="{{ route('careers.positions.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>
                        Add Position
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- News by Category Chart --}}
@if($newsByCategory->count() > 0)
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-chart-pie me-2"></i>
                News by Category
            </h3>
        </div>
        <div class="card-body">
            <div id="chart-news-categories" class="chart-container"></div>
        </div>
    </div>
</div>
@endif

{{-- Quick Actions --}}
{{-- <div class="col-lg-{{ $newsByCategory->count() > 0 ? '6' : '12' }}">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-bolt me-2"></i>
                Quick Actions
            </h3>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-6">
                    <a href="{{ route('news.create') }}" class="btn btn-outline-primary w-100">
                        <i class="ti ti-plus me-1"></i>
                        Add News
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('promos.index') }}" class="btn btn-outline-success w-100">
                        <i class="ti ti-photo me-1"></i>
                        Manage Promos
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('careers.positions.create') }}" class="btn btn-outline-warning w-100">
                        <i class="ti ti-briefcase me-1"></i>
                        Add Job Position
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('home-page.index') }}" class="btn btn-outline-info w-100">
                        <i class="ti ti-home me-1"></i>
                        Homepage Settings
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('partnerships.items.create') }}" class="btn btn-outline-secondary w-100">
                        <i class="ti ti-users-group me-1"></i>
                        Add Partnership
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('services.sections.create') }}" class="btn btn-outline-dark w-100">
                        <i class="ti ti-tools me-1"></i>
                        Add Service
                    </a>
                </div>
            </div>
        </div>
    </div>
</div> --}}

@endsection

@push('scripts')
@include('components.toast')

@if(session('success'))
    <script>
        showToast('{{ session('success') }}', 'success');
    </script>
@endif
@if(session('error'))
    <script>
        showToast('{{ session('error') }}','error');
    </script>
@endif

<script>
    document.addEventListener("DOMContentLoaded", function () {
        setupContentTrendsChart();
        @if($newsByCategory->count() > 0)
        setupNewsCategoriesChart();
        @endif
    });

    function setupContentTrendsChart() {
        const monthlyData = @json($monthlyData);
        
        if (window.ApexCharts) {
            const options = {
                chart: {
                    type: 'area',
                    fontFamily: 'inherit',
                    height: 300,
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false,
                    },
                    animations: {
                        enabled: true,
                    },
                },
                dataLabels: {
                    enabled: false,
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,      // Intensitas shade (0-1)
                        opacityFrom: 0.3,       // Opacity di bagian atas (30%)
                        opacityTo: 0.1,         // Opacity di bagian bawah (10%)
                        stops: [0, 90, 100]     // Titik gradient stops
                    }
                },
                stroke: {
                    width: 2,
                    lineCap: 'round',
                    curve: 'smooth',
                },
                series: [
                    {
                        name: 'News Articles',
                        data: monthlyData.map(item => item.news)
                    },
                    {
                        name: 'Promos',
                        data: monthlyData.map(item => item.promos)
                    }
                ],
                tooltip: {
                    theme: 'dark'
                },
                grid: {
                    padding: {
                        top: -20,
                        right: 0,
                        left: -4,
                        bottom: -4
                    },
                    strokeDashArray: 4,
                },
                xaxis: {
                    labels: {
                        padding: 0,
                    },
                    tooltip: {
                        enabled: false
                    },
                    axisBorder: {
                        show: false,
                    },
                    categories: monthlyData.map(item => item.month),
                },
                yaxis: {
                    labels: {
                        padding: 4
                    },
                },
                colors: ['var(--tblr-primary)', 'var(--tblr-success)'],
                legend: {
                    show: true,
                    position: 'bottom',
                    offsetY: 12,
                    markers: {
                        width: 10,
                        height: 10,
                        radius: 100,
                    },
                    itemMargin: {
                        horizontal: 8,
                        vertical: 8
                    },
                },
            };
            
            const chart = new ApexCharts(document.querySelector("#chart-content-trends"), options);
            chart.render();
        }
    }

    @if($newsByCategory->count() > 0)
    function setupNewsCategoriesChart() {
        const categoryData = @json($newsByCategory);
        
        if (window.ApexCharts) {
            const options = {
                chart: {
                    type: 'donut',
                    fontFamily: 'inherit',
                    height: 300,
                    sparkline: {
                        enabled: true
                    },
                    animations: {
                        enabled: true,
                    },
                },
                fill: {
                    opacity: 1,
                },
                series: categoryData.map(item => item.news_count),
                labels: categoryData.map(item => item.name),
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function(val) {
                            return val + ' articles'
                        }
                    }
                },
                grid: {
                    strokeDashArray: 4,
                },
                colors: [
                    'var(--tblr-primary)', 
                    'var(--tblr-success)', 
                    'var(--tblr-warning)', 
                    'var(--tblr-info)', 
                    'var(--tblr-danger)'
                ],
                legend: {
                    show: true,
                    position: 'bottom',
                    offsetY: 12,
                    markers: {
                        width: 10,
                        height: 10,
                        radius: 100,
                    },
                    itemMargin: {
                        horizontal: 8,
                        vertical: 8
                    },
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                        }
                    }
                },
            };
            
            const chart = new ApexCharts(document.querySelector("#chart-news-categories"), options);
            chart.render();
        }
    }
    @endif
</script>
@endpush