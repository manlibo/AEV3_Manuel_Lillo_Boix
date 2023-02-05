<?php

namespace App\Controller;

use App\Repository\StockRepository;
use App\Repository\ProductosRepository;
use App\Repository\AlmacenesRepository;
use App\Entity\Stock;
use App\Entity\Productos;
use App\Entity\Almacenes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use DateTime;


#[Route('/stock')]
class StockController extends AbstractController
{
    private StockRepository $stockRepository;
    private ProductosRepository $productosRepository;
    private AlmacenesRepository $almacenesRepository;

    public function __construct(StockRepository $StockRepo, ProductosRepository $productosRepo, AlmacenesRepository $almacenesRepo)
    {
        $this->stockRepository = $StockRepo;
        $this->productosRepository = $productosRepo;
        $this->almacenesRepository = $almacenesRepo;
    }

    #[Route('/', name: 'api_stock', methods:['GET','POST'])]
    public function index(): JsonResponse
    {
        $stock = $this->stockRepository->findAll();
        $data = array();
        foreach ($stock as $key => $value) {
            $data[$key] = [
                'Fecha' => $value->getFecha(),
                'PRODUCTO' => [
                    'id' => $value->getIdProducto()->getId(),
                    'Descripción' => $value->getIdProducto()->getDescripcion()
                ],
                'Cantidad' => $value->getCantidad(),
                'Stock' => $value->getStock(),
                'Precio' => $value->getPrecio(),
                'Unidad' => $value->getUnidad(),
                'ALMACEN' => [
                    'id' => $value->getIdAlmacen()->getId(),
                    'Descripción' => $value->getIdAlmacen()->getDescripcion()
                ]

            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

 #[Route('/{id}', name:'api_stock_show', methods:['GET'])]
 public function show(int $id):JsonResponse
 {
     $stock = $this->stockRepository->find($id);
     $data = [
         'fecha' => $stock->getFecha(),
         'PRODUCTO' => [
            'id' => $stock->getIdProducto()->getId(),
            'Descripción' => $stock->getIdProducto()->getDescripcion()
        ],
        'Cantidad' => $stock->getCantidad(),
        'Stock' => $stock->getStock(),
        'Precio' => $stock->getPrecio(),
        'Unidad' => $stock->getUnidad(),
        'ALMACEN' => [
            'id' => $stock->getIdAlmacen()->getId(),
            'Descripción' => $stock->getIdAlmacen()->getDescripcion()
        ]

    ];
     return new JsonResponse($data, Response::HTTP_OK);
 }

     #[Route('/new', name:'api_stocks_new', methods:['POST'])]
     public function add(Request $request): JsonResponse
     {
         $data = json_decode($request->getContent());
         $almacen = $this->almacenesRepository->find($data->almacen->id);
         $producto = $this->productosRepository->find($data->producto->id);
         $stock = new Stock();
         $stock->setFecha(new DateTime('@'.strtotime('now')))
                 ->setIdProducto($producto)
                 ->setCantidad($data->cantidad)
                 ->setStock($data->stock)
                 ->setPrecio($data->precio)
                 ->setUnidad($data->unidad)
                 ->setIdAlmacen($almacen);
             $this->stockRepository->save($stock, true);
         return new JsonResponse(['status' => 'Nuevo stock insertado con la id: ' .  $stock->getId()], Response::HTTP_CREATED);
 
     }   

      #[Route('/edit/{id}', name:'api_stocks_edit', methods:['PUT','PATCH'])]
    public function edit(int $id, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent());
        $stock = $this->stockRepository->find($id);
        if ($_SERVER['REQUEST_METHOD']=='PUT') {
            empty($data->fecha) ? : $stock->setFecha(new DateTime('@'.strtotime('now')));
            empty($data->cantidad) ? : $stock->setCantidad($data->cantidad);
            empty($data->stock) ? : $stock->setStock($data->stock);
            empty($data->precio) ? : $stock->setPrecio($data->precio);
            empty($data->unidad) ? : $stock->setUnidad($data->unidad);
            $msg = 'El Stock con id: '. $stock->getId() .' se ha actualizado correctamente.';
        } else {
            foreach ($data as $key => $value) {
                switch ($key) {
                    case 'fecha':
                        $stock->setFecha(new DateTime('@'.strtotime('now')));
                        $msg = 'Se ha actualizado el campo fecha.';
                        break;
                    case 'cantidad':
                        $stock->setCantidad($data->cantidad);
                        $msg =  'Se ha actualizado el campo cantidad.';
                    break;
                    case 'stock':
                        $stock->setCantidad($data->stock);
                        $msg =  'Se ha actualizado el campo stock.';
                    break;
                    case 'precio':
                        $stock->setPrecio($data->precio);
                        $msg = 'Se ha actualizado el campo precio';
                    break;
                    case 'unidad':
                        $stock->setUnidad($data->unidad);
                        $msg =  'Se ha actualizado el campo unidad.';
                    break;
                }
            }
        }
        $this->stockRepository->save($stock, true);
        return new JsonResponse(['status'=>$msg], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name:'api_stock_delete', methods:['DELETE'])]
        public function remove(int $id):JsonResponse
        {
            $stock = $this->stockRepository->find($id);
            $this->stockRepository->remove($stock, true);
            return new JsonResponse(['status'=> 'Stock: ' . $id . ' borrado correctamente', Response::HTTP_OK]);
        }




}
