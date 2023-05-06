@extends('dashboard.layouts.page')
@section('title', 'Profile')
@section('content')   
    <hr>          

    <table>
        <tr>
            <th>ID:&nbsp;&nbsp;</th>
            <td>{{ Auth::user()->id }}</td>
        </tr>
        <tr>
            <th>Name:&nbsp;&nbsp;</th>
            <td>{{ Auth::user()->name }}</td>
        </tr>
        <tr>
            <th>Email:&nbsp;&nbsp;</th>
            <td>{{ Auth::user()->email }}</td>
        </tr>
        <tr>
            <th>Time Created:&nbsp;&nbsp;</th>
            <td>{{ Auth::user()->created_at }}</td>
        </tr>
    </table>

    <hr>
@endsection