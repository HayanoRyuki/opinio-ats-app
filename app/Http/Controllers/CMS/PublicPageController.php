<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\CMS\Page;

class PublicPageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::with('job')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('cms.pages.public', compact('page'));
    }
}
