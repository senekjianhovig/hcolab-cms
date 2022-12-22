@extends('CMSViews::layout.layout', ['title' => "Change Password"])
@section('head')
<title>Change Password</title>
@endsection


@section('content')

<div class="container-fluid my-3">

    <div class="ui segment raised mb-3">


        <form action="{{route('change-password')}}" method="POST" class="ui form">
            @csrf

            <div class="field">
                <label>Current Password</label>
                <div class="ui left icon input">
                    <input type="password" name="current_password" placeholder="Current Password">
                    <i class="lock icon"></i>
                </div>

            </div>


            <div class="field">
                <label>New Password</label>
                <div class="ui left icon input">
                    <input type="password" name="password" placeholder="Password">
                    <i class="lock icon"></i>
                </div>

            </div>
            <div class="field">
                <label>Confirm Password</label>
                <div class="ui left icon input">
                    <input type="password" name="confirm_password" placeholder="Confirm Password">
                    <i class="lock icon"></i>
                </div>

            </div>

            <button class="ui button" type="submit">Change Password</button>
        </form>



    </div>



</div>
@endsection