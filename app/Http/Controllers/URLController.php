<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Nette\Schema\ValidationException;

class URLController extends Controller
{
    public function index()
    {
        $urls = DB::table('urls')->get();
        return view('url.index', compact('urls'));
    }

    public function show($id)
    {
        $url = DB::table('urls')->find($id);
        return view('url.show', compact('url'));
    }

    public function store(Request $request)
    {
        $data = $request->input('url.name');
        $url = $this->normalizeURL($data);
        if (!$url) {
            return redirect('/')->setStatusCode(422)->with('error', 'Некорректный URL');
        }
        $reqURL = DB::table('urls')->where('name', $url);
        $flashAlert = isset($reqURL->id) ? 'warning' : 'success';
        $flashMessage = isset($reqURL->id) ? 'Страница уже существует' : 'Страница успешно добавлена';
        $id = $reqURL->id ?? DB::table('urls')->insertGetId([
            'name' => $url,
            'created_at' => Carbon::now()->toDateTimeString()
        ]);

        return redirect()->route('urls.show', $id)->with($flashAlert, $flashMessage);
    }

    private function normalizeURL(string $url): string|false
    {
        $urlParts = parse_url($url);
        if ($urlParts === false || !isset($urlParts['scheme']) || !isset($urlParts['host'])) {
            return false;
        }
        return $urlParts['scheme'] . '://' . $urlParts['host'];
    }
}
