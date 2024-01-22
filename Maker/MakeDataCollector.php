<?php

namespace Aldaflux\MyMakerBundle\Maker;

use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;

use Doctrine\Inflector\InflectorFactory;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Str;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
 
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface; 

use Symfony\Bundle\MakerBundle\FileManager;


use Symfony\Bundle\MakerBundle\Util\YamlSourceManipulator;



use Symfony\Component\HttpKernel\Kernel;



/**
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
final class MakeDataCollector extends AbstractMaker
{
    private $doctrineHelper;
    private $parameters;
    private $inflector;
    
    private $folderControllerName;
    private $templateFolder;

    public static function getCommandName(): string
    {
        return 'make:data:collector';
    }
    public static function getCommandDescription(): string
    {
        return 'make:data:collector';
    }

    public function __construct(FileManager $fileManager, DoctrineHelper $doctrineHelper, ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
        $this->fileManager = $fileManager;
        
        
        $this->doctrineHelper = $doctrineHelper;
        if (class_exists(InflectorFactory::class)) {
            $this->inflector = InflectorFactory::create()->build();
        }
    }


    public function getSkeletonFolder()
    {
        return(__DIR__.'/../Resources/skeleton/');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates CRUD for Doctrine entity class')
            ->addArgument('entity-class', InputArgument::OPTIONAL, sprintf('The class name of the entity to create CRUD (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())))
            ->setHelp("file_get_contents(__DIR__.'/../Resources/help/MakeCrud.txt'")
        ;
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        if (null === $input->getArgument('entity-class')) {
            $argument = $command->getDefinition()->getArgument('entity-class');

            $entities = $this->doctrineHelper->getEntitiesForAutocomplete();

            $question = new Question($argument->getDescription());
            $question->setAutocompleterValues($entities);

            $value = $io->askQuestion($question);

            $input->setArgument('entity-class', $value);
        }
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        
         if (Kernel::VERSION_ID >= 50200) 
         {
             $extendClass="Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector";
             $parentClassName="AbstractDataCollector";
         }
         else
         {
             $extendClass="Symfony\Component\HttpKernel\DataCollector\DataCollector";
             $parentClassName="DataCollector";
         }
        $templatesPath = "data_collector";
        
        $CollectorClassName=$input->getArgument('entity-class');
        $collectorClassDetails = $generator->createClassNameDetails(
            $CollectorClassName,
            'DataCollector\\'
        );
        
         $templatesPathCollector=$templatesPath.'/'.Str::asFilePath($collectorClassDetails->getShortName()).'.html.twig';
         $collectorNameString=Str::asTwigVariable($collectorClassDetails->getShortName());

         $generator->generateClass(
            $collectorClassDetails->getFullName(),
             $this->getSkeletonFolder().'DataCollector/DataCollector/Collector.tpl.php',
            [
                'collector_name_string' => $collectorNameString,
                'class_name' => $collectorClassDetails->getShortName(), 
                "template_path"=>$templatesPathCollector,                
                "parent_class_name"=>$parentClassName,                
                "extend_class"=>$extendClass,                
            ]
        );
        
        
            $iconPath=$templatesPath.'/icon/'.Str::asFilePath($collectorClassDetails->getShortName()).'.svg';
        
            $generator->generateTemplate(
                 $templatesPathCollector,
                $this->getSkeletonFolder().'DataCollector/templates/data_collector/template.html.twig.tpl.php',
                    ["collector_name"=>$collectorClassDetails->getShortName(),
                    "icon_path"=>$iconPath]);
            
            $generator->generateTemplate(
                 $iconPath,
                $this->getSkeletonFolder().'DataCollector/templates/data_collector/icon/icon.svg.tpl.php',
                ['color'=>self::getRandomColor()]
            );
        

            if ($this->fileManager->fileExists($path = 'config/services.yaml')) 
            {
                $manipulator = new YamlSourceManipulator($this->fileManager->getFileContents($configFilePath = 'config/services.yaml'));
                $servicesData = $manipulator->getData();
                $tag["name"]="data_collector";
                $tag["id"]=$collectorNameString;
                $servicesData['services'][$collectorClassDetails->getFullName()]['tags'][]=$tag;
                $manipulator->setData($servicesData);
                $generator->dumpFile($configFilePath, $manipulator->getContents());
            }
            else
            {
                
                
            }
                    
            

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

//        $io->text(sprintf('Next: Check your new CRUD by going to <fg=yellow>%s/</>', Str::asRoutePath($controllerClassDetails->getRelativeNameWithoutSuffix())));
    }

    /**
     * {@inheritdoc}
     */
    public function configureDependencies(DependencyBuilder $dependencies)
    {
        
         
   
    }
    
    
    
    public static function getRandomColor(): string
    {
        $colors = [
            'red',
            'blue',
            'purple',
            'yellow',
            'orange',
            'green',
            'gray'
        ];
        return sprintf('%s', $colors[array_rand($colors)]);
    }

    
    
    
 
}
