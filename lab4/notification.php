<?php 

interface Notification 
{ 
    public function send(string $title, string $message); 
} 

class EmailNotification implements Notification 
{ 
    private $adminEmail; 

    public function __construct(string $adminEmail) 
    { 
        $this->adminEmail = $adminEmail; 
    } 

    public function send(string $title, string $message): void 
    { 
        //  mail($this->adminEmail, $title, $message);
        echo "Email → '$title' → {$this->adminEmail}: $message\n";
    } 
}

class SlackService
{
    private $login;
    private $apiKey;
    private $chatId;

    public function __construct($login, $apiKey, $chatId)
    {
        $this->login = $login;
        $this->apiKey = $apiKey;
        $this->chatId = $chatId;
    }

    public function sendMessage($text)
    {
        echo "Slack → ChatID {$this->chatId} (User {$this->login}): $text\n";
    }
}

class SmsService
{
    private $phone;
    private $sender;

    public function __construct($phone, $sender)
    {
        $this->phone = $phone;
        $this->sender = $sender;
    }

    public function sendSms($text)
    {
        echo "SMS → {$this->phone} (Sender: {$this->sender}): $text\n";
    }
}

class SlackNotificationAdapter implements Notification
{
    private $slack;

    public function __construct(SlackService $slack)
    {
        $this->slack = $slack;
    }

    public function send(string $title, string $message)
    {
        $text = "[$title] $message";
        $this->slack->sendMessage($text);
    }
}

class SmsNotificationAdapter implements Notification
{
    private $sms;

    public function __construct(SmsService $sms)
    {
        $this->sms = $sms;
    }

    public function send(string $title, string $message)
    {
        $text = "[$title] $message";
        $this->sms->sendSms($text);
    }
}

echo "EMAIL\n";
$email = new EmailNotification("example@example.com");
$email->send("Test Email", "Hello via Email!");


echo "\nSLACK\n";
$slackService = new SlackService("myLogin", "apiKey123", "chat123");
$slackNotify = new SlackNotificationAdapter($slackService);
$slackNotify->send("Slack Title", "Hello from Slack!");


echo "\nSMS\n";
$smsService = new SmsService("+180501234567", "MyCompany");
$smsNotify = new SmsNotificationAdapter($smsService);
$smsNotify->send("SMS Alert", "This is SMS notification!");

?>

