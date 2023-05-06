@extends('dashboard.layouts.page')
@section('title', 'Users')
@section('content')      
    @if (Auth::user()->hasPrivilege(config('userprivis.MANAGE_USERS.ADD_USERS')))
        <div class="card shadow mb-4">
            <a href="#collapseCardAddUser" class="d-block card-header py-3" data-toggle="collapse"
                role="button" aria-expanded="true" aria-controls="collapseCardAddUser">
                <h6 class="m-0 font-weight-bold text-primary">Rigister New User</h6>
            </a>
            <div class="collapse hide" id="collapseCardAddUser">
                <div class="card-body">
                    <form action="" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Enter user name here" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="Enter user email here" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Enter user password here" required>
                        </div>
                        
                        <input type="hidden" name="g-recaptcha-response">
                        <button class="btn btn-success g-recaptcha" onclick="onClickSubmit(event, this)">Register</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Users</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive" style="padding: 5px;">
                <table class="table table-bordered" id="dataTableUsers" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>TimeCreate</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>TimeCreate</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td><a href="{{ route('web.pages.user', $user->id) }}">{{ $user->name }}</a></td>
                                <td>{{ $user->email }}</td>
                                <td>{{ \Carbon\Carbon::parse($user->created_at)->format('F j, Y \a\t g:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('stylesheets')
    <link href="{{ asset('dashboard/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    <script src="{{ asset("dashboard/vendor/datatables/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("dashboard/vendor/datatables/dataTables.bootstrap4.min.js") }}"></script>

    <script>
        $(document).ready(function() {
            $('#dataTableUsers').DataTable();
        });
    </script>

    @include('dashboard.includes.recaptcha-submit')
@endsection