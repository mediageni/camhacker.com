<?php

function slugify($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function seoUrl($type, $value) {
    $slug = slugify($value);
    switch ($type) {
        case 'country': return SITE_URL . '/country/' . $slug;
        case 'city': return SITE_URL . '/city/' . $slug;
        case 'place': return SITE_URL . '/place/' . $slug;
        case 'manufacturer': return SITE_URL . '/manufacturer/' . $slug;
        case 'cam': return SITE_URL . '/cam/' . (int)$value;
        case 'map': return SITE_URL . '/map';
        default: return SITE_URL;
    }
}

function proxyImage($url, $w = 320, $h = 240) {
    if (empty($url)) return '/assets/img/loading.gif';
    return IMAGE_PROXY . urlencode($url) . ($w ? "&w=$w" : '') . ($h ? "&h=$h" : '');
}

function countryFlag($code) {
    $code = strtolower(trim($code));
    if (empty($code)) return '';
    return '<span class="fi fi-' . htmlspecialchars($code) . ' me-1" title="' . htmlspecialchars(strtoupper($code)) . '"></span>';
}

function ucwords_custom($string) {
    return ucwords(strtolower(str_replace('-', ' ', $string)));
}

function buildDynamicTitle($filters) {
    $title = 'Unsecured Live Streaming Public IP Webcams';
    $elements = [];
    if (!empty($filters['tag'])) $elements[] = ucwords_custom($filters['tag']);
    if (!empty($filters['search'])) $elements[] = ucwords_custom($filters['search']);
    if (!empty($filters['manufacturer'])) $elements[] = ucwords_custom($filters['manufacturer']);
    if (!empty($elements)) {
        $title = 'Live ' . implode(' ', $elements) . ' Webcams';
    }
    if (!empty($filters['city']) || !empty($filters['country'])) {
        $title .= ' in ';
        if (!empty($filters['city'])) $title .= ucwords_custom($filters['city']);
        if (!empty($filters['city']) && !empty($filters['country'])) $title .= ', ';
        if (!empty($filters['country'])) $title .= ucwords_custom($filters['country']);
    }
    return $title;
}

function buildDynamicDescription($filters) {
    $desc = 'Browse unprotected hidden live streaming webcams found';
    if (!empty($filters['city']) || !empty($filters['country'])) {
        $desc .= ' in ';
        if (!empty($filters['city'])) $desc .= ucwords_custom($filters['city']);
        if (!empty($filters['city']) && !empty($filters['country'])) $desc .= ', ';
        if (!empty($filters['country'])) $desc .= ucwords_custom($filters['country']);
    }
    return $desc . '. Watch live webcam streams from around the world.';
}

function renderPagination($totalPages, $currentPage, $baseUrl) {
    if ($totalPages <= 1) return '';
    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center flex-wrap">';

    if ($currentPage > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . 'page=' . ($currentPage - 1) . '">&laquo;</a></li>';
    }

    $start = max(1, $currentPage - 3);
    $end = min($totalPages, $currentPage + 3);

    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . 'page=1">1</a></li>';
        if ($start > 2) $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
    }

    for ($i = $start; $i <= $end; $i++) {
        $active = $i === $currentPage ? ' active' : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . $baseUrl . 'page=' . $i . '">' . $i . '</a></li>';
    }

    if ($end < $totalPages) {
        if ($end < $totalPages - 1) $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . 'page=' . $totalPages . '">' . $totalPages . '</a></li>';
    }

    if ($currentPage < $totalPages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . 'page=' . ($currentPage + 1) . '">&raquo;</a></li>';
    }

    $html .= '</ul></nav>';
    return $html;
}

function renderAdBlock() {
    return '<div class="my-4">
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=' . ADSENSE_CLIENT . '" crossorigin="anonymous"></script>
<ins class="adsbygoogle" style="display:block" data-ad-client="' . ADSENSE_CLIENT . '" data-ad-slot="' . ADSENSE_SLOT . '" data-ad-format="auto" data-full-width-responsive="true"></ins>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
</div>';
}

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
