<?php

use App\Models\News;
use App\Models\PublicInformation;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/{any}', function (Request $request, string $any = '') {
    $setting = Setting::current();
    $siteName = $setting->org_name ?: 'PPID UM Jambi';
    $defaultImage = $setting->getFirstMediaUrl('logo') ?: asset('images/logo-default.png');

    $meta = [
        'title' => $siteName,
        'description' => 'Pejabat Pengelola Informasi dan Dokumentasi (PPID) Universitas Muhammadiyah Jambi — layanan permohonan, keberatan, dan keterbukaan informasi publik sesuai UU No. 14 Tahun 2008.',
        'image' => $defaultImage,
        'url' => $request->url(),
    ];

    if (preg_match('#^berita/([^/]+)$#', $any, $matches)) {
        $news = News::where('slug', $matches[1])->where('is_published', true)->first();

        if ($news) {
            $meta['title'] = "{$news->title} — {$siteName}";
            $meta['description'] = Str::limit(trim(strip_tags($news->excerpt ?: $news->content)), 200);
            $meta['image'] = $news->getFirstMediaUrl('thumbnail') ?: $defaultImage;
        }
    } elseif (preg_match('#^informasi-publik/([^/]+)$#', $any, $matches)) {
        $information = PublicInformation::where('slug', $matches[1])->where('status', 'published')->first();

        if ($information) {
            $meta['title'] = "{$information->title} — {$siteName}";
            $meta['description'] = Str::limit(trim(strip_tags($information->description ?: $meta['description'])), 200);
        }
    }

    return view('app', ['meta' => $meta]);
})->where('any', '^(?!api|storage|sanctum).*$');
