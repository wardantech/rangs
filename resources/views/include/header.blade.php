<header class="header-top" header-theme="light">
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <div class="top-menu d-flex align-items-center">
                <button type="button" class="btn-icon mobile-nav-toggle d-lg-none"><span></span></button>
            </div>
            <div class="top-menu d-flex align-items-center">

                @php
                    $mystore = null;
                    $auth = auth()->user();
                    $user_role = $auth->roles->first();
                    $employee = DB::table('employees')->where('user_id', $auth->id)->where('deleted_at',null)->first();

                    if($employee) {
                        $mystore = DB::table('stores')->where('id', $employee->store_id)->where('deleted_at',null)->first();
                    }
                @endphp

                <div class="dropdown">
                    <a class="dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img class="avatar" src="{{ asset('img/user.jpg')}}" alt=""></a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <a class="dropdown-item text-blue" href="{{ route('hrm.user.profile') }}">
                            <i class="far fa-user fx dropdown-icon"></i>
                            {{ $auth->name }}
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-lock-open dropdown-icon"></i>
                            {{ $user_role->name ?? null }}
                        </a>
                        @if($mystore)
                            <a class="dropdown-item" href="#">
                                <i class="ik ik-home dropdown-icon"></i>
                                {{ $mystore->name }}
                            </a>
                        @endif
                        <a class="dropdown-item" href="{{ url('logout') }}">
                            <i class="ik ik-power dropdown-icon"></i>
                            {{ __('Logout')}}
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</header>
