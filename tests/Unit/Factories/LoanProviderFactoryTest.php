<?php

use PHPUnit\Framework\TestCase;
use App\Factories\LoanProviderFactory;
use App\Services\IngDibaService;
use App\Services\SmavaService;
use App\Services\ConfigurationService;
use GuzzleHttp\Client;

class LoanProviderFactoryTest extends TestCase
{
    private LoanProviderFactory $factory;

    protected function setUp(): void
    {
        // Instantiate the factory before each test
        $this->factory = new LoanProviderFactory();
    }

    public function testCreateIngDibaService(): void
    {
        $provider = 'ing-diba';

        // Create service
        $service = $this->factory->make($provider);

        // Ensure it's an instance of IngDibaService
        $this->assertInstanceOf(IngDibaService::class, $service);
    }

    public function testCreateSmavaService(): void
    {
        $provider = 'smava';

        // Create service
        $service = $this->factory->make($provider);

        // Ensure it's an instance of SmavaService
        $this->assertInstanceOf(SmavaService::class, $service);
    }

    public function testThrowsExceptionOnUnsupportedProvider(): void
    {
        $provider = 'unsupported-provider';

        // Expect an exception to be thrown
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Unsupported provider: $provider");

        // Call the factory method with an unsupported provider
        $this->factory->make($provider);
    }
}
