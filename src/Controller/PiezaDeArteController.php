<?php

namespace App\Controller;

use App\Entity\PiezaDeArte;
use App\Form\PiezaDeArteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PiezaDeArteController extends AbstractController
{
    /**
     * ACCIÓN 1: Mostrar lista de todas las piezas
     */
    #[Route('/', name: 'lista_piezas')]
    public function lista(EntityManagerInterface $em): Response
    {
        /** @var EntityManagerInterface $em */ // Esta línea no es necesaria, pero es para que no salte error el Inteliphense de VS
        $piezas = $em->getRepository(PiezaDeArte::class)->findAll();

        return $this->render('pieza/lista.html.twig', [
            'piezas' => $piezas,
        ]);
    }

    /**
     * ACCIÓN 2: Crear nueva pieza
     */
    #[Route('/nuevo', name: 'nueva_pieza')]
    public function nuevo(EntityManagerInterface $em, Request $request): Response
    {
        /** @var EntityManagerInterface $em */ 
        /** @var Request $request */ // Esta línea no es necesaria, pero es para que no salte error el Inteliphense de VS

        $pieza = new PiezaDeArte();

        $formulario = $this->createForm(PiezaDeArteType::class, $pieza);
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $em->persist($pieza);
            $em->flush();

            return $this->redirectToRoute('ficha_pieza', ['id' => $pieza->getId()]);
        }

        return $this->render('pieza/nuevo.html.twig', [
            'formulario' => $formulario->createView(),
        ]);
    }

    /**
     * ACCIÓN 3: Mostrar ficha de una pieza
     */
    #[Route('/{id}', name: 'ficha_pieza', requirements: ['id' => '\d+'])]
    public function ficha(EntityManagerInterface $em, int $id): Response
    {
        /** @var EntityManagerInterface $em */
        /** @var int $id */ // Esta línea no es necesaria, pero es para que no salte error el Inteliphense de VS

        $pieza = $em->getRepository(PiezaDeArte::class)->find($id);

        if (!$pieza) {
            throw $this->createNotFoundException('No se ha encontrado la pieza con ID: ' . $id);
        }

        return $this->render('pieza/ficha.html.twig', [
            'pieza' => $pieza,
        ]);
    }

    /**
     * ACCIÓN 4: Editar una pieza existente
     */
    #[Route('/editar/{id}', name: 'editar_pieza', requirements: ['id' => '\d+'])]
    public function editar(EntityManagerInterface $em, Request $request, int $id): Response
    {
        /** @var EntityManagerInterface $em */
        /** @var Request $request */
        /** @var int $id */

        $pieza = $em->getRepository(PiezaDeArte::class)->find($id);

        if (!$pieza) {
            throw $this->createNotFoundException('No se ha encontrado la pieza con ID: ' . $id);
        }

        $formulario = $this->createForm(PiezaDeArteType::class, $pieza);
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $em->flush();

            return $this->redirectToRoute('ficha_pieza', ['id' => $pieza->getId()]);
        }

        return $this->render('pieza/editar.html.twig', [
            'formulario' => $formulario->createView(),
            'pieza' => $pieza,
        ]);
    }

    /**
     * BORRAR: Eliminar una pieza
     */
    #[Route('/borrar/{id}', name: 'borrar_pieza', requirements: ['id' => '\d+'])]
    public function borrar(EntityManagerInterface $em, int $id): Response
    {
        /** @var EntityManagerInterface $em */
        /** @var int $id */


        $pieza = $em->getRepository(PiezaDeArte::class)->find($id);

        if (!$pieza) {
            throw $this->createNotFoundException('No se ha encontrado la pieza con ID: ' . $id);
        }

        $em->remove($pieza);
        $em->flush();

        $this->addFlash('success', 'La pieza ha sido eliminada correctamente.');

        return $this->redirectToRoute('lista_piezas');
    }
}
