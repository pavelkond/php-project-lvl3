<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class URLController extends Controller
{
    public function index()
    {
        $urls = DB::table('urls')->get();
        $checksObj = DB::table('url_checks')
            ->groupBy('url_id')
            ->select(DB::raw('max("created_at") as latest, url_id'))
            ->get();
        $latestChecks = $checksObj->mapWithKeys(function ($item, $key) {
                return [$item->url_id => ['latest' => $item->latest]];
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
            return back()->with('error', 'Некорректный URL');
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

    public function check($urlId)
    {
        DB::table('url_checks')->insert([
            'url_id' => $urlId,
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
