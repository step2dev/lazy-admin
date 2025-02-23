<x-lazy-base-layout>
    <div class="bg-white dark:bg-gray-900">
        <div class="flex h-screen justify-center">
            <div class="hidden bg-cover lg:block lg:w-2/3"
                 style="background-image: url(/img/auth/login_background.jpeg)">
                <div class="flex h-full items-center bg-gray-900 bg-opacity-40 px-20">
                    <div>
                        <h1 class="text-4xl font-bold text-white">
                            <img src="/main.svg" width="180px"
                                 alt="{{ config('app.name', 'Laravel') }}"/>
                        </h1>

                        <p class="mt-3 max-w-xl text-gray-300">
                            {{ trans('auth.auth.description') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="mx-auto flex w-full max-w-md items-center px-6 lg:w-2/6">
                <div class="flex-1">
                    <div class="text-center">
                        <h2 class="flex justify-center text-center text-4xl font-bold text-gray-700 dark:text-white">
                            <img src="/main.svg" width="180px"
                                 alt="{{ config('app.name', 'Laravel') }}" loading="lazy"/>
                        </h2>

                        <p class="mt-3 text-gray-500 dark:text-gray-300">Sign in to access your account</p>
                    </div>
                    <x-lazy-alert class="mb-4"/>

                    @if (session('status'))
                        <div class="mb-4 text-sm font-medium text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="mt-8">
                        <form class="row g-3" method="POST" action="{{ route('login') }}">
                            @csrf
                            <div>
                                <x-lazy::label for="email" label="{{ __('Email') }}"
                                               class="mb-2 text-sm text-gray-600 dark:text-gray-200"/>
                                <x-lazy::input-native id="email" type="email" name="email" :value="old('email')"
                                                      required autofocus placeholder="{{ __('Email') }}" hr
                                                      class="mt-2 block w-full rounded-md border border-gray-200 bg-white px-4 py-2 text-gray-700 placeholder-gray-400 focus:border-blue-400 focus:outline-none focus:ring focus:ring-blue-400 focus:ring-opacity-40 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:placeholder-gray-600 dark:focus:border-blue-400"/>
                            </div>

                            <div class="mt-6">
                                <div class="mb-2 flex justify-between">
                                    <x-lazy::label for="password" label="{{ __('Password') }}" hr
                                                   class="mb-2 text-sm text-gray-600 dark:text-gray-200"/>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}"
                                           class="text-sm text-gray-400 underline hover:text-blue-500 hover:underline focus:text-indigo-500"
                                        >{{ __('Forgot your password?') }}</a>
                                    @endif
                                </div>
                                {{--<div class="input-group" id="show_hide_password">
                                    <x-input id="password" type="password" name="password" required
                                                 autocomplete="current-password" class="border-end-0"
                                                 placeholder="{{ __('Password') }}"
                                                 class="mt-2 block w-full rounded-md border border-gray-200 bg-white px-4 py-2 text-gray-700 placeholder-gray-400 focus:border-blue-400 focus:outline-none focus:ring focus:ring-blue-400 focus:ring-opacity-40 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:placeholder-gray-600 dark:focus:border-blue-400"ы/>
                                    <a href="javascript:;" class="input-group-text bg-transparent">
                                        <i class='bx bx-hide'></i>
                                    </a>
                                </div>--}}
                                <x-lazy::input-native type="password" name="password" id="password"
                                                      placeholder="Your Password"
                                                      class="mt-2 block w-full rounded-md border border-gray-200 bg-white px-4 py-2 text-gray-700 placeholder-gray-400 focus:border-blue-400 focus:outline-none focus:ring focus:ring-blue-400 focus:ring-opacity-40 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:placeholder-gray-600 dark:focus:border-blue-400"
                                />
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="remember_me" name="remember"
                                       checked>
                                <x-lazy::label for="password" :value="__('Remember me')"
                                               class="text-sm text-gray-600 dark:text-gray-200"/>
                            </div>

                            <div class="mt-6">
                                <button
                                    class="w-full transform rounded-md bg-indigo-500 px-4 py-2 tracking-wide text-white transition-colors duration-300 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                                    <i class="bx bxs-lock-open"></i>{{ __('Log in') }}
                                </button>
                            </div>
{{--                                                        <x-lazy-divider class="text-indigo-500" :text="__('auth.or_sign_in_with_email')"/>--}}
                            @if (Route::has('auth.social.login'))
                                <div class="mt-5 flex justify-around">
                                    <a href="{{ route('auth.social.login', ['driver' => 'google']) }}"
                                       title="{{ trans('auth.sign_in_with_google') }}">
                                        <svg class="h-8 w-8" viewBox="0 0 24 24">
                                            <path fill="#EA4335"
                                                  d="M5.266 9.765A7.077 7.077 0 0 1 12 4.909c1.69 0 3.218.6 4.418 1.582L19.91 3C17.782 1.145 15.055 0 12 0C7.27 0 3.198 2.698 1.24 6.65l4.026 3.115z"></path>
                                            <path fill="#34A853"
                                                  d="M16.04 18.013c-1.09.703-2.474 1.078-4.04 1.078a7.077 7.077 0 0 1-6.723-4.823l-4.04 3.067A11.965 11.965 0 0 0 12 24c2.933 0 5.735-1.043 7.834-3l-3.793-2.987z"></path>
                                            <path fill="#4A90E2"
                                                  d="M19.834 21c2.195-2.048 3.62-5.096 3.62-9c0-.71-.109-1.473-.272-2.182H12v4.637h6.436c-.317 1.559-1.17 2.766-2.395 3.558L19.834 21z"></path>
                                            <path fill="#FBBC05"
                                                  d="M5.277 14.268A7.12 7.12 0 0 1 4.909 12c0-.782.125-1.533.357-2.235L1.24 6.65A11.934 11.934 0 0 0 0 12c0 1.92.445 3.73 1.237 5.335l4.04-3.067z"></path>
                                        </svg>
                                    </a>

                                    <a href="{{ route('auth.social.login', ['driver' => 'facebook']) }}"
                                       class="btn-light col shadow-sm"
                                       title=" {{ trans('auth.sign_in_with_facebook') }}" style="color: #4267B2">
                                        <svg class="h-8 w-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 448 512">
                                            <path
                                                d="M400 32H48A48 48 0 0 0 0 80v352a48 48 0 0 0 48 48h137.25V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.27c-30.81 0-40.42 19.12-40.42 38.73V256h68.78l-11 71.69h-57.78V480H400a48 48 0 0 0 48-48V80a48 48 0 0 0-48-48z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('auth.social.login', ['driver' => 'telegram']) }}"
                                       title=" {{ trans('auth.sign_in_with_telegram') }}" style="color: grey">
                                        <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" version="1.1"
                                             viewBox="0 0 512 512" fill="currentColor">
                                            <path
                                                d="M470.4354553,45.4225006L16.8273029,221.2490387c-18.253809,8.1874695-24.4278889,24.5854034-4.4127407,33.4840851l116.3710175,37.1726685l281.3674316-174.789505c15.3625488-10.9733887,31.0910339-8.0470886,17.5573425,4.023468L186.0532227,341.074646l-7.5913849,93.0762329c7.0313721,14.3716125,19.9055786,14.4378967,28.1172485,7.2952881l66.8582916-63.5891418l114.5050659,86.1867065c26.5942688,15.8265076,41.0652466,5.6130371,46.7870789-23.3935242L509.835083,83.1804428C517.6329956,47.474514,504.3345032,31.7425518,470.4354553,45.4225006z"></path>
                                        </svg>
                                    </a>
                                    <a href="#"
                                       title=" {{ trans('auth.sign_in_with_twitter') }}" style="color: grey">
                                        <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             fill="currentColor" viewBox="0 0 16 16">
                                            <path
                                                d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"></path>
                                        </svg>
                                    </a>
                                    <a href="#"
                                       title=" {{ trans('auth.sign_in_with_apple') }}" style="color: grey">
                                        <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             fill="currentColor" viewBox="0 0 16 16">
                                            <path
                                                d="M11.182.008C11.148-.03 9.923.023 8.857 1.18c-1.066 1.156-.902 2.482-.878 2.516.024.034 1.52.087 2.475-1.258.955-1.345.762-2.391.728-2.43zm3.314 11.733c-.048-.096-2.325-1.234-2.113-3.422.212-2.189 1.675-2.789 1.698-2.854.023-.065-.597-.79-1.254-1.157a3.692 3.692 0 0 0-1.563-.434c-.108-.003-.483-.095-1.254.116-.508.139-1.653.589-1.968.607-.316.018-1.256-.522-2.267-.665-.647-.125-1.333.131-1.824.328-.49.196-1.422.754-2.074 2.237-.652 1.482-.311 3.83-.067 4.56.244.729.625 1.924 1.273 2.796.576.984 1.34 1.667 1.659 1.899.319.232 1.219.386 1.843.067.502-.308 1.408-.485 1.766-.472.357.013 1.061.154 1.782.539.571.197 1.111.115 1.652-.105.541-.221 1.324-1.059 2.238-2.758.347-.79.505-1.217.473-1.282z"></path>
                                            <path
                                                d="M11.182.008C11.148-.03 9.923.023 8.857 1.18c-1.066 1.156-.902 2.482-.878 2.516.024.034 1.52.087 2.475-1.258.955-1.345.762-2.391.728-2.43zm3.314 11.733c-.048-.096-2.325-1.234-2.113-3.422.212-2.189 1.675-2.789 1.698-2.854.023-.065-.597-.79-1.254-1.157a3.692 3.692 0 0 0-1.563-.434c-.108-.003-.483-.095-1.254.116-.508.139-1.653.589-1.968.607-.316.018-1.256-.522-2.267-.665-.647-.125-1.333.131-1.824.328-.49.196-1.422.754-2.074 2.237-.652 1.482-.311 3.83-.067 4.56.244.729.625 1.924 1.273 2.796.576.984 1.34 1.667 1.659 1.899.319.232 1.219.386 1.843.067.502-.308 1.408-.485 1.766-.472.357.013 1.061.154 1.782.539.571.197 1.111.115 1.652-.105.541-.221 1.324-1.059 2.238-2.758.347-.79.505-1.217.473-1.282z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('auth.social.login', ['driver' => 'github']) }}"
                                       title=" {{ trans('auth.sign_in_with_github') }}" style="color: #333">
                                        <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             fill="currentColor" viewBox="0 0 16 16">
                                            <path
                                                d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.012 8.012 0 0 0 16 8c0-4.42-3.58-8-8-8z"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </form>

                        <p class="mt-6 text-center text-sm text-gray-400">{!! __('blog.dont_have_an_account_yet', ['link' => '<a class="text-blue-500 hover:underline focus:underline focus:outline-none" href="'. route('register').'">Sign up here</a>']) !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-lazy-base-layout>
