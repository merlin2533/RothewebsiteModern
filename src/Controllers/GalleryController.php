<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class GalleryController
{
    public function index(array $args): void
    {
        // Wenn der Admin die Galerie deaktiviert hat: 404.
        if (setting('gallery_enabled', '1') !== '1') {
            (new LegalController())->notFound([]);
            return;
        }

        $media = $GLOBALS['mediaRepo']->all();

        // Logo / Favicon-aehnliche Eintraege ausschliessen (keine echten Fotos)
        $media = array_values(array_filter($media, function ($m) {
            $name = strtolower((string) ($m['original_name'] ?? $m['filename'] ?? ''));
            if (str_contains($name, 'logo')) return false;
            if (str_contains($name, 'favicon')) return false;
            if (str_contains($name, 'cropped-cropped')) return false;
            return true;
        }));

        $cfg = $GLOBALS['config'];
        $base = rtrim((string) $cfg['site_url'], '/');

        // ImageGallery + Bilder als JSON-LD
        $imageObjects = array_map(static function (array $m) use ($base) {
            $url = $base . (string) (\App\Core\MediaResolver::variantUrl($m, 1600, 'jpg') ?? '');
            return [
                '@type'       => 'ImageObject',
                'contentUrl'  => $url,
                'thumbnailUrl' => $base . (string) (\App\Core\MediaResolver::variantUrl($m, 800, 'jpg') ?? ''),
                'caption'     => $m['alt_text'] ?? '',
                'width'       => (int) ($m['width'] ?? 0),
                'height'      => (int) ($m['height'] ?? 0),
            ];
        }, $media);

        $schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'ImageGallery',
            'name'        => 'Bildergalerie – Rothe-Transporte',
            'description' => 'Originalfotos aus dem Fuhrpark- und Werkstattalltag der Spedition Rothe-Transporte.',
            'url'         => $base . '/galerie',
            'image'       => $imageObjects,
        ];

        echo View::render('gallery', [
            'media'           => $media,
            'currentSlug'     => 'galerie',
            'structured_data' => [$schema],
            'page'            => [
                'title'            => 'Galerie',
                'meta_title'       => 'Bildergalerie – Fuhrpark, Maschinen, Touren | Rothe-Transporte',
                'meta_description' => 'Originalfotos aus dem Alltag der Spedition Rothe-Transporte: Tieflader, Tautliner, Maschinentransporte, Werkstatthof.',
                'hero_headline'    => 'Aus unserem Alltag.',
                'hero_sub'         => count($media) . ' Fotos: Fuhrpark, Maschinen, Touren, Werkstatt.',
            ],
        ]);
    }
}
