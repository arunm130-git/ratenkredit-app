<?php

use PHPUnit\Framework\TestCase;
use App\Services\IngDibaService;
use GuzzleHttp\ClientInterface;
use App\Services\ConfigurationServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class IngDibaServiceTest extends TestCase
{
    private ClientInterface $clientMock;
    private IngDibaService $service;

    protected function setUp(): void
    {
        $configMock = $this->createMock(ConfigurationServiceInterface::class);
        $this->clientMock = $this->createMock(ClientInterface::class);

        $this->service = new IngDibaService($configMock, $this->clientMock);

        // Default mock config returned for every test
        $configMock->method('get')->willReturn([
            'url' => 'https://dummyurl.com/',
            'access_token' => 'dummy-token',
        ]);
    }

    public function testFetchLoanOffersReturnsFormattedData(): void
    {
        // Simulate valid API response
        $this->mockHttpClientResponse('{"zinsen": 5.00, "duration": 24}');

        $offers = $this->service->fetchLoanOffers(['amount' => 5000]);

        $this->assertEquals([
            'interest' => '5.00',
            'duration' => '24',
        ], $offers);
    }

    public function testFetchLoanOffersThrowsExceptionOnInvalidResponse(): void
    {
        // Simulate invalid API response (missing expected fields)
        $this->mockHttpClientResponse('{"invalid_field": "value"}');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid API response format from ING DiBa : {"invalid_field":"value"}');

        $this->service->fetchLoanOffers(['amount' => 5000]);
    }

    private function mockHttpClientResponse(string $jsonBody): void
    {
        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->method('getContents')->willReturn($jsonBody);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($streamMock);

        $this->clientMock->method('request')->willReturn($responseMock);
    }
}
