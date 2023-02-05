<?php

namespace App\Controller;

use App\Repository\AlmacenesRepository;
use App\Entity\Almacenes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/almacenes')]
class AlmacenesController extends AbstractController
{
    private AlmacenesRepository $almacenesRepository;


    public function __construct(AlmacenesRepository $almacenesRepo)
    {
        $this->almacenesRepository = $almacenesRepo;
    }

    #[Route('/', name: 'api_almacenes', methods:['GET','POST'])]
    public function index(): JsonResponse
    {
        $almacenes = $this->almacenesRepository->findAll();
        $data = array();
        $detail = array();
        $detail2 = array();
        foreach ($almacenes as $key => $value) {
            $productos = $value->getProductos();
            foreach ($productos as $key2 => $value2) {
                $detail[$key2] = [
                    'ID_PRODUCTO' => $value2->getId(),
                    'Descripción' => $value2->getDescripcion()
                ];
            }
            $stock = $value->getStocks();
            foreach ($stock as $key3 => $value3) {
                $detail2[$key3] = [
                    'ID_STOCK' => $value3->getId(),
                    'Cantidad' => $value3->getCantidad()
                ];
            }
            $data[$key]=[
                'ALMACEN' => $value->getNombre(),
                'localizacion' => $value->getLocalizacion(),
                'descripcion' => $value->getDescripcion(), 
                'PRODUCTO' => [$detail],
                'STOCK' => [$detail2] 
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

     #[Route('/{id}', name: 'api_almacenes_show', methods:['GET'])]
     public function show(int $id): JsonResponse
     {
         $almacen = $this->almacenesRepository->find($id);
         $productos = array();
         $detail = $almacen->getProductos();
         foreach ($detail as $key2 => $value2) {
             $productos[$key2] =[
                 'Producto' => $value2->getId(),
                 'Descripcion' => $value2->getDescripcion()
 
             ];
         }
         $stocks = array();
         $detail2 = $almacen->getStocks();
         foreach ($detail2 as $key => $value) {
             $stocks[$key] = [
                 'Stock' => $value->getId(),
                     'cantidad' => $value->getPrecio(),
             ];
         }
         
         $data = [
             'nombre' => $almacen->getNombre(),
             'localizacion' => $almacen->getLocalizacion(),
             'descripcion' => $almacen->getDescripcion(),
             'productos' => [$productos],
             'stocks' => [$stocks]
         ];
         return new JsonResponse($data, Response::HTTP_OK);
     }


         #[Route('/new', name:'api_almacenes_new', methods:['POST'])]
    public function add(Request $request):JsonResponse
    {
        $data = json_decode($request->getContent());
        $almacen = new Almacenes();
        $almacen->setId($data->id)
                ->setNombre($data->nombre)
                ->setLocalizacion($data->localizacion)
                ->setDescripcion($data->descripcion);
        $this->almacenesRepository->save($almacen, true);
        return new JsonResponse(['status' => 'Nuevo Almacen insertado con la id: ' . $almacen->getId()], Response::HTTP_CREATED);
    }
    
    #[Route('/edit/{id}', name:'api_almacenes_edit', methods:['PUT','PATCH'])]
    public function edit(int $id, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent());
        $almacen = $this->almacenesRepository->find($id);
        if ($_SERVER['REQUEST_METHOD']=='PUT') {
            empty($data->nombre) ? : $almacen->setNombre($data->nombre);
            empty($data->localizacion) ? : $almacen->setLocalizacion($data->localizacion);
            empty($data->descripcion) ? : $almacen->setDescripcion($data->descripcion);
            $msg = 'El Almacen con id: ' . $almacen->getId() . ' se ha actualizado correctamente.';
        } else {
            foreach ($data as $key => $value) {
                switch ($key) {
                    case 'nombre':
                        $almacen->setNombre($data->nombre);
                        $msg = 'Se ha actualizado el campo nombre.';
                        break;
                    case 'localizacion':
                        $almacen->setLocalizacion($data->localizacion);
                        $msg = 'Se ha actualizado el campo localizacion.';
                        break;
                    case 'descripcion':
                        $almacen->setDescripcion($data->descripcion);
                        $msg = 'Se ha actualizado el campo descripcion';
                        break;
                }
            }
        }
        $this->almacenesRepository->save($almacen, true);
        return new JsonResponse(['status'=>$msg], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name:'api_almacenes_delete', methods:['DELETE'])]
    public function remove(int $id):JsonResponse
    {
        $almacen = $this->almacenesRepository->find($id);
        $this->almacenesRepository->remove($almacen, true);
        return new JsonResponse(['status'=> 'Almacen: ' . $id .' eliminado correctamente.', Response::HTTP_OK]);
    }




}
