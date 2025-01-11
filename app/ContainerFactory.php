<?php

declare(strict_types=1);

namespace App;

use App\Supports\Twig;
use DI\ContainerBuilder;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Mrcl\SlimRoutes\SlimRoutes;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Interfaces\ConfigurationInterface;
use Slim\Interfaces\ContainerFactoryInterface;
use Slim\Interfaces\RequestHandlerInvocationStrategyInterface;
use Slim\Interfaces\ServerRequestCreatorInterface;
use Slim\Routing\Strategies\RequestResponseTypedArgs;
use Symfony\Component\Console\Application;
use Symfony\Component\Serializer\Encoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use function DI\autowire;
use function DI\create;
use function DI\get;

final class ContainerFactory implements ContainerFactoryInterface
{
    public function createContainer(array $definitions = []): ContainerInterface
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->useAttributes(true);
        $containerBuilder->useAutowiring(true);
        $containerBuilder->addDefinitions($definitions);

        return $containerBuilder->build();
    }

    public function addDefinitions(): array
    {
        return [
            // STRATEGY
            RequestHandlerInvocationStrategyInterface::class => autowire(RequestResponseTypedArgs::class),

            // RESPONSE & REQUEST
            ServerRequestInterface::class => fn(ServerRequestCreatorInterface $serverRequestCreator): ServerRequestInterface => $serverRequestCreator->createServerRequestFromGlobals(),
            ResponseInterface::class => fn(ResponseFactoryInterface $responseFactory): ResponseInterface => $responseFactory->createResponse(),

            // TWIG
            Twig\TwigRuntimeLoader::class => autowire(Twig\TwigRuntimeLoader::class),
            Twig\Extensions\SlimExtension::class => autowire(Twig\Extensions\SlimExtension::class),
            Twig\Extensions\ViteExtension::class => autowire(Twig\Extensions\ViteExtension::class),
            Twig\TwigInterface::class => function (
                ConfigurationInterface $configuration,
                Twig\TwigRuntimeLoader $twigRuntimeLoader,
                Twig\Extensions\SlimExtension $slimExtension,
                Twig\Extensions\ViteExtension $viteExtension,
            ) {
                $twig = new Twig\Twig($configuration->get('twig.paths'), $configuration->get('twig.options'));
                $twig->addRuntimeLoader($twigRuntimeLoader);
                $twig->addExtension($slimExtension);
                $twig->addExtension($viteExtension);

                return $twig;
            },

            // SYMFONY
            Application::class => autowire(Application::class)->constructor('SLIM', App::VERSION),
            Encoder\JsonEncoder::class => create(Encoder\JsonEncoder::class),
            Encoder\XmlEncoder::class => create(Encoder\XmlEncoder::class),
            ClassMetadataFactoryInterface::class => fn() => new ClassMetadataFactory(new AttributeLoader),
            CamelCaseToSnakeCaseNameConverter::class => autowire(CamelCaseToSnakeCaseNameConverter::class),
            AbstractObjectNormalizer::class => create(ObjectNormalizer::class)
                ->constructor(
                    get(ClassMetadataFactoryInterface::class),
                    get(CamelCaseToSnakeCaseNameConverter::class)
                ),
            SerializerInterface::class => create(Serializer::class)->constructor([get(AbstractObjectNormalizer::class)], [
                get(Encoder\JsonEncoder::class),
                get(Encoder\XmlEncoder::class),
            ]),

            // DOCTRINE
            ORMSetup::class => fn() => ORMSetup::createAttributeMetadataConfiguration([base_path('app\Entity')], env('DOCTRINE_DEBUG')),
            DriverManager::class => fn(ConfigurationInterface $configuration) => DriverManager::getConnection($configuration->get('database')),
            EntityManager::class => create(EntityManager::class)->constructor(get(DriverManager::class), get(ORMSetup::class)),
            EntityManagerInterface::class => get(EntityManager::class),
            PhpFile::class => create(PhpFile::class)->constructor(base_path('bootstrap\migrations.php')),
            ExistingEntityManager::class => create(ExistingEntityManager::class)->constructor(get(EntityManagerInterface::class)),
            DependencyFactory::class => fn(PhpFile $phpFile, ExistingEntityManager $existingEntityManager) => DependencyFactory::fromEntityManager($phpFile, $existingEntityManager),
            EntityManagerProvider::class => create(SingleManagerProvider::class)->constructor(get(EntityManagerInterface::class)),

            // MONOLOG
            LoggerInterface::class => function (): LoggerInterface {
                $log = new Logger('slim');
                $log->pushHandler(new StreamHandler(base_path('storage/slim.log'), Level::Warning));

                return $log;
            },

            // SLIM ATTRIBUTE
            SlimRoutes::class => autowire(SlimRoutes::class)->constructor(get(App::class), base_path('app/Controllers')),
        ];
    }

    public function addSettings(): array
    {
        return [
            'display_error_details' => env('APP_DISPLAY_ERROR_DETAILS', false),
            'log_error_details' => env('APP_LOG_ERROR_DETAILS', false),
            'database' => [
                'driver' => 'pdo_mysql',
                'host' => env('DOCTRINE_HOST'),
                'port' => env('DOCTRINE_PORT'),
                'user' => env('DOCTRINE_USERNAME'),
                'password' => env('DOCTRINE_PASSWORD'),
                'dbname' => env('DOCTRINE_DATABASE'),
                'charset' => 'utf8mb4'
            ],
            'twig' => [
                'paths' => [base_path('templates')],
                'options' => [
                    'debug' => true,
                    'charset' => 'UTF-8',
                    'strict_variables' => false,
                    'autoescape' => 'html',
                    'cache' => env('TWIG_CACHE', false)
                        ? base_path('storage/cache/twig')
                        : false,
                    'auto_reload' => null,
                    'optimizations' => -1,
                ]
            ],
            'mail' => [
                'host' => env('MAIL_HOST', 'smtp.mailtrap.io'),
                'port' => env('MAIL_PORT', '2525'),
                'username' => env('MAIL_USERNAME', 'username'),
                'password' => env('MAIL_PASSWORD', 'password'),
                'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            ],
        ];
    }
}
