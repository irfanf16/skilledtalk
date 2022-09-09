@extends('custom.layouts.guest')
@section('content')

<main class="signup__content">
    <div class="header__logo">
      <img src="assets/img/skilled.png" alt="logo">
    </div>
    <!-- <div class="header__content">
      <h1 class="header__content__heading">Sign Up</h1>
    </div> -->
    <x-auth-validation-errors class="mb-4" style="color:red" :errors="$errors" />
    <form id="signform" class="login__form" action="{{ route('register') }}" method="post">
        @csrf
      <fieldset style="position: relative">
          <div class="pageNumber" style="position: absolute; font-size:12px">1 / 4</div>
        <h2 class="fs-title">SignUp Detail</h2>
        <h3 class="fs-subtitle">Give your login credentails</h3>
        <div class="form__input--floating">
          <label class="form__label--floating" id="label--email">Email</label>
          <input id="input--email" type="email" placeholder="abx@xyz.com" name="email">
        </div>
        <div class="form__input--floating">
          <label class="form__label--floating" id="label--password">Password (6 or more characters)</label>
          <input id="input--password" type="password" placeholder="******" name="password">
        </div>
        <div class="form__input--floating">
            <label class="form__label--floating" id="label--password">Confirm Password</label>
            <input id="input--password--confirmation" type="password" placeholder="******" name="password_confirmation">
          </div>
        <div class="login__form_action_container login__form_action_container--multiple-actions">
          <input type="button" class="continue_button btn__primary--large from__button--floating" aria-label="Sign Up" value="Join" />
        </div>
        <div class="login__form_action_container text-center">
          <p>or</p>
        </div>
        <div class="login__form_action_container login__form_action_container--multiple-actions">
          <button class="btn__secondary--large from__button--floating" aria-label="Join Google"><img src="assets/img/google-icon.png" alt="google-icon" />Join with Google</button>
        </div>
        <div class="footer-app-content-actions">
          <div class="login__para">
            <p>Please read our <a href="{{route('policy')}}">privacy policy</a> & <a href="{{route('terms')}}">terms of use</a> here</p>
          </div>
          <div class="Signin__class">
              <p>Already on <img src="assets/img/skilled.png" alt="">?<a href="{{ route('login') }}">Sign in</a></p>
          </div>
        </div>
      </fieldset>
      <fieldset style="position: relative">
        <div class="pageNumber" style="position: absolute; font-size:12px">2 / 4</div>
        <h2 class="fs-title">Personal Detail</h2>
        <h3 class="fs-subtitle">Tell us something more about you</h3>
        <div class="form__input--floating">
          <label class="form__label--floating" id="label--first-name">First Name</label>
          <input id="input--first-name" type="text" placeholder="abc" name="firstname">
        </div>
        <div class="form__input--floating">
          <label class="form__label--floating" id="label--last-name">Last Name</label>
          <input id="input--last-name" type="text" placeholder="xyz" name="lastname">
          <span class="error">Please enter your last name</span>
        </div>
        <div class="login__form_action_container login__form_action_container--multiple-actions">
          <input type="button" name="" class="continue_button btn__primary--large from__button--floating" aria-label="Sign Up" value="Continue" />
        </div>
      </fieldset>
      <fieldset style="position: relative">
        <div class="pageNumber" style="position: absolute; font-size:12px">3 / 4</div>
        <h2 class="fs-title">Your Location</h2>
        <h3 class="fs-subtitle">Desired location for your network</h3>
        <div class="form__input--floating">
          <label class="form__label--floating" id="label--country">Country/Region <span>*</span></label>
          <input id="input--country" type="text" placeholder="Pakistan" name="country">
        </div>
        <div class="form__input--floating">
          <label class="form__label--floating" id="label--city">City/District <span>*</span></label>
          <input id="input--city" type="text" placeholder="Lahore" name="city">
        </div>
        <div class="login__form_action_container login__form_action_container--multiple-actions">
          <input type="button" name="" class="continue_button btn__primary--large from__button--floating" aria-label="Sign Up" value="Continue" />
        </div>
      </fieldset>
      <fieldset style="position: relative">
        <div class="pageNumber" style="position: absolute; font-size:12px">4 / 4</div>
        <h2 class="fs-title">Total experience</h2>
        <h3 class="fs-subtitle">Recent job employment type</h3>
        <div class="form__input--floating">
          <label class="form__label--floating" id="label--job">Most recent job title <span>*</span></label>
          <input id="input--job" type="text" placeholder="" name="job_title">
        </div>
        <div class="form__input--floating">
          <label class="form__label--floating" id="label--job">Employment type <span>*</span></label>
          <select name="employee_type_id" id="input--employment">
            <option value="">Select One</option>
            @if($employeeTypes)
                @foreach($employeeTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            @endif
          </select>
        </div>
        <div class="form__input--floating">
          <label class="form__label--floating" id="label--company">Most recent company <span>*</span></label>
          <input id="input--company" type="text" placeholder="" name="recent_company">
        </div>
        <div>
          {{-- <button class="btn__secondary--large from__button--floating">I'm a student</button> --}}
          <input type="checkbox" id="vehicle1" name="is_student" value="1">
          <label for="vehicle1">I am student</label>
        </div>
        <div class="login__form_action_container login__form_action_container--multiple-actions">
          <input type="submit" class="continue_button btn__primary--large from__button--floating" value="Sign up" />
        </div>
      </fieldset>
    </form>
</main>

@endsection
