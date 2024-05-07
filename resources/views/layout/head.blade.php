@section('head')
<meta http-equiv="content-language" content="ja" />
<meta charset="UTF-8" />

{{-- CSRF トークン --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@yield('title') | 模試サイト</title>
<meta name="description" itemprop="description" content="@yield('description')">
<meta name="keywords" itemprop="keywords" content="@yield('keywords')">
<meta name="robots" content="noindex,nofollow" />
<meta name="googlebot" content="noindex,nofollow" />
<link rel="stylesheet" href="{{ asset('/css/style.css?'.time()) }}" />
<link rel="stylesheet" href="{{ asset('/css/webfont.css') }}" />
<link rel="stylesheet" href="{{ asset('/css/animation.css') }}" />

<link rel="stylesheet" href="{{asset('/css/psnmenu_css/webfont.css')}}" />
<link rel="stylesheet" href="{{asset('/css/psnmenu_css/animation.css')}}" />
@yield('pageCss')
<script src="{{ asset('/js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('/js/common.js') }}"></script>
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-140481385-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-140481385-1');
</script>
@endsection
