<?php

namespace App\Repository;

use App\Entity\PiezaDeArte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PiezaDeArte>
 */
class PiezaDeArteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PiezaDeArte::class);
    }

    // Aquí puedes añadir métodos personalizados de consulta si los necesitas.
    // Ejemplo:
    /*
    public function findByAnio($anio): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.anio = :val')
            ->setParameter('val', $anio)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}