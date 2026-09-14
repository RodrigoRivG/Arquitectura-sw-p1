<?php

declare(strict_types=1);

namespace App\Core;

class VideoEmbed
{
    private const YOUTUBE_PATTERN = '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([A-Za-z0-9_-]{6,})/';
    private const VIMEO_PATTERN = '/vimeo\.com\/(\d+)/';
    private const EXTENSIONES_VIDEO = ['mp4', 'webm', 'ogg'];

    public static function render(string $url): string
    {
        if (preg_match(self::YOUTUBE_PATTERN, $url, $m) === 1) {
            return self::envolver(self::iframe('https://www.youtube.com/embed/' . $m[1]));
        }

        if (preg_match(self::VIMEO_PATTERN, $url, $m) === 1) {
            return self::envolver(self::iframe('https://player.vimeo.com/video/' . $m[1]));
        }

        $extension = strtolower((string) pathinfo((string) parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

        if (in_array($extension, self::EXTENSIONES_VIDEO, true)) {
            return self::envolver('<video class="video-embed" controls src="' . htmlspecialchars($url) . '"></video>');
        }

        return '<p class="video-fallback">No se pudo generar una vista previa. '
            . '<a href="' . htmlspecialchars($url) . '" target="_blank" rel="noopener">Abrir video</a></p>';
    }

    private static function iframe(string $src): string
    {
        return '<iframe class="video-embed" src="' . htmlspecialchars($src) . '" loading="lazy" '
            . 'allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" '
            . 'allowfullscreen></iframe>';
    }

    private static function envolver(string $embedHtml): string
    {
        return '<div class="video-embed-wrap">' . $embedHtml . '</div>';
    }
}
