<?php

namespace Tests\Feature;

use App\Document;
use App\Exceptions\FileMarkDownDoesNotExist;
use Facades\App\Document as DocumentFacade;
use Illuminate\Support\Str;
use Tests\TestCase;

class DocumentationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_it_assume_the_latest_documentation_version()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/docs')
            ->assertRedirect(route('docs.version', DEFAULT_VERSION));

    }

    public function test_it_assume_the_latest_documentation_version_with_page_name()
    {
        $pageNameSection = Str::random(14);
        $this->get(route('docs.version', $pageNameSection))
            ->assertRedirect(route('docs.version', [DEFAULT_VERSION, $pageNameSection]));

    }

    public function test_it_can_parse_file_mark_down_to_html()
    {
        $this->app->instance(Document::class, \Mockery::mock('App\Document[getPath]', function ($mock) {
            $mock->shouldReceive('getPath')->once()->andReturn(
                base_path("tests/Fixture/docs/9.x/stub.md")
            );
        }));

        $this->get(route('docs.version', [DEFAULT_VERSION, 'stub']))
            ->assertSee('<h1>Document for stub</h1>', false );
    }

    public function test_it_abort_if_request_document_not_found()
    {
        $this->get(route('docs.version', [DEFAULT_VERSION, 'not-exist-doc']))
            ->assertNotFound();
    }

    public function test_it_can_render_dynamic_version()
    {
        $this->get(route('docs.version', ['9.x', 'default']))
            ->assertSee('9.x');
        $this->get(route('docs.version', ['8.x', 'default']))
            ->assertSee('8.x');
    }

    public function test_it_get_the_parsed_mark_down_document_page_for_given_version()
    {
        $mock = \Mockery::mock('App\Document[getPath]', function ($mock) {
            $mock->shouldReceive('getPath')->once()->andReturn(
                base_path("tests/Fixture/docs/9.x/stub.md")
            );
        });
        $content = $mock->get('9.x', 'stub');
        self::assertEquals('<h1>Document for stub</h1>', $content);
    }

    public function test_it_throw_exception_when_the_file_mark_down_does_not_exists()
    {
        $this->expectException(FileMarkDownDoesNotExist::class);
        DocumentFacade::get('10-non-exist-version.*', 'example');
    }
}
