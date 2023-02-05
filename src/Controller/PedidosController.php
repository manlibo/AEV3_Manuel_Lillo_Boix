<?php

namespace App\Controller;

use App\Repository\EmpresasRepository;
use App\Repository\PedidosRepository;
use App\Entity\Pedidos;
use App\Entity\Empresas;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/pedidos')]
class PedidosController extends AbstractController
{
    private PedidosRepository $pedidosRepository;
    private EmpresasRepository $empresasRepository;

    public function __construct(PedidosRepository $pedidosRepo, EmpresasRepository $empresasRepo)
    {
        $this->pedidosRepository = $pedidosRepo;
        $this->empresasRepository = $empresasRepo;
    }

    #[Route('/', name: 'api_pedidos', methods:['GET','POST'])]
    public function index(): JsonResponse
    {
        $pedidos = $this->pedidosRepository->findAll();
        $data = array();
        /* En el array detail iremos añadiendo los datos de las facturas asociadas */
        $detail = array();
        $detail2 = array();
        /* Este foreach recorre todos los pedidos */
        foreach ($pedidos as $key => $value) {
            /* Creamos una variable con el objeto Facturas para recorrer las facturas asociadas al pedido */
            $facturas = $value->getFacturas();
            foreach ($facturas as $key2 => $value2) {
                $detail[$key2] = [
                    'ID_Factura' => $value2->getId(),
                    'Tipo' => $value2->getTipo(),
                ];
                $lineaspedidos = $value->getLineaspedidos();
                foreach ($lineaspedidos as $key3 => $value3) {
                    $detail2[$key3] = [
                    'ID_Lineapedido' => $value3->getId(),
                    'Precio' => $value3->getPrecio(),
                ];
                }
            }
            /* En este array añadimos toda la información que hemos ido recorriendo por los arrays */
            $data[$key]=[

                'PEDIDO' => $value->getId(),
                'tipo' => $value->getTipo(),
                'fecha' => $value->getFecha(),
                'observacion' => $value->getObservacion(),
                'EMPRESA' => [
                    'id' => $value->getIdEmpresa()->getId(),
                    'Nombre' => $value->getIdEmpresa()->getNombre(),
                ],
                'Facturas' => [$detail],
                'Lineaspedidos' => [$detail2]
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }


    #[Route('/{id}', name: 'api_pedidos_show', methods:['GET'])]
    public function show(int $id): JsonResponse
    {
        $pedido = $this->pedidosRepository->find($id);
        $facturas = array();
        $detail = $pedido->getFacturas();
        foreach ($detail as $key2 => $value2) {
            $facturas[$key2] =[
                'FACTURA' => $value2->getId(),
                'Tipo' => $value2->getTipo()

            ];
        $lineaspedidos = array();
        $detail2 = $pedido->getLineaspedidos();
        foreach ($detail2 as $key => $value) {
            $lineaspedidos[$key] = [
                'ID_Lineapedido' => $value->getId(),
                    'Precio' => $value->getPrecio(),
            ];
        }
        }
        $data = [
            'PEDIDO' => $pedido->getId(),
            'Tipo' => $pedido->getTipo(),
            'Fecha' => $pedido->getFecha(),
            'Observación' => $pedido->getObservacion(),
            'EMPRESA' => $pedido->getIdEmpresa()->getNombre(),
            'FACTURAS' => [$facturas],
            'Lineaspedidos' => [$lineaspedidos]
        ];
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/new', name:'api_pedidos_new', methods:['POST'])]
    public function add(Request $request):JsonResponse
    {
        $data = json_decode($request->getContent());
        $empresa = $this->empresasRepository->find($data->empresa->id);
        $pedido = new Pedidos();
        $pedido->setTipo($data->tipo)
                ->setFecha(new DateTime('@'.strtotime('now')))
                ->setObservacion($data->observacion)
                ->setIdEmpresa($empresa);
        $this->pedidosRepository->save($pedido, true);
        return new JsonResponse(['status' => 'Nuevo Pedido insertado con la id: ' . $pedido->getId()], Response::HTTP_CREATED);
    }
    
    #[Route('/edit/{id}', name:'api_pedidos_edit', methods:['PUT','PATCH'])]
    public function edit(int $id, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent());
        $pedido = $this->pedidosRepository->find($id);
        if ($_SERVER['REQUEST_METHOD']=='PUT') {
            empty($data->tipo) ? : $pedido->setTipo($data->tipo);
            empty($data->fecha) ? : $pedido->setFecha(new DateTime('@'.strtotime('now')));
            empty($data->observacion) ? : $pedido->setObservacion($data->observacion);
            $msg = 'El pedido con id: ' . $pedido->getId() . ' se ha actualizado correctamente.';
        } else {
            foreach ($data as $key => $value) {
                switch ($key) {
                    case 'tipo':
                        $pedido->setTipo($data->tipo);
                        $msg = 'Se ha actualizado el campo tipo.';
                        break;
                    case 'fecha':
                        $pedido->setFecha(new DateTime('@'.strtotime('now')));
                        $msg = 'Se ha actualizado el campo fecha.';
                        break;
                    case 'observacion':
                        $pedido->setObservacion($data->observacion);
                        $msg = 'Se ha actualizado el campo observacion';
                        break;
                }
            }
        }
        $this->pedidosRepository->save($pedido, true);
        return new JsonResponse(['status'=>$msg], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name:'api_pedidos_delete', methods:['DELETE'])]
    public function remove(int $id):JsonResponse
    {
        $pedido = $this->pedidosRepository->find($id);
        $this->pedidosRepository->remove($pedido, true);
        return new JsonResponse(['status'=> 'Pedido: ' . $id . ' eliminado correctamente.', Response::HTTP_OK]);
    }
}
