<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('css/reports.css') }}" rel="stylesheet">
    <title>@yield('title')</title>
</head>
<body>
<footer>
    @yield('footer')
</footer>

<div class="information">
    @yield('header')
    @yield('title-content')
</div>

<br/>

<div class="content">
    @yield('main-content')
</div>
<script type="text/php">
if ( isset($pdf) ) {
    $pdf->page_script('
        if ($PAGE_COUNT > 1) {
            $font = $fontMetrics->get_font("Verdana, Arial, sans-serif", "normal");
            $size = 10;
            $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
            $y = 820;
            $x = 520;
            $pdf->text($x, $y, $pageText, $font, $size);
        }
    ');
}
</script></body></html>