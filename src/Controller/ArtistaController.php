<?php

namespace App\Controller;

use App\Entity\Artista;
use App\Form\ArtistaType; // Asegúrate de que este formulario exista
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Define el prefijo de ruta para todas las acciones de este controlador
#[Route('/artistas')]
class ArtistaController extends AbstractController
{
    // --- ACCIONES DE GESTIÓN (CRUD) ---

    /**
     * ACCIÓN 2: Crear un nuevo Artista
     * URL: /artistas/nuevo
     */
    #[Route('/nuevo', name: 'nuevo_artista')]
    public function nuevo(EntityManagerInterface $em, Request $request): Response
    {
        $artista = new Artista();
        $formulario = $this->createForm(ArtistaType::class, $artista);
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $em->persist($artista);
            $em->flush();

            $this->addFlash('success', '¡Artista "' . $artista->getNombre() . '" creado con éxito!');

            // Redirigir a la ficha de la artista
            return $this->redirectToRoute('ficha_artista', ['id' => $artista->getId()]);
        }

        // Renderiza la plantilla unificada para crear/editar
        return $this->render('artista/form.html.twig', [
            'formulario' => $formulario->createView(),
            // No pasamos 'es_edicion', por defecto será false
        ]);
    }

    /**
     * ACCIÓN 3: Editar un Artista existente
     * URL: /artistas/{id}/editar
     */
    #[Route('/{id}/editar', name: 'editar_artista', requirements: ['id' => '\d+'])]
    public function editar(EntityManagerInterface $em, Request $request, int $id): Response
    {
        $artista = $em->getRepository(Artista::class)->find($id);

        if (!$artista) {
            throw $this->createNotFoundException('No se ha encontrado el artista con ID: ' . $id);
        }

        $formulario = $this->createForm(ArtistaType::class, $artista);
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            // Doctrine detecta que el objeto ya existe y hace un UPDATE al hacer flush()
            $em->flush();

            $this->addFlash('success', '¡Artista "' . $artista->getNombre() . '" modificado con éxito!');

            return $this->redirectToRoute('ficha_artista', ['id' => $artista->getId()]);
        }

        // Renderiza la plantilla unificada para crear/editar
        return $this->render('artista/form.html.twig', [
            'formulario' => $formulario->createView(),
            'es_edicion' => true, // Indicador para la plantilla
            'artista_id' => $id, // Para el enlace de cancelación
        ]);
    }

    /**
     * ACCIÓN 4: Eliminar un Artista
     * URL: /artistas/{id}/borrar
     */
    #[Route('/{id}/borrar', name: 'borrar_artista', requirements: ['id' => '\d+'])]
    public function borrar(EntityManagerInterface $em, int $id): Response
    {
        $artista = $em->getRepository(Artista::class)->find($id);

        if (!$artista) {
            throw $this->createNotFoundException('No se ha encontrado el artista con ID: ' . $id);
        }

        $em->remove($artista);
        $em->flush();

        $this->addFlash('warning', 'Artista "' . $artista->getNombre() . '" eliminado correctamente.');

        return $this->redirectToRoute('lista_artistas');
    }

    // --- ACCIONES DE LECTURA (READ) ---

    /**
     * ACCIÓN 1: Mostrar lista de todos los Artistas (READ ALL)
     * URL: /artistas/
     */
    #[Route('/', name: 'lista_artistas')]
    public function lista(EntityManagerInterface $em): Response
    {
        $artistas = $em->getRepository(Artista::class)->findAll();

        return $this->render('artista/lista.html.twig', [
            'artistas' => $artistas,
        ]);
    }

    /**
     * ACCIÓN 5: Mostrar ficha de un Artista (READ ONE)
     * URL: /artistas/{id}
     */
    #[Route('/{id}', name: 'ficha_artista', requirements: ['id' => '\d+'])]
    public function ficha(EntityManagerInterface $em, int $id): Response
    {
        $artista = $em->getRepository(Artista::class)->find($id);

        if (!$artista) {
            throw $this->createNotFoundException('No se ha encontrado el artista con ID: ' . $id);
        }

        return $this->render('artista/ficha.html.twig', [
            'artista' => $artista,
        ]);
    }
}