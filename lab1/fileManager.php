<?php

// ======================
// 1. Абстрактне сховище
// ======================
abstract class Storage {
    abstract public function upload($sourcePath, $destFileName);
    abstract public function download($fileName);
    abstract public function delete($fileName);
    abstract public function listFiles();
}

// ======================
// 2. Локальне сховище
// ======================
class LocalStorage extends Storage {
    private $baseDir;

    public function __construct($baseDir = "user_files") {
        $this->baseDir = $baseDir;

        if (!file_exists($this->baseDir)) {
            mkdir($this->baseDir, 0777, true);
        }
    }

    public function upload($sourcePath, $destFileName) {
        $destination = $this->baseDir . DIRECTORY_SEPARATOR . $destFileName;
        if (copy($sourcePath, $destination)) {
            echo "Файл '{$destFileName}' успішно завантажено у локальне сховище.\n";
        } else {
            echo "Помилка завантаження файлу.\n";
        }
    }

    public function download($fileName) {
        $path = $this->baseDir . DIRECTORY_SEPARATOR . $fileName;
        if (file_exists($path)) {
            echo "Завантаження файлу: '{$fileName}' — успішно (шлях: {$path})\n";
        } else {
            echo "Файл '{$fileName}' не знайдено у локальному сховищі.\n";
        }
    }

    public function delete($fileName) {
        $path = $this->baseDir . DIRECTORY_SEPARATOR . $fileName;
        if (file_exists($path)) {
            unlink($path);
            echo "Файл '{$fileName}' видалено з локального сховища.\n";
        } else {
            echo "Файл '{$fileName}' не знайдено для видалення.\n";
        }
    }

    public function listFiles() {
        $files = scandir($this->baseDir);
        $files = array_diff($files, ['.', '..']);
        echo "Файли у сховищі '{$this->baseDir}':\n";
        foreach ($files as $file) {
            echo "  - {$file}\n";
        }
    }
}

// ======================
// 3. Менеджер файлів (Singleton)
// ======================
class FileManager {
    private static $instance = null;
    private $storage = null;

    // Приватний конструктор — забороняє створення через new
    private function __construct() {}

    // Метод для отримання єдиного екземпляра
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new FileManager();
            echo "Створено новий екземпляр FileManager.\n";
        } else {
            echo "Використовується існуючий екземпляр FileManager.\n";
        }
        return self::$instance;
    }

    // Встановлення сховища для користувача
    public function setStorage(Storage $storage) {
        $this->storage = $storage;
        echo "Встановлено нове сховище для користувача (" . get_class($storage) . ").\n";
    }

    // Операції з файлами
    public function uploadFile($sourcePath, $destFileName) {
        if ($this->storage === null) {
            echo "Помилка: сховище не встановлено!\n";
            return;
        }
        $this->storage->upload($sourcePath, $destFileName);
    }

    public function downloadFile($fileName) {
        if ($this->storage === null) {
            echo "Помилка: сховище не встановлено!\n";
            return;
        }
        $this->storage->download($fileName);
    }

    public function deleteFile($fileName) {
        if ($this->storage === null) {
            echo "Помилка: сховище не встановлено!\n";
            return;
        }
        $this->storage->delete($fileName);
    }

    public function listFiles() {
        if ($this->storage === null) {
            echo "Помилка: сховище не встановлено!\n";
            return;
        }
        $this->storage->listFiles();
    }
}

// ======================
// 4. Демонстрація роботи
// ======================

// Для спрощення створимо тестовий файл
$testFile = "example.txt";
file_put_contents($testFile, "Тестовий вміст файлу");

// Отримуємо єдиний екземпляр менеджера файлів
$fileManager1 = FileManager::getInstance();

// Встановлюємо сховище (локальний диск)
$localStorage = new LocalStorage();
$fileManager1->setStorage($localStorage);

// Виконуємо операції
$fileManager1->uploadFile($testFile, "copy1.txt");
$fileManager1->listFiles();
$fileManager1->downloadFile("copy1.txt");
$fileManager1->deleteFile("copy1.txt");

// Демонструємо, що другий "екземпляр" — той самий об’єкт
$fileManager2 = FileManager::getInstance();
if ($fileManager1 === $fileManager2) {
    echo "Перевірка: fileManager1 та fileManager2 — це один і той самий об’єкт (Singleton).\n";
}

?>
