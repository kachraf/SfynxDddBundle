<?php

namespace Sfynx\DddBundle\Layer\Infrastructure\EventListener;

use Sfynx\DddBundle\Layer\Infrastructure\Security\Connection\Multitenant;
use Sfynx\DddBundle\Layer\Infrastructure\Exception\InfrastructureException;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;

class HandlerDynamicOdmDatabase
{
    protected $database_type;
    protected $database_multitenant_path_file;
    protected $connection;

    public function __construct($database_type, $database_multitenant_path_file, $connection)
    {
        $this->database_type = $database_type;
        $this->database_multitenant_path_file = $database_multitenant_path_file;
        $this->connection = $connection;
    }

    /**
     *
     * @throws \Exception
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ( ('odm' !== $this->database_type)
            || (HttpKernel::MASTER_REQUEST != $event->getRequestType())
        ) {
            return;
        }

        // we create value environment if not exist and get tenant id value
        $tenant_id = Multitenant::getTenant();

        // we get the dbname of the tenant
        $params['dbname'] = Multitenant::getDbName($this->database_multitenant_path_file);

        // we desconnect
        if ($this->connection->isConnected()) {
            $this->connection->close();
        }

        // Set up the parameters for the parent
        $this->connection->__construct(
            $params, $this->connection->getDriver(), $this->connection->getConfiguration(),
            $this->connection->getEventManager()
        );

        try {
            $this->connection->connect();
        } catch (\Exception $e) {
            throw InfrastructureException::NoTenantDatabaseConnection($tenant_id);
        }
    }
}
