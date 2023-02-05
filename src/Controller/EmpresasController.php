<?php

namespace App\Controller;

use App\Repository\EmpresasRepository;
use App\Repository\PedidosRepository;
use App\Entity\Empresas;
use App\Entity\Pedidos;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;

#[Route('/empresas')]
class EmpresasController extends AbstractController
{
    private EmpresasRepository $empresasRepository;

    public function __construct(EmpresasRepository $empresasRepo, PedidosRepository $pedidosRepo)
    {
        $this->empresasRepository = $empresasRepo;
        $this->pedidosRepository = $pedidosRepo;
    }

    #[Route('/', name: 'api_empresas', methods:['GET','POST'])]
    public function index(): JsonResponse
    {
        $empresas = $this->empresasRepository->findAll();
        $data = array();
        $detail = array();
        foreach ($empresas as $key => $value) {
            $pedidos = $value->getPedidos();
            foreach ($pedidos as $key2 => $value2) {
                $detail[$key2] = [
                    'ID_Pedido' => $value2->getId(),
                    'Observación' => $value2->getObservacion()
               ];
            }
            $data[$key]=[
                'EMPRESA' => $value->getNombre(),
                'CIF' => $value->getCIF(),
                'tipo' => $value->getTipo(),
                'PEDIDOS' => [$detail]
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name:'api_empresas_show', methods:['GET'])]
    public function show(int $id): JsonResponse
    {
        $empresa = $this->empresasRepository ->find($id);
        $pedidos = array();
        $detail = $empresa->getPedidos();
        foreach ($detail as $key => $value) {
            $pedidos[$key] = [
                'id_pedido' => $value->getId(),
                'observacion' => $value->getObservacion()
            ];
        }
        $data = [
            'nombre' => $empresa->getNombre(),
            'CIF' => $empresa->getCIF(),
            'tipo' => $empresa->getTipo(),
            'Pedidos' => [$pedidos]
        ];
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/new', name:'api_empresas_new', methods:['POST'])]
    public function add(Request $request):JsonResponse
    {
        $data = json_decode($request->getContent());
        $empresa = new Empresas();
        $empresa->setNombre($data->nombre)
                ->setCIF($data->CIF)
                ->setTipo($data->tipo);
        $this->empresasRepository->save($empresa, true);
        return new JsonResponse(['status'=> 'Nueva Empresa insertada con la ID: ' . $empresa->getId()], Response::HTTP_CREATED);
    }

    #[Route('/edit/{id}', name:'api_empresas_edit', methods:['PUT','PATCH'])]
        public function edit(int $id, Request $request):JsonResponse
        {
            $data = json_decode($request->getContent());
            $empresa = $this->empresasRepository->find($id);
            if ($_SERVER['REQUEST_METHOD']=='PUT') {
                empty($data->nombre) ? : $empresa->setNombre($data->nombre);
                empty($data->CIF) ? : $empresa->setCIF($data->CIF);
                empty($data->tipo) ? : $empresa->setTipo($data->tipo);
                $msg = 'La empresa con id: ' . $empresa->getId() . ' se ha actualizado correctamente.';
            } else {
                foreach ($data as $key => $value) {
                    switch ($key) {
                        case 'nombre':
                            $empresa->setNombre($data->nombre);
                            $msg = 'Se ha actualizado el campo nombre.';
                            break;
                        case 'CIF':
                            $empresa->setCIF($data->CIF);
                            $msg = 'Se ha actualizado el campo CIF.';
                            break;
                        case 'tipo':
                            $empresa->setTipo($data->tipo);
                            $msg = 'Se ha actualizado el campo tipo';
                            break;
                    }
                }
            }
            $this->empresasRepository->save($empresa, true);
            return new JsonResponse(['status'=>$msg], Response::HTTP_CREATED);
        }
    
    #[Route('/{id}', name:'api_empresas_delete', methods:['DELETE'])]
    public function remove(int $id):JsonResponse
    {
        $empresa = $this->empresasRepository->find($id);
        $this->empresasRepository->remove($empresa, true);
        return new JsonResponse(['status'=> 'Empresa: '.$empresa->getNombre(). ' con ID: ' . $id . ' eliminada correctamente.', Response::HTTP_OK]);
    }
}
