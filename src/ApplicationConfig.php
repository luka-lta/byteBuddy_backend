<?php
declare(strict_types=1);

namespace ByteBuddyApi;

use ByteBuddyApi\Factory\LoggerFactory;
use ByteBuddyApi\Factory\PdoFactory;
use DI\Definition\Source\DefinitionArray;
use Exception;
use Monolog\Logger;
use PDO;
use function DI\factory;

class ApplicationConfig extends DefinitionArray
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct($this->getConfig());
    }

    private function getConfig(): array
    {
        return [
            PDO::class => factory(new PdoFactory()),
            Logger::class => factory(new LoggerFactory()),
        ];
    }
}