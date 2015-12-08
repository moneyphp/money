<?php

use Buzz\Client\ClientInterface;
use Buzz\Message\Request;
use Buzz\Message\Response;

require_once __DIR__.'/../vendor/autoload.php';

final class CurrencyUpdater
{
    private $uri = 'http://openexchangerates.org/currencies.json';

    private $file;

    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        $this->file = __DIR__.'/../data/currencies.php';
    }

    public function update()
    {
        $currencies = $this->findCurrencies();

        $lines = array_merge(
            $this->fileHeader(),
            explode("\n", 'return '.var_export($currencies, true).";")
        );

        $this->generateFile($this->file, $lines);
    }

    private function fileHeader()
    {
        return [
            '<?php',
            '// From '.$this->uri,
        ];
    }

    private function findCurrencies()
    {
        return $this->loadJson($this->uri);
    }

    private function loadJson($uri)
    {
        $response = $this->request($uri);

        if (empty($json = @json_decode($response->getContent(), true))) {
            throw new RuntimeException('Not JSON');
        }

        return $json;
    }

    private function request($uri, $method = Request::METHOD_GET)
    {
        $parts = parse_url($uri);

        $request = new Request($method, $parts['path'], $parts['scheme'].'://'.$parts['host']);
        $response = new Response();

        $this->client->send($request, $response);

        return $response;
    }

    private function generateFile($path, $lines)
    {
        if(false === file_put_contents($path, trim(implode("\n", $lines))."\n")) {
            throw new RuntimeException('Failed to generate file '.$path);
        }
    }
}

$updater = new CurrencyUpdater(new \Buzz\Client\FileGetContents());
$updater->update();
