<?php

namespace App\Controller;

use App\Repository\LineaspedidosRepository;
use App\Entity\Lineaspedidos;
use App\Entity\Pedidos;
use App\Entity\Productos;
use App\Repository\PedidosRepository;
use App\Repository\ProductosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/lineaspedidos')]
class LineaspedidosController extends AbstractController
{
    private LineaspedidosRepository $lineaspedidosRepository;
    private PedidosRepository $pedidosRepository;
    private ProductosRepository $productosRepository;

    public function __construct(LineaspedidosRepository $lineaspedidosRepo, PedidosRepository $pedidosRepo, ProductosRepository $productosRepo)
    {
        $this->lineaspedidosRepository = $lineaspedidosRepo;
        $this->pedidosRepository = $pedidosRepo;
        $this->productosRepository = $productosRepo;
    }

    #[Route('/', name: 'api_lineaspedidos', methods:['GET','POST'])]
    public function index(): JsonResponse
    {
        $lineaspedidos = $this->lineaspedidosRepository->findAll();
        $data = array();
        foreach ($lineaspedidos as $key => $value) {
            $data[$key]=[
                'PEDIDO' => [
                    'id' => $value->getIdPedido()->getId(),
                    'Observación' => $value->getIdPedido()->getObservacion(),
                ],
                'PRODUCTOS' => [
                    'id' => $value->getIdProducto()->getId(),
                    'descripcion' => $value->getIdProducto()->getDescripcion(),
                ],
                'Cantidad' => $value->getCantidad(),
                'Precio' => $value->getPrecio(),
                
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name:'api_lineaspedidos_show', methods:['GET'])]
    public function show(int $id):JsonResponse
    {
        $lineaspedido = $this->lineaspedidosRepository->find($id);
        $data = [
            'PEDIDO' => [
                'id' => $lineaspedido->getIdPedido()->getId(),
                'Observación' => $lineaspedido->getIdPedido()->getObservacion(),
            ],
            'PRODUCTOS' => [
                'id' => $lineaspedido->getIdProducto()->getId(),
                'descripcion' => $lineaspedido->getIdProducto()->getDescripcion(),
            ],
            'Cantidad' => $lineaspedido->getCantidad(),
            'Precio' => $lineaspedido->getPrecio(),
        ];
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/new', name:'api_lineaspedidos_new', methods:['POST'])]
     public function add(Request $request): JsonResponse
     {
         $data = json_decode($request->getContent());
         $pedido = $this->pedidosRepository->find($data->pedido->id);
         $producto = $this->productosRepository->find($data->producto->id);
         $lineaspedido = new Lineaspedidos();
         $lineaspedido->setIdPedido($pedido)
                 ->setIdProducto($producto)
                 ->setCantidad($data->cantidad)
                 ->setPrecio($data->precio);
         $this->lineaspedidosRepository->save($lineaspedido, true);
         return new JsonResponse(['status' => 'Nueva Lineaspedido insertada con la id: ' .  $lineaspedido->getId()], Response::HTTP_CREATED);
     }

    #[Route('/edit/{id}', name:'api_lineaspedidos_edit', methods:['PUT','PATCH'])]
    public function edit(int $id, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent());
        $lineaspedido = $this->lineaspedidosRepository->find($id);
        if ($_SERVER['REQUEST_METHOD']=='PUT') {
            empty($data->cantidad) ? : $lineaspedido->setCantidad($data->cantidad);
            empty($data->precio) ? : $lineaspedido->setPrecio($data->precio);
            $msg = 'La Lineaspedido con id: '. $lineaspedido->getId() .' se ha actualizado correctamente.';
        } else {
            foreach ($data as $key => $value) {
                switch ($key) {
                    case 'cantidad':
                        $lineaspedido->setCantidad($data->cantidad);
                        $msg =  'Se ha actualizado el campo cantidad.';
                    break;
                    case 'precio':
                        $lineaspedido->setPrecio($data->precio);
                        $msg = 'Se ha actualizado el campo precio';
                    break;
                }
            }
        }
        $this->lineaspedidosRepository->save($lineaspedido, true);
        return new JsonResponse(['status'=>$msg], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name:'api_lineaspedido_delete', methods:['DELETE'])]
        public function remove(int $id):JsonResponse
        {
            $lineaspedido = $this->lineaspedidosRepository->find($id);
            $this->lineaspedidosRepository->remove($lineaspedido, true);
            return new JsonResponse(['status'=> 'Lineaspedido: ' . $id . ' borrada correctamente', Response::HTTP_OK]);
        }
}
