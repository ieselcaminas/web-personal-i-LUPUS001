<?php

namespace App\Controller;

use App\Entity\Artista;
use App\Form\ArtistaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

#[Route('/artistas')]
class ArtistaController extends AbstractController
{
    // --- ACCIONES DE GESTIÓN (CRUD) ---

    #[Route('/nuevo', name: 'nuevo_artista')]
    // Cambiamos ManagerRegistry $doctrine por EntityManagerInterface $em
    public function nuevo(EntityManagerInterface $em, Request $request): Response
    {
        // 1. Comprobar seguridad
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login'); // O '/index' según tu reto
        }

        $artista = new Artista();
        $formulario = $this->createForm(ArtistaType::class, $artista);
        $formulario->add('save', SubmitType::class, ['label' => 'Insertar Artista']);
        
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            // $artista = $formulario->getData(); // No hace falta, Symfony ya actualiza el objeto $artista
            
            // YA NO NECESITAS: $em = $doctrine->getManager(); 
            // Porque ya tienes $em inyectado directamente
            
            $em->persist($artista);
            $em->flush();
            
            return $this->redirectToRoute('lista_artistas'); // O 'inicio'
        }

        return $this->render('artista/form.html.twig', [
            'formulario' => $formulario->createView(),
        ]);
    }

    /**
     * ACCIÓN 3: Editar (MODIFICADO PARA EL RETO 2)
     * Incluye lógica de botones múltiples (Guardar vs Borrar)
     */
    #[Route('/{id}/editar', name: 'editar_artista', requirements: ['id' => '\d+'])]
    public function editar(EntityManagerInterface $em, Request $request, int $id): Response
    {
        // 1. Seguridad
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login'); 
        }

        $artista = $em->getRepository(Artista::class)->find($id);

        if (!$artista) {
            throw $this->createNotFoundException('No se ha encontrado el artista con ID: ' . $id);
        }

        $formulario = $this->createForm(ArtistaType::class, $artista);

        // 2. AÑADIMOS LOS DOS BOTONES DINÁMICAMENTE
        // Esto es necesario para cumplir el requisito del reto de "Gestionar formulario con varios botones"
        $formulario->add('save', SubmitType::class, [
            'label' => 'Guardar Cambios', 
            'attr' => ['class' => 'btn btn-primary']
        ]);
        $formulario->add('delete', SubmitType::class, [
            'label' => 'Borrar Artista', 
            'attr' => ['class' => 'btn btn-danger', 'onclick' => 'return confirm("¿Seguro que quieres borrar este artista?")']
        ]);

        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            
            // 3. LÓGICA PARA DETECTAR QUÉ BOTÓN SE PULSÓ
            if ($formulario->get('save')->isClicked()) {
                // CASO EDITAR
                $em->flush();
                $this->addFlash('success', '¡Artista modificado con éxito!');
                return $this->redirectToRoute('lista_artistas');

            } elseif ($formulario->get('delete')->isClicked()) {
                // CASO BORRAR
                $em->remove($artista);
                $em->flush();
                $this->addFlash('warning', 'Artista eliminado correctamente.');
                return $this->redirectToRoute('lista_artistas');
            }
        }

        return $this->render('artista/form.html.twig', [
            'formulario' => $formulario->createView(),
            'es_edicion' => true,
            'artista' => $artista
        ]);
    }

    // NOTA: La función 'borrar' independiente ya no es necesaria porque 
    // la hemos integrado dentro de 'editar', tal como pide la lógica de botones múltiples.
    // Puedes borrar el método 'borrar' de abajo o dejarlo comentado.

    // --- ACCIONES DE LECTURA ---

    #[Route('/', name: 'lista_artistas')]
    public function lista(EntityManagerInterface $em): Response
    {
        $artistas = $em->getRepository(Artista::class)->findAll();

        return $this->render('artista/lista.html.twig', [
            'artistas' => $artistas,
        ]);
    }

    #[Route('/{id}', name: 'ficha_artista', requirements: ['id' => '\d+'])]
    public function ficha(EntityManagerInterface $em, int $id): Response
    {
        $artista = $em->getRepository(Artista::class)->find($id);

        if (!$artista) {
            throw $this->createNotFoundException('No se ha encontrado el artista');
        }

        return $this->render('artista/ficha.html.twig', [
            'artista' => $artista,
        ]);
    }
}