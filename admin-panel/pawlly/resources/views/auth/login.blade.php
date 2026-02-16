<x-auth-layout>
    <x-slot name="title">
        @lang('Login')
    </x-slot>

    <x-auth-card>
        <x-slot name="logo">
            <!-- <a href="/"> -->
            <x-application-logo />
            <!-- </a> -->
        </x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Social Login -->
        <x-auth-social-login />



        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ $url ?? route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('Email')" />
                <x-input id="email" type="email" name="email" :value="old('email')" placeholder="Enter Email" required
                    autofocus pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                    title="Please enter a valid email address." />


                <!-- Custom error message for invalid email format -->

                <span id="emailError" class="text-danger" style="display: none;">Invalid email format</span>
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />

                <x-input id="password" type="password" name="password" placeholder="Enter  password" required
                    minlength="8" autocomplete="current-password"
                    title="Password must be between 8 and 12 characters." />

                <!-- Custom error message for invalid password length -->
                <span id="passwordError" class="text-danger" style="display: none;">Password must be between 8 and 12
                    characters</span>
            </div>


            <!-- Remember Me -->
            <div class="mt-4">
                <label for="remember_me" class="d-inline-flex">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="ms-2">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="d-flex align-items-center justify-content-between mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button>
                    {{ __('Login') }}
                </x-button>
            </div>

        </form>
        <div>
            <h6 class="text-center border-top py-3 mt-3">Demo Accounts</h6>
            <div class="parent">

                <select name="select" id="SelectUser" id="select" class="form-control selectpiker"
                    onchange="getSelectedOption()">
                    <option value="12345678,demo@pawlly.com" selected>Demo Admin</option>
                    <option value="12345678,miles@gmail.com">Boarder</option>
                    <option value="12345678,felix@gmail.com">Veterinarian</option>
                    <option value="12345678,richard@gmail.com">Groomer</option>
                    <option value="12345678,tristan@gmail.com">Trainer</option>
                    <option value="12345678,pedro@gmail.com">Walker</option>
                    <option value="12345678,justin@gmail.com">DayCare Taker</option>
                    <option value="12345678,harry@gmail.com">Pet Sitter</option>
                    @if (enableMultivendor() === '1')
                        <option value="12345678,mario@gmail.com">Pet Store</option>
                    @endif
                </select>

            </div>

        </div>
        </div>

        <x-slot name="extra">
            @if (Route::has('register'))
                <p class="text-center text-gray-600 mt-4">
                    Do not have an account? <a href="{{ route('register') }}"
                        class="underline hover:text-gray-900">Register</a>.
                </p>
            @endif
        </x-slot>
    </x-auth-card>

    <script type="text/javascript">
        window.onload = function() {
            getSelectedOption();
        };

        function getSelectedOption() {
            var selectElement = document.getElementById("SelectUser");
            var selectedOption = selectElement.options[selectElement.selectedIndex];

            if (selectedOption) {
                var optionText = selectedOption.textContent || selectedOption
                    .innerText; // Get the text of the selected option
                var optionValue = selectedOption.value; // Get the value of the selected option

                var values = optionValue.split(",");
                var password = values[0];
                var email = values[1];

                domId('email').value = email;
                domId('password').value = password;

                // domId('email').value =optionText;
                // domId('password').value = optionValue;

            } else {

            }
        }

        document.getElementById('password').addEventListener('input', function() {
            var passwordField = document.getElementById('password');
            var passwordError = document.getElementById('passwordError');

            // if (passwordField.value.length < 8 || passwordField.value.length > 12) {
            //     passwordError.style.display = 'block'; // Show error message
            //     passwordField.classList.add('is-invalid'); // Add error styling
            // } else {
            //     passwordError.style.display = 'none'; // Hide error message
            //     passwordField.classList.remove('is-invalid'); // Remove error styling
            // }
        });

        document.getElementById('email').addEventListener('input', function() {
            var emailField = document.getElementById('email');
            var emailError = document.getElementById('emailError');

            // Email validation pattern
            var emailPattern = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{1,}$/i;

            if (emailField.value && !emailPattern.test(emailField.value)) {
                emailError.style.display = 'block'; // Show error message
                emailField.classList.add('is-invalid'); // Add error styling if needed
            } else {
                emailError.style.display = 'none'; // Hide error message
                emailField.classList.remove('is-invalid'); // Remove error styling if fixed
            }
        });

        function domId(name) {
            return document.getElementById(name)
        }

        function setLoginCredentials(type) {
            domId('email').value = domId(type + '_email').textContent
            domId('password').value = domId(type + '_password').textContent
        }
    </script>

</x-auth-layout>
