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

    private int $id;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();
        $this->id = DB::table('urls')->insertGetId([
            'name' => 'https://' . $this->faker->unique()->domainName,
            'created_at' => Carbon::now()->toDateTimeString()
        ]);
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
        $url = optional(
            DB::table('urls')
                ->select('id')
                ->where('name', $data['url']['name'])
        )->first();
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
        $response = $this->get(route('urls.show', $this->id));
        $response->assertOk();
        $response->assertSessionHasNoErrors();
    }

    public function testShowWithInvalidId()
    {
        $id = 1000;
        $response = $this->get(route('urls.show', $id));
        $response->assertNotFound();
    }

    public function testCheck()
    {
        $body = file_get_contents(__DIR__ . '/../fixtures/test.html');
        Http::fake([
            '*' => Http::response($body)
        ]);
        $response = $this->post(route('urls.check', $this->id));
        $response->assertRedirect(route('urls.show', $this->id));
        $this->assertDatabaseHas('url_checks', [
            'url_id' => $this->id,
            'title' => 'Test HTML',
            'h1' => 'Test',
            'description' => 'Test',
            'status_code' => 200
        ]);
    }
}
