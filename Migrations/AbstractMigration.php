<?php

namespace Zenstruck\Bundle\MigrationsBundle\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration as BaseAbstractMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Abstract container/em aware class for individual migrations to extend from.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class AbstractMigration extends BaseAbstractMigration
{
    /**
     * Run container-aware data migration logic
     *
     * @abstract
     * @param ContainerInterface $container
     */
    abstract public function dataUp(ContainerInterface $container);

    /**
     * Returns description of data migation
     *
     * @abstract
     * @return string
     */
    abstract public function getDataDescription();
}
