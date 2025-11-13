<?php

namespace App\Controller;

use App\Entity\PiezaDeArte;
use App\Form\PiezaDeArteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//Ctrl + Shift + P --> escribir = index workspace (esto elimina el error de VS y hace que ya no nos haga falta crear variables extras para que no de error)
class PiezaDeArteController extends AbstractController
{
    /**
     * ACCIÓN 2: Crear nueva pieza
     */
    #[Route('/nuevo', name: 'nueva_pieza')]
    public function nuevo(EntityManagerInterface $em, Request $request): Response
    {

        $pieza = new PiezaDeArte();

        $formulario = $this->createForm(PiezaDeArteType::class, $pieza);
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $em->persist($pieza);
            $em->flush();

            return $this->redirectToRoute('ficha_pieza', ['id' => $pieza->getId()]);
        }

        return $this->render('pieza/form.html.twig', [ //Para no tener 3 archivos casi identicos, los metemos todos en uno solo 
            'formulario' => $formulario->createView(),
        ]);
    }

    /**
     * ACCIÓN 3: Mostrar ficha de una pieza
     */
    #[Route('/{id}', name: 'ficha_pieza', requirements: ['id' => '\d+'])]
    public function ficha(EntityManagerInterface $em, int $id): Response
    {
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
        // 1. Buscar la pieza de arte existente por su ID
        $pieza = $em->getRepository(PiezaDeArte::class)->find($id);

        // Si la pieza no existe, redirigir o mostrar un error 404
        if (!$pieza) {
            throw $this->createNotFoundException('No se ha encontrado la pieza con ID: ' . $id);
        }

        // 2. Crear el formulario, precargado con los datos de $pieza
        $formulario = $this->createForm(PiezaDeArteType::class, $pieza);

        // 3. Manejar la solicitud y la validación
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $em->flush();
            
            // Mensaje Flash de éxito
            $this->addFlash(
                'success',
                '¡La pieza de arte "' . $pieza->getTitulo() . '" ha sido modificada con éxito!'
            );
            
            // Redirigir a la ficha de la pieza editada
            return $this->redirectToRoute('ficha_pieza', ['id' => $pieza->getId()]);
        }

        // 4. Renderizar la plantilla con el formulario cargado
        return $this->render('pieza/form.html.twig', [ //al igual que en nuevo, lo ponemos en form para unificarlo todo
            'formulario' => $formulario->createView(),
            'es_edicion' => true, // Indicador para cambiar el título en la plantilla
            'pieza_id' => $id,
        ]);
    }

    /**
     * BORRAR: Eliminar una pieza
     */
    #[Route('/borrar/{id}', name: 'borrar_pieza', requirements: ['id' => '\d+'])]
    public function borrar(EntityManagerInterface $em, int $id): Response
    {
        $pieza = $em->getRepository(PiezaDeArte::class)->find($id);

        if (!$pieza) {
            throw $this->createNotFoundException('No se ha encontrado la pieza con ID: ' . $id);
        }

        $em->remove($pieza);
        $em->flush();

        $this->addFlash('success', 'La pieza ha sido eliminada correctamente.');

        return $this->redirectToRoute('lista_piezas');
    }

    /**
     * ACCIÓN 1: Mostrar lista de todas las piezas
     */
    #[Route('/', name: 'lista_piezas')]
    public function lista(EntityManagerInterface $em): Response
    {
        $piezas = $em->getRepository(PiezaDeArte::class)->findAll();

        return $this->render('pieza/lista.html.twig', [
            'piezas' => $piezas,
        ]);
    }
}
