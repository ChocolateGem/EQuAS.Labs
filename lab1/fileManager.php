<?php

abstract class Storage {
    abstract public function upload($sourcePath, $destFileName);
    abstract public function download($fileName);
    abstract public function delete($fileName);
    abstract public function listFiles();
}

class LocalStorage extends Storage {
    private $baseDir;

    public function __construct($baseDir = "user_files") {
        $this->baseDir = $baseDir ?? (__DIR__ . DIRECTORY_SEPARATOR . "user_files");

        if (!file_exists($this->baseDir)) {
            mkdir($this->baseDir, 0777, true);
        }
    }

    public function upload($sourcePath, $destFileName) {
        if (!file_exists($sourcePath)) {
            $sourcePath = __DIR__ . DIRECTORY_SEPARATOR . $sourcePath;
        }
        $destination = $this->baseDir . DIRECTORY_SEPARATOR . $destFileName;
        if (copy($sourcePath, $destination)) {
            echo "Файл '{$destFileName}' успішно завантажено у локальне сховище.<br><br>";
        } else {
            echo "Помилка завантаження файлу.<br>";
        }
    }

    public function download($fileName) {
        $path = $this->baseDir . DIRECTORY_SEPARATOR . $fileName;
        if (file_exists($path)) {
            echo "Завантаження файлу: '{$fileName}' — успішно (шлях: {$path})<br><br>";
        } else {
            echo "Файл '{$fileName}' не знайдено у локальному сховищі.<br>";
        }
    }

    public function delete($fileName) {
        $path = $this->baseDir . DIRECTORY_SEPARATOR . $fileName;
        if (file_exists($path)) {
            unlink($path);
            echo "Файл '{$fileName}' видалено з локального сховища.<br><br>";
        } else {
            echo "Файл '{$fileName}' не знайдено для видалення.<br>";
        }
    }

    public function listFiles() {
        $files = scandir($this->baseDir);
        $files = array_diff($files, ['.', '..']);
        echo "Файли у сховищі '{$this->baseDir}':<br>";
        foreach ($files as $file) {
            echo "  - {$file}<br>";
        }
    }
}

class FileManager {
    private static $instance = null;
    private $storage = null;

    // приватний конструктор забороняє створення через new
    private function __construct() {}

    // для отримання єдиного екземпляра
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new FileManager();
            echo "Створено новий екземпляр FileManager.<br>";
        } else {
            echo "Використовується існуючий екземпляр FileManager.<br>";
        }
        return self::$instance;
    }

    public function setStorage(Storage $storage) {
        $this->storage = $storage;
        echo "Встановлено нове сховище для користувача (" . get_class($storage) . ").<br>";
    }

    public function uploadFile($sourcePath, $destFileName) {
        if ($this->storage === null) {
            echo "Помилка: сховище не встановлено!<br>";
            return;
        }
        $this->storage->upload($sourcePath, $destFileName);
    }

    public function downloadFile($fileName) {
        if ($this->storage === null) {
            echo "Помилка: сховище не встановлено!<br>";
            return;
        }
        $this->storage->download($fileName);
    }

    public function deleteFile($fileName) {
        if ($this->storage === null) {
            echo "Помилка: сховище не встановлено!<br>";
            return;
        }
        $this->storage->delete($fileName);
    }

    public function listFiles() {
        if ($this->storage === null) {
            echo "Помилка: сховище не встановлено!<br>";
            return;
        }
        $this->storage->listFiles();
    }
}

// Приклад роботи
$testFile = __DIR__ . DIRECTORY_SEPARATOR . "example.txt";
file_put_contents($testFile, "Тестовий вміст файлу");

$fileManager1 = FileManager::getInstance();

$localStorage = new LocalStorage();
$fileManager1->setStorage($localStorage);

$fileManager1->uploadFile($testFile, "copy1.txt");
$fileManager1->listFiles();
$fileManager1->downloadFile("copy1.txt");
//$fileManager1->deleteFile("copy1.txt");

// другий екземпляр це той самий об’єкт
$fileManager2 = FileManager::getInstance();
if ($fileManager1 === $fileManager2) {
    echo "Перевірка: fileManager1 та fileManager2 — це один і той самий об’єкт.<br>";
}

?>
