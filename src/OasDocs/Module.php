<?php namespace DanBallance\OasDocs;

use DanBallance\OasDocs\Components\LocalFileManager;
use Yii;

/**
 * Class Module
 * Module class for yii2-oai-docs
 *
 * @package DanBallance\OasDocs
 */
class Module extends \yii\base\Module
{
    public $version = 2;
    public $controllerNamespace = "DanBallance\\OasDocs\Controllers";
    public $routePrefix = 'docs';

    // schema location (file path or network URL)
    public $specification = [];

    // location of contents.json providing details of supplementary documentation
    public $documentation = [];

    // set these config properties to use different classes and override behaviours
    public $implSpecificationInterface2
        = "DanBallance\\OasTools\\Specification\\Adapters\\AdapterJCollect2";
    public $implSpecificationInterface3
        = "DanBallance\\OasTools\\Specification\\Adapters\\AdapterJCollect3";
    public $implUrlMapperInterface2
        = "DanBallance\\OasDocs\\Components\\UrlMapper2";
    public $implUrlMapperInterface3
        = "DanBallance\\OasDocs\\Components\\UrlMapper3";
    public $implFragmentMapperInterface2
        = "DanBallance\\OasTools\\FragmentMapper\\FragmentMapper2";
    public $implFragmentMapperInterface3
        = "DanBallance\\OasTools\\FragmentMapper\\FragmentMapper3";
    public $implSpecificationDocsInterface2
        = "DanBallance\\OasDocs\\Components\\SpecificationDocs2";
    public $implSpecificationDocsInterface3
        = "DanBallance\\OasDocs\\Components\\SpecificationDocs3";
    public $implTreeInterface
        = "DanBallance\\OasDocs\\Components\\TreeNicmart";
    public $implTocItemFactoryInterface
        = "DanBallance\\OasDocs\\Components\\TocItemFactory";

    // set these config properties to supply different view and asset files
    public $viewPath = '@DanBallance/OasDocs/Views';
    public $layoutPath = '@DanBallance/OasDocs/Views/Layouts';
    public $assetsPath = __DIR__ . '/Assets';

