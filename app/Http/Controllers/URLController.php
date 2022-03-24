<?php

namespace App\Http\Controllers;

use DiDom\Document;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class URLController extends Controller
{
    public function index()
    {
        $urls = DB::table('urls')->get();
        $latest = DB::table('url_checks')
            ->groupBy('url_id')
            ->select('url_id', DB::raw('max("created_at") as latest_check'));
        $latestWithCode = DB::table('url_checks')->joinSub($latest, 'sub', function ($join) {
            $join->on('url_checks.url_id', '=', 'sub.url_id')
                ->on('url_checks.created_at', '=', 'sub.latest_check');
        })->get();
        $latestChecks = $latestWithCode->mapWithKeys(function ($item, $key) {
                return [$item->url_id => ['latest' => $item->latest_check, 'status' => $item->status_code]];
        });
        return view('url.index', compact('urls', 'latestChecks'));
    }

    public function show($id)
    {

        $url = DB::table('urls')->find($id);
        $checks = DB::table('url_checks')->where('url_id', $id)->get();
        return view('url.show', compact('url', 'checks'));
    }

    public function store(Request $request)
    {
        $data = $request->input('url.name');
        $url = $this->normalizeURL($data);
        if (!$url) {
            return back()->with('error', 'Некорректный URL')->with('currentUrl', $data);
        }
        $reqURL = DB::table('urls')->where('name', $url)->first();
        $flashAlert = isset($reqURL->id) ? 'warning' : 'success';
        $flashMessage = isset($reqURL->id) ? 'Страница уже существует' : 'Страница успешно добавлена';
        if (!is_null($reqURL)) {
            $id = $reqURL->id;
        } else {
            $id = DB::table('urls')->insertGetId([
                'name' => $url,
                'created_at' => Carbon::now()->toDateTimeString()
            ]);
        }

        return redirect()->route('urls.show', $id)->with($flashAlert, $flashMessage);
    }

    public function check($urlId): \Illuminate\Http\RedirectResponse
    {
        $url = DB::table('urls')->find($urlId);
        $response = Http::get($url->name);
        $document = new Document($response->body());
        DB::table('url_checks')->insert([
            'url_id' => $urlId,
            'status_code' => $response->status(),
            'h1' => optional($document->first('h1'))->text(),
            'title' => optional($document->first('title'))->text(),
            'description' => optional($document->first('meta[name=description]'))->text(),
            'created_at' => Carbon::now()->toDateTimeString()
        ]);

        return redirect()->route('urls.show', $urlId)->with('success', 'Страница успешно проверена');
    }

    private function normalizeURL(string $url): string|false
    {
        $urlParts = parse_url($url);
        if ($urlParts === false || !isset($urlParts['scheme']) || !isset($urlParts['host']) || strlen($url) > 255) {
            return false;
        }
        return $urlParts['scheme'] . '://' . $urlParts['host'];
    }
}
