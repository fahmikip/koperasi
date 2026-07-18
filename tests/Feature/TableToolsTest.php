<?php

namespace Tests\Feature;

use Tests\TestCase;

class TableToolsTest extends TestCase
{
    public function test_table_tools_are_included_in_compiled_frontend_source(): void
    {
        $javascript = file_get_contents(resource_path('js/app.js'));

        $this->assertStringContainsString('simple-datatables', $javascript);
        $this->assertStringContainsString('Export CSV', $javascript);
        $this->assertStringContainsString('dataTable.print()', $javascript);
        $this->assertStringContainsString("document.querySelectorAll('main table:not([data-table-static])')", $javascript);
    }
}
