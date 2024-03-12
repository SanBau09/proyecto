<?php

namespace App\Controller;


use App\Entity\Cliente;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ClienteType;
use Symfony\Component\HttpFoundation\Request;

class ClienteController extends AbstractController{
    
    #[Route('/cliente', name: 'verTodosClientes')]
    public function index(EntityManagerInterface $entityManager): Response{

        // Consultar todos los clientes
        $clientes = $entityManager->getRepository(Cliente::class)->findAll();

        // Renderizar la plantilla Twig con los datos de los clientes
        return $this->render('cliente/verTodosClientes.html.twig', [
            'clientes' => $clientes,
        ]);
    }

    #[Route('/crearCliente', name: 'crearCliente')]
    public function addCliente(EntityManagerInterface $entityManager, Request $request): Response{

        // Esta acción solo está disponible para usuarios autenticados
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $cliente = new Cliente();

        $formularioCliente = $this->createForm(ClienteType::class, $cliente);
        $formularioCliente->handleRequest($request);

        if($formularioCliente->isSubmitted() && $formularioCliente->isValid()){
            $cliente = $formularioCliente->getData();

            $entityManager->persist($cliente);
            $entityManager->flush();

            // Agregar el mensaje flash
            $this->addFlash('success', 'El cliente ha sido agregado correctamente.');

            return $this->redirectToRoute('verTodosClientes');
        }

        return $this->render('cliente/addCliente.html.twig', ['formularioCliente'=>$formularioCliente]);
    }

    #[Route('/detallesCliente/{id}', name: 'detallesCliente')]
    public function verCliente(Cliente $cliente): Response{

        // Esta acción solo está disponible para usuarios autenticados
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('cliente/detallesCliente.html.twig', [
            'cliente' => $cliente
        ]);
    }

    #[Route('/eliminarCliente/{id}', name: 'eliminarCliente')]
    public function eliminarCliente(Cliente $cliente, EntityManagerInterface $entityManager): Response{

        // Esta acción solo está disponible para usuarios autenticados
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $entityManager->remove($cliente);
        $entityManager->flush();

        // Agregar el mensaje flash
        $this->addFlash('success', 'El cliente ha sido eliminado correctamente.');

        return $this->redirectToRoute('verTodosClientes');
    }
    
}
