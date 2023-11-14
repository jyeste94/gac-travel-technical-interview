<?php

namespace App\EventListener;

use App\Entity\Product;
use App\Entity\StockHistoric;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Security;

class StockListener
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Product) {
            return;
        }

        if ($args->hasChangedField('stock')) {
            $entityManager = $args->getEntityManager();
            $stockChange = $args->getNewValue('stock') - $args->getOldValue('stock');


            $stockHistoric = new StockHistoric();
            $stockHistoric->setUserId( $this->security->getUser()  );
            $stockHistoric->setProductId($entity);
            $stockHistoric->setStock($args->getNewValue('stock'));

            $entityManager->persist($stockHistoric);
            //$entityManager->flush();
        }
    }


}