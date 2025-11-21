<?php

interface SocialNetworkConnector {
    public function login();
    public function logout();
    public function createPost($content);
}

//фабрика
abstract class SocialNetworkPoster {
    abstract public function getConnector(): SocialNetworkConnector;

    public function post($content) {
        // одна логіка публікації для всіх соцмереж
        $connector = $this->getConnector();
        $connector->login();
        $connector->createPost($content);
        $connector->logout();
    }
}

class FacebookConnector implements SocialNetworkConnector {
    private $login;
    private $password;

    public function __construct($login, $password) {
        $this->login = $login;
        $this->password = $password;
    }

    public function login() {
        echo "Вхід у Facebook як {$this->login}<br>";
    }

    public function logout() {
        echo "Вихід із Facebook<br>";
    }

    public function createPost($content) {
        echo "Публікація у Facebook: '{$content}'<br>";
    }
}

class FacebookPoster extends SocialNetworkPoster {
    private $login;
    private $password;

    public function __construct($login, $password) {
        $this->login = $login;
        $this->password = $password;
    }

    public function getConnector(): SocialNetworkConnector {
        return new FacebookConnector($this->login, $this->password);
    }
}

class LinkedInConnector implements SocialNetworkConnector {
    private $email;
    private $password;

    public function __construct($email, $password) {
        $this->email = $email;
        $this->password = $password;
    }

    public function login() {
        echo "Вхід у LinkedIn як {$this->email}<br>";
    }

    public function logout() {
        echo "Вихід із LinkedIn<br>";
    }

    public function createPost($content) {
        echo "Публікація у LinkedIn: '{$content}'<br>";
    }
}

class LinkedInPoster extends SocialNetworkPoster {
    private $email;
    private $password;

    public function __construct($email, $password) {
        $this->email = $email;
        $this->password = $password;
    }

    public function getConnector(): SocialNetworkConnector {
        return new LinkedInConnector($this->email, $this->password);
    }
}

function clientCode(SocialNetworkPoster $poster) {
    $poster->post("Tестова публікація!");
}

echo "Публікація у Facebook<br>";
$facebookPoster = new FacebookPoster("user_fb", "pass_fb");
clientCode($facebookPoster);

echo "<br>Публікація у LinkedIn<br>";
$linkedinPoster = new LinkedInPoster("user@linkedin.com", "pass_linkedin");
clientCode($linkedinPoster);
?>