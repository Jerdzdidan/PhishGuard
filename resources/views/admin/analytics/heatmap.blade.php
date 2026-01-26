<!-- resources/views/admin/analytics/heatmap.blade.php -->
@extends('admin.layout.base')

@section('title')
DIFFICULTY HEATMAP
@endsection

@section('nav_title')
DIFFICULTY HEATMAP
@endsection

@section('style')
<style>
.heatmap-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 10px;
    padding: 20px;
}
.heatmap-cell {
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    padding: 10px;
    transition: transform 0.3s;
    cursor: pointer;
}
.heatmap-cell:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.heatmap-label {
    font-size: 11px;
    text-align: center;
    margin-top: 8px;
    font-weight: 500;
}
.heatmap-value {
    font-size: 20px;
    font-weight: 700;
}
.legend {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}
.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
}
.legend-color {
    width: 30px;
    height: 30px;
    border-radius: 4px;
}
</style>
@endsection

@section('body')
<!-- Quiz Heatmap -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-2">Quiz Questions Difficulty Heatmap</h5>
                <p class="text-muted mb-0">Visual representation of question difficulty based on success rates</p>
            </div>
            <div class="card-body">
                @if(count($quizHeatmap) > 0)
                    <!-- Legend -->
                    <div class="mb-4 pb-3 border-bottom">
                        <h6 class="mb-3">Difficulty Legend</h6>
                        <div class="legend">
                            <div class="legend-item">
                                <div class="legend-color" style="background: linear-gradient(135deg, #28a745, #20c997);"></div>
                                <span>Easy (80-100%)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background: linear-gradient(135deg, #17a2b8, #007bff);"></div>
                                <span>Medium (60-79%)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background: linear-gradient(135deg, #ffc107, #fd7e14);"></div>
                                <span>Hard (40-59%)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background: linear-gradient(135deg, #dc3545, #bd2130);"></div>
                                <span>Very Hard (0-39%)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Heatmap Grid -->
                    <div class="heatmap-container">
                        @foreach($quizHeatmap as $item)
                            @php
                                $value = $item['value'];
                                if ($value >= 80) {
                                    $bgColor = 'linear-gradient(135deg, #28a745, #20c997)';
                                    $textColor = 'white';
                                } elseif ($value >= 60) {
                                    $bgColor = 'linear-gradient(135deg, #17a2b8, #007bff)';
                                    $textColor = 'white';
                                } elseif ($value >= 40) {
                                    $bgColor = 'linear-gradient(135deg, #ffc107, #fd7e14)';
                                    $textColor = 'white';
                                } else {
                                    $bgColor = 'linear-gradient(135deg, #dc3545, #bd2130)';
                                    $textColor = 'white';
                                }
                            @endphp
                            <div class="heatmap-cell" 
                                 style="background: {{ $bgColor }}; color: {{ $textColor }};"
                                 data-bs-toggle="tooltip" 
                                 title="{{ $item['label'] }}">
                                <div class="heatmap-value">{{ number_format($value, 1) }}%</div>
                                <div class="heatmap-label">Q{{ $item['id'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ri-bar-chart-box-line ri-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No quiz data available for heatmap</h6>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Simulation Heatmap -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-2">Simulation Scenarios Difficulty Heatmap</h5>
                <p class="text-muted mb-0">Visual representation of scenario difficulty based on success rates</p>
            </div>
            <div class="card-body">
                @if(count($simulationHeatmap) > 0)
                    <!-- Legend -->
                    <div class="mb-4 pb-3 border-bottom">
                        <h6 class="mb-3">Difficulty Legend</h6>
                        <div class="legend">
                            <div class="legend-item">
                                <div class="legend-color" style="background: linear-gradient(135deg, #28a745, #20c997);"></div>
                                <span>Easy (80-100%)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background: linear-gradient(135deg, #17a2b8, #007bff);"></div>
                                <span>Medium (60-79%)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background: linear-gradient(135deg, #ffc107, #fd7e14);"></div>
                                <span>Hard (40-59%)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background: linear-gradient(135deg, #dc3545, #bd2130);"></div>
                                <span>Very Hard (0-39%)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Heatmap Grid -->
                    <div class="heatmap-container">
                        @foreach($simulationHeatmap as $index => $item)
                            @php
                                $value = $item['value'];
                                if ($value >= 80) {
                                    $bgColor = 'linear-gradient(135deg, #28a745, #20c997)';
                                    $textColor = 'white';
                                } elseif ($value >= 60) {
                                    $bgColor = 'linear-gradient(135deg, #17a2b8, #007bff)';
                                    $textColor = 'white';
                                } elseif ($value >= 40) {
                                    $bgColor = 'linear-gradient(135deg, #ffc107, #fd7e14)';
                                    $textColor = 'white';
                                } else {
                                    $bgColor = 'linear-gradient(135deg, #dc3545, #bd2130)';
                                    $textColor = 'white';
                                }
                            @endphp
                            <div class="heatmap-cell" 
                                 style="background: {{ $bgColor }}; color: {{ $textColor }};"
                                 data-bs-toggle="tooltip" 
                                 title="{{ $item['label'] }}">
                                <div class="heatmap-value">{{ number_format($value, 1) }}%</div>
                                <div class="heatmap-label">S{{ $index + 1 }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ri-bar-chart-box-line ri-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No simulation data available for heatmap</h6>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection