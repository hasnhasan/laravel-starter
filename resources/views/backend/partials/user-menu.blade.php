<div class="aside-loggedin">
    <div class="d-flex align-items-center justify-content-start">
        <a href="" class="avatar"><img src="{{asset('assets/img/img1.png')}}" class="rounded-circle" alt=""></a>
        <div class="aside-alert-link">
            <a href="" class="new" data-toggle="tooltip" title="You have 2 unread messages"><i
                    data-feather="message-square"></i></a>
            <a href="" class="new" data-toggle="tooltip" title="You have 4 new notifications"><i
                    data-feather="bell"></i></a>
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               data-toggle="tooltip" title="{{__('Oturumu Kapat')}}"><i data-feather="log-out"></i></a>
        </div>
    </div>
    <div class="aside-loggedin-user">
        <a href="#loggedinMenu"
           class="d-flex align-items-center justify-content-between mg-b-2" data-toggle="collapse">
            <h6 class="tx-semibold mg-b-0">{{ Auth::user()->name }}</h6>
            <i data-feather="chevron-down"></i>
        </a>
        <p class="tx-color-03 tx-12 mg-b-0">Administrator</p>
    </div>
    <div class="collapse" id="loggedinMenu">
        <ul class="nav nav-aside mg-b-0">
            <li class="nav-item">
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="nav-link"><i data-feather="log-out"></i>
                    <span>{{__('Oturumu Kapat')}}</span></a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</div><!-- aside-loggedin -->
