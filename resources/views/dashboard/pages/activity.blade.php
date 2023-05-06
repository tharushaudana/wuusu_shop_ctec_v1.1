@extends('dashboard.layouts.blankpage')
@section('title', 'Activity Info #'.$audit->id)
@section('content')
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

        $target = '<span class="badge badge-secondary">'.str_replace("App\Models\\", '', $audit->auditable_type).' #'.$audit->auditable_id.'</span>';
    @endphp

    <table>
        <tr>
            <td>Event:&nbsp;&nbsp;</td>
            <td>{!! $event !!}</td>
        </tr>
        <tr>
            <td>Target:&nbsp;&nbsp;</td>
            <td>{!! $target !!}</td>
        </tr>
        <tr>
            <td>ID of Target:&nbsp;&nbsp;</td>
            <th style="font-family: 'monospace';">{{ $audit->auditable_id }}</td>
        </tr>
        <tr>
            <td>IP Address:&nbsp;&nbsp;</td>
            <th style="font-family: 'monospace';">{{ $audit->ip_address }}</td>
        </tr>
        <tr>
            <td>Date & Time:&nbsp;&nbsp;</td>
            <th><span style="font-family: 'monospace';">{{ \Carbon\Carbon::parse($audit->created_at)->format('F j, Y \a\t g:i A') }}</span> &nbsp;&nbsp; ({{ \Carbon\Carbon::parse($audit->created_at)->diffForHumans() }})</td>
        </tr>
    </table>

    <br>

    @if (!empty($audit->old_values) || !empty($audit->new_values))
        <div class="card mb-4">
            <div class="d-block card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Value Changes</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm">
                        <h5>Old Values</h5>
                        @if (!empty($audit->old_values))
                            <div style="opacity: 0.5">
                                @include('dashboard.components.treeview', [
                                    'topname' => $target, 
                                    'data' => $audit->old_values
                                ])
                            </div>
                        @else
                            <div style="display: flex; justify-content: center; align-items: center; height: 80%;">
                                <span class="text-gray-500">Empty</span>
                            </div>     
                        @endif
                    </div>
                    <div class="col-sm">
                        <h5>New Values</h5>
                        @if (!empty($audit->new_values))
                            @include('dashboard.components.treeview', [
                                'topname' => $target, 
                                'data' => $audit->new_values
                            ])                       
                        @else
                            <div style="display: flex; justify-content: center; align-items: center; height: 80%;">
                                <span class="text-gray-500">Empty</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection