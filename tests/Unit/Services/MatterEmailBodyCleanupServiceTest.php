<?php

namespace Tests\Unit\Services;

use App\Services\MatterEmailBodyCleanupService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MatterEmailBodyCleanupServiceTest extends TestCase
{
    #[Test]
    public function it_returns_zero_for_invalid_matter_id(): void
    {
        $service = new MatterEmailBodyCleanupService();

        $this->assertSame(0, $service->clearBodiesForMatter(0));
        $this->assertSame(0, $service->clearBodiesForMatter(-1));
        $this->assertFalse($service->matterHasBodyContentInDatabase(0));
    }
}
