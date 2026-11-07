<?php
// ---------- View helpers ----------
function e($v): string { return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8'); }
function url(string $path = ''): string { return App::$base . $path; }
function fmtTime($t): string { return $t ? substr((string) $t, 0, 5) : ''; }
function fmtDate($d): string { return $d ? substr((string) $d, 0, 10) : ''; }
function money($n): string { return '$' . number_format((float) ($n ?? 0), 2, '.', ''); }
function statusClass(string $s): string
{
    return [
        'Scheduled' => 'badge-info', 'Completed' => 'badge-success', 'Cancelled' => 'badge-danger',
        'Paid' => 'badge-success', 'Unpaid' => 'badge-warn',
    ][$s] ?? 'badge-muted';
}

// ---------- Small utilities ----------
function today(): string { return date('Y-m-d'); }
function parseId($v): ?int
{
    $n = filter_var($v, FILTER_VALIDATE_INT);
    return ($n !== false && $n > 0) ? $n : null;
}
function flash(string $type, string $text): void { $_SESSION['flash'] = ['type' => $type, 'text' => $text]; }
function currentFlash(): ?array { return $GLOBALS['__flash'] ?? null; }
function currentUser(): ?array { return $_SESSION['user'] ?? null; }

// ---------- CSRF protection ----------
function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrfField(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrfToken()) . '">';
}

// ---------- Page header partial (title, back button, action button, flash) ----------
function pageHeader(array $o): void
{
    echo '<div class="page-header"><div>';
    if (!empty($o['back'])) {
        echo '<a href="' . e(url($o['back'])) . '" class="btn-back">← ' . e($o['backLabel'] ?? 'Back') . '</a>';
    }
    echo '<h2>' . e($o['heading'] ?? '') . '</h2>';
    if (!empty($o['subtitle'])) echo '<p class="subtitle">' . e($o['subtitle']) . '</p>';
    echo '</div>';
    if (!empty($o['action'])) {
        echo '<a href="' . e(url($o['action']['href'])) . '" class="btn btn-primary">' . e($o['action']['label']) . '</a>';
    }
    echo '</div>';
    flashAlert();
}
function flashAlert(): void
{
    if ($f = currentFlash()) {
        echo '<div class="alert alert-' . ($f['type'] === 'error' ? 'error' : 'success') . '">' . e($f['text']) . '</div>';
    }
}
