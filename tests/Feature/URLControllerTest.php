<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
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
                'created_at' => Carbon::now()->toDateTimeString()
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
        $urlID = DB::table('urls')->where('name', $data['url']['name'])->first()->id;
        $response->assertRedirect(route('urls.show', $urlID));
        $response->assertSessionHasNoErrors();


        $response = $this->post(route('urls.store'), $data);
        $response->assertRedirect(route('urls.show', $urlID));
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
        $this->assertDatabaseMissing('urls', [
            'name' => $data['url']['name']
        ]);
    }

    public function testShow()
    {
        $url = DB::table('urls')->inRandomOrder()->first();
        $response = $this->get(route('urls.show', $url->id));
        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $response->assertSeeText($url->name);
    }
}