    /**
     * Yii framework init function for the module.
     *
     * Used to set up all of the classes that will be used by the DI container.
     * By setting name spaced config properties in the config file to use
     * new classes that implement one of the interfaces
     * the module can thereby be easily customised.
     *
     * @return void
     */
    public function init() : void
    {
        parent::init();
        // view paths
        $this->setViewPath($this->viewPath);
        $this->setLayoutPath($this->layoutPath);
        $this->setAliases(
            [
                '@OasDocs-assets' => $this->assetsPath
            ]
        );
        $specificationLocation = $this->specification;
        [$contentsSchema, $localFileManager] = $this->getDocsComponents();

        // configure DI container -
        // these interfaces can be implemented & overridden to customise behaviour

        // Specification2Interface
        $specInterface = $this->getSpecInterface();
        $implSpecificationInterface = $this->getImpl('implSpecificationInterface');
        Yii::$container->setSingleton(
            $specInterface,
            function (
                $container,
                $params,
                $config
            ) use ($specificationLocation, $implSpecificationInterface) {
                if (array_key_exists("network", $specificationLocation)) {
                    $guzzle = new \GuzzleHttp\Client();
                    return new $implSpecificationInterface(
                        $specificationLocation['network'],
                        $guzzle
                    );
                } else {
                    $filePath = Yii::getAlias($specificationLocation['file']);
                    return new $implSpecificationInterface($filePath);
                }
            }
        );
        // UrlMapperInterface
        $implUrlMapperInterface =  $this->getImpl('implUrlMapperInterface');
        Yii::$container->setSingleton(
            "DanBallance\\OasDocs\\Components\\UrlMapperInterface",
            function (
                $container,
                $params,
                $config
            ) use ($implUrlMapperInterface, $specInterface) {
                $schema = $container->get(
                    $specInterface
                );
                return new $implUrlMapperInterface($schema->toArray());
            }
        );
        // FragmentMapperInterface
        $implFragmentMapperInterface = $this->getImpl('implFragmentMapperInterface');
        Yii::$container->setSingleton(
            "DanBallance\\OasTools\\FragmentMapper\\FragmentMapperInterface",
            function (
                $container,
                $params,
                $config
            ) use ($implFragmentMapperInterface, $specInterface) {
                $schema = $container->get(
                    $specInterface
                );
                return new $implFragmentMapperInterface($schema->toArray());
            }
        );
        // TreeInterface
        $implTreeInterface = $this->implTreeInterface;
        Yii::$container->setSingleton(
            "DanBallance\\OasDocs\\Components\\TreeInterface",
            function ($container, $params, $config) use ($implTreeInterface) {
                return new $implTreeInterface();
            }
        );
        // TocItemFactoryInterface
        $implTocItemFactoryInterface = $this->implTocItemFactoryInterface;
        Yii::$container->setSingleton(
            "DanBallance\\OasDocs\\Components\\TocItemFactoryInterface",
            function (
                $container,
                $params,
                $config
            ) use ($implTocItemFactoryInterface, $specInterface) {
                $urlMapper = $container->get(
                    "DanBallance\\OasDocs\\Components\\UrlMapperInterface"
                );
                $specification = $container->get($specInterface);
                return new $implTocItemFactoryInterface(
                    $urlMapper,
                    $specification
                );
            }
        );
        // SpecificationDocsInterface
        $implSpecificationDocsInterface = $this->getImpl(
            'implSpecificationDocsInterface'
        );
        $routePrefix = $this->routePrefix;
        Yii::$container->setSingleton(
            'DanBallance\\OasDocs\\Components\\SpecificationDocsInterface',
            function ($container, $params, $config) use (
                $specificationLocation,
                $implSpecificationDocsInterface,
                $contentsSchema,
                $routePrefix,
                $localFileManager,
                $specInterface
            ) {
                $urlMapper = $container->get(
                    "DanBallance\\OasDocs\\Components\\UrlMapperInterface"
                );
                $fragmentMapper = $container->get(
                    "DanBallance\\OasTools\\FragmentMapper\\FragmentMapperInterface"
                );
                $schema = $container->get($specInterface);
                $tree = $container->get(
                    "DanBallance\\OasDocs\\Components\\TreeInterface"
                );
                $tocItemFactory = $container->get(
                    "DanBallance\\OasDocs\\Components\\TocItemFactoryInterface"
                );
                $instance = new $implSpecificationDocsInterface(
                    $schema,
                    $urlMapper,
                    $fragmentMapper,
                    $localFileManager,
                    $tree,
                    $tocItemFactory,
                    [
                        'routePrefix' => $routePrefix
                    ]
                );
                if ($contentsSchema) {
                    $instance->setContentsSchema($contentsSchema);
                }
                return $instance;
            }
        );
    }

    /**
     * Returns the correct namespace for the specification interface
     *
     * @return string
     */
    protected function getSpecInterface() : string
    {
        if ($this->version == 2) {
            return "DanBallance\\OasTools\\Specification\\Specification2Interface";
        } else {
            return "DanBallance\\OasTools\\Specification\\Specification3Interface";
        }
    }

    /**
     * Fetches the versioned implementation, based upon $this->version
     *
     * @param string $implementation The implementation to fetch
     *
     * @return string
     */
    protected function getImpl(string $implementation) : string
    {
        $propertyName = "$implementation{$this->version}";
        return $this->$propertyName;
    }

    /**
     * Returns the contents schema and local file manager components as an array
     *
     * @return array
     */
    protected function getDocsComponents() : array
    {
        $contentsSchema = [];
        $localFileManager = null;
        if ($this->documentation && isset($this->documentation['file'])) {  // network not yet implemented
            $filePath = Yii::getAlias($this->documentation['file']);
            if (file_exists($filePath)) {
                $contentsSchema = json_decode(file_get_contents($filePath), true);
                $localFileManager = new LocalFileManager(
                    pathinfo($filePath, PATHINFO_DIRNAME) . '/'
                );
            }
        }
        return [$contentsSchema, $localFileManager];
    }
}
