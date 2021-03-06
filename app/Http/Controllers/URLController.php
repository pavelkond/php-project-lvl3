<?php

namespace App\Http\Controllers;

use DiDom\Document;
use GuzzleHttp\Exception\ConnectException;
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
        $latestWithCode = DB::table('url_checks')
            ->joinSub($latest, 'sub', function ($join) {
                $join->on('url_checks.url_id', '=', 'sub.url_id')
                    ->on('url_checks.created_at', '=', 'sub.latest_check');
            })->get();
        $latestChecks = $latestWithCode->mapWithKeys(function ($item, $key) {
            return [
                $item->url_id => [
                    'latest' => $item->latest_check,
                    'status' => $item->status_code
                ]
            ];
        });
        return view('url.index', compact('urls', 'latestChecks'));
    }

    public function show(int $id)
    {
        $url = DB::table('urls')->find($id);
        if (is_null($url)) {
            abort(404);
        }
        $checks = DB::table('url_checks')
            ->where('url_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('url.show', compact('url', 'checks'));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->input('url.name');
        $url = is_null($data) ? false : $this->normalizeURL($data);
        if (!$url) {
            return back()
                ->with('currentUrl', $data)
                ->withErrors(['error' => 'Некорректный URL']);
        }
        $reqURL = DB::table('urls')->where('name', $url)->first();
        $flashAlert = isset($reqURL->id) ? 'warning' : 'success';
        $flashMessage = isset($reqURL->id) ? 'Страница уже существует' : 'Страница успешно добавлена';
        if (!is_null($reqURL)) {
            $id = $reqURL->id;
        } else {
            $id = DB::table('urls')->insertGetId([
                'name' => $url,
                'created_at' => Carbon::now()->toISOString()
            ]);
        }

        return redirect()->route('urls.show', $id)->with($flashAlert, $flashMessage);
    }

    private function normalizeURL(string $url): string|false
    {
        $urlParts = parse_url($url);
        if (
            $urlParts === false
            || !isset($urlParts['scheme'])
            || !isset($urlParts['host'])
            || strlen($url) > 255
        ) {
            return false;
        }
        return $urlParts['scheme'] . '://' . $urlParts['host'];
    }
}
