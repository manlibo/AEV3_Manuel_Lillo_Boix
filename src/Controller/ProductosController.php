<?php

namespace App\Controller;

use App\Repository\ProductosRepository;
use App\Repository\AlmacenesRepository;
use App\Entity\Productos;
use App\Entity\Pedidos;
use App\Entity\Almacenes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/productos')]
class ProductosController extends AbstractController
{
    private ProductosRepository $productosRepository;
    private AlmacenesRepository $almacenesRepository;

    public function __construct(ProductosRepository $productosRepo, AlmacenesRepository $almacenesRepo)
    {
        $this->productosRepository = $productosRepo;
        $this->almacenesRepository = $almacenesRepo;
    }

    #[Route('/', name: 'api_productos', methods:['GET','POST'])]
    public function index(): JsonResponse
    {
        $productos = $this->productosRepository->findAll();
        $data = array();
        $detail = array();
        $detail2 = array();
        foreach ($productos as $key => $value) {
            $stocks = $value->getStocks();
            foreach ($stocks as $key2 => $value2) {
                $detail[$key2] = [
                    'ID_Stock' => $value2->getId(),
                    'Cantidad' => $value2->getCantidad()
                ];
                $lineaspedidos = $value->getLineaspedidos();
                foreach ($lineaspedidos as $key3 => $value3) {
                    $detail2[$key3] = [
                    'ID_Lineaspedido' => $value3->getId(),
                    'Precio' => $value3->getPrecio()
                ];
                }
            }
            $data[$key]=[
                'PRODUCTO' => $value->getDescripcion(),
                'unidad' => $value->getUnidad(),
                'clasificacion' => $value->getClasificacion(),
                'preciounidad' => $value->getPreciounidad(),
                'STOCK' => [$detail],
                'LINEASPEDIDO' => [$detail2]
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }


    #[Route('/{id}', name: 'api_productos_show', methods:['GET'])]
        public function show(int $id): JsonResponse
        {
            $producto = $this->productosRepository->find($id);
            $lineaspedidos = array();
            $detail = $producto->getLineaspedidos();
            foreach ($detail as $key => $value) {
                $lineaspedidos[$key] = [
                    'ID_Lineaspedido' => $value->getId(),
                    'Precio' => $value->getPrecio()
                ];
            }
            $stocks = array();
            $detail2 = $producto->getStocks();
            foreach ($detail2 as $key2 => $value2) {
                $stocks[$key2] = [
                    'ID_Stock' => $value2->getId(),
                    'Cantidad' => $value2->getCantidad()
                ];
            }
            $data = [
                'descripcion' => $producto->getDescripcion(),
                'unidad' => $producto->getUnidad(),
                'clasificacion' => $producto->getClasificacion(),
                'preciounidad' => $producto->getPreciounidad(),
                'STOCK' => [$stocks],
                'LINEASPEDIDO' => [$lineaspedidos]
            ];
            return new JsonResponse($data, Response::HTTP_OK);
        }

    #[Route('/new', name:'api_productos_new', methods:['POST'])]
    public function add(Request $request):JsonResponse
    {
        $data = json_decode($request->getContent());
        $almacen = $this->almacenesRepository->find($data->almacen->id);
        $producto = new Productos();
        $producto->setDescripcion($data->descripcion)
                ->setIdAlmacen($almacen)
                ->setUnidad($data->unidad)
                ->setClasificacion($data->clasificacion)
                ->setPreciounidad($data->preciounidad);
        $this->productosRepository->save($producto, true);
        return new JsonResponse(['status' => 'Nuevo Producto insertado con la id: ' . $producto->getId()], Response::HTTP_CREATED);
    }

    #[Route('/edit/{id}', name:'api_productos_edit', methods:['PUT','PATCH'])]
  public function edit(int $id, Request $request):JsonResponse
  {
      $data = json_decode($request->getContent());
      $producto = $this->productosRepository->find($id);
      if ($_SERVER['REQUEST_METHOD']=='PUT') {
          empty($data->descripcion) ? : $producto->setDescripcion($data->descripcion);
          empty($data->unidad) ? : $producto->setUnidad($data->unidad);
          empty($data->clasificacion) ? : $producto->setClasificacion($data->clasificacion);
          empty($data->preciounidad) ? : $producto->setPreciounidad($data->preciounidad);
          $msg = 'El producto con id: ' . $producto->getId() . ' se ha actualizado correctamente.';
      } else {
          foreach ($data as $key => $value) {
              switch ($key) {
                  case 'descripcion':
                    $producto->setDescripcion($data->descripcion);
                    $msg = 'Se ha actualizado el campo descripción.';
                    break;
                case 'unidad':
                    $producto->setUnidad($data->unidad);
                    $msg = 'Se ha actualizado el campo unidad.';
                    break;
                case 'clasificacion':
                    $producto->setClasificacion($data->clasificacion);
                    $msg = 'Se ha actualizado el campo clasificación';
                    break;
                case 'preciounidad':
                    $producto->setPreciounidad($data->preciounidad);
                    $msg = 'Se ha actualizado el campo preciounidad.';
                    break;
              }
          }
      }
      $this->productosRepository->save($producto, true);
      return new JsonResponse(['status'=>$msg], Response::HTTP_CREATED);
  }
      #[Route('/{id}', name:'api_productos_delete', methods:['DELETE'])]
      public function remove(int $id):JsonResponse
      {
          $producto = $this->productosRepository->find($id);
          $this->productosRepository->remove($producto, true);
          return new JsonResponse(['status'=> 'Producto: ' . $id . ' eliminado correctamente.', Response::HTTP_OK]);
      }
}
