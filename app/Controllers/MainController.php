<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Facades\View;
use App\Middlewares\AppMiddleware;
use Mrcl\SlimRoutes\Attribute;
use Psr\Http\Message\ResponseInterface;

#[Attribute\Controller(pattern: '/', middleware: [AppMiddleware::class])]
final class MainController
{
    #[Attribute\Route(method: ['GET'], pattern: '', middleware: [], priority: 1, name: 'main.index')]
    public function index(): ResponseInterface
    {
        return View::twig('main.html.twig');
    }
}
