<div class="top-bar">
  <div class="top-bar-title">
    <span data-responsive-toggle="responsive-menu" data-hide-for="medium">
      <span class="menu-icon dark" data-toggle></span>
    </span>
    <a href="/" style="color:white;" class="menu-text topi">SpeakOut App Dashboard</a>
  </div>
  <div id="responsive-menu">
    @if(!Auth::check())
    <div class="top-bar-right">
      <ul class="menu">
          <li><a href="/login" class="hollow button">Login</a></li>
          <li><a href="/register" class="button white-a">Sign up</a></li>
      </ul>
    </div>
    @else
      <div class="top-bar-right">
      <ul class="dropdown menu" style="margin-right:100px; background-color: transparent;"  data-dropdown-menu>
        <li>
          <a href="#"id="navpic"><img class="navprofile" src="{{ Auth::user()->getAvatarListUrl() }}">{{ Auth::user()->name }}</a>

          <ul class="menu vertical">
            <li><a href="/logout">Logout</a></li>
            @if(Auth::user()->email == 'edantonio505@gmail.com')
            <li><a href="{{ route('adminDashboard') }}">Dashboard</a></li>
            @endif
          </ul>
        </li>
        {{ (Auth::user()->connectionRequests()->count() > 0 ? Auth::user()->connectionRequests()->count() : '') }}</a></li>
      </ul>
    </div>
    @endif
  </div>
</div>