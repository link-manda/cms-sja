@php
    $seoTitle = $title ?? setting('site_title', 'PT Sistem Jaya Abadi - Professional Contractor');
    $seoDescription = $description ?? setting('site_description', 'Professional contractors for premium, on-time construction projects.');
    $seoUrl = $url ?? url()->current();
    $seoImage = $image ?? asset('assets/logo.png');
@endphp

<meta name="description" content="{{ $seoDescription }}">
<link rel="canonical" href="{{ $seoUrl }}">
<meta property="og:type" content="{{ $type ?? 'website' }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoUrl }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">
