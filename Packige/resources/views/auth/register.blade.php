@extends('layouts.app')

@section('content')
<div class="content">
    <div>
        <div>
            <div>
                <h1 class="register-title">Crée ton compte</h1>
                <p class="register-subtitle">Pour les étudiants HEIG-VD</p>


                <div>
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div>

                            <div>
                                <input id="firstname" type="text" class="form-login @error('firstname') is-invalid @enderror" name="firstname" value="{{ old('firstname') }}" required autocomplete="firstname" placeholder="Prénom" autofocus>

                                @error('firstname')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div>

                            <div>
                                <input id="lastname" type="text" class="form-login @error('lastname') is-invalid @enderror" name="lastname" value="{{ old('lastname') }}" required autocomplete="lastname" placeholder="Nom" autofocus>

                                @error('lastname')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div>

                            <div>
                                <input id="email" type="email" class="form-login @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="E-mail HEIG-VD">

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div>

                            <div>
                                <!-- <input id="group-name" type="text" class="form-control @error('groupName') is-invalid @enderror" name="groupName" value="{{ old('groupName') }}" required autocomplete="groupName" autofocus> -->
                                <select name="groupName" id="group-name">
                                    <option value="" disabled selected>Classe</option>
                                    @foreach ($groups as $group)
                                    <option value="{{$group->id}}" {{ old('groupName') == $group->id ? "selected":""}}>{{$group->promotion->name}}-{{$group->name}}</option>
                                    @endforeach
                                </select>
                                @error('groupName')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div>

                            <div>
                                <input id="groupPwd" type="text" class="form-login @error('groupPwd') is-invalid @enderror" name="groupPwd" value="{{ old('groupPwd') }}" required autocomplete="groupPwd" placeholder="ID Classe" autofocus>

                                @error('groupPwd')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div>

                            <div>
                                <input id="password" type="password" class="form-login @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Mot de passe">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div>

                            <div>
                                <input id="password-confirm" type="password" class="form-login" name="password_confirmation" required autocomplete="new-password" placeholder="Confirmation du mot de passe"
                            </div>
                        </div>

                        <div>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection