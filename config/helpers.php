<?php
/**
 * Global Helper Functions
 */

/**
 * Get base URL path for the application
 */
function baseUrl(string $path = ''): string {
    // Determine base script path
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = str_replace('\\', '/', dirname($scriptName));
    $base = ($dir === '/' || $dir === '.') ? '' : $dir;
    
    // Normalize path
    if ($path !== '' && $path[0] !== '/') {
        $path = '/' . $path;
    }
    
    return $base . $path;
}

function url(string $path = ''): string {
    return baseUrl($path);
}

function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path, ?string $success = null, ?string $error = null): void {
    if ($success) {
        $_SESSION['flash_success'] = $success;
    }
    if ($error) {
        $_SESSION['flash_error'] = $error;
    }
    header('Location: ' . url($path));
    exit;
}

function getFlash(string $key): ?string {
    if (isset($_SESSION['flash_' . $key])) {
        $msg = $_SESSION['flash_' . $key];
        unset($_SESSION['flash_' . $key]);
        return $msg;
    }
    return null;
}

function slugify(string $text): string {
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // trim
    $text = trim($text, '-');
    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
    // lowercase
    $text = strtolower($text);

    return empty($text) ? 'n-a' : $text;
}

function timeAgo(string $datetime): string {
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) {
        return 'Baru saja';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' menit lalu';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' jam lalu';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' hari lalu';
    } else {
        return date('d M Y', $time);
    }
}

function formatHits(int $count): string {
    if ($count >= 1000000) {
        return round($count / 1000000, 1) . 'M';
    }
    if ($count >= 1000) {
        return round($count / 1000, 1) . 'k';
    }
    return (string)$count;
}

function getStatusBadge(string $status): string {
    switch (strtolower($status)) {
        case 'approved':
            return '<span class="status-badge status-approved"><i class="fa-solid fa-circle-check"></i> Disetujui</span>';
        case 'pending':
            return '<span class="status-badge status-pending"><i class="fa-solid fa-clock"></i> Menunggu Review</span>';
        case 'revision':
            return '<span class="status-badge status-revision"><i class="fa-solid fa-triangle-exclamation"></i> Perlu Revisi</span>';
        case 'rejected':
            return '<span class="status-badge status-rejected"><i class="fa-solid fa-circle-xmark"></i> Ditolak</span>';
        case 'draft':
            return '<span class="status-badge status-draft"><i class="fa-solid fa-pencil"></i> Draf</span>';
        default:
            return '<span class="status-badge">' . e($status) . '</span>';
    }
}
