{{-- Dashboard Chart Card Component - Matches Landing Page Design Quality --}}
@props([
    'title' => 'إحصائيات',
    'subtitle' => '',
    'chartId' => 'chart-' . uniqid(),
    'chartType' => 'area', // 'area', 'line', 'bar', 'donut'
    'height' => '350',
    'series' => [],
    'categories' => [],
    'colors' => ['#0069af', '#199b69'], // medical-blue-500, medical-green-500
])

@php
    // Convert PHP arrays to JSON for JavaScript
    $seriesJson = json_encode($series);
    $categoriesJson = json_encode($categories);
    $colorsJson = json_encode($colors);
@endphp

<div class="bg-white rounded-2xl p-6 shadow-medical hover:shadow-medical-lg transition-all duration-300">
    {{-- Header --}}
    <div class="mb-6">
        <h3 class="text-lg font-bold text-medical-gray-900 font-display">{{ $title }}</h3>
        @if ($subtitle)
            <p class="text-sm text-medical-gray-600 mt-1">{{ $subtitle }}</p>
        @endif
    </div>

    {{-- Chart Container --}}
    <div id="{{ $chartId }}" class="w-full" style="min-height: {{ $height }}px;"></div>

    {{-- Additional Content Slot --}}
    @if ($slot->isNotEmpty())
        <div class="mt-6 pt-6 border-t border-medical-gray-200">
            {{ $slot }}
        </div>
    @endif
</div>

{{-- ApexCharts CDN (only load once) --}}
@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js"></script>
    @endpush
@endonce

{{-- Chart Initialization Script --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartElement = document.querySelector('#{{ $chartId }}');
    if (!chartElement) return;

    const series = {!! $seriesJson !!};
    const categories = {!! $categoriesJson !!};
    const colors = {!! $colorsJson !!};

    // Common chart options matching medical theme
    const commonOptions = {
        chart: {
            fontFamily: 'Cairo, Tajawal, sans-serif',
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false,
                },
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
            },
        },
        colors: colors,
        dataLabels: {
            enabled: false,
        },
        stroke: {
            curve: 'smooth',
            width: 3,
        },
        grid: {
            borderColor: '#e5e7eb',
            strokeDashArray: 4,
            xaxis: {
                lines: {
                    show: false,
                },
            },
            yaxis: {
                lines: {
                    show: true,
                },
            },
            padding: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 10,
            },
        },
        xaxis: {
            categories: categories,
            labels: {
                style: {
                    colors: '#6b7280',
                    fontSize: '12px',
                },
            },
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#6b7280',
                    fontSize: '12px',
                },
                formatter: function(value) {
                    return Math.round(value);
                },
            },
        },
        tooltip: {
            theme: 'light',
            style: {
                fontSize: '12px',
                fontFamily: 'Cairo, Tajawal, sans-serif',
            },
            y: {
                formatter: function(value) {
                    return Math.round(value);
                },
            },
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            fontSize: '13px',
            fontFamily: 'Cairo, Tajawal, sans-serif',
            labels: {
                colors: '#374151',
            },
            markers: {
                width: 12,
                height: 12,
                radius: 3,
            },
        },
    };

    // Chart type specific options
    let chartOptions = {
        series: series,
        ...commonOptions,
    };

    if ('{{ $chartType }}' === 'area') {
        chartOptions.chart.type = 'area';
        chartOptions.chart.height = {{ $height }};
        chartOptions.fill = {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.1,
                stops: [0, 90, 100],
            },
        };
    } else if ('{{ $chartType }}' === 'line') {
        chartOptions.chart.type = 'line';
        chartOptions.chart.height = {{ $height }};
    } else if ('{{ $chartType }}' === 'bar') {
        chartOptions.chart.type = 'bar';
        chartOptions.chart.height = {{ $height }};
        chartOptions.plotOptions = {
            bar: {
                borderRadius: 8,
                columnWidth: '60%',
            },
        };
    } else if ('{{ $chartType }}' === 'donut') {
        chartOptions.chart.type = 'donut';
        chartOptions.chart.height = {{ $height }};
        chartOptions.plotOptions = {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: '14px',
                            fontFamily: 'Cairo, Tajawal, sans-serif',
                            color: '#374151',
                        },
                        value: {
                            show: true,
                            fontSize: '24px',
                            fontFamily: 'Cairo, Tajawal, sans-serif',
                            fontWeight: 'bold',
                            color: '#111827',
                        },
                    },
                },
            },
        };
        chartOptions.labels = categories;
        delete chartOptions.xaxis;
    }

    // Render chart
    const chart = new ApexCharts(chartElement, chartOptions);
    chart.render();
});
</script>
@endpush

