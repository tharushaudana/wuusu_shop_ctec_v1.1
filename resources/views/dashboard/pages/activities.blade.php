@extends('dashboard.layouts.blankpage')
@section('title', is_null($user) ? "My Activities" : "Activities of ".$user->name)

@php
    $filter_form_content_single = '
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" name="date" id="date" value="'.$date.'">
        </div>
    ';

    $filter_form_content_range = '
        <div class="form-group">
            <label for="date_start">Date Start</label>
            <input type="date" class="form-control" name="date_start" id="date_start" value="'.$date_start.'">
        </div>
        <div class="form-group">
            <label for="date_end">Date End</label>
            <input type="date" class="form-control" name="date_end" id="date_end" value="'.$date_end.'">
        </div>
    ';
@endphp

@section('content') 
    <div class="card mb-4">
        <a href="#collapseCardFilterActivities" class="d-block card-header py-3" data-toggle="collapse"
            role="button" aria-expanded="true" aria-controls="collapseCardFilterActivities">
            <h6 class="m-0 font-weight-bold text-primary">Filter&nbsp;&nbsp;{!! !is_null($date) || !is_null($date_start) || !is_null($date_end) ? '<span class="badge badge-info">ENABLED</span>' : '<span class="badge badge-secondary">NOT ENABLED</span>' !!}</h6>
        </a>
        <div class="collapse hide" id="collapseCardFilterActivities">
            <div class="card-body">
                <form action="{{ url(Request::path().'/filter') }}" method="POST">
                    @csrf

                    <input type="radio" name="filter_type" value="single" {{ $filter_type != 'range' ? 'checked' : '' }}>&nbsp;Single
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="filter_type" value="range" {{ $filter_type == 'range' ? 'checked' : '' }}>&nbsp;Range
                    <hr>

                    <div id="filterFormContent">
                        @switch($filter_type)
                            @case('single')
                                {!! $filter_form_content_single !!}
                                @break
                            @case('range')
                                {!! $filter_form_content_range !!}
                                @break
                            @default
                                {!! $filter_form_content_single !!}
                        @endswitch
                    </div>

                    <input type="hidden" name="g-recaptcha-response">
                    <button class="btn btn-primary g-recaptcha" onclick="onClickSubmit(event, this)">Filter</button>
                </form>
            </div>
        </div>
    </div>

    @if (is_null($user) || Auth::user()->hasPrivilege(config('userprivis.MANAGE_USERS.DOWNLOAD_USER_ACTIVITIES')))
        @if (!empty($audits->total()))
            <form action="{{ url(Request::path().'/download') }}" method="POST">
                @csrf

                @foreach (request()->query() as $key => $value)
                    @if ($key != 'page')
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach

                <input type="hidden" name="g-recaptcha-response">
                <button class="btn btn-success g-recaptcha" onclick="onClickSubmit(event, this)">
                    <i class="fa-solid fa-file-arrow-down"></i>
                    &nbsp;
                    Download Excel
                </button>
            </form>
        @endif
    @endif

    <table class="table table-bordered" id="dataTableActivities" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Event</th>
                <th>Target</th>
                <th>Target ID</th>
                <th>Date & Time</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>ID</th>
                <th>Event</th>
                <th>Target</th>
                <th>Target ID</th>
                <th>Date & Time</th>
            </tr>
        </tfoot>
        <tbody>
            @foreach ($audits->items() as $audit)
                @php
                    $event = '';
                    
                    switch ($audit->event) {
                        case 'created':
                            $event = '<span class="badge badge-primary">CREATED</span>';
                            break;
                        case 'updated':
                            $event = '<span class="badge badge-warning">UPDATED</span>';
                            break;
                        case 'deleted':
                            $event = '<span class="badge badge-danger">DELETED</span>';
                            break;
                        default:
                            $event = '<span class="badge badge-secondary">'.strtoupper($audit->event).'</span>';
                            break;
                    }

                    $target = '<span class="badge badge-secondary">'.str_replace("App\Models\\", '', $audit->auditable_type).'</span>';
                @endphp

                <tr>
                    <td><a target="_blank" href="{{ is_null($user) ? route('web.pages.my.activity', $audit->id) : route('web.pages.user.activity', [$user->id, $audit->id]) }}">{{ $audit->id }}</a></td>
                    <td style="font-family: 'monospace';">{!! $event !!}</td>
                    <td style="font-family: 'monospace';">{!! $target !!}</td>
                    <td>{{ $audit->auditable_id }}</td>
                    <td>{{ \Carbon\Carbon::parse($audit->created_at)->format('F j, Y \a\t g:i A') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="height: 8px;"></div>

    <table style="width: 100%;">
        <tr>
            <td style="vertical-align: top;">
                Showing {{ (($audits->currentPage() - 1) * $audits->perPage()) + 1 }} to {{ (($audits->currentPage() - 1) * $audits->perPage()) + $audits->count() }} of {{ $audits->total() }} entries
            </td>
            <td style="vertical-align: top;">
                <div class="d-flex flex-row-reverse">{{ $audits->appends(request()->query())->links() }}</div>
            </td>
        </tr>
    </table>

    <div style="height: 80px;"></div>
@endsection

@section('stylesheets')
    <link href="{{ asset('dashboard/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    <script src="{{ asset("dashboard/vendor/datatables/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("dashboard/vendor/datatables/dataTables.bootstrap4.min.js") }}"></script>

    <script>
        $(function() {
            $(document).ready(function() {
                $('#dataTableActivities').DataTable({
                    paging:   false,
                    info:     false,
                    order: [[0, 'desc']],
                });
            });

            $('input:radio[name="filter_type"]').change(function() {
                if ($(this).val() == 'single') {
                    $('#filterFormContent').html(`{!! $filter_form_content_single !!}`);
                } else if ($(this).val() == 'range') {
                    $('#filterFormContent').html(`{!! $filter_form_content_range !!}`);
                }
            });
        });
    </script>

    @include('dashboard.includes.recaptcha-submit')
@endsection