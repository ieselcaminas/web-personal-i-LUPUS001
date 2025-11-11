<?php

namespace App\DataFixtures;

use App\Entity\Artista;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/* Fixtures es algo parecido a lo que hicimos de al principio del tema de poner datos bases de prueba, solo que en este caso
además de darnos unos datos base para la base de datos, también borrara toda la base de datos, así que **usarlo con cuidado**
*/

// Para crear la plantilla (que no twig) de DataFictures --> php bin/console make:fixtures
// Para ejecutarlo con los nuevos datos base --> php bin/console doctrine:fixtures:load
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $artistas = [
            ['nombre' => 'Vincent van Gogh', 'nacionalidad' => 'Neerlandés', 'movimiento' => 'Postimpresionismo'],
            ['nombre' => 'Frida Kahlo', 'nacionalidad' => 'Mexicana', 'movimiento' => 'Surrealismo'],
            ['nombre' => 'Pablo Picasso', 'nacionalidad' => 'Español', 'movimiento' => 'Cubismo'],
        ];

        foreach ($artistas as $data) {
            $a = new Artista();
            $a->setNombre($data['nombre']);
            $a->setNacionalidad($data['nacionalidad']);
            $a->setMovimiento($data['movimiento']);
            $manager->persist($a);
        }

        $manager->flush();
    }
}

