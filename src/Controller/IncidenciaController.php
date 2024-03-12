<?php

namespace App\Controller;

use App\Entity\Incidencia;
use App\Entity\Cliente;
use App\Entity\User;
use App\Form\EditarIncidenciaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\IncidenciaType;
use App\Form\IncidenciaClientesType;
use DateTime;

class IncidenciaController extends AbstractController{

    #[Route('/verTodasIncidencias', name: 'verTodasIncidencias')]
    public function verTodasIncidencias(EntityManagerInterface $entityManager): Response{

        // Esta acción solo está disponible para usuarios autenticados
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Consultar todas las incidencias
        $incidencias = $entityManager->getRepository(Incidencia::class)->findBy(array(), array('fechaCreacion' => 'DESC'));

        // Renderizar la plantilla Twig con los datos de los clientes
        return $this->render('incidencia/verTodasIncidencias.html.twig', [
            'incidencias' => $incidencias,
        ]);
    }
    
    #[Route('/crearIncidencia/{idCliente}', name: 'crearIncidencia')]
    public function addIncidencia(EntityManagerInterface $entityManager, Request $request, int $idCliente): Response{

        // Esta acción solo está disponible para usuarios autenticados
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $incidencia = new Incidencia();

        // Establecer la fecha de creación como la fecha y hora actual
        $incidencia->setFechaCreacion(new DateTime());

        $formularioIncidencia = $this->createForm(IncidenciaType::class, $incidencia);
        $formularioIncidencia->handleRequest($request);

        if($formularioIncidencia->isSubmitted() && $formularioIncidencia->isValid()){
            $incidencia = $formularioIncidencia->getData();

            //obtenemos el cliente correspondiente al ID proporcionado
            $cliente = $entityManager->getRepository(Cliente::class)->find($idCliente);
            $incidencia->setIdCliente($cliente);

            $user = $this->getUser();
            if ($user instanceof \App\Entity\User) {
                $userId = $user->getId();

                 // Obtener el usuario asociado al ID
                $user = $entityManager->getRepository(User::class)->find($userId);

                // Asignar el usuario a la incidencia
                $incidencia->setIdUser($user);

                $entityManager->persist($incidencia);
                $entityManager->flush();

                return $this->redirectToRoute('detallesCliente',['id' => $idCliente]);
                } else {
                    // Manejar el caso en el que $user no es una instancia de User
                }
          
        }

        return $this->render('incidencia/addIncidencia.html.twig', ['formularioIncidencia'=>$formularioIncidencia]);
    }

    #[Route('/crearIncidenciaSelect', name: 'crearIncidenciaSelect')]
    public function addIncidenciaCliente(EntityManagerInterface $entityManager, Request $request): Response{

        // Esta acción solo está disponible para usuarios autenticados
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $incidencia = new Incidencia();

        // Establecer la fecha de creación como la fecha y hora actual
        $incidencia->setFechaCreacion(new DateTime());

        // Consultar todos los clientes
        $clientes = $entityManager->getRepository(Cliente::class)->findAll(); 

        $choices = [];

        // Se añade cada cliente a la lista de selecciones
        for ($i = 0; $i < count($clientes); $i++) {
            $choices += [$clientes[$i]->getNombre() => $clientes[$i]];
        }


        $formularioIncidencia = $this->createForm(IncidenciaClientesType::class, $incidencia, ['clientes' => $choices]);
        $formularioIncidencia->handleRequest($request);

        if($formularioIncidencia->isSubmitted() && $formularioIncidencia->isValid()){
            $incidencia = $formularioIncidencia->getData();

            $user = $this->getUser();
            if ($user instanceof \App\Entity\User) {
                $userId = $user->getId();

                 // Obtener el usuario asociado al ID
                $user = $entityManager->getRepository(User::class)->find($userId);

                // Asignar el usuario a la incidencia
                $incidencia->setIdUser($user);

                $entityManager->persist($incidencia);
                $entityManager->flush();

                // Agregar el mensaje flash
                $this->addFlash('success', 'La incidencia se ha sido creado correctamente.');

                return $this->redirectToRoute('verTodasIncidencias');
            } else {
                // Manejar el caso en el que $user no es una instancia de User
            }
          
        }

        return $this->render('incidencia/addIncidencia.html.twig', ['formularioIncidencia'=>$formularioIncidencia]);
    }

    #[Route('/detallesIncidencia/{id}', name: 'detallesIncidencia')]
    public function verIncidencia(Incidencia $incidencia): Response{

        // Esta acción solo está disponible para usuarios autenticados
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('incidencia/detallesIncidencia.html.twig', [
            'incidencia' => $incidencia
        ]);
    }

    #[Route('/eliminarIncidencia/{id}', name: 'eliminarIncidencia')]
    public function eliminarIncidencia(Incidencia $incidencia, EntityManagerInterface $entityManager): Response{

        // Esta acción solo está disponible para usuarios autenticados
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $entityManager->remove($incidencia);
        $entityManager->flush();

        // Agregar el mensaje flash
        $this->addFlash('success', 'La incidencia ha sido eliminada correctamente.');

        return $this->redirectToRoute('detallesCliente',['id' => $incidencia->getIdCliente()->getId()]);
    }

    #[Route('/editarIncidencia/{id}', name: 'editarIncidencia')]
    public function editarIncidencia(Incidencia $incidencia, EntityManagerInterface $entityManager, Request $request): Response{     
        
        // Esta acción solo está disponible para usuarios autenticados
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $formularioIncidencia = $this->createForm(EditarIncidenciaType::class, $incidencia);
        $formularioIncidencia->handleRequest($request);

        if($formularioIncidencia->isSubmitted() && $formularioIncidencia->isValid()){
            $incidencia = $formularioIncidencia->getData();
            $entityManager->persist($incidencia);
            $entityManager->flush();
            return $this->redirectToRoute('detallesCliente',['id' => $incidencia->getIdCliente()->getId()]);  
        }
        
        return $this->render('incidencia/editarIncidencia.html.twig', ['formularioIncidencia'=>$formularioIncidencia]);
    }
}
