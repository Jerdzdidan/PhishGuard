@extends('admin.layout.base')

@section('title')
Assessment Analytics
@endsection

@section('nav_title')
Assessment Analytics
@endsection

@section('body')
<div class="container-fluid">
    <div class="content-container">
        <h4 class="mb-4">Assessment Analytics</h4>

        {{-- Section Filter --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Filter by Section</label>
                        <select name="section_id" class="form-select">
                            <option value="">All Sections</option>
                            @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ $sectionId == $section->id ? 'selected' : '' }}>
                                {{ $section->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Summary Stats --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Pre-Assessments</h6>
                        <h2 class="mb-0 text-warning">{{ $stats['total_pre'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Post-Assessments</h6>
                        <h2 class="mb-0 text-success">{{ $stats['total_post'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Avg Pre Score</h6>
                        <h2 class="mb-0 text-warning">{{ $stats['avg_pre_score'] }}%</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Avg Improvement</h6>
                        <h2 class="mb-0 {{ $stats['avg_improvement'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $stats['avg_improvement'] >= 0 ? '+' : '' }}{{ $stats['avg_improvement'] }}%
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- Student Comparisons Table --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Pre vs Post Comparison by Student</h5>
            </div>
            <div class="card-body">
                @if(count($studentComparisons) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Section</th>
                                <th>Pre-Assessment</th>
                                <th>Post-Assessment</th>
                                <th>Improvement</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentComparisons as $comparison)
                            <tr>
                                <td>{{ $comparison['student'] }}</td>
                                <td>{{ $comparison['section'] }}</td>
                                <td>
                                    <span class="badge bg-label-warning">{{ $comparison['pre_score'] }}%</span>
                                </td>
                                <td>
                                    <span class="badge bg-label-success">{{ $comparison['post_score'] }}%</span>
                                </td>
                                <td>
                                    @php
                                        $impr = $comparison['improvement'];
                                        $color = $impr >= 0 ? 'success' : 'danger';
                                        $icon = $impr >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt';
                                    @endphp
                                    <span class="text-{{ $color }} fw-bold">
                                        <i class="bx {{ $icon }}"></i>
                                        {{ abs($impr) }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="fa-solid fa-chart-bar fa-3x mb-3"></i>
                    <p>No students have completed both pre and post assessments yet.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
