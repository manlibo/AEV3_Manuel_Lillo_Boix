<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use function PHPSTORM_META\map;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(): JsonResponse
    {
        return $this->json([
            
            'Corporacion2' => [
                'Tabla1' => [
                    'name1' => 'facturas',
                'asociadas1' => [
                    '1' => 'pedidos'
                ],
                'ruta1' => 'http://localhost:8000/facturas/'
                ],
                'Tabla2' => [
                    'name2' => 'pedidos',
                'asociadas2' => [
                    '1' => 'facturas',
                    '2' => 'empresas',
                    '3' => 'lineaspedidos'
                ],
                'ruta2' => 'http://localhost:8000/pedidos/'
                ],
                'Tabla3' => [
                    'name3' => 'empresas',
                'asociadas3' => [
                    '1' => 'pedidos',
                ],
                'ruta3' => 'http://localhost:8000/empresas/'
                ],
                'Tabla4' => [
                    'name4' => 'lineaspedidos',
                'asociadas4' => [
                    '1' => 'productos',
                    '2' => 'pedidos'
                ],
                'ruta4' => 'http://localhost:8000/lineaspedidos/'
                ],
                'Tabla5' => [
                    'name5' => 'productos',
                'asociadas5' => [
                    '1' => 'lineaspedidos',
                    '2' => 'almacenes',
                    '3' => 'stock'
                ],
                'ruta5' => 'http://localhost:8000/productos/'
                ],
                'Tabla6' => [
                    'name6' => 'almacenes',
                'asociadas6' => [
                    '1' => 'productos',
                    '2' => 'stock'
                ],
                'ruta6' => 'http://localhost:8000/almacenes/'
                ],
                'Tabla7' => [
                    'name7' => 'stock',
                'asociadas7' => [
                    '1' => 'productos',
                    '2' => 'almacenes'
                ],
                'ruta7' => 'http://localhost:8000/stock/'
                ],
                ],
        ]);
    }
}
