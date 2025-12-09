<?php

namespace App\Controller;

use App\Entity\PiezaDeArte;
use App\Form\PiezaDeArteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

// Añado un prefijo de ruta para que no se mezcle con la home o artistas
class PiezaDeArteController extends AbstractController
{
    /**
     * ACCIÓN 1: Lista de piezas (Portada de esta sección)
     */
    #[Route('/', name: 'lista_piezas')]
    public function lista(EntityManagerInterface $em): Response
    {
        $piezas = $em->getRepository(PiezaDeArte::class)->findAll();

        return $this->render('pieza/lista.html.twig', [
            'piezas' => $piezas,
        ]);
    }

    /**
     * ACCIÓN 2: Crear nueva pieza (Con seguridad y botón manual)
     */
    #[Route('/nuevo', name: 'nueva_pieza')]
    public function nuevo(EntityManagerInterface $em, Request $request): Response
    {
        // 1. Seguridad (Requisito del Reto)
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $pieza = new PiezaDeArte();
        $formulario = $this->createForm(PiezaDeArteType::class, $pieza);
        
        // Botón manual
        $formulario->add('save', SubmitType::class, ['label' => 'Insertar Pieza']);

        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $em->persist($pieza);
            $em->flush();

            $this->addFlash('success', 'Pieza creada correctamente');
            return $this->redirectToRoute('lista_piezas');
        }

        return $this->render('pieza/form.html.twig', [
            'formulario' => $formulario->createView(),
            'es_edicion' => false
        ]);
    }

    /**
     * ACCIÓN 3: Editar y Borrar (El núcleo del Reto 2)
     */
    #[Route('/editar/{id}', name: 'editar_pieza', requirements: ['id' => '\d+'])]
    public function editar(EntityManagerInterface $em, Request $request, int $id): Response
    {
        // 1. Seguridad
        if (!$this->getUser()) {
            return $this->redirect('/index');
        }

        $pieza = $em->getRepository(PiezaDeArte::class)->find($id);

        if (!$pieza) {
            throw $this->createNotFoundException('No se ha encontrado la pieza con ID: ' . $id);
        }

        $formulario = $this->createForm(PiezaDeArteType::class, $pieza);

        // 2. Botones Dinámicos (Guardar y Borrar)
        $formulario->add('save', SubmitType::class, [
            'label' => 'Guardar Cambios', 
            'attr' => ['class' => 'btn btn-primary']
        ]);
        
        $formulario->add('delete', SubmitType::class, [
            'label' => 'Borrar Pieza', 
            'attr' => ['class' => 'btn btn-danger', 'onclick' => 'return confirm("¿Seguro que quieres eliminar esta obra?")']
        ]);

        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            
            // 3. Lógica de isClicked()
            if ($formulario->get('save')->isClicked()) {
                // GUARDAR
                $em->flush();
                $this->addFlash('success', 'Pieza modificada con éxito');
                return $this->redirectToRoute('lista_piezas');

            } elseif ($formulario->get('delete')->isClicked()) {
                // BORRAR
                $em->remove($pieza);
                $em->flush();
                $this->addFlash('warning', 'Pieza eliminada correctamente');
                return $this->redirectToRoute('lista_piezas');
            }
        }

        return $this->render('pieza/form.html.twig', [
            'formulario' => $formulario->createView(),
            'es_edicion' => true,
            'pieza' => $pieza, // Pasamos el objeto para evitar el error de variable inexistente
        ]);
    }

    /**
     * ACCIÓN 4: Ficha individual 
     */
    #[Route('/{id}', name: 'ficha_pieza', requirements: ['id' => '\d+'])]
    public function ficha(EntityManagerInterface $em, int $id): Response
    {
        $pieza = $em->getRepository(PiezaDeArte::class)->find($id);

        if (!$pieza) {
            throw $this->createNotFoundException('No encontrada');
        }

        return $this->render('pieza/ficha.html.twig', [
            'pieza' => $pieza,
        ]);
    }
}