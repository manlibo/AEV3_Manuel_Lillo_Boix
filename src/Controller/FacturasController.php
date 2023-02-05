<?php

namespace App\Controller;

use App\Repository\FacturasRepository;
use App\Entity\Facturas;
use App\Entity\Pedidos;

use App\Repository\PedidosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use PhpParser\Node\Expr\Empty_;

#[Route('/facturas')]
class FacturasController extends AbstractController
{
    private FacturasRepository $facturasRepository;
    private PedidosRepository $pedidosRepository;

    public function __construct(FacturasRepository $facturasRepo, PedidosRepository $pedidosRepo)
    {
        $this->pedidosRepository = $pedidosRepo;
        $this->facturasRepository = $facturasRepo;
    }

    #[Route('/', name: 'api_facturas', methods:['GET','POST'])]
    public function index(): JsonResponse
    {
        $facturas = $this->facturasRepository->findAll();
        $data = array();
        foreach ($facturas as $key => $value) {
            $data[$key]=[
                'fecha' => $value->getFecha(),
                'pedido' => [
                    'id' => $value->getIdPedido()->getId(),
                    'observacion' => $value->getIdPedido()->getObservacion(),
                ],
                'tipo' => $value->getTipo(),
                'valor' => $value->getValor(),
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name:'api_facturas_show', methods:['GET'])]
    public function show(int $id): JsonResponse
    {
        $factura = $this->facturasRepository->find($id);
        $data = [
            'factura' => $factura->getId(),
            'fecha' => $factura->getFecha(),
            'id_pedido' => [
                'id' => $factura->getIdPedido()->getId(),
                'observacion' => $factura->getIdPedido()->getObservacion()
            ],
            'tipo' => $factura->getTipo(),
            'valor' => $factura->getValor(),
            ];
            return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/new', name:'api_facturas_new', methods:['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent());
        $pedido = $this->pedidosRepository->find($data->pedido->id);
        $factura = new Facturas();
        $factura->setFecha(new DateTime('@'.strtotime('now')))
                ->setIdPedido($pedido)
                ->setTipo($data->tipo)
                ->setValor($data->valor);
            $this->facturasRepository->save($factura, true);
        return new JsonResponse(['status' => 'Nueva Factura insertada con la id: ' .  $factura->getId()], Response::HTTP_CREATED);

    }

    #[Route('/edit/{id}', name:'api_facturas_edit', methods:['PUT','PATCH'])]
    public function edit(int $id, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent());
        $factura = $this->facturasRepository->find($id);
        if ($_SERVER['REQUEST_METHOD']=='PUT') {
            empty($data->fecha) ? : $factura->setFecha(new DateTime('@'.strtotime('now')));
            empty($data->tipo) ? : $factura->setTipo($data->tipo);
            empty($data->valor) ? : $factura->setValor($data->valor);
            $msg = 'La factura con id: '.$factura->getId() .' se ha actualizado correctamente.';
        } else {
            foreach ($data as $key => $value) {
                switch($key){
                    case 'fecha':
                        $factura->setFecha(new DateTime('@'.strtotime('now')));
                        $msg = 'Se ha actualizado el campo fecha.';
                    break;
                    case 'tipo':
                        $factura->setTipo($data->tipo);
                        $msg =  'Se ha actualizado el campo tipo.';
                    break;
                    case 'valor':
                        $factura->setValor($data->valor);
                        $msg = 'Se ha actualizado el campo valor';
                    break;
                }
            }
        }
        $this->facturasRepository->save($factura,true);
        return new JsonResponse(['status'=>$msg], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name:'api_facturas_delete', methods:['DELETE'])]
    public function remove(int $id):JsonResponse
    {
        $factura = $this->facturasRepository->find($id);
        $this->facturasRepository->remove($factura,true);
        return new JsonResponse(['status'=> 'Factura: ' . $id . ' borrada correctamente', Response::HTTP_OK]);
    }



}
