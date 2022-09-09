
@extends('custom.layouts.guest')
@section('content')

<main class="signup__content">
    <div class="header__logo">
      <img src="{{asset('assets/img/skilled.png')}}" alt="logo">
    </div>
    <!-- <div class="header__content">
      <h1 class="header__content__heading">Sign Up</h1>
    </div> -->
    <x-auth-validation-errors class="mb-4" style="color:red" :errors="$errors" />

    <form id="signform" class="login__form" action="{{ route('login') }}" method="post">
      @csrf
      <fieldset>
        <h2 class="fs-title">SignIn Detail</h2>
        <h3 class="fs-subtitle">Give your login credentails</h3>
        <div class="form__input--floating">
          <label class="form__label--floating" id="label--email">Email</label>
          <input id="email" value="{{ old('email') }}" type="email" placeholder="abx@xyz.com" name="email" required>
        </div>
        <div class="form__input--floating">
          <label class="form__label--floating" id="label--password">Password</label>
          <input id="password" type="password"  placeholder="******" name="password" required>
        </div>


        <div class="login__form_action_container login__form_action_container--multiple-actions">
          <input type="submit" class="continue_button btn__primary--large from__button--floating" aria-label="Sign Up" value="Join" />
        </div>
        <div class="login__form_action_container text-center">
          <p>or</p>
        </div>
        <div class="login__form_action_container login__form_action_container--multiple-actions">
          <button class="btn__secondary--large from__button--floating" aria-label="Join Google"><img src="{{asset('assets/img/google-icon.png')}}" alt="google-icon" />Continue with Google</button>
        </div>
        <div class="footer-app-content-actions">
          @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}">
                  {{ __('Forgot your password?') }}
              </a>
          @endif

          <div class="login__para">
            <p>Please read our <a href="{{route('policy')}}">privacy policy</a> & <a href="{{route('terms')}}">terms of use</a> here</p>
          </div>
          <div class="Signin__class">
              <p>Create account on <img src="{{asset('assets/img/skilled.png')}}" alt="">?<a href="{{ route('register') }}">Sign Up</a></p>
          </div>
        </div>
      </fieldset>
    </form>

</main>
@endsection
