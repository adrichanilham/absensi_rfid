<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PwaManifestTest extends TestCase
{
    public function test_guest_can_read_the_manifest_without_starting_a_session(): void
    {
        $manifest = json_decode(File::get(public_path('manifest.webmanifest')), true, flags: JSON_THROW_ON_ERROR);

        $this->get('/pwa/manifest.json')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/manifest+json')
            ->assertHeaderMissing('Set-Cookie')
            ->assertExactJson($manifest);
    }

    public function test_login_page_references_the_manifest_endpoint_with_same_origin_credentials(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('<link rel="manifest" href="/pwa/manifest.json" crossorigin="use-credentials">', false);
    }
}
