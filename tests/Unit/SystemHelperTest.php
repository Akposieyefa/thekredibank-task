<?php

namespace Tests\Unit;

use App\Helpers\SystemHelper;
use PHPUnit\Framework\TestCase;

class SystemHelperTest extends TestCase
{

    public function test_clean_string_returns_a_string(): void
    {
        $helper = new SystemHelper();
        $string = null;
        $response = $helper->cleanStringHelper($string);
        $this->assertIsString($response);
    }

    public function test_that_system_slug_helpers_works()
    {
        $helper = new SystemHelper();
        $response = $helper->systemSlugHelper('Orutu Akposieyefa Williams');
        $this->assertIsString($response);
    }

}
