@extends('dashboard.layouts.page')
@section('title', 'Users / '.$user->name)
@section('content')   
    <hr>          

    <table>
        <tr>
            <th>ID:&nbsp;&nbsp;</th>
            <td>{{ $user->id }}</td>
        </tr>
        <tr>
            <th>Name:&nbsp;&nbsp;</th>
            <td>{{ $user->name }}</td>
        </tr>
        <tr>
            <th>Email:&nbsp;&nbsp;</th>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <th>Time Created:&nbsp;&nbsp;</th>
            <td>{{ $user->created_at }}</td>
        </tr>
    </table>

    @if (Auth::user()->hasPrivilege(config('userprivis.MANAGE_USERS.SHOW_USER_ACTIVITIES')))
        <br>
        <a class="btn btn-primary" href="{{ route('web.pages.user.activities', $user->id) }}" target="_blank">Shop Activities</a>
    @endif

    @if (Auth::user()->hasPrivilege(config('userprivis.MANAGE_USERS.SHOW_USER_PRIVILEGES')))
        <hr>    
        <div class="card shadow mb-4">
            <a href="#collapseCardPrivileges" class="d-block card-header py-3" data-toggle="collapse"
                role="button" aria-expanded="true" aria-controls="collapseCardPrivileges">
                <h6 class="m-0 font-weight-bold text-primary">Privileges</h6>
            </a>
            <div class="collapse hide" id="collapseCardPrivileges">
                <div class="card-body">
                    <form method="POST" action="{{ url(Request::path().'/setprivileges') }}">
                        @csrf
                        @foreach ($privileges as $group => $items)
                            <div class="card mb-4">
                                <a href="#collapseCardPrivileges_{{ $group }}" class="d-block card-header py-3" data-toggle="collapse"
                                    role="button" aria-expanded="true" aria-controls="collapseCardPrivileges_{{ $group }}">
                                    <h6 class="m-0 font-weight-bold text-primary">{{ $group }}</h6>
                                </a>
                                <div class="collapse hide" id="collapseCardPrivileges_{{ $group }}">
                                    <div class="card-body">
                                        @foreach ($items as $privilege => $data)
                                            <input type="checkbox" name="privileges[]" value="{{ $data[0] }}" {{ $data[1] ? 'checked' : '' }}>&nbsp;{{ $privilege }}<br>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if (Auth::user()->hasPrivilege(config('userprivis.MANAGE_USERS.UPDATE_USER_PRIVILEGES')))
                            <input type="hidden" name="g-recaptcha-response">
                            <button class="btn btn-primary g-recaptcha" onclick="onClickSubmit(event, this)">Update</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@include('dashboard.includes.recaptcha-submit')