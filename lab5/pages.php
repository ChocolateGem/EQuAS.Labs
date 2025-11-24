<?php
interface Renderer
{
    public function renderTitle(string $title): string;
    public function renderTextBlock(string $text): string;
    public function renderImage(string $url): string;
    public function renderProduct(array $data): string;
    public function renderPage(array $data): string;
}

class HTMLRenderer implements Renderer
{
    public function renderTitle(string $title): string
    {
        return "<h1>$title</h1>";
    }

    public function renderTextBlock(string $text): string
    {
        return "<p>$text</p>";
    }

    public function renderImage(string $url): string
    {
        return "<img src='$url' />";
    }

    public function renderProduct(array $data): string
    {
        return "
            <div class='product'>
                <h2>{$data['name']}</h2>
                <img src='{$data['image']}' />
                <p>{$data['description']}</p>
                <small>ID: {$data['id']}</small>
            </div>";
    }

    public function renderPage(array $data): string
    {
        return "<html><body>" . implode("\n", $data) . "</body></html>";
    }
}


class JsonRenderer implements Renderer
{
    public function renderTitle(string $title): string
    {
        return json_encode(["title" => $title]);
    }

    public function renderTextBlock(string $text): string
    {
        return json_encode(["text" => $text]);
    }

    public function renderImage(string $url): string
    {
        return json_encode(["image" => $url]);
    }

    public function renderProduct(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    public function renderPage(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}


class XmlRenderer implements Renderer
{
    public function renderTitle(string $title): string
    {
        return "<title>$title</title>";
    }

    public function renderTextBlock(string $text): string
    {
        return "<text>$text</text>";
    }

    public function renderImage(string $url): string
    {
        return "<image>$url</image>";
    }

    public function renderProduct(array $data): string
    {
        return "
            <product>
                <name>{$data['name']}</name>
                <description>{$data['description']}</description>
                <image>{$data['image']}</image>
                <id>{$data['id']}</id>
            </product>";
    }

    public function renderPage(array $data): string
    {
        return "<page>" . implode("\n", $data) . "</page>";
    }
}


abstract class Page
{
    protected Renderer $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    abstract public function view(): string;
}


class SimplePage extends Page
{
    private string $title;
    private string $content;

    public function __construct(Renderer $renderer, string $title, string $content)
    {
        parent::__construct($renderer);
        $this->title = $title;
        $this->content = $content;
    }

    public function view(): string
    {
        return $this->renderer->renderPage([
            $this->renderer->renderTitle($this->title),
            $this->renderer->renderTextBlock($this->content)
        ]);
    }
}


class Product
{
    public string $name;
    public string $description;
    public string $image;
    public int $id;

    public function __construct(string $name, string $description, string $image, int $id)
    {
        $this->name = $name;
        $this->description = $description;
        $this->image = $image;
        $this->id = $id;
    }
}


class ProductPage extends Page
{
    private Product $product;

    public function __construct(Renderer $renderer, Product $product)
    {
        parent::__construct($renderer);
        $this->product = $product;
    }

    public function view(): string
    {
        return $this->renderer->renderProduct([
            'name' => $this->product->name,
            'description' => $this->product->description,
            'image' => $this->product->image,
            'id' => $this->product->id
        ]);
    }
}


$html = new HTMLRenderer();
$json = new JsonRenderer();
$xml  = new XmlRenderer();

$simple = new SimplePage($html, "Hello", "This is a simple page!");
echo "HTML Simple Page:\n" . $simple->view() . "\n\n";

$simpleJson = new SimplePage($json, "Hello", "This is a simple page!");
echo "JSON Simple Page:\n" . $simpleJson->view() . "\n\n";

$product = new Product("Laptop", "Powerful device", "image.jpg", 101);

$productHtml = new ProductPage($html, $product);
echo "HTML Product Page:\n" . $productHtml->view() . "\n\n";

$productXml = new ProductPage($xml, $product);
echo "XML Product Page:\n" . $productXml->view() . "\n\n";

?>