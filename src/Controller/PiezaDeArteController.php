<?php

namespace App\Controller;

use App\Entity\PiezaDeArte;
use App\Form\PiezaDeArteType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Prefijo de ruta para todas las acciones de este controlador
 */
#[Route('/piezas')]
class PiezaDeArteController extends AbstractController
{
    /**
     * ACCIÓN 1: Mostrar una lista de todas las piezas
     * Esta será como tu página de "índice" de la galería
     */
    #[Route('', name: 'lista_piezas')] // Ruta: /piezas
    public function lista(ManagerRegistry $doctrine): Response
    {
        // 1. Pedir al repositorio todas las piezas
        $repositorio = $doctrine->getRepository(PiezaDeArte::class);
        $piezas = $repositorio->findAll();

        // 2. Renderizar la plantilla con las piezas
        return $this->render('pieza/lista.html.twig', [
            'piezas' => $piezas,
        ]);
    }

    /**
     * ACCIÓN 2: Mostrar el formulario para crear una NUEVA pieza
     */
    #[Route('/nuevo', name: 'nueva_pieza')] // Ruta: /pieza/nuevo
    public function nuevo(ManagerRegistry $doctrine, Request $request): Response 
    {
        $pieza = new PiezaDeArte();
        
        // 1. Crear el formulario (basado en PiezaDeArteType)
        $formulario = $this->createForm(PiezaDeArteType::class, $pieza); 
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            // 2. Si es válido, guardar en la BBDD
            $entityManager = $doctrine->getManager();
            $entityManager->persist($pieza);
            $entityManager->flush();
            
            // 3. Redirigir a la ficha de la pieza recién creada
            return $this->redirectToRoute('ficha_pieza', ["id" => $pieza->getId()]);
        }
        
        // 4. Si no se ha enviado (o no es válido), mostrar el formulario
        return $this->render('pieza/nuevo.html.twig', [
            'formulario' => $formulario->createView()
        ]);
    }

    /**
     * ACCIÓN 3: Mostrar la "Ficha" de UNA pieza de arte
     * (Esta ruta debe ir al final para que no choque con '/nuevo' o '/lista')
     */
    #[Route('/{id}', name: 'ficha_pieza', requirements: ['id' => '\d+'])] // Ruta: /pieza/1, /pieza/2, etc.
    public function ficha(ManagerRegistry $doctrine, int $id): Response
    {
        // 1. Buscar la pieza por su ID
        $repositorio = $doctrine->getRepository(PiezaDeArte::class);
        $pieza = $repositorio->find($id);

        // 2. Comprobar si existe
        if (!$pieza) {
            // Lanzar un error 404 si no se encuentra
            throw $this->createNotFoundException('No se ha encontrado la pieza con ID: ' . $id);
        }

        // 3. Renderizar la plantilla
        return $this->render('pieza/ficha.html.twig', [
            'pieza' => $pieza
        ]);
    }
}