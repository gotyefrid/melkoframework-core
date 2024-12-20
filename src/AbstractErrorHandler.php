<?php
declare(strict_types=1);

namespace Gotyefrid\MelkoframeworkCore;

use Gotyefrid\MelkoframeworkCore\exceptions\HttpErrorInterface;
use Gotyefrid\MelkoframeworkCore\exceptions\JsonErrorInterface;
use Throwable;

abstract class AbstractErrorHandler
{
    private bool $debug;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }

    /**
     * @param Throwable $throwable
     *
     * @return string
     * @throws Throwable
     */
    public function handle(Throwable $throwable): string
    {
        if ($this->debug) {
            // Проверка, если это localhost или IP в диапазоне 192.168.*
            if (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']) || preg_match('/^192\.168\./', $_SERVER['REMOTE_ADDR'])) {
                throw $throwable; // выбрасываем исключение в случае отладки на локалке
            }
        }

        if ($throwable instanceof HttpErrorInterface) {
            return $throwable->getErrorHtml();
        }

        if ($throwable instanceof JsonErrorInterface) {
            return $throwable->getErrorJson();
        }

        $text = 'Вызвано исключение: ' . get_class($throwable);
        $text .= '<br>' . $throwable->getMessage();

        return $text;
    }


    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $debug): AbstractErrorHandler
    {
        $this->debug = $debug;
        return $this;
    }
}
