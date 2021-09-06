<?php

namespace Aldaflux\MyMakerBundle\Maker;

 

use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Common\Inflector\Inflector as LegacyInflector;
use Doctrine\Inflector\InflectorFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Renderer\FormTypeRenderer;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Validator\Validation;

use Doctrine\Common\Annotations\AnnotationReader;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface; 

 
class MakeAdmin extends AbstractMaker
{
    protected $doctrineHelper;

    protected $formTypeRenderer;

    protected $parameters;

    protected $inflector;

    protected $folderControllerName;
    protected $routeNamePrefix;
    protected $routePathPrefix;
    protected $templateFolder; 
    
    public function __construct(DoctrineHelper $doctrineHelper, FormTypeRenderer $formTypeRenderer, ParameterBagInterface $parameters)
    {
        $this->doctrineHelper = $doctrineHelper;
        $this->formTypeRenderer = $formTypeRenderer;
        $this->parameters = $parameters;
        
        $config=$this->parameters->Get("aldaflux_mymaker.config");
        
        if (isset($config["backoffice"]))
        {
            $config=$config["backoffice"];
        }
       
        if (isset($config["folder"]))
        {
            
            if (isset($config["folder"]["controller"]))
            {
                if ($config["folder"]["controller"])
                {
                    $this->folderControllerName = $config["folder"]["controller"].'\\';
                }
            }
            if (isset($config["folder"]["template"]))
            {
                $this->templateFolder = $config["folder"]["template"].'/';
            }
        }
        if (isset($config["route"]))
        {
            if (isset($config["route"]["name_prefix"]))
            {
                $this->routeNamePrefix = $config["route"]["name_prefix"];
            }
            if (isset($config["route"]["path_prefix"]))
            {
                $this->routePathPrefix = $config["route"]["path_prefix"];
            }
        }

        if (isset($config["extend"]))
        {
            $this->extend = $config["extend"];
        }
        else
        {
            $this->extend="";
        }

        
        
        if (class_exists(InflectorFactory::class)) {
            $this->inflector = InflectorFactory::create()->build();
        }
    }
    
    public static function getCommandName(): string
    {
        return '';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        
    }
    
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
         
     }
 
    /**
     * {@inheritdoc}
     */
    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(
            Route::class,
            'router'
        );

        $dependencies->addClassDependency(
            AbstractType::class,
            'form'
        );

        $dependencies->addClassDependency(
            Validation::class,
            'validator'
        );

        $dependencies->addClassDependency(
            TwigBundle::class,
            'twig-bundle'
        );

        $dependencies->addClassDependency(
            DoctrineBundle::class,
            'orm-pack'
        );

        $dependencies->addClassDependency(
            CsrfTokenManager::class,
            'security-csrf'
        );

        $dependencies->addClassDependency(
            ParamConverter::class,
            'annotations'
        );
    }

    protected function pluralize(string $word): string
    {
        if (null !== $this->inflector) {
            return $this->inflector->pluralize($word);
        }

        return LegacyInflector::pluralize($word);
    }

    protected function singularize(string $word): string
    {
        if (null !== $this->inflector) {
            return $this->inflector->singularize($word);
        }

        return LegacyInflector::singularize($word);
    }

    protected function getFieldType($entity, $fieldName)
    {
        $annotationReader = new AnnotationReader();
        $refClass = new \ReflectionClass($entity);
        $annotations = $annotationReader->getPropertyAnnotations($refClass->getProperty($fieldName));

        if (count($annotations) > 0) {
            foreach ($annotations as $annotation) {
                if (
                    $annotation instanceof \Doctrine\ORM\Mapping\Column
                    && property_exists($annotation, 'type')
                ) {
                    return $annotation->type;
                }
            }
        }

        return null;
    }
}
