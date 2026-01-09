@extends('CMSViews::layout.layout-minimal')
@section('title')
<title>Login</title>
@endsection


@section('content')
<div class="login-screen">
    <div class="login-box ui piled segment py-4">

        <h2 class="">Login</h2>
        <div class="ui  divider pb-1"></div>
        <form  method="POST" class="ui form">
            @csrf
            <div class="field">
                <label>Email / Phone</label>
                <div class="ui left icon input">
                    <input type="text" name="username" placeholder="Username">
                    <i class="user icon"></i>
                </div>

            </div>
            <div class="field">
                <label>Password</label>
                <div class="ui left icon input">
                    <input type="password" name="password" placeholder="Password">
                    <i class="lock icon"></i>
                </div>

            </div>

            <button class="ui button" type="submit">Login</button>
        </form>
    </div>
</div>
@endsection