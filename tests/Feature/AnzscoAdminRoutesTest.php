<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AnzscoAdminRoutesTest extends TestCase
{
    public function test_existing_anzsco_admin_routes_remain_registered(): void
    {
        $this->assertTrue(Route::has('adminconsole.database.anzsco.index'));
        $this->assertTrue(Route::has('adminconsole.database.anzsco.data'));
        $this->assertTrue(Route::has('adminconsole.database.anzsco.create'));
        $this->assertTrue(Route::has('adminconsole.database.anzsco.store'));
        $this->assertTrue(Route::has('adminconsole.database.anzsco.edit'));
        $this->assertTrue(Route::has('adminconsole.database.anzsco.update'));
        $this->assertTrue(Route::has('adminconsole.database.anzsco.import'));
        $this->assertTrue(Route::has('adminconsole.database.anzsco.import.store'));

        $this->assertSame(url('/adminconsole/database/anzsco'), route('adminconsole.database.anzsco.index'));
        $this->assertSame(url('/adminconsole/database/anzsco/create'), route('adminconsole.database.anzsco.create'));
        $this->assertSame(url('/adminconsole/database/anzsco/store'), route('adminconsole.database.anzsco.store'));
        $this->assertSame(url('/adminconsole/database/anzsco/edit/504'), route('adminconsole.database.anzsco.edit', 504));
        $this->assertSame(url('/adminconsole/database/anzsco/504'), route('adminconsole.database.anzsco.update', 504));
        $this->assertSame(url('/adminconsole/database/anzsco/import'), route('adminconsole.database.anzsco.import'));
    }

    public function test_crm_anzsco_search_routes_remain_registered(): void
    {
        $this->assertTrue(Route::has('anzsco.search'));
        $this->assertTrue(Route::has('anzsco.getByCode'));
        $this->assertSame(url('/anzsco/search'), route('anzsco.search'));
        $this->assertSame(url('/anzsco/code/111111'), route('anzsco.getByCode', '111111'));
    }

    public function test_mutation_and_template_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('adminconsole.database.anzsco.destroy'));
        $this->assertTrue(Route::has('adminconsole.database.anzsco.toggle-status'));
        $this->assertTrue(Route::has('adminconsole.database.anzsco.download-template'));

        $this->assertSame(url('/adminconsole/database/anzsco/504'), route('adminconsole.database.anzsco.destroy', 504));
        $this->assertSame(
            url('/adminconsole/database/anzsco/504/toggle-status'),
            route('adminconsole.database.anzsco.toggle-status', 504)
        );
        $this->assertSame(
            url('/adminconsole/database/anzsco/download-template'),
            route('adminconsole.database.anzsco.download-template')
        );
    }

    public function test_edit_form_posts_to_update_route_not_edit_route(): void
    {
        $blade = file_get_contents(resource_path('views/AdminConsole/database/anzsco/form.blade.php'));
        $this->assertNotFalse($blade);

        $this->assertStringContainsString("route('adminconsole.database.anzsco.update'", $blade);
        $this->assertStringNotContainsString(
            "isset(\$occupation) ? route('adminconsole.database.anzsco.edit'",
            $blade
        );
    }

    public function test_list_js_uses_database_prefixed_anzsco_urls(): void
    {
        $blade = file_get_contents(resource_path('views/AdminConsole/database/anzsco/index.blade.php'));
        $this->assertNotFalse($blade);

        $this->assertStringContainsString('/adminconsole/database/anzsco', $blade);
        $this->assertStringNotContainsString("'/adminconsole/anzsco/'", $blade);
    }

    public function test_list_js_refreshes_lucide_icons_after_datatable_draw(): void
    {
        $blade = file_get_contents(resource_path('views/AdminConsole/database/anzsco/index.blade.php'));
        $this->assertNotFalse($blade);

        $this->assertStringContainsString('drawCallback', $blade);
        $this->assertStringContainsString('refreshLucideIcons', $blade);
    }

    public function test_anzsco_admin_views_only_reference_registered_named_routes(): void
    {
        $paths = [
            resource_path('views/AdminConsole/database/anzsco/index.blade.php'),
            resource_path('views/AdminConsole/database/anzsco/form.blade.php'),
            resource_path('views/AdminConsole/database/anzsco/import.blade.php'),
            resource_path('views/AdminConsole/database/anzsco/partials/actions.blade.php'),
        ];

        foreach ($paths as $path) {
            $blade = file_get_contents($path);
            $this->assertNotFalse($blade, "Failed to read [{$path}].");

            preg_match_all("/route\(\s*['\"]([^'\"]+)['\"]/", $blade, $matches);

            foreach ($matches[1] as $name) {
                $this->assertTrue(
                    Route::has($name),
                    basename($path).' references undefined route ['.$name.'].'
                );
            }
        }
    }
}
