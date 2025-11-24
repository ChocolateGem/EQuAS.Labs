<?php


// інтерфейс стратегія розрахунку вартості доставки
interface DeliveryStrategy
{

    public function calculatePrice(float $orderAmount): float;
}


 // Стратегія: самовивіз
 // Вартість доставки = 0

class PickupDelivery implements DeliveryStrategy
{
    public function calculatePrice(float $orderAmount): float
    {
        return 0;
    }
}


 // Стратегія: зовнішня служба доставки
 // вартість 50 грн

class ExternalDelivery implements DeliveryStrategy
{
    public function calculatePrice(float $orderAmount): float
    {
        return 50; 
    }
}


 // Стратегія: власна служба доставки
 // 20 грн + 5% від суми замовлення

class InternalDelivery implements DeliveryStrategy
{
    public function calculatePrice(float $orderAmount): float
    {
        return 20 + ($orderAmount * 0.05);
    }
}


 // клас використовує одну зі стратегій
class DeliveryContext
{
    private DeliveryStrategy $strategy;

    public function setStrategy(DeliveryStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function calculate(float $orderAmount): float
    {
        return $this->strategy->calculatePrice($orderAmount);
    }
}


 // КЛІЄНТСЬКИЙ КОД
$orderAmount = 500; // сума замовлення

$context = new DeliveryContext();

$context->setStrategy(new PickupDelivery());
echo "Самовивіз: " . $context->calculate($orderAmount) . " грн\n";

$context->setStrategy(new ExternalDelivery());
echo "Зовнішня служба доставки: " . $context->calculate($orderAmount) . " грн\n";

$context->setStrategy(new InternalDelivery());
echo "Власна служба доставки: " . $context->calculate($orderAmount) . " грн\n";

