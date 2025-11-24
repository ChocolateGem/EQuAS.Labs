<?php

interface Downloader
{
    public function download(string $url): string;
}

class SimpleDownloader implements Downloader
{
    public function download(string $url): string
    {
        echo "SimpleDownloader: Завантаження даних з URL: $url\n";
        // Імітація завантаження
        return "Дані з {$url}";
    }
}


//клас-замісник
class ProxyDownloader implements Downloader
{
    private Downloader $downloader;
    private array $cache = [];

    public function __construct(Downloader $downloader)
    {
        $this->downloader = $downloader;
    }

    public function download(string $url): string
    {
        if (isset($this->cache[$url])) {
            echo "ProxyDownloader: повернення даних з кешу для $url\n";
            return $this->cache[$url];
        }

        echo "ProxyDownloader: кешу немає, виклик SimpleDownloader...\n";

        $data = $this->downloader->download($url);

        $this->cache[$url] = $data;

        return $data;
    }
}

echo "Клієнтський код (SimpleDownloader)\n";
$simple = new SimpleDownloader();
echo $simple->download("https://example.com/file1") . "\n\n";

echo "Клієнтський код (ProxyDownloader з кешем)\n";
$proxy = new ProxyDownloader(new SimpleDownloader());

// Перше завантаження, справжнє
echo $proxy->download("https://example.com/file1") . "\n\n";

// Друге завантаження, з кешу
echo $proxy->download("https://example.com/file1") . "\n\n";

// Завантаження іншого файлу, кеш пустий
echo $proxy->download("https://example.com/file2") . "\n\n";

// Повторне, з кешу
echo $proxy->download("https://example.com/file2") . "\n\n";
