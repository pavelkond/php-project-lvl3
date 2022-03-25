<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DiDom\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class URLCheckController extends Controller
{
    public function check(int $urlId): \Illuminate\Http\RedirectResponse
    {
        $createdAt = Carbon::now()->toDateTimeString();
        $url = DB::table('urls')->find($urlId);
        try {
            $response = Http::get($url->name);
            $body = empty($response->body()) ? 'html' : $response->body();
            $document = new Document($body);
            DB::table('url_checks')->insert([
                'url_id' => $urlId,
                'status_code' => $response->status(),
                'h1' => optional($document->first('h1'))->text(),
                'title' => optional($document->first('title'))->text(),
                'description' => optional($document->first('meta[name=description]'))->attr('content'),
                'created_at' => $createdAt
            ]);
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('urls.show', $urlId)->with('success', 'Страница успешно проверена');
    }
}
