<?php

declare(strict_types=1);

use phpDocumentor\Guides\Compiler\DocumentNodeTraverser;
use phpDocumentor\Guides\Compiler\NodeTransformers\CustomNodeTransformerFactory;
use phpDocumentor\Guides\Compiler\NodeTransformers\NodeTransformerFactory;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\NodeRenderers\DefaultNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\DelegatingNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\InMemoryNodeRendererFactory;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactory;
use phpDocumentor\Guides\References\ReferenceResolver;
use phpDocumentor\Guides\Renderer\HtmlRenderer;
use phpDocumentor\Guides\Renderer\InMemoryRendererFactory;
use phpDocumentor\Guides\Renderer\IntersphinxRenderer;
use phpDocumentor\Guides\Renderer\TypeRendererFactory;
use phpDocumentor\Guides\TemplateRenderer;
use phpDocumentor\Guides\Twig\AssetsExtension;
use phpDocumentor\Guides\Twig\EnvironmentBuilder;
use phpDocumentor\Guides\Twig\TwigTemplateRenderer;
use phpDocumentor\Guides\UrlGenerator;
use phpDocumentor\Guides\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Twig\Loader\FilesystemLoader;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->instanceof(phpDocumentor\Guides\References\Resolver\Resolver::class)
        ->tag('phpdoc.guides.reference.resolver')

        ->instanceof(phpDocumentor\Guides\NodeRenderers\NodeRendererFactoryAware::class)
        ->tag('phpdoc.guides.noderendererfactoryaware')

        ->instanceof(phpDocumentor\Guides\Compiler\CompilerPass::class)
        ->tag('phpdoc.guides.compiler.passes')

        ->instanceof(phpDocumentor\Guides\Compiler\NodeTransformer::class)
        ->tag('phpdoc.guides.compiler.nodeTransformers')

        ->load(
            'phpDocumentor\\Guides\\Compiler\\NodeTransformers\\',
            '%vendor_dir%/phpdocumentor/guides/src/Compiler/NodeTransformers/*Transformer.php',
        )

        ->load(
            'phpDocumentor\\Guides\\Compiler\\Passes\\',
            '%vendor_dir%/phpdocumentor/guides/src/Compiler/Passes/*Pass.php',
        )

        ->load(
            'phpDocumentor\\Guides\\References\\Resolver\\',
            '%vendor_dir%/phpdocumentor/guides/src/References/Resolver',
        )->load(
            'phpDocumentor\\Guides\\NodeRenderers\\',
            '%vendor_dir%/phpdocumentor/guides/src/NodeRenderers',
        )

        ->set(Metas::class)
        ->set(UrlGeneratorInterface::class, UrlGenerator::class)
        ->set(ReferenceResolver::class)
        ->arg('$resolvers', tagged_iterator('phpdoc.guides.reference.resolver'))

        ->set(phpDocumentor\Guides\Parser::class)
        ->arg('$parserStrategies', tagged_iterator('phpdoc.guides.parser.markupLanguageParser'))

        ->set(phpDocumentor\Guides\Compiler\Compiler::class)
        ->arg('$passes', tagged_iterator('phpdoc.guides.compiler.passes'))

        ->set(NodeTransformerFactory::class, CustomNodeTransformerFactory::class)
        ->arg('$transformers', tagged_iterator('phpdoc.guides.compiler.nodeTransformers'))

        ->set(DocumentNodeTraverser::class)

        ->set(HtmlRenderer::class)
        ->tag('phpdoc.renderer.typerenderer')
        ->args(
            ['$renderer' => service(DelegatingNodeRenderer::class)],
        )

        ->set(IntersphinxRenderer::class)
        ->tag('phpdoc.renderer.typerenderer')

        ->set(phpDocumentor\Guides\NodeRenderers\Html\SpanNodeRenderer::class)
        ->tag('phpdoc.guides.noderenderer.html')
        ->set(phpDocumentor\Guides\NodeRenderers\Html\TableNodeRenderer::class)
        ->tag('phpdoc.guides.noderenderer.html')
        ->set(phpDocumentor\Guides\NodeRenderers\Html\TocNodeRenderer::class)
        ->tag('phpdoc.guides.noderenderer.html')
        ->set(phpDocumentor\Guides\NodeRenderers\Html\TocEntryRenderer::class)
        ->tag('phpdoc.guides.noderenderer.html')

        ->set(phpDocumentor\Guides\NodeRenderers\InMemoryNodeRendererFactory::class)
        ->args([
            '$nodeRenderers' => tagged_iterator('phpdoc.guides.noderenderer.html'),
            '$defaultNodeRenderer' => new Reference(DefaultNodeRenderer::class),
        ])
        ->alias(NodeRendererFactory::class, InMemoryNodeRendererFactory::class)

         ->set(InMemoryRendererFactory::class)
        ->arg('$renderSets', tagged_iterator('phpdoc.renderer.typerenderer'))
        ->alias(TypeRendererFactory::class, InMemoryRendererFactory::class)


        ->set(AssetsExtension::class)
        ->arg('$nodeRenderer', service(DelegatingNodeRenderer::class))
        ->tag('twig.extension')
        ->autowire()

        ->set(FilesystemLoader::class)
        ->arg(
            '$paths',
            [
                __DIR__ . '/../../../guides/resources/template/html/guides',
            ],
        )


        ->set(EnvironmentBuilder::class)
        ->arg('$extensions', tagged_iterator('twig.extension'))
        ->arg('$filesystemLoader', service(FilesystemLoader::class))

        ->set(TemplateRenderer::class, TwigTemplateRenderer::class)
        ->arg('$environmentBuilder', new Reference(EnvironmentBuilder::class));
};
