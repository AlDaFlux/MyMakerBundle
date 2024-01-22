<?php

namespace Aldaflux\MyMakerBundle\Maker;


use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;


use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface; 

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;

use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Component\Console\Input\InputOption;



use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Str;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\HttpKernel\Kernel;




/**
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
final class MakeBundle extends AbstractMaker
{

    private $vendor;
    private $bundleName;
    private $parameters;

    public function __construct(FileManager $fileManager, ParameterBagInterface $parameters)
    {
        $this->fileManager = $fileManager;
        $this->parameters = $parameters;
         
    }
 
    public static function getCommandName(): string
    {
        return 'make:aldalflux:bundle';
    }
    
    
    public static function getCommandDescription(): string
    {
        return 'make:aldalflux:bundle';
    }
    
    

    /**
     * {@inheritdoc}
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates Bundle test - Dont work yet')
            ->setHelp("Create an empty bundle")
            ->addArgument('vendor', InputArgument::OPTIONAL, sprintf('The class name of the vendor (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm()."")))
            ->addArgument('bundle-name', InputArgument::OPTIONAL, sprintf('The class name of the bundle to create (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm()."Bundle")))
            ->addOption('with-collector', null, InputOption::VALUE_NONE, 'Create a Collector')
       ;
      
    }
  
    public function getSkeletonFolderBundle()
    {
        return($this->getSkeletonFolder().'/bundle/');
    }
    
    public function getSkeletonFolder()
    {
        return(__DIR__.'/../Resources/skeleton/');
    }
    

    public function getBundleRootFolder()
    {
        
        return(strtolower($this->getVendorName())."/". Str::asCommand($this->getBundleNameWhithoutVendor())."/");
//        return(strtolower($this->getVendorName())."/".Str::asCommand($this->getBundleName())."/");
    }
    public function getTemplateFolder()
    {
        return($this->getBundleRootFolder()."Resources/views/");
    }
    
    public function getVendorName()
    {
        return(Str::asCamelCase(strtolower($this->getVendorOriginalName())));
    }
    
    
    public function getVendorOriginalName()
    {
        return($this->vendor);
    }
    
    
    public function getBundleName()
    {
        return($this->bundleName);
    }
    
    public function getBundleNameWhithoutVendor()
    {
        $vendorLenght= strlen($this->vendor);
        if ((substr($this->bundleName,0,$vendorLenght))==$this->vendor)
        {   
            return(substr($this->bundleName,$vendorLenght));
        }
        else
        {
            return($this->bundleName);
        }
    }
    
    
    public function getBundleShortestName()
    {
        return(Str::removeSuffix($this->getBundleNameWhithoutVendor(),"Bundle"));
    }
    public function getBundleNameSimple()
    {
        return(Str::removeSuffix($this->getBundleName(),"Bundle"));
    }
    
    
    public function getAuthorInfos()
    {
     
        $config=$this->parameters->Get("aldaflux_mymaker.config");

        if (isset($config["author"]))
        {
            return ($config["author"]);
        }
        else
        {
            return (array());
            
        }

        
//        return("ChartjsBundle");
    }
    
    public function getHomepage()
    {
         return("https://github.com/".$this->getVendorName()."/".$this->getBundleName());
    }
    
    
    

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
            
            $this->vendor=$input->getArgument('vendor');

            $this->bundleName=$input->getArgument('bundle-name');
            
            
            if (! Str::hasSuffix($this->bundleName,"Bundle"))
            {
                $this->bundleName.="Bundle";
            }
            $vendorLenght= strlen($this->vendor);
                    
            if ((substr($this->bundleName,0,$vendorLenght))!=$this->vendor)
            {
                $this->bundleName=$this->vendor.$this->bundleName;
            }
            
            if (! Str::hasSuffix($this->bundleName,"Bundle"))
            {
                $this->bundleName.="Bundle";
            }
            
            $this->generator = new Generator($this->fileManager, $this->vendor.'\\'.$this->bundleName);
            
            $withCollector=$input->getOption('with-collector');
                    
            $loadServiceYml=$withCollector;
            
       
            
            $bundleClassDetails = $this->generator->createClassNameDetails($this->vendor.$this->bundleName,"");
            
            
//            $rootNode=Str::asRouteName($this->getVendorName().'/'.$this->getBundleNameSimple());
            $rootNode=Str::asRouteName($this->getBundleNameSimple());
            


            $bundleClassDetails = $this->generator->createClassNameDetails($this->bundleName,"");
            

            if ($withCollector)
            {
                $this->makeCollector();
            }
      
        
        
       
        $bundleClassNameDetails = $generator->createClassNameDetails('\\'.$this->getVendorName().'\\'.$this->getBundleName(),"Bundle");
        

        $nameSpace= ($bundleClassNameDetails->getFullName());

 
         
        $this->generator->generateFile(
                $this->getBundleRootFolder().$this->getBundleName().'.php',
                $this->getSkeletonFolderBundle().'bundle.tpl.php',[
                'class_name' => Str::getShortClassName($this->getBundleName()),
                'namespace' => $nameSpace,
                'loadServiceYml' => $loadServiceYml,
                    
            ]);


        
        $variables=[
                'name' => strtolower( $this->getVendorName().'/'.Str::asCommand($this->getBundleNameWhithoutVendor())),
                'namespace' => $nameSpace,
                'homepage' => $this->getHomepage(),
                'author' => $this->getAuthorInfos(),
                'psr4' => $this->getVendorName().'\\\\'.$this->getBundleName()."\\\\",
            ];
        $this->generator->generateFile($this->getBundleRootFolder()."composer.json",$this->getSkeletonFolderBundle().'composer.tpl.json',$variables);
        $this->generator->generateFile($this->getBundleRootFolder()."README.md",$this->getSkeletonFolderBundle().'README.tpl.md',["name"=>$this->getBundleName()]);
        $this->generator->generateFile($this->getBundleRootFolder()."DependencyInjection/Configuration.php",$this->getSkeletonFolderBundle().'/DependencyInjection/Configuration.tpl.php',['namespace' => $nameSpace,'root_node' => $rootNode]);
        $this->generator->generateFile($this->getBundleRootFolder()."DependencyInjection/".$this->getBundleNameSimple()."Extension.php",$this->getSkeletonFolderBundle().'/DependencyInjection/Extension.tpl.php',
                ['namespace' => $nameSpace,
                    'bundle_name_simple' => $this->getBundleNameSimple(),
                    'root_node' => $rootNode,
                    'load_service_yml' => $loadServiceYml
                ]);

        
        $this->generator->writeChanges();
        $this->writeSuccessMessage($io);
        


                
        $io->text([
            'Next: Open your voter and add your logic.',
            'Find the documentation at <fg=yellow>https://symfony.com/doc/current/security/voters.html</>',
        ]);
          
        $io->text(sprintf('Next: Check your new CRUD by going to <fg=yellow>%s/</>', "321321"));
    }

    
    
    /**
     * {@inheritdoc}
     */
    public function configureDependencies(DependencyBuilder $dependencies)
    {
 
    }

    
    function makeCollector()
    {
        
        
        
             $CollectorClassName=$this->getBundleShortestName()."Collector";
                $collectorClassDetails = $this->generator->createClassNameDetails(
                    $CollectorClassName,
                    'DataCollector\\'
                ); 
                $collectorNameString=Str::asTwigVariable($this->vendor).".".Str::asTwigVariable($collectorClassDetails->getShortName());
                
                
                $this->generator->generateFile(
                        $this->getBundleRootFolder().'Resources/config/services.yml',
                        $this->getSkeletonFolderBundle().'Resources/config/services.yml.tpl.php', 
                        [
                            "collector_service"=>$collectorClassDetails->getFullName(), 
                            "id_collector"=>$collectorNameString
                        ]);        
                
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
         
        $templatesPath = "data_collector/";
        
        
        
         $templatesPathCollector=$templatesPath.''.Str::asFilePath($collectorClassDetails->getShortName()).'.html.twig';

                  
         $templatesPathCollectorBundle="@".(Str::getShortClassName($this->getBundleNameSimple()))."/".$templatesPathCollector;
         
        // $collectorNameString=Str::asTwigVariable($collectorClassDetails->getShortName());
         
         
         
            
         
            $this->generator->generateFile(
            $this->getBundleRootFolder().'DataCollector/'.$this->getBundleShortestName().'Collector.php',
            $this->getSkeletonFolder().'DataCollector/DataCollector/Collector.tpl.php',
            [
                'namespace' => Str::getNamespace($collectorClassDetails->getFullName()),
                'collector_name_string' => $collectorNameString,
                'class_name' => $collectorClassDetails->getShortName(), 
                "template_path"=>$templatesPathCollectorBundle,                
                "parent_class_name"=>$parentClassName,                
                "extend_class"=>$extendClass,                
            ]
        );
            
            
            
            $iconRelativePath=$templatesPath.'icon/'.Str::asFilePath($this->getBundleShortestName()).'.svg';
            
            $iconPath="@".(Str::getShortClassName($this->getBundleName()))."/".$iconRelativePath;

            
            $this->generator->generateFile(
            $this->getTemplateFolder().$iconRelativePath,
            $this->getSkeletonFolder().'DataCollector/templates/data_collector/icon/icon.svg.tpl.php',['color'=>"gray"]);
            
            $this->generator->generateFile(
            $this->getTemplateFolder().$templatesPathCollector,
            $this->getSkeletonFolder().'DataCollector/templates/data_collector/template.html.twig.tpl.php',
                [   
                    "collector_name"=>$collectorClassDetails->getShortName(),
                    "icon_path"=>$iconPath
                    ]
            );
             
    }
     
}
