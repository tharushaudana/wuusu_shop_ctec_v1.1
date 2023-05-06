@extends('dashboard.layouts.page')
@section('title', 'Your Other Active Sessions')
@section('content')      
    <div class="card shadow" style="border: none;">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Admin Sessions (Web Sessions)</h6>
        </div>
        <div class="card-body">
            @forelse ($websessions as $ses)
                @php
                    $icon = '';

                    switch ($ses->user_agent->browser()) {
                        case 'Chrome':
                            $icon = '<i class="fa-brands fa-chrome"></i>';
                            break;

                        case 'Edge':
                            $icon = '<i class="fa-brands fa-edge"></i>';
                            break;

                        case 'Firefox':
                            $icon = '<i class="fa-brands fa-firefox-browser"></i>';
                            break;

                        case 'Mozilla':
                            $icon = '<i class="fa-brands fa-firefox-browser"></i>';
                            break;

                        case 'Opera':
                            $icon = '<i class="fa-brands fa-opera"></i>';
                            break;

                        case 'Opera Mini':
                            $icon = '<i class="fa-brands fa-opera"></i>';
                            break;

                        case 'Safari':
                            $icon = '<i class="fa-brands fa-safari"></i>';
                            break;

                        case 'IE':
                            $icon = '<i class="fa-brands fa-internet-explorer"></i>';
                            break;
                        
                        default:
                            $icon = '<i class="fa-solid fa-question"></i>';
                            break;
                    }    
                @endphp

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex bd-highlight">
                            <div class="p-2 bd-highlight" style="width: 65px; text-align: center;">
                                <div style="height: 100%; display: flex; justify-content: center; align-items: center">
                                    <div>
                                        <span style="font-size: 35px; width:">{!! $icon !!}</span>
                                        <div style="height: 10px"></div>
                                        <a class="text-danger" style="font-size: 10px;" href="#" data-toggle="modal" data-target="#terminateWebSessionModal_{{ $ses->id }}"><b>Terminate</b></a>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2 bd-highlight flex-fill">
                                <span><b>{{ $ses->user_agent->browser().' ('.$ses->user_agent->platform().')' }}</b></span><br>
                                <table style="font-size: 10px;">
                                    <tr>
                                        <td>Last activity&nbsp;</td>
                                        <th>{{ \Carbon\Carbon::parse($ses->last_used_at)->diffForHumans() }}</th>
                                    </tr>
                                    <tr>
                                        <td>IP Address&nbsp;</td>
                                        <th>{{ $ses->ip_address }}</th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="terminateWebSessionModal_{{ $ses->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Terminate Session?</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">Select "Terminate" below if you are want to terminate selected session.</div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                <form action="{{ url(Request::path()."/terminate/websession/".$ses->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="g-recaptcha-response">
                                    <button class="btn btn-danger btn-user btn-block g-recaptcha" onclick="onClickSubmit(event, this)">Terminate</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="height: 15px"></div>
            @empty
                You havn't any other active session(s).
            @endforelse
        </div>
    </div>

    <br>

    <div class="card shadow" style="border: none;">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Client Sessions</h6>
        </div>
        <div class="card-body">
            @forelse ($apisessions as $ses)
                @php
                    $icon = '';
                    $name = '';

                    switch ($ses->os) {
                        case 'windows':
                            $icon = '<i class="fa-brands fa-windows"></i>';
                            break;
                        
                        default:
                            $icon = '<i class="fa-solid fa-question"></i>';
                            break;
                    }    

                    if (!is_null($ses->hostname)) {
                        $name .= $ses->hostname;
                    } else {
                        $name .= 'Unknown';
                    }

                    if (!is_null($ses->os)) {
                        $name .= ' ('.ucfirst($ses->os).')';
                    }
                @endphp

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex bd-highlight">
                            <div class="p-2 bd-highlight" style="width: 65px; text-align: center;">
                                <div style="height: 100%; display: flex; justify-content: center; align-items: center">
                                    <div>
                                        <span style="font-size: 35px; width:">{!! $icon !!}</span>
                                        <div style="height: 10px"></div>
                                        <a class="text-danger" style="font-size: 10px;" href="#" data-toggle="modal" data-target="#terminateApiSessionModal_{{ $ses->id }}"><b>Terminate</b></a>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2 bd-highlight flex-fill">
                                <span><b>{{ $name }}</b></span><br>
                                <table style="font-size: 10px;">
                                    <tr>
                                        <td>Last activity&nbsp;</td>
                                        <th>{{ \Carbon\Carbon::parse($ses->token()->last_used_at)->diffForHumans() }}</th>
                                    </tr>
                                    <tr>
                                        <td>IP Address&nbsp;</td>
                                        <th>{{ $ses->ip_address }}</th>
                                    </tr>
                                    <tr>
                                        <td>Logged at&nbsp;</td>
                                        <th>{{ \Carbon\Carbon::parse($ses->token()->created_at)->format('F j, Y \a\t g:i A') }}</th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="terminateApiSessionModal_{{ $ses->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Terminate Session?</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">Select "Terminate" below if you are want to terminate selected session.</div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                <form action="{{ url(Request::path()."/terminate/apisession/".$ses->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="g-recaptcha-response">
                                    <button class="btn btn-danger btn-user btn-block g-recaptcha" onclick="onClickSubmit(event, this)">Terminate</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="height: 15px"></div>
            @empty
                You havn't any active session(s).
            @endforelse
        </div>
    </div>
@endsection

@include('dashboard.includes.recaptcha-submit')