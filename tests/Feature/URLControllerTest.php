<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class URLControllerTest extends TestCase
{
    use WithFaker;
    use WithoutMiddleware;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();
        for ($i = 0; $i < 5; $i++) {
            DB::table('urls')->insert([
                'name' => 'https://' . $this->faker->unique()->domainName,
                'created_at' => Carbon::now()->toISOString()
            ]);
        }
    }

    public function testIndex()
    {
        $response = $this->get(route('index'));
        $response->assertOk();
    }

    public function testUrlIndex()
    {
        $response = $this->get(route('urls.index'));
        $response->assertOk();
    }

    public function testStoreWithValidUrl()
    {
        $data = [
            'url' => [
                'name' => 'https://' . $this->faker->unique()->domainName
            ]
        ];
        $response = $this->post(route('urls.store'), $data);
        $this->assertDatabaseHas('urls', [
            'name' => $data['url']['name']
        ]);
        $url = DB::table('urls')->where('name', $data['url']['name'])->first();
        $this->assertNotNull($url);
        if (!is_null($url)) {
            $response->assertRedirect(route('urls.show', $url->id));
        }
        $response->assertSessionHasNoErrors();


        $response = $this->post(route('urls.store'), $data);
        if (!is_null($url)) {
            $response->assertRedirect(route('urls.show', $url->id));
        }
        $response->assertSessionHasNoErrors();
        $urlCount = DB::table('urls')->where('name', $data['url']['name'])->count();
        $this->assertEquals(1, $urlCount);
    }

    public function testStoreWithInvalidUrl()
    {
        $data = [
            'url' => [
                'name' => $this->faker->unique()->domainName
            ]
        ];
        $response = $this->post(route('urls.store'), $data);
        $response->assertRedirect(route('index'));
        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('urls', [
            'name' => $data['url']['name']
        ]);
    }

    public function testShow()
    {
        $url = DB::table('urls')->inRandomOrder()->first();
        $this->assertNotNull($url);
        if (!is_null($url)) {
            $response = $this->get(route('urls.show', $url->id));
            $response->assertSeeText($url->name);
            $response->assertOk();
            $response->assertSessionHasNoErrors();
        }
    }

    public function testCheck()
    {
        $url = DB::table('urls')->inRandomOrder()->first();
        $this->assertNotNull($url);
        Http::fake();
        if (!is_null($url)) {
            $response = $this->post(route('urls.check', $url->id));
            $response->assertRedirect(route('urls.show', $url->id));
            $this->assertDatabaseHas('url_checks', [
                'url_id' => $url->id
            ]);
        }
    }
}
